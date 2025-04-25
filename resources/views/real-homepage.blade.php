<x-app-layout>
    @section('title', 'New Home Page')

    <div class="flex flex-col items-center justify-center min-h-[calc(100vh-121px)] bg-gray-100">

        <!-- Hero Section -->
        <section x-data="{ open: false, tracking: '', postal: '' }" class="w-full bg-white py-16">
            <div class="max-w-7xl mx-auto px-6 text-center" data-aos="fade-down">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Fast & Reliable Shipping</h1>
                <p class="text-lg text-gray-600 mb-8">Track your parcels, calculate shipping costs, and find our locations easily.</p>

                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <button @click="open = !open"
                            class="px-6 py-3 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition">
                        Track a Parcel
                    </button>
                    <a href={{route('packages.send-package')}} class="px-6 py-3 bg-gray-200 text-gray-800 rounded-md shadow hover:bg-gray-300 transition">
                        Calculate Shipping
                    </a>
                    <a href="#" class="px-6 py-3 bg-gray-200 text-gray-800 rounded-md shadow hover:bg-gray-300 transition">
                        Find a Location
                    </a>
                </div>

                <!-- Tracking Form -->
                <div x-show="open" x-transition
                     class="mt-8 max-w-xl mx-auto bg-white p-6 rounded-xl shadow-md border border-gray-200 text-left w-full">
                    <form @submit.prevent="if (tracking) window.location.href = `/track/${tracking}?postal_code=${postal}`">
                        <div class="mb-4">
                            <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-1">Tracking Number</label>
                            <input type="text" x-model="tracking" id="tracking_number"
                                   class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                   placeholder="Enter tracking code" required>
                        </div>
                        <div class="mb-4">
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                            <input type="text" x-model="postal" id="postal_code"
                                   class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                   placeholder="Enter postal code" required>
                        </div>
                        <button type="submit"
                                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section class="w-full bg-gray-100 py-16">
            <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8">

                <div data-aos="fade-up" data-aos-delay="100" class="bg-white p-6 rounded-2xl shadow hover:scale-105 transform transition-all duration-300">
                    <div class="flex items-center mb-4">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 3h18v4H3V3z" />
                            <path d="M3 7h18v13H3z" />
                            <path d="M8 21h8v-2H8v2z" />
                        </svg>
                        <h2 class="ml-3 text-xl font-semibold text-gray-800">Track & Trace</h2>
                    </div>
                    <p class="text-gray-600">Monitor your shipments in real-time using our dedicated tracking tool.</p>
                </div>

                <div data-aos="fade-up" data-aos-delay="200" class="bg-white p-6 rounded-2xl shadow hover:scale-105 transform transition-all duration-300">
                    <div class="flex items-center mb-4">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <h2 class="ml-3 text-xl font-semibold text-gray-800">Shipping Calculator</h2>
                    </div>
                    <p class="text-gray-600">Get fast estimates for your package’s shipping cost and delivery time.</p>
                </div>

                <div data-aos="fade-up" data-aos-delay="300" class="bg-white p-6 rounded-2xl shadow hover:scale-105 transform transition-all duration-300">
                    <div class="flex items-center mb-4">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2C8.134 2 5 5.134 5 9c0 6.075 7 13 7 13s7-6.925 7-13c0-3.866-3.134-7-7-7z" />
                            <circle cx="12" cy="9" r="2.5" />
                        </svg>
                        <h2 class="ml-3 text-xl font-semibold text-gray-800">Find a Location</h2>
                    </div>
                    <p class="text-gray-600">Easily locate your nearest drop-off or pickup point.</p>
                </div>

            </div>
        </section>

        <!-- How It Works -->
        <section class="w-full bg-white py-20" data-aos="fade-up">
            <div class="max-w-5xl mx-auto px-4 text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">How It Works</h2>
                <p class="text-gray-600 mb-12">We simplify your shipping process in 3 easy steps.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
                    <div>
                        <h3 class="font-semibold text-lg mb-2">1. Enter Package Info</h3>
                        <p class="text-gray-500">Tell us the size, weight, and destination of your parcel.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg mb-2">2. Get a Quote</h3>
                        <p class="text-gray-500">We calculate your shipping cost instantly — no third parties involved.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg mb-2">3. Print & Ship</h3>
                        <p class="text-gray-500">Print your label and drop off your package or schedule a pickup.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="w-full bg-blue-600 py-16" data-aos="zoom-in">
            <div class="max-w-4xl mx-auto px-4 text-center text-white">
                <h2 class="text-3xl font-bold mb-4">Ready to ship smarter?</h2>
                <p class="mb-6">Join thousands who rely on our network to move packages fast and safely.</p>
                <a href="#" class="inline-block bg-white text-blue-600 px-6 py-3 rounded-md font-semibold shadow hover:bg-gray-100 transition">Start Shipping</a>
            </div>
        </section>

    </div>
</x-app-layout>
