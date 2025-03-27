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
            @php $firstPackage = $route[0] ?? null; @endphp
            @if ($firstPackage)
                <div class="flex items-center bg-green-100 shadow-md rounded-lg p-4 mb-6">
                    <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                        <img src="{{ asset('th.png') }}" alt="Package Logo" class="w-8 h-8">
                    </div>

                    <div class="ml-4 flex-1">
                        <p class="text-lg font-semibold text-green-700">Next to Deliver: {{ $firstPackage['ref'] ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-600">Coordinates: {{ $firstPackage['latitude'] }}, {{ $firstPackage['longitude'] }}</p>
                    </div>

                    <button class="text-green-700 hover:text-green-900 focus:outline-none deliver-btn" data-ref="{{ $firstPackage['ref'] }}">
                    ✓ Deliver
                    </button>
                </div>
            @endif

            <!-- Route Map -->
            <div class="mt-8">
                <h2 class="text-xl font-bold mb-4">Route Map</h2>
                <div id="map" class="w-full h-96 bg-gray-200"></div>
            </div>

            <div class="mt-8">
                <h2 class="text-xl font-bold mb-4">Upcoming Deliveries</h2>
                <ul class="space-y-4">
                    @foreach (array_slice($route, 1) as $location)
                        <li class="flex items-center bg-white shadow-md rounded-lg p-4">
                            <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                                <img src="{{ asset('th.png') }}" alt="Package Logo" class="w-8 h-8">
                            </div>

                            <div class="ml-4 flex-1">
                                <p class="text-lg font-semibold">Package Ref: {{ $location['ref'] ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">Coordinates: {{ $location['latitude'] }}, {{ $location['longitude'] }}</p>
                            </div>

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
            </div>

            <div class="mt-8 mb-8">
                <h2 class="text-xl font-bold mb-4">Route Details</h2>
                <p id="total-distance" class="text-lg text-gray-700">Total Distance: Calculating...</p>
            </div>
        @endif
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const route = @json($route);
        const deliverRoute = "{{ route('courier.deliver', ['id' => ':id']) }}";

        document.addEventListener('DOMContentLoaded', function () {
            if (route.length === 0) return;

            const map = L.map('map').setView([route[0].latitude, route[0].longitude], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            const coordinates = route.map(location => [location.latitude, location.longitude]);

            route.forEach(location => {
                L.marker([location.latitude, location.longitude])
                    .addTo(map)
                    .bindPopup(`<strong>Package Ref:</strong> ${location.ref ?? 'N/A'}`);
            });

            if (coordinates.length > 1) {
                const osrmCoordinates = coordinates.map(coord => coord.reverse()).join(';'); 
                const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${osrmCoordinates}?overview=full&geometries=geojson`;

                fetch(osrmUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (data.routes && data.routes.length > 0) {
                            const routeData = data.routes[0];
                            const routeCoordinates = routeData.geometry.coordinates.map(coord => coord.reverse());
                            L.polyline(routeCoordinates, { color: 'blue' }).addTo(map);

                            const totalDistanceKm = (routeData.distance / 1000).toFixed(2);
                            document.getElementById('total-distance').textContent = `Total Distance: ${totalDistanceKm} km`;
                        } else {
                            document.getElementById('total-distance').textContent = 'Total Distance: No route found';
                        }
                    })
                    .catch(error => {
                        document.getElementById('total-distance').textContent = 'Total Distance: Error fetching route';
                    });
            }

            document.querySelector('.deliver-btn')?.addEventListener('click', function () {
                const packageRef = this.getAttribute('data-ref');

                const url = deliverRoute.replace(':id', packageRef);
                fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }})
                    .then(response => response.json())
                    .then(data => {
                        console.log(data.message);
                        if (data.success) {
                            location.reload(); 
                        }
                    })
                    .catch(error => console.error('Delivery error:', error));
            });
        });
    </script>

    <style>
        #map { z-index: 0; }
        nav { z-index: 10; position: relative; }
    </style>
</x-courier>