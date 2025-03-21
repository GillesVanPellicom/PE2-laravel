<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Holiday Request System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        #holidayInfo {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            padding: 15px;
            background: #007bff;
            color: white;
            border-radius: 5px;
        }

        #calendar {
            margin: 20px 0;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        button, .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }

        #saveRequests {
            background-color: #28a745;
            color: white;
        }

        #saveRequests:hover {
            background-color: #218838;
        }

        .btn {
            background-color: #007bff;
            color: white;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        #clearStorage {
            background-color: #dc3545;
            color: white;
        }

        #clearStorage:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div id="holidayInfo">
            Remaining Holidays: <span id="remainingHolidays">0</span> | 
            Sick Days Taken: <span id="sickDaysTaken">0</span>
        </div>
        <div id="calendar"></div>
        <div class="button-container">
            <button id="saveRequests">Save Requests</button>
            <a href="/manager-calendar" class="btn">View Requests</a>
            <button id="clearStorage">Clear Data</button>
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
            var loggedInUser = @json(auth()->user()->name); // Get logged-in user's name

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

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'en-gb',
                firstDay: 1,
                dateClick: function(info) {
                    toggleEvent(info.dateStr);
                }
            });

            calendar.render();
            Object.keys(selectedHolidays).forEach(date => addEvent(date, "holiday", selectedHolidays[date]));
            selectedSickDays.forEach(date => addEvent(date, "sick"));
            updateCounters();

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
                        remainingHolidays = data.remainingHolidays; // Update remaining holidays from the response
                        updateCounters(); // Refresh UI
                    } else {
                        alert("⚠️ Something went wrong!");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("❌ Failed to save requests. Please try again.");
                });
            });



            document.getElementById('clearStorage').addEventListener('click', function() {
                localStorage.clear();
                alert("All data has been cleared.");
                location.reload();
            });
        });
    </script>
</body>

</html>
