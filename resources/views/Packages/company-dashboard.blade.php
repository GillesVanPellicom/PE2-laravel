<x-app-layout>
    <div class="bg-gray-100 min-h-screen py-12">
        <div class="container mx-auto px-4">
            <!-- Title -->
            <h1 class="text-4xl font-bold text-center text-gray-800 mb-16">Company Dashboard</h1>

            <!-- Links -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                <!-- My Packages Link -->
                <a href="{{ route('packages.mypackages') }}" 
                   class="block bg-blue-500 hover:bg-blue-600 text-white text-center py-12 rounded-lg shadow-lg transition duration-300">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-box text-4xl mb-2"></i>
                        <h2 class="text-2xl font-bold">My Packages</h2>
                        <p class="mt-2 text-sm">View and manage all your packages</p>
                    </div>
                </a>

                <!-- Bulk Order Link -->
                <a href="{{ route('packages.bulk-order') }}" 
                   class="block bg-green-500 hover:bg-green-600 text-white text-center py-12 rounded-lg shadow-lg transition duration-300">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-truck text-4xl mb-2"></i>
                        <h2 class="text-2xl font-bold">Bulk Order</h2>
                        <p class="mt-2 text-sm">Create and manage bulk orders</p>
                    </div>
                </a>
            </div>

            <!-- Overview Section -->
            <div class="bg-white rounded-lg shadow-md p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Overview</h2>
                <p class="text-gray-600 mb-2">Total Packages: <span class="font-bold text-gray-800">{{ $totalPackages }}</span></p>
                <p class="text-gray-600">Unpaid Packages: <span class="font-bold text-gray-800">{{ $unpaidPackages }}</span></p>
            </div>
        </div>
    </div>
</x-app-layout>