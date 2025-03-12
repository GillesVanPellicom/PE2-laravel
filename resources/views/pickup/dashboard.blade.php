<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="flex px-12 py-4 overflow-x-auto gap-6 mt-4 max-w-7xl justify-evenly  w-auto mx-auto">
        <div class="bg-white shadow-md w-1/3 w-auto overflow-hidden py-12 sm:px-6 lg:px-8 sm:rounded-lg flex justify-center">
            <div class="p-4 flex flex-col items-center">
                <h2 class="text-xl font-bold text-gray-800">25</h2>
                <p class="text-gray-600 mt-2">Orders</p>
            </div>
        </div>
        <div class="bg-white shadow-md w-2/3 overflow-hidden py-12 sm:px-6 lg:px-8 sm:rounded-lg flex justify-center">
            <div class="p-4 flex flex-col items-center">
                <h2 class="text-xl font-bold text-gray-800">{{count($packages)}}</h2>
                <p class="text-gray-600 mt-2">Orders</p>
            </div>
        </div>
        <div class="justify-center shadow-md bg-white w-1/3 w-auto overflow-hidden py-12 sm:px-6 lg:px-8 sm:rounded-lg flex">
            <div class="p-4 flex flex-col items-center">
                <h2 class="text-xl font-bold text-gray-800">25</h2>
                <p class="text-gray-600 mt-2">Orders</p>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto py-12 max-w-7xl sm:rounded-lg mx-auto sm:px-6 lg:px-8">


        <table class="min-w-full table-auto bg-white dark:shadow-none shadow-lg  sm:rounded-b-lg overflow-hidden">
            <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-t-lg flex sm:justify-between p-3">
                <div class="relative w-full max-w-xs">
                    <input type="text" placeholder="Search..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.9 14.32a8 8 0 111.414-1.414l4.387 4.387a1 1 0 01-1.414 1.414l-4.387-4.387zM8 14a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3">
                    <x-secondary-button><x-refresh-logo ></x-refresh-logo></x-secondary-button>
                    <x-secondary-button><x-filter-logo></x-filter-logo></x-secondary-button>
                </div>
            </div>
            <thead class="bg-gray-800 text-white ">
            <tr>
                <th class="py-3 px-4 text-center">#</th>
                <th class="py-3 px-4 text-center">Client Name</th>
                <th class="py-3 px-4 text-center">Barcode</th>
                <th class="py-3 px-4 text-center">Delivery Service</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-center">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($packages as $package)
                <tr class="border-b last:border-b-0 text-center hover:bg-gray-50 bg-gray-50 dark:hover:bg-gray-200">
                    <td class="py-3 px-4">{{ $package->id }}</td>
                    <td class="py-3 px-4">{{ $package->customer_id }}</td>
                    <td class="py-3 px-4">{{ $package->reference }}</td>
                    <td class="py-3 px-4">{{ $package->delivery_method_id }}</td>
                    <td class="py-3 px-4">{{ $package->status }}</td>
                    <td class="py-3 px-4 text-blue-500 cursor-pointer">Edit</td>
                </tr>
            @endforeach
            </tbody>

        </table>
        <div class="min-w-full flex justify-center items-center gap-6 mt-4">
            <x-primary-button><p class="text-xl"> < </p></x-primary-button>
            <div class="flex items-center px-4 py-2 p-4 rounded bg-gray-300 dark:bg-gray-600"><p class="text-black text-xl dark:text-gray-400">1</p></div>
            <x-primary-button><p class="text-xl"> > </p></x-primary-button>
        </div>
    </div>

</x-app-layout>
