<x-courier>
    <x-slot:title>
        Route
    </x-slot:title>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Courier Route</h1>

        <a href="{{ route('courier.route') }}">View Courier Route</a>

        @if (empty($route))
            <p class="text-gray-500">No packages to deliver.</p>
        @else
            <ul class="space-y-4">
                @foreach ($route as $index => $location)
                    <li class="flex items-center bg-white shadow-md rounded-lg p-4">
                        <!-- Logo -->
                        <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                            <img src="{{ asset('th.png') }}" alt="Package Logo" class="w-8 h-8">
                        </div>

                        <!-- Package Details -->
                        <div class="ml-4 flex-1">
                            <p class="text-lg font-semibold">Package Ref: {{ $location['ref'] ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">Coordinates: {{ $location['latitude'] }}, {{ $location['longitude'] }}</p>
                        </div>

                        <!-- Vertical Ellipsis Button -->
                        <div>
                            <button class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v.01M12 12v.01M12 18v.01" />
                                </svg>
                            </button>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        <!-- Map Section -->
        <div class="mt-8">
            <h2 class="text-xl font-bold mb-4">Route Map</h2>
            <div id="map" class="w-full h-96 bg-gray-200"></div>
        </div>

        <!-- Route Details Section -->
        <div class="mt-8 mb-8">
            <h2 class="text-xl font-bold mb-4">Route Details</h2>
            <p id="total-distance" class="text-lg text-gray-700">Total Distance: Calculating...</p>
        </div>
    </div>

    <!-- Include JavaScript for Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
        
    
    <script>
 const route = @json($route);

        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('map').setView([50.8503, 4.3517], 13); // default center (Brussels)

            // add OpenStreetMap tiles (generating map)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            // add a marker for each package
            const coordinates = route.map(location => [location.latitude, location.longitude]);

            route.forEach(location => {
                L.marker([location.latitude, location.longitude])
                    .addTo(map)
                    .bindPopup(`<strong>Package Ref:</strong> ${location.ref ?? 'N/A'}`);
            });

            // Use OSRM to calculate the route point to point by streets
            if (coordinates.length > 1) {
                const osrmCoordinates = coordinates.map(coord => coord.reverse()).join(';'); // Reverse lat/lng to lng/lat for OSRM
                const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${osrmCoordinates}?overview=full&geometries=geojson`;

                fetch(osrmUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (data.routes && data.routes.length > 0) {
                            const routeData = data.routes[0];
                            const routeCoordinates = routeData.geometry.coordinates.map(coord => coord.reverse()); // Reverse lng/lat back to lat/lng
                            L.polyline(routeCoordinates, { color: 'blue' }).addTo(map);

                            // Display total distance
                            const totalDistanceKm = (routeData.distance / 1000).toFixed(2); // Convert meters to kilometers
                            document.getElementById('total-distance').textContent = `Total Distance: ${totalDistanceKm} km`;
                        } else {
                            console.error('No route found');
                            document.getElementById('total-distance').textContent = 'Total Distance: No route found';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching OSRM route:', error);
                        document.getElementById('total-distance').textContent = 'Total Distance: Error fetching route';
                    });
            }
        });
    </script>

    <style>
        #map {
            z-index: 0; /* Ensure the map is below other elements */
        }

        nav {
            z-index: 10; /* Ensure the navbar is above the map */
            position: relative; /* Ensure z-index applies */
        }
    </style>
</x-courier>