<x-courier>
    <x-slot:showBack>true</x-slot:showBack>
    <x-slot:title>
        Scanner
    </x-slot:title>

    <div class="flex flex-col justify-center items-center w-full">
        <div id="qr-reader" style="width: min(500px, 90%)" class="border-8 border-solid border-gray-800 rounded-md"></div>
        <p id="current_action" class="text-lg mt-1 font-semibold">

        </p>
        <button onclick="openModal()"
            class="mt-1 flex flex-col items-center justify-center bg-gray-800 hover:bg-gray-600 text-white p-4 rounded-xl focus:outline-none">
            Choose A Different Action
        </button>

        <div class="flex flex-col justify-center items-center p-2 mb-14">
            <h3 class="text-xl font-semibold mb-4">Scanned QR Codes</h3>
            <div id="lastPackages">
            @include('components.courier-card')
            </div>
        </div>
    </div>

    <div id="infoModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
        style="display: none;">
        <div class="bg-red-800"></div>
    </div>

    <div id="actionModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-80 relative">
            <h2 class="text-xl font-semibold text-center mb-6">Choose your action</h2>
            <div class="grid grid-cols-2 gap-4">
                <button onclick="chooseAction('IN')"
                    class="flex flex-col items-center justify-center bg-gray-800 hover:bg-gray-600 text-white p-4 rounded-xl focus:outline-none aspect-square">
                    <i class="fa-regular fa-circle-down text-2xl"></i>
                    <span class="mt-2 text-md">Scan In</span>
                </button>
                <button onclick="chooseAction('OUT')"
                    class="flex flex-col items-center justify-center bg-gray-800 hover:bg-gray-600 text-white p-4 rounded-xl focus:outline-none aspect-square">
                    <i class="fa-regular fa-circle-up text-2xl"></i>
                    <span class="mt-2 text-md">Scan Out</span>
                </button>
                <button onclick="chooseAction('INFO')"
                    class="flex flex-col items-center justify-center bg-gray-800 hover:bg-gray-600 text-white p-4 rounded-xl focus:outline-none aspect-square">
                    <i class="fa-solid fa-info text-2xl"></i>
                    <span class="mt-2 text-md">Info</span>
                </button>
                <button onclick="chooseAction('DELIVER')"
                    class="flex flex-col items-center justify-center bg-gray-800 hover:bg-gray-600 text-white p-4 rounded-xl focus:outline-none aspect-square">
                    <i class="fa-solid fa-house-flag text-2xl"></i>
                    <span class="mt-2 text-md">Deliver</span>
                </button>
                <button onclick="chooseAction('RETURN')"
                    class="flex flex-col items-center justify-center bg-gray-800 hover:bg-gray-600 text-white p-4 rounded-xl focus:outline-none aspect-square">
                    <i class="fa-solid fa-arrow-rotate-left text-2xl"></i>
                    <span class="mt-2 text-md">Return</span>
                </button>
                <button onclick="chooseAction('FAILED')"
                    class="flex flex-col items-center justify-center bg-gray-800 hover:bg-gray-600 text-white p-4 rounded-xl focus:outline-none aspect-square">
                    <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                    <span class="mt-2 text-md">Failed</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        const scanQrRoute = "{{ route('workspace.courier.scanQr') }}";
        const getLastPackagesRoute = "{{ route('workspace.courier.lastPackages') }}"
        const csrf = "{{ csrf_token() }}";
    </script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="{{ asset('js/scan.js') }}"></script>
</x-courier>
