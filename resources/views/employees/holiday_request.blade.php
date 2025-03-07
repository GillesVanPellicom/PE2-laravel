<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Holiday Requests</title>
    <style>
        .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Holiday Requests</h1>
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Requested Holidays</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="requestTableBody">
            <!-- Requests will be added here dynamically -->
        </tbody>
    </table>
    <a href="manager-calendar" class="btn">View Manager Calendar</a>
    <button onclick="clearStorage()">Clear All Data</button>

    <script>
        function clearStorage() {
            localStorage.clear(); // Clears all localStorage data
            alert("All data has been cleared.");
            location.reload(); // Reload the page to reset the state
        }

        document.addEventListener('DOMContentLoaded', function() {
            let requests = JSON.parse(localStorage.getItem('holidayRequests')) || [];
            let tableBody = document.getElementById('requestTableBody');

            console.log('Holiday Requests:', requests);

            if (requests.length === 0) {
                tableBody.innerHTML = "<tr><td colspan='3'>No holiday requests yet.</td></tr>";
            } else {
                requests.forEach((request, index) => {
                    if (request && request.name && request.holidays) {
                        let row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${request.name}</td>
                            <td>${request.holidays.join(', ')}</td>
                            <td>
                                <button class="btn approve" onclick="approveRequest(${index})">Approve</button>
                                <button class="btn reject" onclick="rejectRequest(${index})">Reject</button>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    } else {
                        console.error('Invalid holiday request:', request);
                    }
                });
            }

            // Approve request function
            window.approveRequest = function(index) {
                let approvedRequest = requests[index];
                approvedRequest.status = 'Approved';

                let approvedHolidays = JSON.parse(localStorage.getItem('approvedHolidays')) || [];
                approvedHolidays.push({
                    name: approvedRequest.name,
                    holidays: approvedRequest.holidays
                });

                localStorage.setItem('holidayRequests', JSON.stringify(requests));
                localStorage.setItem('approvedHolidays', JSON.stringify(approvedHolidays));

                alert('Holiday request approved.');
                updateTable(); // Update the table without reloading the page
            }

            // Reject request function
            window.rejectRequest = function(index) {
                requests[index].status = 'Rejected';
                localStorage.setItem('holidayRequests', JSON.stringify(requests));
                alert('Holiday request rejected.');
                updateTable(); // Update the table without reloading the page
            }

            // Function to update the table dynamically
            function updateTable() {
                let tableBody = document.getElementById('requestTableBody');
                tableBody.innerHTML = ''; // Clear the current table body

                requests.forEach((request, index) => {
                    let row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${request.name}</td>
                        <td>${request.holidays.join(', ')}</td>
                        <td>
                            <button class="btn approve" onclick="approveRequest(${index})">Approve</button>
                            <button class="btn reject" onclick="rejectRequest(${index})">Reject</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            }
        });
    </script>
</body>
</html>
