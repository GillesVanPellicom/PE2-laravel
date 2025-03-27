<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-6xl mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-800">My Packages</h1>
                    <div class="flex space-x-4">
                        <a href="{{ route('packages.send-package')}}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>Send Package
                        </a>
                        <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors duration-200">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                    </div>
                </div>
    
                <!-- Tab Navigation -->
                <div class="mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-8" aria-label="Tabs">
                            <button onclick="switchTab('receiving')"
                                    class="tab-btn receiving-tab border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 whitespace-nowrap flex items-center">
                                <i class="fas fa-inbox mr-2"></i>
                                Receiving
                                <span class="bg-blue-100 text-blue-600 ml-2 py-0.5 px-2.5 rounded-full">{{$receiving_packages->count()}}</span>
                            </button>
                            <button onclick="switchTab('sending')"
                                    class="tab-btn sending-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap flex items-center">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Sending
                                <span class="bg-blue-100 text-blue-600 ml-2 py-0.5 px-2.5 rounded-full">{{$packages->count()}}</span>
                            </button>
                        </nav>
                    </div>
                </div>
    
                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="relative">
                        <input type="text"
                            id="packageSearch"
                            placeholder="Search packages by tracking number or recipient..."
                            class="w-full px-4 py-2 pl-10 pr-4 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
    
                <!-- Receiving Packages Section -->
                <div id="receiving-packages" class="tab-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Package Card 1 -->
                        @forelse ($receiving_packages as $package)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                            <div class="p-6 h-full flex flex-col">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-box text-blue-500"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-lg font-semibold text-gray-800">{{$package->user->first_name}} {{$package->user->last_name}}</h3>
                                            <p class="text-sm text-gray-500">Sender</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end space-y-2">
                                        <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-sm font-medium">
                                            {{ ucfirst(str_replace('_', ' ', $package->status)) }}
                                        </span>
                                        <span class="hidden">{{ $package->reference }}</span>
                                    </div>
                                </div>
                                <div class="space-y-3 flex-grow">
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-truck-fast w-5 text-gray-400"></i>
                                        <span class="ml-2 text-sm">{{ $package->deliveryMethod->name }}</span>
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-map-marker-alt w-5 text-gray-400"></i>
                                        <span class="ml-2 text-sm">
                                            @if($package->deliveryMethod->requires_location)
                                            {{ $package->destinationLocation->address->street }}
                                            {{ $package->destinationLocation->address->house_number }}
                                            @if($package->destinationLocation->address->bus_number)
                                            - {{ $package->destinationLocation->address->bus_number }}
                                            @endif
                                            ,
                                            {{ $package->destinationLocation->address->city->postcode }}
                                            {{ $package->destinationLocation->address->city->name }}, 
                                            {{ $package->destinationLocation->address->city->country->country_name }}
                                    @else
                                            {{ $package->address->street }}
                                            {{ $package->address->house_number }}
                                            @if($package->address->bus_number)
                                            - {{ $package->address->bus_number }},
                                            @endif
                                            ,
                                            {{ $package->address->city->postcode }}
                                            {{ $package->address->city->name }}, 
                                            {{ $package->address->city->country->country_name }}
                                    @endif
                                        </span>
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-signature w-5 text-purple-500"></i>
                                        <span class="ml-2 text-sm text-purple-600 font-medium">Signature Required</span>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('packages.packagedetails', $package->id) }}" class="block w-full bg-blue-50 hover:bg-blue-100 text-blue-600 text-center py-2 rounded-md transition-colors duration-200">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        @empty
                            <div class="col-span-3 text-center py-8">
                                <p class="text-gray-500 text-lg">No packages found</p>
                            </div>
                        @endforelse
                    </div>
                </div>
    
                <!-- Sending Packages Section -->
                <div id="sending-packages" class="tab-content hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($packages as $package)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                            <div class="p-6 h-full flex flex-col">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <i class="fas fa-box text-indigo-500"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-lg font-semibold text-gray-800">{{ $package->name }} {{ $package->lastName }}</h3>
                                            <p class="text-sm text-gray-500">Receiver</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end space-y-2">
                                        <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-sm font-medium">
                                            {{ ucfirst(str_replace('_', ' ', $package->status)) }}
                                            <span class="hidden">{{ $package->reference }}</span>
                                        </span>
                                        <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-sm font-medium">
                                            Paid
                                        </span>
                                    </div>
                                </div>
                                <div class="space-y-3 flex-grow">
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-truck-fast w-5 text-gray-400"></i>
                                        <span class="ml-2 text-sm">{{ $package->deliveryMethod->name }}</span>
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-map-marker-alt w-5 text-gray-400"></i>
                                        <span class="ml-2 text-sm">
                                            @if($package->deliveryMethod->requires_location)
                                                {{ $package->destinationLocation->address->street }}
                                                {{ $package->destinationLocation->address->house_number }}
                                                @if($package->destinationLocation->address->bus_number)
                                                - {{ $package->destinationLocation->address->bus_number }}
                                                @endif
                                                ,
                                                {{ $package->destinationLocation->address->city->postcode }}
                                                {{ $package->destinationLocation->address->city->name }}, 
                                                {{ $package->destinationLocation->address->city->country->country_name }}
                                        @else
                                                {{ $package->address->street }}
                                                {{ $package->address->house_number }}
                                                @if($package->address->bus_number)
                                                - {{ $package->address->bus_number }},
                                                @endif
                                                ,
                                                {{ $package->address->city->postcode }}
                                                {{ $package->address->city->name }}, 
                                                {{ $package->address->city->country->country_name }}
                                        @endif
                                        </span>
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-signature w-5 text-purple-500"></i>
                                        <span class="ml-2 text-sm text-purple-600 font-medium">Signature Required</span>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('packages.packagedetails', $package->id) }}" class="block w-full bg-blue-50 hover:bg-blue-100 text-blue-600 text-center py-2 rounded-md transition-colors duration-200">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        @empty
                            <div class="col-span-3 text-center py-8">
                                <p class="text-gray-500 text-lg">No packages found</p>
                            </div>
                        @endforelse
    
                    </div>
                </div>
            </div>
        </div>
    
        <script>
            function switchTab(tab) {
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });
                document.getElementById(`${tab}-packages`).classList.remove('hidden');
                document.querySelectorAll('.tab-btn').forEach(btn => {
                    btn.classList.remove('border-blue-500', 'text-blue-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });
                const activeTab = document.querySelector(`.${tab}-tab`);
                activeTab.classList.remove('border-transparent', 'text-gray-500');
                activeTab.classList.add('border-blue-500', 'text-blue-600');
            }

            function searchPackages(searchTerm) {
        const activeTab = document.querySelector('.tab-btn:not(.border-transparent)').classList.contains('receiving-tab') ? 'receiving' : 'sending';
        const packageCards = document.querySelectorAll(`#${activeTab}-packages .grid > div:not(.col-span-3)`);
        let hasVisiblePackages = false;

        const existingMessage = document.querySelector(`#${activeTab}-packages .col-span-3`);
        if (existingMessage) {
            existingMessage.remove();
        }

        packageCards.forEach(card => {
            const searchableContent = card.textContent.toLowerCase();
            const matches = searchableContent.includes(searchTerm.toLowerCase());
            
            card.style.display = matches ? '' : 'none';
            if (matches) hasVisiblePackages = true;
        });

        if (!hasVisiblePackages && searchTerm.length > 0) {
            const noResults = document.createElement('div');
            noResults.className = 'col-span-3 text-center py-8';
            noResults.innerHTML = '<p class="text-gray-500 text-lg">No packages found</p>';
            document.querySelector(`#${activeTab}-packages .grid`).appendChild(noResults);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('packageSearch');
        
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                searchPackages(e.target.value);
            });
        }
    });
        </script>
    
</x-app-layout>