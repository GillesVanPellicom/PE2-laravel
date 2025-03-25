<x-app-layout>
    <div class="max-w-4xl mx-auto bg-white p-6 shadow-md rounded-lg mt-6">
        <!-- Notification Bell -->
        <div class="relative mb-4">
            <span class="text-2xl cursor-pointer" onclick="toggleNotifications()">🔔</span>
            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-2" id="notificationBadge">0</span>
            <div class="absolute right-0 mt-2 w-64 bg-white shadow-lg p-4 hidden" id="notificationDropdown"></div>
        </div>
        <div class="notifications">
    <h3>Notifications</h3>
    <ul>
        @foreach ($notifications as $notification)
            <li class="{{ $notification->is_read ? 'read' : 'unread' }}">
                <p>{{ $notification->messageTemplate->message }}</p>
                <span>{{ $notification->created_at->diffForHumans() }}</span>

                <!-- Mark as read button -->
                @if (!$notification->is_read)
                    <form action="{{ url('/notifications/'.$notification->id.'/read') }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-primary btn-sm">Mark as Read</button>
                    </form>
                @endif
            </li>
        @endforeach
    </ul>
</div>

        <!-- Holiday Info -->
        <div id="holidayInfo" class="bg-gray-100 p-4 rounded-md flex justify-between items-center">
            <span class="font-semibold text-lg">Remaining Holidays: <span id="remainingHolidays" class="text-blue-600 font-bold">0</span></span> 
            <span class="font-semibold text-lg">Sick Days Taken: <span id="sickDaysTaken" class="text-red-600 font-bold">0</span></span>
        </div>

        <!-- Calendar -->
        <div id="calendar" class="mt-4"></div>

        <!-- Buttons -->
        <div class="flex justify-between mt-6">
            <button id="saveRequests" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition">Save Requests</button>
            <a href="/manager-calendar" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition">View Requests</a>
            <button id="clearStorage" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition">Clear Data</button>
        </div>
    </div>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var selectedHolidays = JSON.parse(localStorage.getItem('selectedHolidays')) || {};
            var selectedSickDays = new Set(JSON.parse(localStorage.getItem('selectedSickDays')) || []);
            var remainingHolidays = {{ auth()->user()->employee->leave_balance }};
            var sickDaysTaken = selectedSickDays.size;
            var loggedInUser = @json(auth()->user()->name);

            // Update counters for holidays and sick days
            function updateCounters() {
                document.getElementById('remainingHolidays').textContent = remainingHolidays;
                document.getElementById('sickDaysTaken').textContent = sickDaysTaken;
            }

            // Add an event to the calendar
            function addEvent(date, type, period = "Full Day") {
                let title = type === "holiday" ? Holiday (${period}) : 'Sick Day';
                calendar.addEvent({
                    id: date,
                    title: title,
                    start: date,
                    allDay: true,
                    backgroundColor: type === "holiday" ? '#ff7f7f' : '#7fafff',
                    borderColor: type === "holiday" ? '#ff7f7f' : '#7fafff',
                    textColor: 'white'
                });
            }

            // Remove event from the calendar
            function removeEvent(date) {
                let event = calendar.getEventById(date);
                if (event) event.remove();
            }

            // Toggle holiday or sick day event
            function toggleEvent(date) {
                let dateObj = new Date(date);
                let today = new Date();
                today.setHours(0, 0, 0, 0);

                if (dateObj < today) {
                    alert('You cannot select a past date.');
                    return;
                }

                if (dateObj.getDay() === 0 || dateObj.getDay() === 6) {
                    alert('You cannot select weekends.');
                    return;
                }

                let choice = prompt("Choose: Type '1' for Paid Holiday, Type '2' for Sick Day");

                if (choice === "1") {
                    if (selectedHolidays[date]) {
                        delete selectedHolidays[date];
                        removeEvent(date);
                        remainingHolidays++;
                    } else {
                        if (remainingHolidays > 0) {
                            let period = prompt("Enter '1' for Full Day, '2' for AM, '3' for PM");
                            let periodText = period === "1" ? "Full Day" : period === "2" ? "AM" : "PM";

                            selectedHolidays[date] = periodText;
                            addEvent(date, "holiday", periodText);
                            remainingHolidays--;
                        } else {
                            alert('No remaining holidays.');
                        }
                    }
                } else if (choice === "2") {
                    if (selectedSickDays.has(date)) {
                        selectedSickDays.delete(date);
                        removeEvent(date);
                        sickDaysTaken--;
                    } else {
                        selectedSickDays.add(date);
                        addEvent(date, "sick");
                        sickDaysTaken++;
                    }
                } else {
                    alert("Invalid choice.");
                    return;
                }

                updateCounters();
                localStorage.setItem('selectedHolidays', JSON.stringify(selectedHolidays));
                localStorage.setItem('selectedSickDays', JSON.stringify([...selectedSickDays]));
            }

            // Calendar initialization
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'en-gb',
                firstDay: 1,
                dateClick: function(info) {
                    toggleEvent(info.dateStr);
                }
            });

            // Render the calendar
            calendar.render();
            Object.keys(selectedHolidays).forEach(date => addEvent(date, "holiday", selectedHolidays[date]));
            selectedSickDays.forEach(date => addEvent(date, "sick"));
            updateCounters();

            // Save requests
            document.getElementById('saveRequests').addEventListener('click', function() {
                let requestData = JSON.stringify({
                    holidays: selectedHolidays
                });

                fetch('/save-vacation', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: requestData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        alert("✅ Holiday requests saved successfully!");
                        remainingHolidays = data.remainingHolidays;
                        updateCounters();
                    } else {
                        alert("⚠️ Something went wrong!");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("❌ Failed to save requests. Please try again.");
                });
            });

            // Clear localStorage data
            document.getElementById('clearStorage').addEventListener('click', function() {
                localStorage.clear();
                alert("All data has been cleared.");
                location.reload();
            });

            // Notification System
            function toggleNotifications() {
                const dropdown = document.getElementById('notificationDropdown');
                dropdown.classList.toggle('hidden');
                fetchNotifications();
            }

            function fetchNotifications() {
                fetch('/notifications')
                    .then(response => response.json())
                    .then(data => {
                        const notificationDropdown = document.getElementById('notificationDropdown');
                        const notificationBadge = document.getElementById('notificationBadge');
                        let unreadCount = 0;

                        // Clear previous notifications
                        notificationDropdown.innerHTML = '';

                        // Loop through notifications and display them
                        data.forEach(notification => {
                            let li = document.createElement('li');
                            li.classList.add('p-2', 'bg-gray-100', 'rounded-md', 'cursor-pointer', 'hover:bg-gray-200');
                            li.innerHTML = notification.message_template.message;
                            li.addEventListener('click', function() {
                                markAsRead(notification.id, li);
                            });

                            notificationDropdown.appendChild(li);
                            if (!notification.is_read) unreadCount++;
                        });

                        // Update notification badge
                        notificationBadge.textContent = unreadCount;
                        notificationBadge.classList.toggle('hidden', unreadCount === 0);
                    });
            }

            function markAsRead(notificationId, notificationElement) {
                fetch(/notifications/${notificationId}/read, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                }).then(response => response.json())
                  .then(() => {
                      notificationElement.classList.add('bg-gray-300'); // Mark it as read visually
                      fetchNotifications(); // Refresh notifications
                  });
            }

            // Fetch notifications on page load
            fetchNotifications();
        });
    </script>
</x-app-layout> 