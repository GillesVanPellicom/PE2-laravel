<x-app-layout>
    @section('title', 'Track Your Package')
    <!-- Hero Section with Tracking Input -->
    <div class="bg-gray-50 p-8 md:py-20 py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 rounded-xl shadow-2xl bg-white">
            <div class="text-left">
                <h1 class="text-3xl font-bold pt-3 text-gray-700 mb-6 tracking-tight">
                    {{ __('Track Your Package') }}
                </h1>

                <div class="max-w-md p-3">
                    <form
                        method="GET"
                        action=""
                        onsubmit="event.preventDefault(); window.location.href='/track/' + encodeURIComponent(this.tracking_number.value);"
                        class="flex items-center space-x-4"
                    >
                        <input
                            type="text"
                            name="tracking_number"
                            class="flex-1 px-4 py-3 rounded-lg border border-gray-200 focus:ring-0 bg-gray-50 text-base placeholder-gray-400 transition"
                            placeholder="{{ __('Tracking number') }}"
                            required
                        >
                        <input
                            type="text"
                            name="postal_code"
                            class="flex-1 px-4 py-3 rounded-lg border border-gray-200  focus:ring-0 bg-gray-50 text-base placeholder-gray-400 transition"
                            placeholder="{{ __('Postal code') }}"
                            required
                        >
                        <button
                            type="submit"
                            class="px-5 py-3 rounded-lg bg-blue-200 text-blue-900 font-semibold text-base hover:bg-blue-300 transition"
                            aria-label="{{ __('Track') }}"
                        >
                            <span class="material-symbols-outlined text-lg">local_shipping</span>
                        </button>
                    </form>
                    @error('tracking_number')
                    <p class="mt-2 text-red-600 text-xs">{{ $message }}</p>
                    @enderror
                    <div class="mt-4 text-gray-400  text-xs">
                        {{ __('You can find your tracking number in your shipping confirmation email.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Track Anywhere -->
            <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                <span class="material-symbols-outlined text-4xl text-blue-600 mb-4">language</span>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('Track Anywhere') }}</h3>
                <p class="text-gray-600">
                    {{ __('Track your packages from anywhere in the world, at any time.') }}
                </p>
            </div>

            <!-- Real-time Updates -->
            <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                <span class="material-symbols-outlined text-4xl text-blue-600 mb-4">update</span>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('Real-time Updates') }}</h3>
                <p class="text-gray-600">
                    {{ __('Get real-time status updates and estimated delivery times.') }}
                </p>
            </div>

            <!-- Detailed History -->
            <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                <span class="material-symbols-outlined text-4xl text-blue-600 mb-4">history</span>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('Detailed History') }}</h3>
                <p class="text-gray-600">
                    {{ __('View complete shipment history and all transit points.') }}
                </p>
            </div>
        </div>

        <!-- FAQs Section -->
        <div class="mt-16">
            <h2 class="text-2xl font-semibold text-gray-900 mb-8 text-center">{{ __('Frequently Asked Questions') }}</h2>

            <div class="max-w-3xl mx-auto space-y-4">
                <div class="bg-white rounded-lg shadow-sm">
                    <button class="w-full px-6 py-4 text-left flex justify-between items-center"
                            @click="faq1 = !faq1"
                            x-data="{ faq1: false }">
                        <span class="font-medium text-gray-900">{{ __('Where can I find my tracking number?') }}</span>
                        <span class="material-symbols-outlined" x-show="!faq1">add</span>
                        <span class="material-symbols-outlined" x-show="faq1">remove</span>
                    </button>
                    <div class="px-6 pb-4" x-show="faq1">
                        <p class="text-gray-600">
                            {{ __('Your tracking number can be found in your shipping confirmation email or on your shipping label.') }}
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm">
                    <button class="w-full px-6 py-4 text-left flex justify-between items-center"
                            @click="faq2 = !faq2"
                            x-data="{ faq2: false }">
                        <span class="font-medium text-gray-900">{{ __('How often is tracking updated?') }}</span>
                        <span class="material-symbols-outlined" x-show="!faq2">add</span>
                        <span class="material-symbols-outlined" x-show="faq2">remove</span>
                    </button>
                    <div class="px-6 pb-4" x-show="faq2">
                        <p class="text-gray-600">
                            {{ __('Tracking information is updated in real-time as your package moves through our network.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
