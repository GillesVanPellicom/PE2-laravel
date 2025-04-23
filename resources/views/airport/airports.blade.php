<x-app-layout>
    <x-sidebar-airport>
        <div class="max-w-4xl mx-auto bg-white p-6 shadow-md rounded-lg mt-6">
            <p>Notifications</p>
            @if(isset($messages) && count($messages) > 0)
                <div class="mt-4 p-4 bg-red-100 text-red-800 rounded">
                    <ul>
                        @foreach($messages as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p>No notifications available at the moment.</p>
            @endif
            <a href="{{ route('airports') }}">Airports</a> <!-- Ensure this matches the route name -->
            <br/>

            <!-- Next Outgoing Flight Section -->
            <div class="mt-6">
                <h2 class="text-xl font-bold">Next Outgoing Flight</h2>
                @if(isset($nextFlight) && $nextFlight)
                    <p><strong>Flight ID:</strong> {{ $nextFlight->id }}</p>
                    <p><strong>Departure Time:</strong> {{ $nextFlight->departure_time }}</p>
                    <p><strong>Destination:</strong> {{ $nextFlight->arrivalAirport->name ?? 'Unknown' }}</p>
                    <h3 class="text-lg font-semibold mt-4">Packages to Load:</h3>
                    <ul class="list-disc pl-6">
                        @forelse($packages->filter(fn($package) => $package->assigned_flight == $nextFlight->id) as $package)
                            <li>{{ $package->reference }} - {{ $package->weight }} kg</li>
                        @empty
                            <li>No packages to load.</li>
                        @endforelse
                    </ul>
                @else
                    <p>No outgoing flights scheduled.</p>
                @endif
            </div>
        </div>
    </x-sidebar-airport>
</x-app-layout>