<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FullCalendar in Laravel</title>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

    <style>
        /* Custom styles for your page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        #calendar {
            max-width: 900px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #holidayInfo {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        #saveHolidays {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>

    <div id="holidayInfo">
        Remaining Holidays: <span id="remainingHolidays">5</span>
    </div>

    <div id="calendar"></div>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

    <div class="button-container">
        <button id="saveHolidays">Save Holidays</button>
        <!-- Link to navigate to the Holiday Requests page -->
        <a href="/holiday-requests" class="btn">View Holiday Requests</a>
    </div>

    <button onclick="clearStorage()">Clear All Data</button>

<script>
    function clearStorage() {
        localStorage.clear(); // Clears all localStorage data
        alert("All data has been cleared.");
        location.reload(); // Reload the page to reset the state
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var selectedHolidays = new Set(JSON.parse(localStorage.getItem('selectedHolidays')) || []);
        var remainingHolidays = 5; // Adjust this number based on your requirements

        function updateRemainingHolidays() {
            document.getElementById('remainingHolidays').textContent = remainingHolidays;
        }

        function addHoliday(date) {
            calendar.addEvent({
                id: date,
                title: 'Holiday',
                start: date,
                allDay: true,
                backgroundColor: '#ff7f7f',
                borderColor: '#ff7f7f',
                textColor: 'white'
            });
        }

        function removeHoliday(date) {
            let event = calendar.getEventById(date);
            if (event) event.remove();
        }

        function toggleHoliday(date) {
            let dateObj = new Date(date);
            let today = new Date();
            today.setHours(0, 0, 0, 0);

            if (dateObj < today) {
                alert('You cannot select a past date as a holiday.');
                return;
            }

            if (dateObj.getDay() === 0 || dateObj.getDay() === 6) {
                alert('You cannot select weekends as holidays.');
                return;
            }

            if (selectedHolidays.has(date)) {
                selectedHolidays.delete(date);
                removeHoliday(date);
                remainingHolidays++;
            } else {
                if (remainingHolidays > 0) {
                    selectedHolidays.add(date);
                    addHoliday(date);
                    remainingHolidays--;
                } else {
                    alert('You have no remaining holidays.');
                }
            }

            updateRemainingHolidays();
            localStorage.setItem('selectedHolidays', JSON.stringify([...selectedHolidays]));
        }

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            firstDay: 1,
            dateClick: function(info) {
                toggleHoliday(info.dateStr);
            }
        });

        calendar.render();

        // Load saved holidays
        selectedHolidays.forEach(date => addHoliday(date));
        updateRemainingHolidays();

        // Handle "Save Holidays" button
        document.getElementById('saveHolidays').addEventListener('click', function() {
            let employeeName = prompt("Enter your name to save holidays:");

            if (!employeeName) {
                alert("Name is required to save holidays.");
                return;
            }

            let holidays = Array.from(selectedHolidays);
            let requests = JSON.parse(localStorage.getItem('holidayRequests')) || [];

            requests.push({
                name: employeeName,
                holidays: holidays
            });

            localStorage.setItem('holidayRequests', JSON.stringify(requests));

            alert("Holidays saved successfully!");
        });
    });
</script>

</body>

</html>
