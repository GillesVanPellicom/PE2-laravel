<div class="bg-white p-6 rounded-lg shadow-lg w-80 relative flex flex-col">
    <h2 class="text-xl font-semibold text-center mb-3"> {{ $title ?? '' }} </h2>
    <p class="mb-3"> {{ $message ?? '' }} </p>
    <button
        class="flex flex-col items-center justify-center bg-red-800 hover:bg-red-900 text-white p-4 rounded-xl focus:outline-none"
        onclick="confirmFail({{ $id ?? 0 }})"> Yes
    </button>
    <button
        class="flex flex-col items-center justify-center bg-gray-800 mt-2 hover:bg-gray-900 text-white p-4 rounded-xl focus:outline-none"
        onclick="closeInfoModal()"> No
    </button>
</div>
