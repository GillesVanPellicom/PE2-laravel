<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Calendar</title>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

    <style>
        #calendar {
            max-width: 900px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Manager Calendar</h1>
    <a href="holiday-requests" class="btn">Back to Holiday Requests</a>

    <button onclick="clearStorage()">Clear All Data</button>

<script>
    function clearStorage() {
        localStorage.clear(); // Clears all localStorage data
        alert("All data has been cleared.");
        location.reload(); // Reload the page to reset the state
    }
</script>

    <div id="calendar"></div>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let approvedHolidays = JSON.parse(localStorage.getItem('approvedHolidays')) || [];

            // Prepare events for FullCalendar
            let events = approvedHolidays.flatMap(holiday => {
                return holiday.holidays.map(date => {
                    let holidayDate = new Date(date);
                    return {
                        title: `${holiday.name}'s Holiday`,
                        start: holidayDate.toISOString().split('T')[0], // Use date format (YYYY-MM-DD)
                        allDay: true
                    };
                });
            });

            // Initialize FullCalendar
            let calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                initialView: 'dayGridMonth', // Default view is month
                events: events, // Pass the events array to FullCalendar
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                editable: false,
                droppable: false
            });

            calendar.render(); // Render the calendar
        });
    </script>
</body>
</html>


