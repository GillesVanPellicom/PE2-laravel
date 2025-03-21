<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manager Calendar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
</head>

    <style>
        /* General Page Styling */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    background-color: #f4f4f4;
}

/* Sidebar Styling */
.sidebar {
    width: 250px;
    background-color: #2c3e50;
    color: white;
    padding: 20px;
    height: 100vh;
    overflow-y: auto;
}

.sidebar h2 {
    font-size: 18px;
    border-bottom: 2px solid #34495e;
    padding-bottom: 5px;
    margin-bottom: 10px;
}

.employee, .sick-employee, .training-session {
    background: #34495e;
    padding: 8px;
    margin: 5px 0;
    border-radius: 5px;
    text-align: center;
    cursor: pointer;
    transition: 0.3s;
}

.employee:hover {
    background: #1abc9c;
}

/* Main Content */
.calendar-container {
    flex: 1;
    padding: 20px;
    background: white;
}

h1 {
    font-size: 24px;
    margin-bottom: 10px;
}

/* Calendar Styling */
#calendar {
    max-width: 900px;
    margin: 20px auto;
    background: white;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

/* Button Styling */
button {
    background: #1abc9c;
    border: none;
    color: white;
    padding: 10px 15px;
    margin-top: 10px;
    cursor: pointer;
    border-radius: 5px;
    transition: 0.3s;
}

button:hover {
    background: #16a085;
}

/* Notification Bell */
.notification-container {
    position: relative;
    display: inline-block;
}

.notification-bell {
    font-size: 24px;
    cursor: pointer;
    position: relative;
}

.notification-badge {
    background: red;
    color: white;
    font-size: 12px;
    padding: 5px 8px;
    border-radius: 50%;
    position: absolute;
    top: 0;
    right: -10px;
    display: inline-block;
}

/* Notification Dropdown */
.notification-dropdown {
    display: none;
    position: absolute;
    right: 0;
    background: white;
    width: 250px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
    padding: 10px;
    z-index: 100;
}

.notification-dropdown p {
    font-size: 14px;
    margin: 5px 0;
}

/* Approve/Reject Buttons */
.btn.approve {
    background: #27ae60;
}

.btn.reject {
    background: #e74c3c;
}

.btn.approve:hover {
    background: #219150;
}

.btn.reject:hover {
    background: #c0392b;
}

/* Clear Storage Button */
.clear-btn {
    background: #e67e22;
    margin-top: 20px;
}

.clear-btn:hover {
    background: #d35400;
}

/* Responsive */
@media (max-width: 768px) {
    body {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
        height: auto;
        text-align: center;
    }

    .calendar-container {
        padding: 10px;
    }

    #calendar {
        width: 100%;
    }
}

</style>
<body>

    <div class="sidebar">
        <h2>Employee Status</h2>
        <div id="employeeList"></div>
        <h2>Sick Employees</h2>
        <div id="sickEmployeeList"></div>
        <h2>Training Sessions</h2>
        <div id="trainingList"></div>
        <button onclick="addTrainingSession()">Add Training</button>
        
    </div>

    

    <div class="calendar-container">
        <h1>Manager Calendar</h1>
        
        <!-- Notifications for Holiday Requests -->
        <div class="notification-container">
            <span class="notification-bell" onclick="toggleNotifications()">🔔</span>
            <span class="notification-badge" id="notificationBadge">0</span>
            <div class="notification-dropdown" id="notificationDropdown"></div>
        </div>

        <button onclick="clearStorage()">Clear All Data</button>
        <div id="calendar"></div>
    </div>
    <a href="calendar" class="btn">View Calendar</a>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let employees = @json($employees);
        let sickEmployees = JSON.parse(localStorage.getItem('sickEmployees')) || [];
        let trainingSessions = JSON.parse(localStorage.getItem('trainingSessions')) || [];

        let employeeList = document.getElementById("employeeList");
        employees.forEach(emp => {
            let div = document.createElement("div");
            div.className = "employee";
            
            // Correctly reference first_name and last_name inside user
            div.innerText = `${emp.user.first_name} ${emp.user.last_name}`;
            
            div.onclick = function() { markSick(emp.user.first_name); };
            employeeList.appendChild(div);
        });

        updateSickEmployees();
        updateTrainingSessions();

        let approvedHolidays = JSON.parse(localStorage.getItem('approvedHolidays')) || [];
        let events = [];

        approvedHolidays.forEach(holiday => {
            Object.entries(holiday.holidays).forEach(([date, period]) => {
                let startTime, endTime;

                if (period === "AM") {
                    startTime = `${date}T08:00:00`;
                    endTime = `${date}T12:00:00`;
                } else if (period === "PM") {
                    startTime = `${date}T13:00:00`;
                    endTime = `${date}T17:00:00`;
                } else {
                    startTime = date;
                    endTime = date;
                }

                events.push({
                    title: `${holiday.name}'s Holiday (${period})`,
                    start: startTime,
                    end: endTime,
                    allDay: period === "Full Day",
                    backgroundColor: '#ff7f7f',
                    borderColor: '#ff4f4f',
                    textColor: 'white'
                });
            });
        });

        trainingSessions.forEach(session => {
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
            droppable: false
        });

        calendar.render();
        fetchHolidayRequests();
    });

    function markSick(employeeName) {
        let sickEmployees = JSON.parse(localStorage.getItem('sickEmployees')) || [];
        if (!sickEmployees.includes(employeeName)) {
            sickEmployees.push(employeeName);
            localStorage.setItem('sickEmployees', JSON.stringify(sickEmployees));
            updateSickEmployees();
        }
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
            location.reload();
        }
    }

    function updateTrainingSessions() {
        let trainingList = document.getElementById("trainingList");
        trainingList.innerHTML = "";
        let trainingSessions = JSON.parse(localStorage.getItem('trainingSessions')) || [];
        trainingSessions.forEach(session => {
            let div = document.createElement("div");
            div.className = "training-session";
            div.innerText = `${session.topic} on ${session.date}`;
            trainingList.appendChild(div);
        });
    }

    function clearStorage() {
        localStorage.clear();
        alert("All data has been cleared.");
        location.reload();
    }

    function approveVacation(vacationId) 
    {
        sendVacationUpdate(vacationId, "approved");
        
    }

    function denyVacation(vacationId) {
        sendVacationUpdate(vacationId, "denied");
    }

function sendVacationUpdate(vacationId, status) {
    console.log("Request ID:", vacationId);
    console.log("state: ", status);
    console.log($('meta[name="csrf-token"]').attr('content'));
        $.ajax({
        url: "/vacations/" + vacationId + "/update-status",
        type: "POST",
        data: {
            status: status
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log("Vacation updated:", response);
        },
        error: function(xhr, status, error) {
            console.error("Failed to update vacation", xhr.responseText);
        }
});

}


    function fetchHolidayRequests() {
    $.ajax({
        url: "/pending-vacations",
        type: "GET",
        dataType: "json",
        success: function (requests) {
            let notificationDropdown = document.getElementById("notificationDropdown");
            notificationDropdown.innerHTML = "";

            if (requests.length === 0) {
                notificationDropdown.innerHTML = "<p>No pending holiday requests.</p>";
                document.getElementById("notificationBadge").innerText = "0";
                return;
            }

            document.getElementById("notificationBadge").innerText = requests.length;

            requests.forEach(request => {
                let requestItem = document.createElement("div");
                requestItem.className = "notification-item";

                let formattedDate = new Date(request.start_date).toISOString().split("T")[0];

                // Debugging log (Move console log outside innerHTML)
                console.log("Request ID:", request.id);

                requestItem.innerHTML = `
                    <p><strong>${request.employee_name}</strong> requested a holiday on ${formattedDate}</p>
                    <button class="btn approve" onclick="approveVacation(${request.id})">Approve</button>
                    <button class="btn reject" onclick="denyVacation(${request.id})">Reject</button>
                `;
                notificationDropdown.appendChild(requestItem);
            });
        },
        error: function () {
            document.getElementById("notificationDropdown").innerHTML = "<p>Failed to load requests.</p>";
        }
    });
}


    function toggleNotifications() {
        let dropdown = document.getElementById("notificationDropdown");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }
</script>


</body>
</html>
