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
    const sickDayRangePicker = document.getElementById('sickDayRangePicker');
    const sickStartDate = document.getElementById('sickStartDate');
    const sickEndDate = document.getElementById('sickEndDate');
    const applySickDaysBtn = document.getElementById('applySickDaysBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const eventModal = document.getElementById('eventModal');

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
        fetch(`/workspace/notifications/${notificationId}/read`, {
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

    let selectedHolidays = JSON.parse(localStorage.getItem('selectedHolidays')) || {};
    let selectedSickDays = new Set(JSON.parse(localStorage.getItem('selectedSickDays')) || []);
    let remainingHolidays = {{ auth()->user()->employee->leave_balance }};
    let sickDaysTaken = selectedSickDays.size;
    let currentDate = null;

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

        currentDate = date;

        // Show the modal
        document.getElementById('eventModal').classList.remove('hidden');

        // Holiday button logic
        document.getElementById('holidayBtn').onclick = function () {
            document.getElementById('holidayOptions').classList.remove('hidden');
            document.getElementById('sickDayBtn').disabled = true; // Disable sick day selection
        };

        // Sick day button logic
        document.getElementById('sickDayBtn').onclick = function () {
            document.getElementById('sickDayRangePicker').classList.remove('hidden'); // Show the date range picker
        };

        // Handle sick day range submission
        document.getElementById('applySickDaysBtn').onclick = function () {
            const startDate = document.getElementById('sickStartDate').value;
            const endDate = document.getElementById('sickEndDate').value;

            if (!startDate || !endDate) {
                alert('Please select both start and end dates for the sick leave.');
                return;
            }

            let current = new Date(startDate);
            const end = new Date(endDate);

            while (current <= end) {
                const formattedDate = current.toISOString().split('T')[0];
                selectedSickDays.add(formattedDate);
                addEvent(formattedDate, "sick");
                current.setDate(current.getDate() + 1);
            }

            sickDaysTaken = selectedSickDays.size;
            closeModal();
        };

        // Holiday option buttons
        document.getElementById('wholeDayBtn').onclick = function () {
            selectedHolidays[date] = 'Whole Day';
            addEvent(date, "holiday", "Whole Day");
            remainingHolidays--;
            closeModal();
        };

        document.getElementById('firstHalfBtn').onclick = function () {
            selectedHolidays[date] = 'First Half';
            addEvent(date, "holiday", "First Half");
            remainingHolidays -= 0.5;
            closeModal();
        };

        document.getElementById('secondHalfBtn').onclick = function () {
            selectedHolidays[date] = 'Second Half';
            addEvent(date, "holiday", "Second Half");
            remainingHolidays -= 0.5;
            closeModal();
        };

        // Close the modal
        document.getElementById('closeModalBtn').onclick = closeModal;

        updateCounters();
        localStorage.setItem('selectedHolidays', JSON.stringify(selectedHolidays));
        localStorage.setItem('selectedSickDays', JSON.stringify([...selectedSickDays]));
    }

    function closeModal() {
        document.getElementById('eventModal').classList.add('hidden');
        document.getElementById('holidayOptions').classList.add('hidden'); // Hide holiday options again
        document.getElementById('sickDayRangePicker').classList.add('hidden'); // Hide sick day range picker
        document.getElementById('sickDayBtn').disabled = false; // Re-enable sick day button
        sickStartDate.value = '';
        sickEndDate.value = '';
    }

    // Fetch vacations and add them to the calendar
    function fetchVacations() {
        fetch('/workspace/get-vacations')
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
        const sickDaysArray = [...selectedSickDays].map(date => {
            return { start_date: date, end_date: date }; // Format sick days as date ranges
        });

        fetch('/workspace/save-vacation', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                holidays: selectedHolidays,
                sickDays: sickDaysArray // Send sick days as date ranges
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                alert("✅ Requests saved successfully!");

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
        fetch(`/workspace/vacations/${vacationId}/update-status`, {
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
        <a href="/workspace/manager-calendar" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition">View Requests</a>

    </div>
</div>

<!-- Modal Structure -->
<div id="eventModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
  <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
    <h2 class="text-xl font-semibold mb-4">Choose Event Type</h2>

    <div>
      <label class="block mb-2">Choose: Type '1' for Paid Holiday, Type '2' for Sick Day</label>
      <button id="holidayBtn" class="block w-full bg-green-500 text-white py-2 px-4 rounded mb-2">Holiday</button>
      <button id="sickDayBtn" class="block w-full bg-blue-500 text-white py-2 px-4 rounded">Sick Day</button>
    </div>

    <div id="holidayOptions" class="mt-4 hidden">
      <label class="block mb-2">Enter '1' for Whole Day, '2' for First Half, '3' for Second Half</label>
      <button id="wholeDayBtn" class="w-full bg-yellow-500 text-white py-2 px-4 rounded mb-2">Whole Day</button>
      <button id="firstHalfBtn" class="w-full bg-yellow-500 text-white py-2 px-4 rounded mb-2">First Half</button>
      <button id="secondHalfBtn" class="w-full bg-yellow-500 text-white py-2 px-4 rounded">Second Half</button>
    </div>

    <div id="sickDayRangePicker" class="mt-4 hidden">
      <label class="block mb-2">Select Sick Leave Date Range:</label>
      <input type="date" id="sickStartDate" class="block w-full mb-2 border rounded px-2 py-1">
      <input type="date" id="sickEndDate" class="block w-full mb-2 border rounded px-2 py-1">
      <button id="applySickDaysBtn" class="w-full bg-blue-500 text-white py-2 px-4 rounded">Apply Sick Days</button>
    </div>

    <button id="closeModalBtn" class="mt-4 bg-gray-300 py-2 px-4 rounded w-full">Close</button>
  </div>
</div>



<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    </x-sidebar>
</x-app-layout>
