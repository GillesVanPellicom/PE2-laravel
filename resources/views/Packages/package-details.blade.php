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
    
                    <!-- Package Label and QR Code Section -->
                    <div class="mb-8">
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 px-6 py-4 border-b border-gray-200">
                                <div class="flex items-center">
                                    <i class="fas fa-tag text-yellow-600 mr-3"></i>
                                    <h2 class="text-xl font-semibold text-gray-800">Package Information</h2>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- QR Code Section -->
                                    <div class="bg-white rounded-lg border border-gray-200 p-6 flex flex-col items-center">
                                        <div class="w-48 h-48 bg-gray-100 rounded-lg flex items-center justify-center mb-4">
                                            <img src="data:image/png;base64,{{ $qrCode }}" 
                                            alt="Package QR Code"
                                            style="width: 150px; height: 150px; margin: 10px auto; display: block;">
                                        </div>
                                        <p class="text-sm text-gray-600 text-center mb-2">Show this QR code when collecting your package</p>
                                        <p class="text-sm font-medium text-gray-800 text-center">Tracking Number: {{$package->reference}}</p>
                                    </div>
    
                                    <!-- Package Label Section -->
                                    @if(Auth::user()->id === $package->user_id)
                                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                                        <div class="flex flex-col h-full">
                                            <div class="flex-grow">
                                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Package Label</h3>
                                                <div class="space-y-2 mb-6">
                                                    <p class="text-sm text-gray-600">Download the package label for printing or reference.</p>
                                                    <p class="text-sm text-gray-600">Available formats: PDF</p>
                                                </div>
                                                <div class="space-y-3">
                                                    <a href="{{ route('generate-package-label', $package->id) }}" class="w-full bg-white border-2 border-red-500 text-red-500 hover:bg-red-50 transition-colors duration-200 py-2 px-4 rounded-md flex items-center justify-center space-x-2">
                                                        <i class="fas fa-file-pdf text-xl"></i>
                                                        <span class="font-medium">Download PDF Label</span>
                                                    </a>
                                                    <a href="{{ route('generate-package-label', $package->id) }}" class="mt-4 w-full bg-gradient-to-r from-gray-700 to-gray-800 text-white py-3 px-4 rounded-md hover:from-gray-800 hover:to-gray-900 transition-all duration-200 flex items-center justify-center space-x-2">
                                                        <i class="fas fa-print"></i>
                                                        <span class="font-medium">Print Label</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <!-- Return Package Button - Only visible if package is delivered and logged in user is the receiver -->
                                    @if($package->status === 'Delivered' && Auth::user()->email === $package->receiverEmail)
                                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                                        <div class="flex flex-col h-full">
                                            <div class="flex-grow">
                                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Return Package</h3>
                                                <div class="space-y-2 mb-6">
                                                    <p class="text-sm text-gray-600">Need to return this package?</p>
                                                    <p class="text-sm text-gray-600">Click the button below to create a return shipment.</p>
                                                </div>
                                                <div class="space-y-3">
                                                    <form action="{{ route('packages.return', $package->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" style="background: linear-gradient(to right, #f97316, #ef4444); color: white;" class="w-full text-white py-3 px-4 rounded-md hover:opacity-90 transition-all duration-200 flex items-center justify-center space-x-2">
                                                            <i class="fas fa-undo-alt mr-2"></i>
                                                            <span class="font-medium">Return This Package</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
    
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
                                                <p class="text-gray-800 font-semibold">
                                                    {{ Carbon\Carbon::parse($package->delivery_estimate['estimated_date'])->format('F j, Y') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="ml-14">
                                            <p class="text-sm text-gray-500">
                                                Between {{ Carbon\Carbon::parse($package->delivery_estimate['delivery_window']['start'])->format('H:i') }} - 
                                                {{ Carbon\Carbon::parse($package->delivery_estimate['delivery_window']['end'])->format('H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
    
                                <!-- Track & Trace Button -->
                                <button class="mt-8 group relative w-full md:w-auto">
                                    <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-md filter blur opacity-75 group-hover:opacity-100 transition-opacity duration-200"></div>
                                    <div class="relative bg-gradient-to-r from-purple-500 to-indigo-500 text-white py-4 px-8 rounded-md flex items-center justify-center space-x-3 hover:from-purple-600 hover:to-indigo-600 transition-all duration-200">
                                        <i class="fas fa-map-marked-alt text-xl"></i>
                                        <a href="{{ route('track.package', $package->reference) }}" class="font-semibold text-lg">Track & Trace Package</a>
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