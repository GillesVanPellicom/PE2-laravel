<x-app-layout>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
     <div class="flex gap-6">
        <!-- Sidebar -->
        <div class="w-1/4 bg-white p-4 shadow-md rounded-lg">
            <h2 class="text-xl font-semibold mb-2">Available Employees</h2>
            <div id="employeeList" class="p-2 bg-gray-100 rounded-md"></div>

            <h2 class="text-xl font-semibold mt-4 mb-2">Sick Employees</h2>
            <div id="sickEmployeeList" class="p-2 bg-red-100 rounded-md"></div>

            <h2 class="text-xl font-semibold mt-4 mb-2">Holiday Requests</h2>
            <div id="holidayEmployeeList" class="p-2 bg-yellow-100 rounded-md"></div>

            <h2 class="text-xl font-semibold mt-4 mb-2">Training Sessions</h2>
            <div id="trainingList" class="p-2 bg-blue-100 rounded-md"></div>
            <button onclick="addTrainingSession()" class="mt-3 w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Add Training</button>
        </div>

        <!-- Calendar Section -->
        <div class="w-3/4 bg-white p-6 shadow-md rounded-lg">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Manager Calendar</h1>
                
                <!-- Notifications -->
                <div class="relative">
                    <span class="text-2xl cursor-pointer" onclick="toggleNotifications()">🔔</span>
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-2" id="notificationBadge">0</span>
                    <div class="absolute right-0 mt-2 w-64 bg-white shadow-lg p-4 hidden" id="notificationDropdown"></div>
                </div>
            </div>

            <button onclick="clearStorage()" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Clear All Data</button>
            <div id="calendar" class="mt-4"></div>
        </div>
    </div>

    <div class="mt-6">
        <a href="employees/calendar" class="inline-block bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">View Calendar</a>
    </div>


    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let employees = @json($employees);
        let sickEmployees = JSON.parse(localStorage.getItem('sickEmployees')) || [];
        let trainingSessions = JSON.parse(localStorage.getItem('trainingSessions')) || [];
        let approvedHolidays = JSON.parse(localStorage.getItem('approvedHolidays')) || [];

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
        initializeCalendar(approvedHolidays, trainingSessions);
        fetchHolidayRequests();
    });

    function initializeCalendar(holidays, trainings) {
        let events = [];

        holidays.forEach(holiday => {
            Object.entries(holiday.holidays).forEach(([date, period]) => {
                events.push({
                    title: `${holiday.name}'s Holiday (${period})`,
                    start: date,
                    allDay: true,
                    backgroundColor: '#ff7f7f',
                    borderColor: '#ff4f4f',
                    textColor: 'white'
                });
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

        let calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
            initialView: 'timeGridWeek',
            firstDay: 1,
            locale: 'en-gb',
            events: events,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridWeek,timeGridDay'
            },
            editable: false,
            droppable: false,
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

    function updateAvailableEmployees(holidays) {
    let employeeList = document.getElementById("employeeList");
    employeeList.innerHTML = ""; // Clear list

    let employees = @json($employees); // Original list of all employees

    // ✅ Get employees who are on holiday
    let holidayEmployeeNames = holidays.map(h => h.name);

    employees.forEach(emp => {
        let fullName = `${emp.user.first_name} ${emp.user.last_name}`;
        
        // ✅ Only add employee if NOT on holiday
        if (!holidayEmployeeNames.includes(fullName)) {
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

    // ✅ Fetch Approved Holidays from Backend
    fetch('/approved-vacations')
        .then(response => response.json())
        .then(data => {
            console.log("Fetched approved holidays:", data); // Debugging log

            // ✅ Find employees who have vacations on the selected date
            let holidayEmployees = data.filter(holiday => 
                formattedDate >= holiday.start_date && formattedDate <= holiday.end_date
            );

            console.log("Employees on holiday:", holidayEmployees); // Debugging log

            // ✅ Display employees on holiday
            displayHolidays(formattedDate, holidayEmployees);

            // ✅ Update available employees list
            updateAvailableEmployees(holidayEmployees);
        })
        .catch(error => console.error("Error fetching approved vacations:", error));
}


// ✅ Function to Display Holidays in the Sidebar
function displayHolidays(formattedDate, holidayEmployees) {
    let holidayEmployeeList = document.getElementById("holidayEmployeeList");
    
    // ✅ Clear the list first to prevent duplicates
    holidayEmployeeList.innerHTML = "";  

    if (holidayEmployees.length > 0) {
        let header = document.createElement("h3");
        header.innerText = `Employees on Holiday (${formattedDate})`;
        holidayEmployeeList.appendChild(header);

        holidayEmployees.forEach(holiday => {
            let div = document.createElement("div");
            div.className = "holiday-employee";
            div.innerText = `${holiday.name}`;
            holidayEmployeeList.appendChild(div);
        });
    } else {
        let noHoliday = document.createElement("p");
        noHoliday.innerText = "No employees on holiday this day.";
        holidayEmployeeList.appendChild(noHoliday);
    }
}


// ✅ Format date for backend comparison ("YYYY-MM-DD")
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

    function denyVacation(vacationId) {
        sendVacationUpdate(vacationId, "rejected");
    }

    function sendVacationUpdate(vacationId, status) {
        $.ajax({
            url: `/vacations/${vacationId}/update-status`,
            type: "POST",
            data: { status: status },
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


    function fetchHolidayRequests() {
        $.ajax({
            url: "/pending-vacations",
            type: "GET",
            dataType: "json",
            success: function (requests) {
                let notificationDropdown = document.getElementById("notificationDropdown");
                notificationDropdown.innerHTML = "";

                document.getElementById("notificationBadge").innerText = requests.length;

                requests.forEach(request => {
                    let requestItem = document.createElement("div");
                    requestItem.className = "notification-item";
                    let formattedDate = formatDate(request.start_date);

                    requestItem.innerHTML = `
                        <p><strong>${request.employee_name}</strong> requested a holiday on ${formattedDate}</p>
                        <button class="btn approve" onclick="approveVacation(${request.id})">Approve</button>
                        <button class="btn reject" onclick="denyVacation(${request.id})">Reject</button>
                    `;
                    notificationDropdown.appendChild(requestItem);
                });
            }
        });
    }

    function formatDate(date) {
        return new Date(date).toISOString().split("T")[0];
    }

    function toggleNotifications() {
        let dropdown = document.getElementById("notificationDropdown");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }
</script>

</x-app-layout>