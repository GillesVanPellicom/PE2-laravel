@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Track & Trace - Pakket: {{ $package->reference }}</h2>
    <h4 class="text-lg mb-6">Huidige Locatie: {{ $currentLocation ? $currentLocation->name : 'Onbekend' }}</h4>

    {{-- Timeline Bullets --}}
    <h3 class="text-xl font-semibold mb-4">Tijdlijn</h3>
    <div class="relative flex items-center justify-between mb-10">

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
                        <h4 class="font-semibold text-lg">{{ $movement->fromLocation->name }} → {{ $movement->toLocation->name }}</h4>
                        
                        {{-- Show check-in and optional check-out times --}}
                        @if ($movement->status == 'completed')
                            <p>✅ Aangekomen op: <span class="font-medium">{{ $movement->check_in_time }}</span></p>
                            @if ($movement->check_out_time)
                                <p>🚚 Vertrokken op: <span class="font-medium">{{ $movement->check_out_time }}</span></p>
                            @endif
                        @elseif ($movement->status == 'current')
                            <p>📍 Huidige locatie sinds: <span class="font-medium">{{ $movement->check_in_time }}</span></p>
                        @elseif ($movement->status == 'in_transit')
                            <p>🚚 Onderweg sinds: <span class="font-medium">{{ $movement->departure_time }}</span></p>
                        @else
                            <p>⏳ Nog niet vertrokken</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
