<div>
    <!-- Receiver Details -->
    <div class="space-y-6">
        <div>
            <label for="firstname" class="block mb-2">First name</label>
            <input type="text" name="firstname" id="firstname"
                value="{{ old('firstname') ?? session('parcel_data.step2.firstname') }}"
                class="w-full border rounded p-2">
            @error('firstname')
                <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="lastname" class="block mb-2">Last name</label>
            <input type="text" name="lastname" id="lastname"
                value="{{ old('lastname') ?? session('parcel_data.step2.lastname') }}"
                class="w-full border rounded p-2">
            @error('lastname')
                <p class="text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="company" class="block mb-2">Company (optional)</label>
            <input type="text" name="company" id="company"
                value="{{ old('company') ?? session('parcel_data.step2.company') }}"
                class="w-full border rounded p-2">
        </div>

        <div>
            <label for="email" class="block mb-2">Email address</label>
            <input type="email" name="email" id="email"
                value="{{ old('email') ?? session('parcel_data.step2.email') }}"
                class="w-full border rounded p-2">
            @error('email')
                <p class="text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="phone" class="block mb-2">Phone number</label>
            <input type="tel" name="phone" id="phone"
                value="{{ old('phone') ?? session('parcel_data.step2.phone') }}"
                class="w-full border rounded p-2">
            @error('phone')
                <p class="text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Delivery Location -->
    <div class="mt-8 pt-8 border-t">
        @php
            $deliveryMethod = App\Models\DeliveryMethod::where('code', $deliveryMethod)->first();
        @endphp

        @if($deliveryMethod && $deliveryMethod->requires_location)
            <!-- Location Selection -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Select a {{ $deliveryMethod->name }}</h3>
                <div class="space-y-3">
                    @foreach(App\Models\Location::where('location_type', $deliveryMethod->code)->where('is_active', true)->get() as $location)
                        <label class="block border rounded-lg hover:border-blue-500 cursor-pointer">
                            <div class="flex items-start p-4">
                                <input type="radio" name="location_code" value="{{ $location->id }}"
                                    {{ (old('location_code') ?? session('parcel_data.step2.location_code')) == $location->id ? 'checked' : '' }}
                                    class="mt-1">
                                <div class="ml-3 flex-1">
                                    <div class="flex justify-between">
                                        <div class="font-medium">{{ $location->name }}</div>
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $location->address }}, {{ $location->city }}</div>
                                    @if($location->opening_hours)
                                        <div class="text-sm text-gray-500">{{ $location->opening_hours }}</div>
                                    @endif
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        @elseif($deliveryMethod && $deliveryMethod->code === 'address')
            <!-- Address Fields -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Delivery Address</h3>
                <div class="space-y-4">
                    <div>
                        <label for="street" class="block mb-2">Street and number</label>
                        <input type="text" name="street" id="street"
                            value="{{ old('street') ?? session('parcel_data.step2.street') }}"
                            class="w-full border rounded p-2">
                        @error('street')
                            <p class="text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="postal_code" class="block mb-2">Postal code</label>
                        <input type="text" name="postal_code" id="postal_code"
                            value="{{ old('postal_code') ?? session('parcel_data.step2.postal_code') }}"
                            class="w-full border rounded p-2">
                        @error('postal_code')
                            <p class="text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block mb-2">City</label>
                        <input type="text" name="city" id="city"
                            value="{{ old('city') ?? session('parcel_data.step2.city') }}"
                            class="w-full border rounded p-2">
                        @error('city')
                            <p class="text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        @endif
    </div>
</div> 