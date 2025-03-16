<x-courier>
    <x-slot:title>
        Route
    </x-slot:title>
    <div>
        <h1>Courier Route</h1>
        @if (empty($route))
            <p>No packages to deliver.</p>
        @else
            <ul>
                @foreach ($route as $location)
                    <li>
                        Latitude: {{ $location['latitude'] }}, Longitude: {{ $location['longitude'] }}
                    </li>
                @endforeach
            </ul>
        @endif

        {{-- Debugging output --}}
        <pre>{{ print_r($route, true) }}</pre>
    </div>
</x-courier>