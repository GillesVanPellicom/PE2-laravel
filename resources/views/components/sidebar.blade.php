<div class="flex">
<div class="w-64 min-h-screen bg-gray-800 text-white">
    <div class="px-6 py-4">
        <h1 class="text-2xl font-semibold">Employee Dashboard</h1>
    </div>
    <ul class="mt-6 space-y-4">
        <li>
            <a href="/employees" class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                Employees List
            </a>
        </li>
        <li>
            <a href="/employees/contracts" class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                Employee Contracts
            </a>
        </li>

        <li>
            <a href="/employees/teams" class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                Employee Teams
            </a>
        </li>

        <li>
            <a href="/employees/functions" class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                Employee Functions
            </a>
        </li>
        <li>
            <a href="/employees/calendar" class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                Employee calendar
            </a>
        </li>

        

        @if(auth()->user()->can('HR.create'))
            <div class="px-6 py-4">
                <h1 class="text-2xl font-semibold">Manager Pages</h1>
            </div>
            <li>
                <a href="/employees/create" class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                    Add Employee
                </a>
            </li>
            <li>
                <a href="/employees/create-contract" class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                    Add Contract
                </a>
            </li>
            <li>
                <a href="/employees/create-team" class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                    Add Team
                </a>
            </li>
            <li>
                <a href="/employees/create-function" class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                    Add Function
                </a>
            </li>
            
            <li>
                <a href="/manager-calendar" class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                    Manager Calendar
                </a>
            </li>
        @endif
    </ul>
</div>
<div class="flex-1 p-6">
    {{ $slot }}
</div>
</div>
