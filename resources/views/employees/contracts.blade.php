<x-app-layout>
    @section("pageName","Contracts")

<x-sidebar>
    <script>
        window.onload = function() {
            const pdfUrl = "{{ session('pdf_url') }}";
            if (pdfUrl) {
                window.open(pdfUrl, '_blank');
            }
        }

        function show_contract(contract) {
            window.open('/contracts/' + contract, '_blank');
        }

        const csrf_token = "{{ csrf_token() }}";
        const route = (name, param) => {
            return `{{ url('${name}') }}/${param}`;
        };

        window.onload = function() {
            const searchInput = document.getElementById('search');
            const activeSelect = document.getElementById('active');
            const tableBody = document.querySelector('tbody');

            function fetchContracts() {
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

                fetch(`{{ route('employees.searchContract') }}?query=${query}&active=${active}`, {
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
                    const contracts = data.contracts.data || data.contracts; // Handle paginated or non-paginated response
                    if (contracts.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="8" class="text-center py-4">No contracts found</td></tr>';
                        return;
                    }
                    contracts.forEach(contract => {
                        const formattedDate = new Date(contract.created_at).toISOString().replace('T', ' ').split('.')[0];
                        const row = `
                            <tr class="even:bg-gray-50 odd:bg-white">
                                <td class="border border-gray-300 px-4 py-2">${contract.contract_id}</td>
                                <td class="border border-gray-300 px-4 py-2">${contract.employee.user.first_name} ${contract.employee.user.last_name}</td>
                                <td class="border border-gray-300 px-4 py-2">${contract.function.name}</td>
                                <td class="border border-gray-300 px-4 py-2">${contract.location.description}</td>
                                <td class="border border-gray-300 px-4 py-2">${contract.start_date}</td>
                                <td class="border border-gray-300 px-4 py-2">${contract.end_date || 'Not set yet'}</td>
                                <td class="border border-gray-300 px-4 py-2">${new Date(contract.end_date) > new Date() || contract.end_date == null ? 'Active' : 'Expired'}</td>
                                <td class="border border-gray-300 py-2 flex justify-center">
                                    <div class="flex items-center space-x-8">
                                        ${contract.end_date == null ? `
                                        <form action="/contracts/${contract.contract_id}" method="POST" class="flex items-center space-x-2">
                                            <input type="hidden" name="_token" value="${csrf_token}">
                                            <input name="end_date" type="date" value="${new Date().toISOString().split('T')[0]}" class="text-xs">
                                            <button type="submit" class="text-white bg-blue-500 hover:bg-blue-600 px-2 py-1 rounded shadow text-xs">
                                                Set End Date
                                            </button>
                                        </form>` : '<button disabled class="text-white bg-gray-400 px-2 py-1 rounded shadow text-xs cursor-not-allowed">End date already set</button>'}
                                        <button onclick="show_contract('contract_${contract.employee.user.last_name}_${contract.employee.user.first_name}_${formattedDate}.pdf')" class="text-blue-500 underline">View Contract</button>
                                    </div>
                                </td>
                            </tr>
                        `;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });


                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
            }

            searchInput.addEventListener('input', fetchContracts);
            activeSelect.addEventListener('change', fetchContracts);
        };
    </script>

    <div class="container mx-auto py-10">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold mb-4">Contracts</h1>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-4 mb-6 rounded shadow">
                {{ session('success') }}
            </div>
        @elseif (session('error'))
            <div class="bg-red-100 text-red-800 p-4 mb-6 rounded shadow">
                {{ session('error') }}
            </div>
        @endif
        @error('end_date')
            <div class="bg-red-100 text-red-800 p-4 mb-6 rounded shadow">
                {{ $message }}
            </div>
        @enderror

        <div class="flex justify-center">
            <input type="text" name="search" id="search" placeholder="Search by name or email" class="border border-gray-300 rounded px-4 py-2 mr-2">
            <select name="active" id="active" class="border border-gray-300 rounded px-4 py-2">
                <option value="1">All Contracts</option>
                <option value="2">Active Contracts</option>
                <option value="3">Ended Contracts</option>
                <option value="4">Future Contracts</option>
            </select>
        </div>
        <div class="flex justify-center mt-4">
            <table class="table-auto border-collapse border border-gray-300 w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">ID</th>
                        <th class="border border-gray-300 px-4 py-2">Employee</th>
                        <th class="border border-gray-300 px-4 py-2">Function</th>
                        <th class="border border-gray-300 px-4 py-2">Location</th>
                        <th class="border border-gray-300 px-4 py-2">Start Date</th>
                        <th class="border border-gray-300 px-4 py-2">End Date</th>
                        <th class="border border-gray-300 px-4 py-2">Contract Status</th>
                        <th class="border border-gray-300 px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contracts as $contract)
                        
                        <tr class="even:bg-gray-50 odd:bg-white">
                            <td class="border border-gray-300 px-4 py-2">{{ $contract->contract_id }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $contract->employee->user->first_name }} {{ $contract->employee->user->last_name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $contract->function->name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $contract->location->description }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $contract->start_date }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $contract->end_date ?? 'Not set yet' }}</td>
                            <td class="border border-gray-300 px-4 py-2">
                                @if($contract->end_date > now() or $contract->end_date == NULL)
                                    Active
                                @else
                                    Expired
                                @endif
                            </td>
                            <td class="border border-gray-300 py-2 flex justify-center">
                                <div class="flex items-center space-x-8">
                                    @if(is_null($contract->end_date))
                                        <form action="{{ route('employee.contracts.updateEndDate', $contract->contract_id) }}" method="POST" class="flex items-center space-x-2">
                                            @csrf
                                            @method('POST')
                                            <input name="end_date" type="date" value="{{ date('Y-m-d') }}" class="text-xs">
                                            <button type="submit" class="text-white bg-blue-500 hover:bg-blue-600 px-2 py-1 rounded shadow text-xs">
                                                Set End Date
                                            </button>
                                        </form>
                                    @else
                                        <button type="submit" disabled class="text-white bg-gray-400 hover:bg-gray-500 px-4 py-2 rounded-md shadow-md text-xs cursor-not-allowed opacity-50">
                                            End date already set
                                        </button>
                                    @endif
                                    <button onclick="show_contract('contract_{{ $contract->employee->user->last_name }}_{{ $contract->employee->user->first_name }}_{{ $contract->created_at }}.pdf')" class="text-blue-500 underline">View Contract</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6 flex justify-center" id="pagination">
            {{ $contracts->links() }}
        </div>
</div>
</x-sidebar>
</x-app-layout>