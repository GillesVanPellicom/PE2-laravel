<div>
    <!-- Country Selection -->
    <div>
        <label for="country" class="block mb-2">Country of destination</label>
        <select name="country" id="country" class="w-full border rounded p-2" onchange="updateDistance()">
            @foreach($countries as $code => $name)
                <option value="{{ $code }}" data-name="{{ $name }}"
                    {{ (old('country') ?? session('parcel_data.step1.country') ?? 'BE') == $code ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
        @error('country')
            <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
        @enderror
        <p id="distance-display" class="text-sm text-gray-600 mt-2">
            @php
                $selectedCountry = old('country') ?? session('parcel_data.step1.country') ?? 'BE';
                $selectedCountryName = $countries[$selectedCountry] ?? 'België';
                $distance = round(GoogleDistance::calculate('België', $selectedCountryName)/1000, 2);
            @endphp
            Distance: {{ $distance }} km
        </p>
    </div>

    <h1>{{round(GoogleDistance::calculate('België', 'België')/1000,2);}}km</h1>

    <!-- Delivery Method -->
    <div class="mt-6">
        <label class="block mb-2">Destination of the package</label>
        <div class="flex flex-wrap sm:flex-nowrap gap-4 justify-center">
            @foreach($deliveryMethods as $method)
                <label class="peer group relative bg-white rounded-lg border-2 p-4 cursor-pointer transition-all duration-200
                    w-full sm:w-1/3 min-w-[200px] flex-1
                    peer-checked:border-blue-500 peer-checked:shadow-lg
                    border-gray-200 hover:border-blue-500/50">
                    <div class="absolute" style="top: 3px; left: 6px;">
                        <input type="radio" 
                            name="delivery_method" 
                            value="{{ $method['id'] }}" 
                            data-price="{{ $method['price'] }}"
                            class="w-4 h-4 text-blue-500 border-gray-300 focus:ring-blue-500 focus:ring-2"
                            {{ (old('delivery_method') ?? session('parcel_data.step1.delivery_method')) == $method['id'] ? 'checked' : '' }}>
                    </div>
                    <div class="peer-checked:border-blue-500 peer-checked:shadow-blue-500/20 
                        w-full h-full absolute inset-0 rounded-lg border-2 border-transparent 
                        transition-all duration-200 pointer-events-none"></div>
                    <span class="block relative peer-checked:text-blue-500">
                        <div class="flex flex-col items-center justify-center">
                            <span class="transition-all duration-100">
                                @if($method['id'] === 'pickup')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                @elseif($method['id'] === 'locker')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="transition-all duration-300 text-center mt-2 font-medium">{{ $method['name'] }}</span>
                            <span class="text-sm">€ {{ number_format($method['price'], 2) }}</span>
                            <span class="text-xs text-gray-500 mt-1">{{ $method['description'] }}</span>
                        </div>
                    </span>
                </label>
            @endforeach
        </div>
        @error('delivery_method')
            <p class="text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Weight Class -->
    <div class="mt-6">
        <label class="block mb-2">Weight</label>
        <div class="space-y-2">
            @foreach($weightClasses as $weight)
                <label class="flex items-center">
                    <input type="radio" name="weight_class" value="{{ $weight['id'] }}"
                        {{ (old('weight_class') ?? session('parcel_data.step1.weight_class')) == $weight['id'] ? 'checked' : '' }}
                        data-price="{{ $weight['price'] }}">
                    <span class="ml-2">
                        {{ $weight['name'] }} ({{ $weight['weight_min'] }}-{{ $weight['weight_max'] }}kg) - € {{ number_format($weight['price'], 2) }}
                    </span>
                </label>
            @endforeach
        </div>
        @error('weight_class')
            <p class="text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Reference -->
    <div class="mt-6">
        <label for="reference" class="block mb-2">Your reference (optional)</label>
        <input type="text" name="reference" id="reference"
            value="{{ old('reference') ?? session('parcel_data.step1.reference') }}"
            class="w-full border rounded p-2"
            placeholder="Enter a reference for your package">
        @error('reference')
            <p class="text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<script>
function updateDistance() {
    const selectedOption = document.getElementById('country').selectedOptions[0];
    const selectedCountryName = selectedOption.dataset.name;
    
    fetch(`/calculate-distance/${encodeURIComponent(selectedCountryName)}`)
        .then(response => response.text())
        .then(distance => {
            document.getElementById('distance-display').textContent = `Distance: ${distance} km`;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('distance-display').textContent = 'Error calculating distance';
        });
}
</script> 