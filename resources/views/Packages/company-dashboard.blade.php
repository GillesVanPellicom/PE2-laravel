<x-app-layout>
    @section('title', 'Dashboard - Company')
    <div class="bg-gray-100 min-h-screen py-12">
        <div class="container mx-auto px-4">
            <!-- Title -->
            <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">Company Dashboard</h1>

            <!-- Links Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-12">

                <!-- Bulk Order Link -->
                <a href="{{ route('packages.bulk-order') }}"
                   class="block bg-green-500 hover:bg-green-600 text-white text-center py-6 rounded-lg shadow-lg transition duration-300">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-truck text-2xl mb-2"></i>
                        <h2 class="text-lg font-bold">Bulk Order Packages</h2>
                        <p class="mt-1 text-sm">Send multiple packages</p>
                    </div>
                </a>

                <!-- Customer List Link -->
                <a href="{{ route('customers.index') }}"
                   class="block bg-red-500 hover:bg-red-600 text-white text-center py-6 rounded-lg shadow-lg transition duration-300">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-users text-2xl mb-2"></i>
                        <h2 class="text-lg font-bold">Customer List</h2>
                        <p class="mt-1 text-sm">View and manage your customers</p>
                    </div>
                </a>
            </div>

            <!-- My Packages and My Invoices Links -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- My Packages Link -->
                <a href="{{ route('packages.mypackages') }}"
                   class="block bg-blue-500 hover:bg-blue-600 text-white text-center py-6 rounded-lg shadow-lg transition duration-300">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-box text-2xl mb-2"></i>
                        <h2 class="text-lg font-bold">My Packages</h2>
                        <p class="mt-1 text-sm">View and manage all your packages</p>
                    </div>
                </a>

                <!-- My Invoices Link -->
                <a href="{{ route('invoices.myinvoices') }}"
                   class="block bg-purple-500 hover:bg-purple-600 text-white text-center py-6 rounded-lg shadow-lg transition duration-300">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-file-invoice text-2xl mb-2"></i>
                        <h2 class="text-lg font-bold">My Invoices</h2>
                        <p class="mt-1 text-sm">View and manage your invoices</p>
                    </div>
                </a>
            </div>

            <!-- Overview Section -->
            <div class="bg-white rounded-lg shadow-md p-8 mt-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Overview</h2>
                <p class="text-gray-600 mb-2">Total Packages: <span class="font-bold text-gray-800">{{ $totalPackages }}</span></p>
                <p class="text-gray-600">Unpaid Packages: <span class="font-bold text-gray-800">{{ $unpaidPackages }}</span></p>
            </div>
        </div>
    </div>
</x-app-layout>
