<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Calendar</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <style>
        /* Reset body margin/padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            /* Allow page to grow beyond 100vh */
            background-color: #f4f7fb;
        }

        /* Sidebar styling */
        .sidebar {
            position: fixed;
            top: 0;
            right: 0;
            width: 250px;
            height: 100%;
            background-color: #343a40;
            color: white;
            padding: 20px;
            box-shadow: -3px 0 5px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .sidebar h2 {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .employee {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            color: white;
            font-size: 1rem;
        }

        .present {
            background-color: #28a745;
        }

        .sick {
            background-color: #dc3545;
        }

        .holiday {
            background-color: #007bff;
        }

        /* Main content container */
        .calendar-container {
            flex-grow: 1;
            margin-right: 270px;
            /* Sidebar width */
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            overflow-y: auto;
            /* Allow scrolling within the container */
        }

        .calendar-container h1 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #333;
        }

        .calendar-container button {
            padding: 10px 20px;
            font-size: 1rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .calendar-container button:hover {
            background-color: #0056b3;
        }

        /* Notifications */
        .notification-container {
            position: relative;
            display: inline-block;
        }

        .notification-bell {
            font-size: 2rem;
            cursor: pointer;
            color: #007bff;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            border-radius: 50%;
            padding: 5px 10px;
            font-size: 14px;
        }

        .notification-dropdown {
    display: none;
    position: absolute;

}


.notification-item {
    font-size: 1rem;
    color: #333;
    background-color: #f8f9fa;
    padding: 20px; /* More padding for readability */
    border-radius: 8px;
    margin-bottom: 10px;
    width: 100%;
    max-width: 100%;
    word-wrap: break-word;
    white-space: normal;
    display: flex;
    align-items: center;
    min-height: 90px; /* Increased height */
    line-height: 1.6; /* Improve readability */
}


        .notification-item strong {
            color: #007bff;
        }

        /* Full Calendar */
        #calendar {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 65%;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h2>Employee Status</h2>
        <div id="employeeList"></div>
    </div>
    <div class="calendar-container">
        <h1>Manager Calendar</h1>
        <div class="notification-container">
            <span class="notification-bell" onclick="toggleNotifications()">🔔</span>
            <span class="notification-badge" id="notificationBadge">0</span>
            <div class="notification-dropdown" id="notificationDropdown"></div>
        </div>
        <button onclick="clearStorage()">Clear All Data</button>
        <div id="calendar"></div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let employees = [{
                    name: "Alice",
                    status: "present"
                },
                {
                    name: "Bob",
                    status: "sick"
                },
                {
                    name: "Charlie",
                    status: "holiday"
                },
                {
                    name: "David",
                    status: "present"
                },
                {
                    name: "Eve",
                    status: "sick"
                }
            ];
            let employeeList = document.getElementById("employeeList");
            employees.forEach(emp => {
                let div = document.createElement("div");
                div.className = `employee ${emp.status}`;
                div.innerText = `${emp.name} - ${emp.status}`;
                employeeList.appendChild(div);
            });
            let approvedHolidays = JSON.parse(localStorage.getItem('approvedHolidays')) || [];
            let events = approvedHolidays.flatMap(holiday => {
                return holiday.holidays.map(date => ({
                    title: `${holiday.name}'s Holiday`,
                    start: new Date(date).toISOString().split('T')[0],
                    allDay: true
                }));
            });
            let calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                initialView: 'dayGridMonth',
                events: events,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                editable: false,
                droppable: false
            });
            calendar.render();
            loadNotifications();
        });

        function clearStorage() {
            localStorage.clear();
            alert("All data has been cleared.");
            location.reload();
        }

        function loadNotifications() {
            let requests = JSON.parse(localStorage.getItem('holidayRequests')) || [];
            let unreadCount = requests.length;
            let badge = document.getElementById('notificationBadge');
            let dropdown = document.getElementById('notificationDropdown');
            if (unreadCount > 0) {
                badge.textContent = unreadCount;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
            dropdown.innerHTML = '';
            requests.forEach((request, index) => {
                let item = document.createElement('div');
                item.className = 'notification-item';
                item.innerHTML = `<strong>${request.name}</strong> requested holidays: ${request.holidays.join(', ')}`;
                item.onclick = function() {
                    approveRequest(index);
                };
                dropdown.appendChild(item);
            });
            let markAllRead = document.createElement('button');
            markAllRead.textContent = 'Mark all as read';
            markAllRead.onclick = function() {
                localStorage.removeItem('holidayRequests');
                loadNotifications();
            };
            dropdown.appendChild(markAllRead);
        }

        function toggleNotifications() {
            let dropdown = document.getElementById('notificationDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        function approveRequest(index) {
            let requests = JSON.parse(localStorage.getItem('holidayRequests')) || [];
            let approvedHolidays = JSON.parse(localStorage.getItem('approvedHolidays')) || [];
            approvedHolidays.push(requests[index]);
            localStorage.setItem('approvedHolidays', JSON.stringify(approvedHolidays));
            requests.splice(index, 1);
            localStorage.setItem('holidayRequests', JSON.stringify(requests));
            alert("Holiday request approved!");
            loadNotifications();
            location.reload();
        }
    </script>
</body>

</html>