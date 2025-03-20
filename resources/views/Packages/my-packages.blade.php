<x-app-layout>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">My Packages</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($packages as $package)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <i class="fas fa-box text-blue-500 mr-3"></i>
                            <h2 class="text-xl font-semibold text-gray-800">{{ $package->name }} {{ $package->lastName }}</h2>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium 
                            @if($package->status === 'delivered')
                                bg-green-100 text-green-800
                            @elseif($package->status === 'in_transit')
                                bg-blue-100 text-blue-800
                            @else
                                bg-gray-100 text-gray-800
                            @endif
                        ">
                            {{ ucfirst(str_replace('_', ' ', $package->status)) }}
                        </span>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-truck text-blue-500"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Delivery Method</p>
                            <p class="text-gray-800">{{ $package->deliveryMethod->name }}</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-blue-500"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Delivery Address</p>
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
                        </div>
                    </div>
                    <a href="{{ route('packages.packagedetails', $package->id) }}" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 px-4 rounded-md hover:from-blue-600 hover:to-blue-700 transition-all duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-info-circle"></i>
                        <span>View Details</span>
                    </a>
                </div>
            </div>
            @empty
                <div class="col-span-3 text-center py-8">
                    <p class="text-gray-500 text-lg">No packages found</p>
                </div>
            @endforelse
        </div>
    </div>
    
</x-app-layout>