<x-app-layout>
    @section("pageName","Employees")

<x-sidebar>

    <div class="container mx-auto py-10">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold mb-4">Employees</h1>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-4 mb-6 rounded shadow">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-center">
            <input type="text" name="search" id="search" placeholder="Search by name or email" class="border border-gray-300 rounded px-4 py-2 mr-2">
            <select name="active" id="active" class="border border-gray-300 rounded px-4 py-2">
                <option value="1">All Employees</option>
                <option value="2">Active Employees</option>
                <option value="3">Employees Without Contract</option>
            </select>
        </div>
        <div class="flex justify-center mt-4">
            <table class="table-auto border-collapse border border-gray-300 w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">ID</th>
                        <th class="border border-gray-300 px-4 py-2">Last Name</th>
                        <th class="border border-gray-300 px-4 py-2">First Name</th>
                        <th class="border border-gray-300 px-4 py-2">Email</th>
                        <th class="border border-gray-300 px-4 py-2">Phone Number</th>
                        <th class="border border-gray-300 px-4 py-2">Birth Date</th>
                        <th class="border border-gray-300 px-4 py-2">Address</th>
                        <th class="border border-gray-300 px-4 py-2">City</th>
                        <th class="border border-gray-300 px-4 py-2">Country</th>
                        <th class="border border-gray-300 px-4 py-2">Leave Balance</th>
                        <th class="border border-gray-300 px-4 py-2">Team</th>
                        <th class="border border-gray-300 px-4 py-2">Created At</th>
                        <th class="border border-gray-300 px-4 py-2">Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                        <tr class="even:bg-gray-50 odd:bg-white">
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->employee->id }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->last_name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->first_name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->email }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->phone_number }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->birth_date }}</td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $employee->address->street }} {{ $employee->address->house_number }} {{ $employee->address->bus_number }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->address->city->name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->address->city->country->country_name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->employee->leave_balance }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->employee->team->department }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->created_at }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->updated_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6 flex justify-center" id="pagination">
            {{ $employees->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search');
            const activeSelect = document.getElementById('active');
            const tableBody = document.querySelector('tbody');

            function fetchEmployees() {
                const query = searchInput.value;
                const active = activeSelect.value;

                if (query.length == "" && active == "1")
                {
                    document.querySelector('#pagination').style.display = 'block';
                }
                else
                {
                    document.querySelector('#pagination').style.display = 'none';
                }

                fetch(`{{ route('workspace.employees.search') }}?query=${query}&active=${active}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    tableBody.innerHTML = ''; // Clear the table body
                    const employees = data.employees; // Access the actual employee data
                    if (employees.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="13" class="text-center py-4">No employees found</td></tr>';
                        return;
                    }
                    employees.forEach(employee => {
                        console.log(employee);
                        const row = `
                            <tr class="even:bg-gray-50 odd:bg-white">
                                <td class="border border-gray-300 px-4 py-2">${employee.employee.id}</td>
                                <td class="border border-gray-300 px-4 py-2">${employee.last_name}</td>
                                <td class="border border-gray-300 px-4 py-2">${employee.first_name}</td>
                                <td class="border border-gray-300 px-4 py-2">${employee.email}</td>
                                <td class="border border-gray-300 px-4 py-2">${employee.phone_number}</td>
                                <td class="border border-gray-300 px-4 py-2">${employee.birth_date}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    ${employee.address ? `${employee.address.street} ${employee.address.house_number} ${employee.address.bus_number}` : 'N/A'}
                                </td>
                                <td class="border border-gray-300 px-4 py-2">${employee.address?.city?.name || 'N/A'}</td>
                                <td class="border border-gray-300 px-4 py-2">${employee.address?.city?.country?.country_name || 'N/A'}</td>
                                <td class="border border-gray-300 px-4 py-2">${employee.employee.leave_balance}</td>
                                <td class="border border-gray-300 px-4 py-2">${employee.employee.team?.department || 'N/A'}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $employee->created_at->format('Y-m-d H:i:s') }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $employee->updated_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        `;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
            }

            searchInput.addEventListener('input', fetchEmployees);
            activeSelect.addEventListener('change', fetchEmployees);
        });
    </script>

</x-sidebar>
</x-app-layout>

