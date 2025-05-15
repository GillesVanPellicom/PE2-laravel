<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <body class="bg-gray-100">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-6xl mx-auto">
                <a href="{{ route('packages.mypackages') }}" class="mb-6 flex items-center text-blue-500 hover:text-blue-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Packages
                </a>

                <div class="bg-white rounded-lg shadow-md p-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-8">Bulk Package Details</h1>

                    @foreach($packages as $package)
                        <div class="mb-8 border-b border-gray-200 pb-8">
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Package #{{ $loop->iteration }}</h2>

                            <!-- Package Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- QR Code Section -->
                                <div class="bg-white rounded-lg border border-gray-200 p-6 flex flex-col items-center">
                                    <div class="w-48 h-48 bg-gray-100 rounded-lg flex items-center justify-center mb-4">
                                        <img src="data:image/png;base64,{{ $package->qrCode }}" 
                                        alt="Package QR Code"
                                        style="width: 150px; height: 150px; margin: 10px auto; display: block;">
                                    </div>
                                    <p class="text-sm text-gray-600 text-center mb-2">Show this QR code when collecting your package</p>
                                    <p class="text-sm font-medium text-gray-800 text-center">Tracking Number: {{ $package->reference }}</p>
                                </div>

                                <!-- Package Label Section -->
                                @if(Auth::user()->id === $package->user_id)
                                <div class="bg-white rounded-lg border border-gray-200 p-6">
                                    <div class="flex flex-col h-full">
                                        <div class="flex-grow">
                                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Package Label</h3>
                                            <div class="space-y-2 mb-6">
                                                <p class="text-sm text-gray-600">Download the package label for printing or reference.</p>
                                                <p class="text-sm text-gray-600">Available formats: PDF</p>
                                            </div>
                                            <div class="space-y-3">
                                                <a href="{{ route('generate-package-label', $package->id) }}" class="w-full bg-white border-2 border-red-500 text-red-500 hover:bg-red-50 transition-colors duration-200 py-2 px-4 rounded-md flex items-center justify-center space-x-2">
                                                    <i class="fas fa-file-pdf text-xl"></i>
                                                    <span class="font-medium">Download PDF Label</span>
                                                </a>
                                                <a href="{{ route('generate-package-label', $package->id) }}" class="mt-4 w-full bg-gradient-to-r from-gray-700 to-gray-800 text-white py-3 px-4 rounded-md hover:from-gray-800 hover:to-gray-900 transition-all duration-200 flex items-center justify-center space-x-2">
                                                    <i class="fas fa-print"></i>
                                                    <span class="font-medium">Print Label</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Sender and Receiver Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                                <!-- Sender Information -->
                                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-gray-200">
                                        <div class="flex items-center">
                                            <i class="fas fa-paper-plane text-blue-500 mr-3"></i>
                                            <h2 class="text-xl font-semibold text-gray-800">Sender Information</h2>
                                        </div>
                                    </div>
                                    <div class="p-6 space-y-4">
                                        @if($package->user->isCompany)
                                            <p class="text-sm font-medium text-gray-500">Company Name: {{ $package->user->company_name }}</p>
                                        @else
                                            <p class="text-sm font-medium text-gray-500">Name: {{ $package->user->first_name }} {{ $package->user->last_name }}</p>
                                        @endif
                                        <p class="text-sm font-medium text-gray-500">Email: {{ $package->user->email }}</p>
                                        <p class="text-sm font-medium text-gray-500">Phone: {{ $package->user->phone_number }}</p>
                                    </div>
                                </div>

                                <!-- Receiver Information -->
                                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                                    <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-gray-200">
                                        <div class="flex items-center">
                                            <i class="fas fa-box text-green-500 mr-3"></i>
                                            <h2 class="text-xl font-semibold text-gray-800">Receiver Information</h2>
                                        </div>
                                    </div>
                                    <div class="p-6 space-y-4">
                                        <p class="text-sm font-medium text-gray-500">Name: {{ $package->name }} {{ $package->lastName }}</p>
                                        <p class="text-sm font-medium text-gray-500">Email: {{ $package->receiverEmail }}</p>
                                        <p class="text-sm font-medium text-gray-500">Phone: {{ $package->receiver_phone_number }}</p>
                                    </div>
                                </div>
                                <div class="bg-white rounded-lg border border-gray-200 p-6 flex flex-col justify-center">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Track & Trace</h3>
                                    <p class="text-sm text-gray-600 mb-4">Click the button below to track the status of this package.</p>
                                    <a href="{{ route('track.package', $package->reference) }}" 
                                    class="w-full bg-gradient-to-r from-purple-500 to-indigo-500 text-white py-3 px-4 rounded-md hover:from-purple-600 hover:to-indigo-600 transition-all duration-200 flex items-center justify-center space-x-2">
                                        <i class="fas fa-map-marked-alt text-xl"></i>
                                        <span class="font-medium">Track Package</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </body>
</x-app-layout>