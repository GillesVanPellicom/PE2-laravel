<x-app-layout>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <a href="{{ route('packages.mypackages') }}" class="mb-6 flex items-center text-blue-500 hover:text-blue-600">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Packages
            </a>

            <div class="bg-white rounded-lg shadow-md p-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-8">Package Details</h1>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Sender Information -->
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center">
                                <i class="fas fa-paper-plane text-blue-500 mr-3"></i>
                                <h2 class="text-xl font-semibold text-gray-800">Sender Information</h2>
                            </div>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-user text-blue-500"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Name</p>
                                    <p class="text-gray-800 font-medium">{{$package->user->first_name}} {{$package->user->last_name}}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-map-marker-alt text-blue-500"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Address</p>
                                    <p class="text-gray-800">
                                        {{ $package->user->address->street }}
                                        {{ $package->user->address->house_number }}
                                        @if($package->user->address->bus_number)
                                        - {{ $package->user->address->bus_number }},
                                        @endif
                                        ,
                                        {{ $package->user->address->city->postcode }}
                                        {{ $package->user->address->city->name }}, 
                                        {{ $package->user->address->city->country->country_name }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-phone text-blue-500"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Phone</p>
                                    <p class="text-gray-800">{{$package->user->phone_number}}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-envelope text-blue-500"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Email</p>
                                    <p class="text-gray-800">{{$package->user->email}}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Receiver Information -->
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center">
                                <i class="fas fa-box text-green-500 mr-3"></i>
                                <h2 class="text-xl font-semibold text-gray-800">Receiver Information</h2>
                            </div>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-user text-green-500"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Name</p>
                                    <p class="text-gray-800 font-medium">{{ $package->name }} {{ $package->lastName }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-map-marker-alt text-green-500"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Address</p>
                                    <p class="text-gray-800">
                                        @if($package->deliveryMethod->requires_location)
                                        <p class="text-gray-800">
                                            {{ $package->destinationLocation->address->street }}
                                            {{ $package->destinationLocation->address->house_number }}
                                            @if($package->destinationLocation->address->bus_number)
                                            - {{ $package->destinationLocation->address->bus_number }}
                                            @endif
                                            ,
                                            {{ $package->destinationLocation->address->city->postcode }}
                                            {{ $package->destinationLocation->address->city->name }}, 
                                            {{ $package->destinationLocation->address->city->country->country_name }}
                                        </p>
                                    @else
                                        <p class="text-gray-800">
                                            {{ $package->address->street }}
                                            {{ $package->address->house_number }}
                                            @if($package->address->bus_number)
                                            - {{ $package->address->bus_number }},
                                            @endif
                                            ,
                                            {{ $package->address->city->postcode }}
                                            {{ $package->address->city->name }}, 
                                            {{ $package->address->city->country->country_name }}
                                        </p>
                                    @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-phone text-green-500"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Phone</p>
                                    <p class="text-gray-800">{{ $package->receiver_phone_number }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-envelope text-green-500"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Email</p>
                                    <p class="text-gray-800">{{ $package->receiverEmail }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery Information -->
                <div class="border-t pt-8">
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center">
                                <i class="fas fa-shipping-fast text-indigo-500 mr-3"></i>
                                <h2 class="text-xl font-semibold text-gray-800">Delivery Information</h2>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Status Card -->
                                <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-center mb-3">
                                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                            <i class="fas fa-check-circle text-green-500"></i>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-gray-500">Status</p>
                                            <p class="text-green-600 font-semibold">{{ ucfirst(str_replace('_', ' ', $package->status)) }}</p>
                                        </div>
                                    </div>
                                    <div class="ml-14">
                                        <p class="text-sm text-gray-500">Last updated: 2 hours ago</p>
                                    </div>
                                </div>

                                <!-- Delivery Method Card -->
                                <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-center mb-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-truck text-blue-500"></i>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-gray-500">Delivery Method</p>
                                            <p class="text-gray-800 font-semibold">{{$package->deliveryMethod->name}}</p>
                                        </div>
                                    </div>
                                    <div class="ml-14">
                                        <p class="text-sm text-gray-500">Standard Delivery</p>
                                    </div>
                                </div>

                                <!-- Expected Delivery Card -->
                                <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-center mb-3">
                                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                            <i class="fas fa-calendar-alt text-purple-500"></i>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-gray-500">Expected Delivery</p>
                                            <p class="text-gray-800 font-semibold">March 21, 2025</p>
                                        </div>
                                    </div>
                                    <div class="ml-14">
                                        <p class="text-sm text-gray-500">Between 14:00 - 16:00</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Track & Trace Button -->
                            <button class="mt-8 group relative w-full md:w-auto">
                                <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-md filter blur opacity-75 group-hover:opacity-100 transition-opacity duration-200"></div>
                                <div class="relative bg-gradient-to-r from-purple-500 to-indigo-500 text-white py-4 px-8 rounded-md flex items-center justify-center space-x-3 hover:from-purple-600 hover:to-indigo-600 transition-all duration-200">
                                    <i class="fas fa-map-marked-alt text-xl"></i>
                                    <span class="font-semibold text-lg">Track & Trace Package</span>
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</x-app-layout>