<x-app-layout>
    <x-sidebar-airport>
        <div class="max-w-4xl mx-auto bg-white p-6 shadow-md rounded-lg mt-6">
            <h1>Airport</h1>
            <a href="{{ route('contract') }}">Contracts</a>
            <br>
            <a href="{{ route('flights') }}">Flights</a>
            <br>
            <a href="{{ route('flightpackages') }}">Flight Packages</a>
            <br/>
        </div>
    </x-sidebar-airport>
</x-app-layout>