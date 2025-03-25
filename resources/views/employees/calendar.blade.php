<x-app-layout>
    <div class="max-w-4xl mx-auto bg-white p-6 shadow-md rounded-lg mt-6">
        <!-- Holiday Info -->
        <div id="holidayInfo" class="bg-gray-100 p-4 rounded-md flex justify-between items-center">
            <span class="font-semibold text-lg">Remaining Holidays: <span id="remainingHolidays" class="text-blue-600 font-bold">0</span></span> 
            <span class="font-semibold text-lg">Sick Days Taken: <span id="sickDaysTaken" class="text-red-600 font-bold">0</span></span>
        </div>

        <!-- Calendar -->
        <div id="calendar" class="mt-4"></div>

        <!-- Buttons -->
        <div class="flex justify-between mt-6">
            <button id="saveRequests" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition">Save Requests</button>
            <a href="/manager-calendar" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition">View Requests</a>
            <button id="clearStorage" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition">Clear Data</button>
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
            var loggedInUser = @json(auth()->user()->name);

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
                        remainingHolidays = data.remainingHolidays;
                        updateCounters();
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
</x-app-layout>
