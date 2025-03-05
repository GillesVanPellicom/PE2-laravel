<div>
    <h2 class="text-lg font-medium text-gray-900 mb-6">Sender Details</h2>
    <div class="space-y-6">
        <!-- Name -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="sender_firstname" class="block mb-2">First name</label>
                <input type="text" name="sender_firstname" id="sender_firstname"
                    value="{{ old('sender_firstname') ?? session('parcel_data.step3.sender_firstname') }}"
                    class="w-full border rounded p-2">
                @error('sender_firstname')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sender_lastname" class="block mb-2">Last name</label>
                <input type="text" name="sender_lastname" id="sender_lastname"
                    value="{{ old('sender_lastname') ?? session('parcel_data.step3.sender_lastname') }}"
                    class="w-full border rounded p-2">
                @error('sender_lastname')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Address -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label for="sender_street" class="block mb-2">Street</label>
                <input type="text" name="sender_street" id="sender_street"
                    value="{{ old('sender_street') ?? session('parcel_data.step3.sender_street') }}"
                    class="w-full border rounded p-2">
                @error('sender_street')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label for="sender_number" class="block mb-2">Number</label>
                    <input type="text" name="sender_number" id="sender_number"
                        value="{{ old('sender_number') ?? session('parcel_data.step3.sender_number') }}"
                        class="w-full border rounded p-2">
                    @error('sender_number')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sender_bus" class="block mb-2">Bus</label>
                    <input type="text" name="sender_bus" id="sender_bus"
                        value="{{ old('sender_bus') ?? session('parcel_data.step3.sender_bus') }}"
                        class="w-full border rounded p-2">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="sender_postal_code" class="block mb-2">Postal code</label>
                <input type="text" name="sender_postal_code" id="sender_postal_code"
                    value="{{ old('sender_postal_code') ?? session('parcel_data.step3.sender_postal_code') }}"
                    class="w-full border rounded p-2">
                @error('sender_postal_code')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sender_city" class="block mb-2">City</label>
                <input type="text" name="sender_city" id="sender_city"
                    value="{{ old('sender_city') ?? session('parcel_data.step3.sender_city') }}"
                    class="w-full border rounded p-2">
                @error('sender_city')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="sender_country" class="block mb-2">Country</label>
            <select name="sender_country" id="sender_country" class="w-full border rounded p-2">
                @foreach($countries as $code => $name)
                    <option value="{{ $code }}" 
                        {{ (old('sender_country') ?? session('parcel_data.step3.sender_country') ?? 'BE') == $code ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('sender_country')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Contact -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="sender_email" class="block mb-2">Email address</label>
                <input type="email" name="sender_email" id="sender_email"
                    value="{{ old('sender_email') ?? session('parcel_data.step3.sender_email') }}"
                    class="w-full border rounded p-2">
                @error('sender_email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sender_phone" class="block mb-2">Phone number (optional)</label>
                <input type="tel" name="sender_phone" id="sender_phone"
                    value="{{ old('sender_phone') ?? session('parcel_data.step3.sender_phone') }}"
                    class="w-full border rounded p-2">
                @error('sender_phone')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div> 