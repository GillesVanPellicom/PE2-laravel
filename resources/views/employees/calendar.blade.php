<x-app-layout>
    <x-sidebar>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Ensure required libraries are loaded
    if (typeof moment === 'undefined') {
        console.error("Moment.js is not loaded. Ensure it's included in your project.");
        return;
    }

    // Get DOM elements safely
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationBadge = document.getElementById('notificationBadge');
    const saveRequestsBtn = document.getElementById('saveRequests');
    const calendarEl = document.getElementById('calendar');

    if (!calendarEl) {
        console.error("Calendar element not found!");
        return;
    }

    // Fetch notifications function
    function fetchNotifications() {
        fetch('/notifications')
            .then(response => response.json())
            .then(data => {
                notificationDropdown.innerHTML = '';
                let unreadCount = 0;

                data.forEach(notification => {
                    let li = document.createElement('li');
                    li.classList.add('p-2', 'bg-gray-100', 'rounded-md', 'cursor-pointer', 'hover:bg-gray-200');

                    // Include the start_date in the notification message
                    li.innerHTML = `
                        ${notification.message_template?.message || 'No message'} <br>
                        <span class='text-sm text-gray-500'>Date: ${notification.start_date || 'N/A'}</span><br>
                        <span class='text-sm text-gray-500'>${moment(notification.created_at).fromNow()}</span>
                    `;

                    const markAsReadButton = document.createElement('button');
                    markAsReadButton.textContent = 'Mark as Read';
                    markAsReadButton.classList.add('btn', 'btn-primary', 'btn-sm', 'mt-2', 'text-xs');
                    markAsReadButton.addEventListener('click', function () {
                        markAsRead(notification.id, li);
                    });
                    li.appendChild(markAsReadButton);

                    notificationDropdown.appendChild(li);
                    unreadCount++;
                });

                notificationBadge.textContent = unreadCount;
                notificationBadge.classList.toggle('hidden', unreadCount === 0);
            })
            .catch(error => console.error("Error fetching notifications:", error));
    }

    function markAsRead(notificationId, notificationElement) {
        fetch(`/notifications/${notificationId}/read`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(() => {
            // Remove the notification from the dropdown
            notificationElement.remove();

            // Update the unread count
            let unreadCount = parseInt(notificationBadge.textContent);
            unreadCount--; // Decrease the unread count
            notificationBadge.textContent = unreadCount;
            notificationBadge.classList.toggle('hidden', unreadCount === 0); // Hide badge if no unread notifications
        })
        .catch(error => console.error("Error marking notification as read:", error));
    }

    // Calendar setup
    let selectedHolidays = JSON.parse(localStorage.getItem('selectedHolidays')) || {};
    let selectedSickDays = new Set(JSON.parse(localStorage.getItem('selectedSickDays')) || []);
    let remainingHolidays = {{ auth()->user()->employee->leave_balance }};
    let sickDaysTaken = selectedSickDays.size;

    function updateCounters() {
        document.getElementById('remainingHolidays').textContent = remainingHolidays;
        document.getElementById('sickDaysTaken').textContent = sickDaysTaken;
    }

    function addEvent(date, type, period = "Full Day") {
        let title = type === "holiday" ? `Holiday (${period})` : 'Sick Day';
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

    function removeEvent(date) {
        let event = calendar.getEventById(date);
        if (event) event.remove();
    }

    function toggleEvent(date) {
        let dateObj = new Date(date);
        let today = new Date();
        today.setHours(0, 0, 0, 0);

        if (dateObj < today) return alert('You cannot select a past date.');
        if (dateObj.getDay() === 0 || dateObj.getDay() === 6) return alert('You cannot select weekends.');

        let choice = prompt("Choose: Type '1' for Paid Holiday, Type '2' for Sick Day");

        if (choice === "1") {
            if (selectedHolidays[date]) {
                delete selectedHolidays[date];
                removeEvent(date);
                remainingHolidays++;
            } else if (remainingHolidays > 0) {
                let periodChoice = prompt("Enter '1' for Whole Day, '2' for First Half, '3' for Second Half");
                let periodText = periodChoice === "1" ? "Whole Day" : periodChoice === "2" ? "First Half" : "Second Half";
                selectedHolidays[date] = periodText;
                addEvent(date, "holiday", periodText);
                remainingHolidays -= (periodChoice === "1") ? 1 : 0.5; // Deduct full or half day
            } else {
                return alert('No remaining holidays.');
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
            return alert("Invalid choice.");
        }

        updateCounters();
        localStorage.setItem('selectedHolidays', JSON.stringify(selectedHolidays));
        localStorage.setItem('selectedSickDays', JSON.stringify([...selectedSickDays]));
    }

    // Fetch vacations and add them to the calendar
    function fetchVacations() {
        fetch('/get-vacations')
            .then(response => response.json())
            .then(data => {
                data.forEach(vacation => {
                    let color;
                    switch (vacation.approve_status.toLowerCase()) {
                        case 'approved':
                            color = '#28A745'; // Green
                            break;
                        case 'pending':
                            color = '#FFC107'; // Yellow
                            break;
                        case 'rejected':
                            color = '#DC3545'; // Red
                            break;
                        default:
                            color = '#6C757D'; // Gray for unknown status
                    }

                    // Include day_type in the event title
                    calendar.addEvent({
                        title: `${vacation.vacation_type} (${vacation.day_type}) - ${vacation.approve_status}`,
                        start: vacation.start_date,
                        end: vacation.end_date ? vacation.end_date : vacation.start_date, // Use end_date if available
                        allDay: true,
                        backgroundColor: color,
                        borderColor: color,
                        textColor: 'white'
                    });
                });
            })
            .catch(error => console.error("Error fetching vacations:", error));
    }

    let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'en-gb',
        firstDay: 1,
        dateClick: function (info) {
            toggleEvent(info.dateStr);
        }
    });

    calendar.render();
    Object.keys(selectedHolidays).forEach(date => addEvent(date, "holiday", selectedHolidays[date]));
    selectedSickDays.forEach(date => addEvent(date, "sick"));
    fetchVacations(); // Fetch vacations from the database and add them to the calendar
    updateCounters();

    saveRequestsBtn.addEventListener('click', function () {
        fetch('/save-vacation', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ holidays: selectedHolidays })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                alert("✅ Holiday requests saved successfully!");

                // Clear localStorage after saving requests
                localStorage.removeItem('selectedHolidays');
                localStorage.removeItem('selectedSickDays');

                // Reset local variables
                selectedHolidays = {};
                selectedSickDays = new Set();

                // Reload the page to refresh the calendar and counters
                location.reload();
            } else {
                alert("⚠️ Something went wrong!");
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("❌ Failed to save requests. Please try again.");
        });
    });

    fetchNotifications();

    // Function to update vacation status
    function updateVacationStatus(vacationId, newStatus) {
        fetch(`/vacations/${vacationId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(updatedVacation => {
            // Remove the pending event from the calendar
            const event = calendar.getEventById(updatedVacation.id);
            if (event) event.remove();

            // Add the updated vacation with the new status
            calendar.addEvent({
                id: updatedVacation.id,
                title: `${updatedVacation.vacation_type} (${updatedVacation.approve_status})`,
                start: updatedVacation.start_date,
                end: updatedVacation.end_date ? updatedVacation.end_date : updatedVacation.start_date,
                allDay: true,
                backgroundColor: updatedVacation.color,
                borderColor: updatedVacation.color,
                textColor: 'white'
            });
        })
        .catch(error => console.error("Error updating vacation status:", error));
    }

    // Example usage: Call this function when a vacation status is updated
    // updateVacationStatus(vacationId, 'approved');
});

function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.toggle('hidden');
    fetchNotifications();
}

</script>

<div class="max-w-4xl mx-auto bg-white p-6 shadow-md rounded-lg mt-6">
    <!-- Notification Bell on the left side -->
    <div class="flex ">
        <!-- Notification Bell and Badge (Left Side) -->
        <div class="relative">
            <span class="text-2xl cursor-pointer" onclick="toggleNotifications()">🔔</span>
            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-2" id="notificationBadge">0</span>
            <div class="absolute right-0 mt-2 w-64 bg-white shadow-lg p-4 hidden" id="notificationDropdown"></div>
        </div>

        <!-- Holiday Info and Calendar Section -->
        <div class="flex-1">
            <!-- Holiday Info -->
            <div id="holidayInfo" class="bg-gray-100 p-4 rounded-md flex justify-between items-center">
                <span class="font-semibold text-lg">Remaining Holidays: <span id="remainingHolidays" class="text-blue-600 font-bold">0</span></span> 
                <span class="font-semibold text-lg">Sick Days Taken: <span id="sickDaysTaken" class="text-red-600 font-bold">0</span></span>
            </div>

            <!-- Calendar -->
            <div id="calendar" class="mt-4" style="height: 600px;"></div>
        </div>
    </div>

    <!-- Buttons -->
    <div class="flex justify-between mt-6">
        <button id="saveRequests" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition">Save Requests</button>
        <a href="/manager-calendar" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition">View Requests</a>
        <button id="clearStorage" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition">Clear Data</button>
    </div>
</div>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>

   
    <script>
    // Notification System


</script>
    </x-sidebar>
</x-app-layout>