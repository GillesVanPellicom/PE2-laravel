<div class="bg-white p-6 rounded-lg shadow-lg w-80 relative flex flex-col">
    <h2 class="text-xl font-semibold text-center mb-3"> #{{ $ref ?? 'UNKNOWN' }} </h2>
    <p class="mb-2"> {{ $sender ?? 'Unknown' }} -> {{ $reciever ?? 'Unknown' }} </p>
    <p class="mb-2"> {{ $from ?? 'Unknown' }} -> {{ $to ?? 'Unknown' }} </p>
    @if (isset($nextStop)) <p class="mb-2"> Next Stop: {{ $nextStop }} </p> @endif
    <p class="mb-3"> Weight Class: {{ $weight ?? 'Unknown' }} </p>
    <p class="mb-3"> Dimension: {{ $dimension ?? 'Unknown' }} </p>
    <p class="mb-3"> Reciever Phone: {{ $phone ?? 'Unknown' }} </p>
    <button
        class="flex flex-col items-center justify-center bg-gray-800 hover:bg-gray-900 text-white p-4 rounded-xl focus:outline-none"
        onclick="closeInfoModal()"> Close </button>
</div>
