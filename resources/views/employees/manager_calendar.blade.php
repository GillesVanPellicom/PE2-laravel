<x-app-layout>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script> <!-- Ensure moment.js is loaded -->
    <script>
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

                    // Display the notification message
                    li.innerHTML = `
                        ${notification.message} <br>
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

    function displaySickLeaveNotification(employeeName, date) {
        let notificationDropdown = document.getElementById("notificationDropdown");

        // Create a new notification item
        let notificationItem = document.createElement("div");
        notificationItem.className = "notification-item p-2 bg-gray-100 rounded-md shadow-sm mb-2";

        // Format the notification content
        notificationItem.innerHTML = `
            <p>Employee <strong>${employeeName}</strong> has called in sick for <strong>${formatDate(date)}</strong>.</p>
            <span class="text-sm text-gray-500">${moment().fromNow()}</span>
            <button class="btn btn-primary btn-sm mt-2 text-xs" onclick="markAsReadFromManager('${employeeName}', '${date}')">Mark as Read</button>
        `;

        // Append the notification to the dropdown
        notificationDropdown.appendChild(notificationItem);
    }

    function markAsReadFromManager(employeeName, date) {
        // Simulate marking the notification as read
        console.log(`Marked as read: ${employeeName} on ${date}`);

        // Optionally, remove the notification from the dropdown
        fetchNotifications(); // Refresh notifications
    }

    function fetchSickDaysWithEmoji() {
        const sickLeaveDropdown = document.getElementById("sickLeaveNotificationDropdown");
        const sickLeaveBadge = document.getElementById("sickLeaveNotificationBadge");

        if (!sickLeaveDropdown || !sickLeaveBadge) {
            console.error("Sick leave dropdown or badge element not found!");
            return;
        }

        fetch('/manager/sick-day-notifications')
            .then(response => {
                if (!response.ok) {
                    console.error(`HTTP error! status: ${response.status}`);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                sickLeaveDropdown.innerHTML = ''; // Clear the dropdown content
                let unreadCount = 0;

                data.forEach(notification => {
                    let li = document.createElement('li');
                    li.classList.add('p-2', 'bg-gray-100', 'rounded-md', 'cursor-pointer', 'hover:bg-gray-200');

                    // Display the notification message
                    li.innerHTML = `
                        ${notification.message} <br>
                        <span class='text-sm text-gray-500'>${moment(notification.created_at).fromNow()}</span>
                    `;

                    const markAsReadButton = document.createElement('button');
                    markAsReadButton.textContent = 'Mark as Read';
                    markAsReadButton.classList.add('btn', 'btn-primary', 'btn-sm', 'mt-2', 'text-xs');
                    markAsReadButton.addEventListener('click', function () {
                        markSickLeaveAsRead(notification.id, li);
                    });
                    li.appendChild(markAsReadButton);

                    sickLeaveDropdown.appendChild(li);
                    unreadCount++;
                });

                sickLeaveBadge.textContent = unreadCount;
                sickLeaveBadge.classList.toggle('hidden', unreadCount === 0);
            })
            .catch(error => console.error("Error fetching sick leave data:", error));
    }

    function markSickLeaveAsRead(notificationId, notificationElement) {
        const sickLeaveBadge = document.getElementById("sickLeaveNotificationBadge"); // Ensure this element is retrieved
        fetch(`/manager/sick-day-notifications/${notificationId}/read`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(() => {
            // Remove the notification from the dropdown
            notificationElement.remove();

            // Update the unread count
            let unreadCount = parseInt(sickLeaveBadge.textContent);
            unreadCount--; // Decrease the unread count
            sickLeaveBadge.textContent = unreadCount;
            sickLeaveBadge.classList.toggle('hidden', unreadCount === 0); // Hide badge if no unread notifications
        })
        .catch(error => console.error("Error marking notification as read:", error));
    }
    </script>
    <div class="flex gap-6">
    <!-- Sidebar (Smaller) -->
    <div class="w-1/5 bg-white p-6 shadow-lg rounded-lg space-y-6">
        <h2 class="text-xl font-semibold text-gray-800">Available Employees</h2>
        <div id="employeeList" class="p-4 bg-gray-100 rounded-md shadow-sm"></div>

        <h2 class="text-xl font-semibold text-gray-800">Sick Employees</h2>
        <div id="sickEmployeeList" class="p-4 bg-red-100 rounded-md shadow-sm"></div>

        <h2 class="text-xl font-semibold text-gray-800">Holiday Requests</h2>
        <div id="holidayEmployeeList" class="p-4 bg-yellow-100 rounded-md shadow-sm"></div>

        <h2 class="text-xl font-semibold text-gray-800">Training Sessions</h2>
        <div id="trainingList" class="p-4 bg-blue-100 rounded-md shadow-sm"></div>
        
        <button onclick="addTrainingSession()" class="w-full bg-blue-500 text-white py-2 rounded-md shadow-md hover:bg-blue-600 transition duration-200 ease-in-out">Add Training</button>
    </div>

    <!-- Calendar Section (Centered) -->
    <div class="w-3/5 bg-white p-8 shadow-lg rounded-lg space-y-6">
    <div class="flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">Manager Calendar</h1>

    <!-- Notifications Container -->
    <div class="flex space-x-6 relative">
        <!-- Holiday Requests Notification -->
        <div class="relative">
            <span class="text-2xl cursor-pointer text-gray-800" onclick="toggleNotifications()">🔔</span>
            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-2" id="notificationBadge">0</span>
            <div class="absolute right-0 mt-2 w-64 bg-white shadow-lg p-4 rounded-md hidden z-50" id="notificationDropdown"></div>
        </div>

        <!-- Sick Leave Notification -->
        <div class="relative">
            <span class="text-2xl cursor-pointer text-gray-800" onclick="toggleSickLeaveNotifications()">🤒</span>
            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-2" id="sickLeaveNotificationBadge">0</span>
            <div class="absolute right-0 mt-2 w-64 bg-white shadow-lg p-4 rounded-md hidden z-50" id="sickLeaveNotificationDropdown"></div>
        </div>
    </div>
</div>

        <div id="calendar" class="mt-4"></div>
    </div>
</div>

<div class="mt-6 text-center">
    <a href="employees/calendar" class="inline-block bg-green-500 text-white px-6 py-3 rounded-md shadow-md hover:bg-green-600 transition duration-200 ease-in-out">View Calendar</a>
</div>


    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let employees = @json($employees);
        let sickEmployees = JSON.parse(localStorage.getItem('sickEmployees')) || [];
        let trainingSessions = JSON.parse(localStorage.getItem('trainingSessions')) || [];
        let approvedHolidays = JSON.parse(localStorage.getItem('approvedHolidays')) || [];
        let availability = @json($availability);

        let employeeList = document.getElementById("employeeList");

        // Display Employees
        employees.forEach(emp => {
            let div = document.createElement("div");
            div.className = "employee";
            div.innerText = `${emp.user.first_name} ${emp.user.last_name}`;
            div.onclick = function() { markSick(emp.user.first_name); };
            employeeList.appendChild(div);
        });

        updateSickEmployees();
        updateTrainingSessions();
        initializeCalendar(approvedHolidays, trainingSessions, availability);
        fetchHolidayRequests();
        fetchSickDaysWithEmoji();
    });

    function calculateAvailablePercentages(availability, startDate, endDate) {
        let availabilityBars = [];

        // Generate bars for all days in the given range
        for (let date = new Date(startDate); date <= endDate; date.setDate(date.getDate() + 1)) {
            let dayOfWeek = date.getDay(); // 0 = Sunday, 6 = Saturday

            // Skip Saturday (6) and Sunday (0)
            if (dayOfWeek === 1 || dayOfWeek === 0) {
                continue;
            }

            let formattedDate = date.toISOString().split('T')[0];
            let dayAvailability = availability[formattedDate] || {
                morning: { available: availability.totalEmployees, percentage: 100 },
                afternoon: { available: availability.totalEmployees, percentage: 100 },
                fullDay: { available: availability.totalEmployees, percentage: 100 },
            };

            // Morning (08:00–12:00)
            let morningColor = dayAvailability.morning.percentage < 80 ? '#DC3545' : '#28A745'; // Red if < 80%, green otherwise
            let morningTitle = `Morning (08:00–12:00): ${dayAvailability.morning.percentage.toFixed(1)}% (${dayAvailability.morning.available}/${availability.totalEmployees})`;

            // Afternoon (12:00–17:00)
            let afternoonColor = dayAvailability.afternoon.percentage < 80 ? '#DC3545' : '#28A745'; // Red if < 80%, green otherwise
            let afternoonTitle = `Afternoon (12:00–17:00): ${dayAvailability.afternoon.percentage.toFixed(1)}% (${dayAvailability.afternoon.available}/${availability.totalEmployees})`;

            // Full Day (08:00–17:00)
            let fullDayColor = dayAvailability.fullDay.percentage < 80 ? '#DC3545' : '#28A745'; // Red if < 80%, green otherwise
            let fullDayTitle = `Full Day (08:00–17:00): ${dayAvailability.fullDay.percentage.toFixed(1)}% (${dayAvailability.fullDay.available}/${availability.totalEmployees})`;

            // Add events for each time slot
            availabilityBars.push({
                title: morningTitle,
                start: formattedDate + 'T08:00:00',
                end: formattedDate + 'T12:00:00',
                backgroundColor: morningColor,
                borderColor: morningColor,
                textColor: 'white',
            });

            availabilityBars.push({
                title: afternoonTitle,
                start: formattedDate + 'T12:00:00',
                end: formattedDate + 'T17:00:00',
                backgroundColor: afternoonColor,
                borderColor: afternoonColor,
                textColor: 'white',
            });

            availabilityBars.push({
                title: fullDayTitle,
                start: formattedDate + 'T08:00:00',
                end: formattedDate + 'T17:00:00',
                backgroundColor: fullDayColor,
                borderColor: fullDayColor,
                textColor: 'white',
            });
        }

        return availabilityBars;
    }

    function initializeCalendar(approvedHolidays, trainings, availability) {
        let calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
            initialView: 'timeGridWeek', // Week view
            firstDay: 1, // Start the week on Monday
            locale: 'en-gb',
            weekends: false, // Exclude weekends (Saturday and Sunday)
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridWeek,timeGridDay'
            },
            editable: false,
            droppable: false,
            slotMinTime: '07:00:00', // Start time for the calendar
            slotMaxTime: '19:00:00', // End time for the calendar
            events: function(fetchInfo, successCallback, failureCallback) {
                // Calculate the visible date range
                let startDate = fetchInfo.start;
                let endDate = fetchInfo.end;

                // Generate availability bars for the visible range
                let availabilityBars = calculateAvailablePercentages(availability, startDate, endDate);

                // Combine holiday, training, and availability bar events
                let events = [];

                approvedHolidays.forEach(holiday => {
                    events.push({
                        title: `${holiday.name}'s Holiday (${holiday.day_type})`,
                        start: holiday.start_date,
                        end: holiday.end_date || holiday.start_date,
                        allDay: true,
                        backgroundColor: '#ff7f7f',
                        borderColor: '#ff4f4f',
                        textColor: 'white'
                    });
                });

                trainings.forEach(session => {
                    events.push({
                        title: `Training: ${session.topic}`,
                        start: session.date,
                        allDay: true,
                        backgroundColor: '#7f7fff',
                        borderColor: '#4f4fff',
                        textColor: 'white'
                    });
                });

                events.push(...availabilityBars); // Add availability bars to the events array

                successCallback(events); // Pass events to the calendar
            },
            dateClick: function(info) {
                updateSidebar(info.dateStr);
            }
        });

        calendar.render();
    }

    function markSick(employeeName) {
        let sickEmployees = JSON.parse(localStorage.getItem('sickEmployees')) || [];
        if (!sickEmployees.includes(employeeName)) {
            sickEmployees.push(employeeName);
            localStorage.setItem('sickEmployees', JSON.stringify(sickEmployees));
            updateSickEmployees();
        }
    }

    function updateAvailableEmployees(holidays, sickEmployees = []) {
        let employeeList = document.getElementById("employeeList");
        employeeList.innerHTML = ""; // Clear list

        let employees = @json($employees); // Original list of all employees

        // Get employees who are on holiday
        let holidayEmployeeNames = holidays.map(h => h.name);

        // Get employees who are sick
        let sickEmployeeNames = sickEmployees.map(s => s.name);

        employees.forEach(emp => {
            let fullName = `${emp.user.first_name} ${emp.user.last_name}`;
            
            // Only add employee if NOT on holiday and NOT sick
            if (!holidayEmployeeNames.includes(fullName) && !sickEmployeeNames.includes(fullName)) {
                let div = document.createElement("div");
                div.className = "employee";
                div.innerText = fullName;
                div.onclick = function () { markSick(fullName); };
                employeeList.appendChild(div);
            }
        });
    }

    function updateSidebar(date) {
        console.log("Clicked date:", date); // Debugging log

        let formattedDate = formatDateForComparison(date); // Format the date as "YYYY-MM-DD"

        // Fetch Approved Holidays and Sick Leaves from Backend
        fetch('/approved-vacations')
            .then(response => response.json())
            .then(data => {
                console.log("Fetched approved vacations:", data); // Debugging log

                // Filter out sick leave entries from the holiday list
                let holidayEmployees = data.filter(holiday => 
                    holiday.vacation_type !== 'Sick Leave' && // Exclude sick leave
                    formattedDate >= holiday.start_date && formattedDate <= holiday.end_date
                );

                console.log("Employees on holiday:", holidayEmployees); // Debugging log

                // Display employees on holiday in the sidebar
                displayHolidays(formattedDate, holidayEmployees);

                // Fetch Sick Leaves for the selected date
                fetch('/get-vacations')
                    .then(response => response.json())
                    .then(data => {
                        console.log("Fetched vacations:", data); // Debugging log

                        // Filter sick leaves for the selected date
                        let sickEmployees = data.filter(vacation => 
                            vacation.vacation_type === 'Sick Leave' &&
                            formattedDate >= vacation.start_date && formattedDate <= vacation.end_date
                        );

                        console.log("Employees on sick leave:", sickEmployees); // Debugging log

                        // Display sick employees in the sidebar
                        displaySickLeaves(formattedDate, sickEmployees);

                        // Update available employees list
                        updateAvailableEmployees(holidayEmployees, sickEmployees);
                    })
                    .catch(error => console.error("Error fetching sick leaves:", error));
            })
            .catch(error => console.error("Error fetching approved vacations:", error));
    }

    // Function to Display Holidays in the Sidebar
    function displayHolidays(formattedDate, holidayEmployees) {
        let holidayEmployeeList = document.getElementById("holidayEmployeeList");
        
        // Clear the list first to prevent duplicates
        holidayEmployeeList.innerHTML = "";  

        if (holidayEmployees.length > 0) {
            let header = document.createElement("h3");
            header.innerText = `Employees on Holiday (${formattedDate})`;
            holidayEmployeeList.appendChild(header);

            holidayEmployees.forEach(holiday => {
                let div = document.createElement("div");
                div.className = "holiday-employee";
                div.innerText = `${holiday.name} (${holiday.day_type || 'N/A'})`; // Include day_type in the display
                holidayEmployeeList.appendChild(div);
            });
        } else {
            let noHoliday = document.createElement("p");
            noHoliday.innerText = "No employees on holiday this day.";
            holidayEmployeeList.appendChild(noHoliday);
        }
    }

    // Function to Display Sick Leaves in the Sidebar
    function displaySickLeaves(formattedDate, sickEmployees) {
        let sickEmployeeList = document.getElementById("sickEmployeeList");
        
        // Clear the list first to prevent duplicates
        sickEmployeeList.innerHTML = "";  

        if (sickEmployees.length > 0) {
            let header = document.createElement("h3");
            header.innerText = `Employees on Sick Leave (${formattedDate})`;
            sickEmployeeList.appendChild(header);

            sickEmployees.forEach(sick => {
                let div = document.createElement("div");
                div.className = "sick-employee";
                div.innerText = `${sick.name} (${sick.day_type || 'N/A'})`; // Include day_type in the display
                sickEmployeeList.appendChild(div);
            });
        } else {
            let noSick = document.createElement("p");
            noSick.innerText = "No employees on sick leave this day.";
            sickEmployeeList.appendChild(noSick);
        }
    }

    // Format date for backend comparison ("YYYY-MM-DD")
    function formatDateForComparison(date) {
        return new Date(date).toISOString().split('T')[0]; 
    }

    // Helper function to format date cleanly
    function formatDate(dateString) {
        let date = new Date(dateString);
        return date.toLocaleDateString("en-GB", { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    }

    function updateSickEmployees() {
        let sickEmployeeList = document.getElementById("sickEmployeeList");
        sickEmployeeList.innerHTML = "";
        let sickEmployees = JSON.parse(localStorage.getItem('sickEmployees')) || [];

        sickEmployees.forEach(emp => {
            let div = document.createElement("div");
            div.className = "sick-employee";
            div.innerText = emp;
            sickEmployeeList.appendChild(div);
        });
    }

    function addTrainingSession() {
        let topic = prompt("Enter training topic:");
        let date = prompt("Enter training date (YYYY-MM-DD):");
        if (topic && date) {
            let trainingSessions = JSON.parse(localStorage.getItem('trainingSessions')) || [];
            trainingSessions.push({ topic, date });
            localStorage.setItem('trainingSessions', JSON.stringify(trainingSessions));
            updateTrainingSessions();
        }
    }

    function updateTrainingSessions() {
        let trainingList = document.getElementById("trainingList");
        trainingList.innerHTML = "";
        let trainingSessions = JSON.parse(localStorage.getItem('trainingSessions')) || [];

        trainingSessions.forEach(session => {
            let div = document.createElement("div");
            div.className = "training-session";
            div.innerText = `${session.topic} on ${formatDate(session.date)}`;
            trainingList.appendChild(div);
        });
    }

    function clearStorage() {
        localStorage.clear();
        alert("All data has been cleared.");
        location.reload();
    }

    function approveVacation(vacationId) {
        sendVacationUpdate(vacationId, "approved");
    }

    function denyVacation(vacationId, dayType) {
        sendVacationUpdate(vacationId, "rejected", dayType);
    }

    function sendVacationUpdate(vacationId, status, dayType = null) {
        $.ajax({
            url: `/vacations/${vacationId}/update-status`,
            type: "POST",
            data: { status: status, day_type: dayType },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                console.log("Vacation updated:", response);
            },
            error: function(xhr) {
                console.error("Failed to update vacation", xhr.responseText);
            }
        });
    }

    async function fetchApprovedVacations(date) {
        try {
            let response = await fetch('/approved-vacations');
            let vacations = await response.json();

            // Filter only the vacations that match the selected date
            let filteredVacations = vacations.filter(vacation => {
                return date >= vacation.start_date && date <= vacation.end_date;
            });

            return filteredVacations;
        } catch (error) {
            console.error("Error fetching approved vacations:", error);
            return [];
        }
    }

    function updateVacationStatus(vacationId, newStatus, vacationElement) {
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
            // Remove the vacation element from the UI
            vacationElement.remove();

            // Optionally, update the notification badge count
            const notificationBadge = document.getElementById('notificationBadge');
            if (notificationBadge) {
                let unreadCount = parseInt(notificationBadge.textContent);
                unreadCount = Math.max(0, unreadCount - 1); // Ensure count doesn't go below 0
                notificationBadge.textContent = unreadCount;
                notificationBadge.classList.toggle('hidden', unreadCount === 0);
            }

            console.log(`Vacation ${newStatus} successfully.`);
        })
        .catch(error => console.error("Error updating vacation status:", error));
    }

    // Attach event listeners to the Approve and Reject buttons dynamically
    function attachVacationActionListeners() {
        document.querySelectorAll('.approve-button').forEach(button => {
            button.addEventListener('click', function () {
                const vacationId = this.dataset.vacationId;
                const vacationElement = this.closest('.vacation-item');
                updateVacationStatus(vacationId, 'approved', vacationElement);
            });
        });

        document.querySelectorAll('.reject-button').forEach(button => {
            button.addEventListener('click', function () {
                const vacationId = this.dataset.vacationId;
                const vacationElement = this.closest('.vacation-item');
                updateVacationStatus(vacationId, 'rejected', vacationElement);
            });
        });
    }

    // Call this function after fetching holiday requests
    function fetchHolidayRequests() {
        fetch('/pending-vacations')
            .then(response => response.json())
            .then(requests => {
                const notificationDropdown = document.getElementById("notificationDropdown");
                notificationDropdown.innerHTML = "";

                document.getElementById("notificationBadge").innerText = requests.length;

                requests.forEach(request => {
                    let requestItem = document.createElement("div");
                    requestItem.className = "notification-item vacation-item bg-yellow-500 p-2 rounded-md shadow-sm mb-2";
                    let formattedDate = formatDate(request.start_date);

                    // Include day_type in the notification
                    requestItem.innerHTML = `
                        <p><strong>${request.employee_name}</strong> requested a holiday on ${formattedDate} (${request.day_type})</p>
                        <span class="status-text">Pending</span>
                        <button class="btn approve-button" data-vacation-id="${request.id}">Approve</button>
                        <button class="btn reject-button" data-vacation-id="${request.id}">Reject</button>
                    `;
                    notificationDropdown.appendChild(requestItem);
                });

                // Attach event listeners to the Approve and Reject buttons
                attachVacationActionListeners();
            })
            .catch(error => console.error("Error fetching holiday requests:", error));
    }

    function formatDate(date) {
        return new Date(date).toISOString().split("T")[0];
    }

    function toggleNotifications() {
        let dropdown = document.getElementById("notificationDropdown");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }

    function toggleSickLeaveNotifications() {
        const dropdown = document.getElementById('sickLeaveNotificationDropdown');
        dropdown.classList.toggle('hidden');
        fetchSickDaysWithEmoji();
    }
</script>

</x-app-layout>