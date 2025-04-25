<x-app-layout>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notificationDropdown = document.getElementById('notificationDropdown');
            const notificationBadge = document.getElementById('notificationBadge');
            const sickLeaveNotificationDropdown = document.getElementById('sickLeaveNotificationDropdown');
            const sickLeaveNotificationBadge = document.getElementById('sickLeaveNotificationBadge');

            window.toggleNotifications = function () {
                notificationDropdown.classList.toggle('hidden');
            };

            window.toggleSickLeaveNotifications = function () {
                sickLeaveNotificationDropdown.classList.toggle('hidden');
            };

            function fetchHolidayRequestNotifications() {
                fetch('/pending-vacations')
                    .then(response => response.json())
                    .then(data => {
                        notificationDropdown.innerHTML = '';
                        let unreadCount = 0;

                        data.forEach(request => {
                            let li = document.createElement('li');
                            li.classList.add('p-2', 'bg-gray-100', 'rounded-md', 'cursor-pointer', 'hover:bg-gray-200');

                            li.innerHTML = `
                                <strong>${request.employee_name}</strong> requested a holiday on ${request.start_date} (${request.day_type}) <br>
                                <span class='text-sm text-gray-500'>Pending</span>
                            `;

                            const approveButton = document.createElement('button');
                            approveButton.textContent = 'Approve';
                            approveButton.classList.add('btn', 'btn-success', 'btn-sm', 'mt-2', 'text-xs');
                            approveButton.addEventListener('click', function () {
                                updateVacationStatus(request.id, 'approved', li);
                            });

                            const rejectButton = document.createElement('button');
                            rejectButton.textContent = 'Reject';
                            rejectButton.classList.add('btn', 'btn-danger', 'btn-sm', 'mt-2', 'text-xs');
                            rejectButton.addEventListener('click', function () {
                                updateVacationStatus(request.id, 'rejected', li);
                            });

                            li.appendChild(approveButton);
                            li.appendChild(rejectButton);

                            notificationDropdown.appendChild(li);
                            unreadCount++;
                        });

                        notificationBadge.textContent = unreadCount;
                        notificationBadge.classList.toggle('hidden', unreadCount === 0);
                    })
                    .catch(error => console.error("Error fetching holiday request notifications:", error));
            }

            function fetchSickDayNotifications() {
                fetch('/manager/sick-day-notifications')
                    .then(response => response.json())
                    .then(data => {
                        sickLeaveNotificationDropdown.innerHTML = '';
                        let unreadCount = 0;

                        data.forEach(notification => {
                            let li = document.createElement('li');
                            li.classList.add('p-2', 'bg-gray-100', 'rounded-md', 'cursor-pointer', 'hover:bg-gray-200');

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
                            sickLeaveNotificationDropdown.appendChild(li);
                            unreadCount++;
                        });

                        sickLeaveNotificationBadge.textContent = unreadCount;
                        sickLeaveNotificationBadge.classList.toggle('hidden', unreadCount === 0);
                    })
                    .catch(error => console.error("Error fetching sick day notifications:", error));
            }

            function markSickLeaveAsRead(notificationId, notificationElement) {
                fetch(`/manager/sick-day-notifications/${notificationId}/read`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(() => {
                    notificationElement.remove();
                    let unreadCount = parseInt(sickLeaveNotificationBadge.textContent);
                    unreadCount = Math.max(0, unreadCount - 1);
                    sickLeaveNotificationBadge.textContent = unreadCount;
                    sickLeaveNotificationBadge.classList.toggle('hidden', unreadCount === 0);
                })
                .catch(error => console.error("Error marking sick leave notification as read:", error));
            }

            function updateVacationStatus(vacationId, newStatus, notificationElement) {
                fetch(`/vacations/${vacationId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                .then(response => response.json())
                .then(() => {
                    notificationElement.remove();
                    let unreadCount = parseInt(notificationBadge.textContent);
                    unreadCount = Math.max(0, unreadCount - 1);
                    notificationBadge.textContent = unreadCount;
                    notificationBadge.classList.toggle('hidden', unreadCount === 0);
                })
                .catch(error => console.error("Error updating vacation status:", error));
            }

            fetchHolidayRequestNotifications();
            fetchSickDayNotifications();
        });
    </script>

    <div class="flex gap-6">
        <!-- Sidebar -->
        <div class="w-1/5 bg-white p-6 shadow-lg rounded-lg space-y-6">
            <h2 class="text-xl font-semibold text-gray-800">Available Employees</h2>
            <div id="employeeList" class="p-4 bg-gray-100 rounded-md shadow-sm"></div>

            <h2 class="text-xl font-semibold text-gray-800">Sick Employees</h2>
            <div id="sickEmployeeList" class="p-4 bg-red-100 rounded-md shadow-sm"></div>

            <h2 class="text-xl font-semibold text-gray-800">Holiday Requests</h2>
            <div id="holidayEmployeeList" class="p-4 bg-yellow-100 rounded-md shadow-sm"></div>
        </div>

        <!-- Notifications and Diagram Section -->
        <div class="flex-1 space-y-6">
            <!-- Notifications -->
            <div class="flex space-x-6">
                <!-- Holiday Requests Notification -->
                <div class="relative">
                    <span class="text-2xl cursor-pointer text-gray-800" onclick="toggleNotifications()">🔔</span>
                    <span id="notificationBadge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-2 hidden">0</span>
                    <div id="notificationDropdown" class="absolute right-0 mt-2 w-64 bg-white shadow-lg p-4 rounded-md hidden z-50"></div>
                </div>

                <!-- Sick Leave Notification -->
                <div class="relative">
                    <span class="text-2xl cursor-pointer text-gray-800" onclick="toggleSickLeaveNotifications()">🤒</span>
                    <span id="sickLeaveNotificationBadge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-2 hidden">0</span>
                    <div id="sickLeaveNotificationDropdown" class="absolute right-0 mt-2 w-64 bg-white shadow-lg p-4 rounded-md hidden z-50"></div>
                </div>
            </div>

            <!-- Diagram Section -->
            <div class="bg-white p-6 shadow-lg rounded-lg">
                <h2 class="text-xl font-bold mb-4">Employee Availability Overview</h2>
                <div class="flex items-center space-x-4 mb-4">
                    <label for="startDatePicker" class="font-semibold">Start Date:</label>
                    <input type="date" id="startDatePicker" class="border rounded-md p-2">
                    <label for="endDatePicker" class="font-semibold">End Date:</label>
                    <input type="date" id="endDatePicker" class="border rounded-md p-2">
                </div>
                <canvas id="availabilityChart" class="w-full" style="height: 400px;"></canvas>
            </div>
        </div>
    </div>

    <div id="calendar" class="mt-4"></div>

    <div class="mt-6 text-center">
        <a href="employees/calendar" class="inline-block bg-green-500 text-white px-6 py-3 rounded-md shadow-md hover:bg-green-600 transition duration-200 ease-in-out">View Calendar</a>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const employeeList = document.getElementById('employeeList');
        const sickEmployeeList = document.getElementById('sickEmployeeList');
        const holidayEmployeeList = document.getElementById('holidayEmployeeList');
        const startDatePicker = document.getElementById('startDatePicker');
        const endDatePicker = document.getElementById('endDatePicker');

        let availabilityChart = null;

        function updateSidebar(date) {
            console.log("Clicked date:", date);

            fetch('/approved-vacations')
                .then(response => response.json())
                .then(holidayData => {
                    let holidayEmployees = holidayData.filter(holiday =>
                        holiday.vacation_type !== 'Sick Leave' &&
                        date >= holiday.start_date && date <= holiday.end_date
                    );

                    displayHolidays(date, holidayEmployees);

                    fetch('/get-vacations')
                        .then(response => response.json())
                        .then(sickData => {
                            let sickEmployees = sickData.filter(vacation =>
                                vacation.vacation_type === 'Sick Leave' &&
                                date >= vacation.start_date && date <= vacation.end_date
                            );

                            displaySickLeaves(date, sickEmployees);
                            updateAvailableEmployees(holidayEmployees, sickEmployees);
                        })
                        .catch(error => console.error("Error fetching sick leaves:", error));
                })
                .catch(error => console.error("Error fetching approved vacations:", error));
        }

        function displayHolidays(date, holidayEmployees) {
            holidayEmployeeList.innerHTML = "";

            if (holidayEmployees.length > 0) {
                const header = document.createElement("h3");
                header.innerText = `Employees on Holiday (${date})`;
                holidayEmployeeList.appendChild(header);

                holidayEmployees.forEach(holiday => {
                    const div = document.createElement("div");
                    div.className = "holiday-employee";
                    div.innerText = `${holiday.name} (${holiday.day_type || 'N/A'})`;
                    holidayEmployeeList.appendChild(div);
                });
            } else {
                holidayEmployeeList.innerHTML = "<p>No employees on holiday this day.</p>";
            }
        }

        function displaySickLeaves(date, sickEmployees) {
            sickEmployeeList.innerHTML = "";

            if (sickEmployees.length > 0) {
                const header = document.createElement("h3");
                header.innerText = `Employees on Sick Leave (${date})`;
                sickEmployeeList.appendChild(header);

                sickEmployees.forEach(sick => {
                    const div = document.createElement("div");
                    div.className = "sick-employee";
                    div.innerText = `${sick.name} (${sick.day_type || 'N/A'})`;
                    sickEmployeeList.appendChild(div);
                });
            } else {
                sickEmployeeList.innerHTML = "<p>No employees on sick leave this day.</p>";
            }
        }

        function updateAvailableEmployees(holidays, sickEmployees = []) {
            employeeList.innerHTML = "";
            const employees = @json($employees);

            const holidayNames = holidays.map(h => h.name);
            const sickNames = sickEmployees.map(s => s.name);

            employees.forEach(emp => {
                const fullName = `${emp.user.first_name} ${emp.user.last_name}`;
                if (!holidayNames.includes(fullName) && !sickNames.includes(fullName)) {
                    const div = document.createElement("div");
                    div.className = "employee";
                    div.innerText = fullName;
                    employeeList.appendChild(div);
                }
            });
        }

        function fetchAvailabilityData(startDate, endDate) {
            if (!startDate || !endDate) {
                console.warn("Please select both start and end dates.");
                return;
            }

            fetch(`/get-availability-data?start_date=${startDate}&end_date=${endDate}`)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (!Array.isArray(data)) throw new Error('Invalid data format');
                    updateChart(data);
                })
                .catch(error => console.error('Error fetching availability data:', error));
        }

        function updateChart(data) {
            const labels = data.map(day => day.date);
            const availableData = data.map(day => day.available);
            const onHolidayData = data.map(day => day.onHoliday);
            const sickData = data.map(day => day.sick);

            if (availabilityChart) {
                availabilityChart.destroy();
            }

            const ctx = document.getElementById('availabilityChart').getContext('2d');
            availabilityChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        { label: 'Available', data: availableData, backgroundColor: '#28A745' },
                        { label: 'On Holiday', data: onHolidayData, backgroundColor: '#FFC107' },
                        { label: 'Sick Leave', data: sickData, backgroundColor: '#DC3545' },
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: context => `${context.dataset.label}: ${context.raw}`
                            }
                        }
                    },
                    scales: {
                        x: { stacked: true },
                        y: { stacked: true, beginAtZero: true }
                    },
                    onClick: function (event, elements) {
                        if (elements.length > 0) {
                            const index = elements[0].index;
                            const selectedDate = this.data.labels[index];
                            updateSidebar(selectedDate);
                        }
                    }
                }
            });
        }

        function onDateRangeChange() {
            const startDate = startDatePicker.value;
            const endDate = endDatePicker.value;
            fetchAvailabilityData(startDate, endDate);
        }

        startDatePicker.addEventListener('change', onDateRangeChange);
        endDatePicker.addEventListener('change', onDateRangeChange);

        const initialStartDate = moment().startOf('week').format('YYYY-MM-DD');
        const initialEndDate = moment().endOf('week').format('YYYY-MM-DD');
        fetchAvailabilityData(initialStartDate, initialEndDate);
    });
</script>

</x-app-layout>