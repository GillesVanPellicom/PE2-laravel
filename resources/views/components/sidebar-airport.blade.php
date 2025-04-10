<div class="flex">
    <div class="w-64 min-h-screen bg-gray-800 text-white">
        <div class="px-6 py-4">
            <h1 class="text-2xl font-semibold">Airport Dashboard</h1>
        </div>
        <ul class="mt-6 space-y-4">
            <li>
                <a href="/airport" class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                    Airport
                </a>
            </li>
            <li>
                <a href="/flights" class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                    Flights
                </a>
            </li>
            <li>
                <a href="/flightpackages" class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                    Flight Packages
                </a>
            </li>
            <li>
                <a href="/contract" class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                    Contracts
                </a>
            </li>
        </ul>
    </div>
    <div class="flex-1 p-6">
        {{ $slot }}
    </div>
</div>
