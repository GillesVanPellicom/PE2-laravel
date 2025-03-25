@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Track & Trace - Pakket: {{ $package->reference }}</h2>
    <h4 class="text-lg mb-6">Current Location: {{ $currentLocation ? $currentLocation->getDescription() : 'Unknown' }}</h4>

    {{-- Timeline Bullets --}}
    <h3 class="text-xl font-semibold mb-4">Tijdlijn</h3>
    <div class="relative flex items-center justify-between mb-10">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />

    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>



        @foreach ($movements as $index => $movement)
            @php
                $isCompleted = $movement->status == 'completed' || $movement->status == 'current';
            @endphp

            {{-- Line before bullet (except first item) --}}
            @if ($index > 0)
                <div class="h-1 flex-1 {{ $isCompleted ? 'bg-green-500' : 'bg-gray-300' }}"></div>
            @endif

            {{-- Bullet --}}
            <div class="relative">
                <div class="w-5 h-5 rounded-full 
                    {{ $movement->status == 'completed' ? 'bg-green-500' : 
                       ($movement->status == 'current' ? 'bg-orange-500' : 'bg-gray-300') }} 
                    border-2 border-black">
                </div>
            </div>
        @endforeach
    </div>

    {{-- Step Details --}}
    <h3 class="text-xl font-semibold mb-4">Details per Stap</h3>
    <div class="space-y-4">
        @foreach ($movements as $movement)
            <div class="p-4 border rounded-lg shadow-sm 
                {{ $movement->status == 'completed' ? 'bg-green-50 border-green-500' : 
                   ($movement->status == 'current' ? 'bg-orange-50 border-orange-500' : 
                   ($movement->status == 'in_transit' ? 'bg-yellow-50 border-yellow-500' : 'bg-gray-50 border-gray-300')) }}">

                <div class="flex justify-between">
                    <div>
                        <h4 class="font-semibold text-lg">
                            {{ $movement->getDescription() ?? 'Unknown' }}
                        </h4>
                        
                        {{-- Show timestamps based on status --}}
                        @if ($movement->status == 'completed')
                            <p>✅ Aangekomen op: <span class="font-medium">{{ $movement->getArrivedAt() }}</span></p>
                            <p>🚚 Vertrokken op: <span class="font-medium">{{ $movement->getDepartedAt() }}</span></p>
                        @elseif ($movement->status == 'current')
                            <p>📍 Huidige locatie sinds: <span class="font-medium">{{ $movement->getCheckedInAt() }}</span></p>
                        @elseif ($movement->status == 'in_transit')
                            <p>🚚 Onderweg sinds: <span class="font-medium">{{ $movement->getDepartedAt() }}</span></p>
                        @else
                            <p>⏳ Nog niet vertrokken</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>




<h3 class="text-xl font-semibold mb-4 mt-10 text-center">📍 Current location on the map</h3>
<div id="map" class="w-3/4 h-[350px] rounded-lg shadow-md border m-auto"></div>

<script>
    const lat = {!! json_encode($currentLat) !!};
    const lng = {!! json_encode($currentLng) !!};

    console.log('Latitude:', lat);
    console.log('Longitude:', lng);

    const mapDiv = document.getElementById('map');

    if (lat !== null && lng !== null) {
        console.log('Rendering map...');
        const map = L.map('map').setView([lat, lng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        L.marker([lat, lng]).addTo(map)
            .bindPopup('Package current location')
            .openPopup();
    } else {
        console.log('No coordinates, showing message.');
        mapDiv.innerHTML = '<p>Current location does not exist.</p>';
    }
</script>



@endsection
