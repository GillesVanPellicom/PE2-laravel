<x-app-layout>
    @section("pageName","Employees")
    <div class="container mx-auto py-10">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold mb-4">New Employee</h1>
            <a href="{{ route('employees.index') }}" 
               class="text-lg text-white bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded shadow mr-2">
                Home
            </a>
            <a href="{{ route('employees.contracts') }}" 
               class="text-lg text-white bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded shadow mr-2">
                Show Contracts
            </a>
            <a href="{{ route('employees.create_contract') }}" 
               class="text-lg text-white bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded shadow mr-2">
                Create Contract
            </a>
        </div>

        <div class="max-w-3xl mx-auto bg-white p-8 rounded shadow">
            <form method="post" action="{{ route('employees.store_employee') }}">
                @csrf
                @method('POST')

                <div class="mb-4">
                    <label for="lastname" class="block text-sm font-medium text-gray-700">Lastname:</label>
                    <input type="text" name="lastname" id="lastname" value="{{ old('lastname') }}" autocomplete="random-something-goofy-to-prevent-autocomplete"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('lastname')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="firstname" class="block text-sm font-medium text-gray-700">Firstname:</label>
                    <input type="text" name="firstname" id="firstname" value="{{ old('firstname') }}" autocomplete="random-something-goofy-to-prevent-autocomplete"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('firstname')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="random-something-goofy-to-prevent-autocomplete"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone:</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" autocomplete="random-something-goofy-to-prevent-autocomplete"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="birth_date" class="block text-sm font-medium text-gray-700">Birth date:</label>
                    <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" autocomplete="random-something-goofy-to-prevent-autocomplete"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('birth_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="street" class="block text-sm font-medium text-gray-700">Street:</label>
                    <input type="text" name="street" id="street" value="{{ old('street') }}" autocomplete="random-something-goofy-to-prevent-autocomplete"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('street')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div id="autocomplete-container" class="mb-4"></div>

                <div class="mb-4">
                    <label for="city" class="block text-sm font-medium text-gray-700">City:</label>
                    <input type="text" name="city" id="city" value="{{ old('city') }}" readonly autocomplete="random-something-goofy-to-prevent-autocomplete"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('city')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="country" class="block text-sm font-medium text-gray-700">Country:</label>
                    <input type="text" name="country" id="country" value="{{ old('country') }}" readonly autocomplete="random-something-goofy-to-prevent-autocomplete"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('country')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="house_number" class="block text-sm font-medium text-gray-700">House number:</label>
                    <input type="text" name="house_number" id="house_number" value="{{ old('house_number') }}" readonly autocomplete="random-something-goofy-to-prevent-autocomplete"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('house_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="Apartment_number" class="block text-sm font-medium text-gray-700">Bus number (Optional):</label>
                    <input type="text" name="Apartment_number" id="Apartment_number" value="{{ old('Apartment_number') }}" readonly autocomplete="random-something-goofy-to-prevent-autocomplete"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('Apartment_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <input type="number" name="postcode" id="postcode" value="{{ old('postcode') }}" hidden>
                </div>

                <div class="mb-4">
                    <label for="team" class="block text-sm font-medium text-gray-700">Team:</label>
                    <select name="team" id="team"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="-1">Select a team</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->department }}</option>
                        @endforeach
                    </select>
                    @error('team')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" 
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function addressAutocomplete(containerElement, streetInput, cityInput, countryInput, options) {
    const MIN_ADDRESS_LENGTH = 3;
    const DEBOUNCE_DELAY = 300;

    let currentTimeout;
    let currentPromiseReject;
    let currentItems = [];
    let focusedItemIndex = -1;

    streetInput.addEventListener("input", function () {
        const currentValue = streetInput.value;

        if (currentTimeout) clearTimeout(currentTimeout);
        if (currentPromiseReject) currentPromiseReject({ canceled: true });

        currentTimeout = setTimeout(() => {
            if (currentValue.length < MIN_ADDRESS_LENGTH) return;

            const url = `https://api.geoapify.com/v1/geocode/autocomplete?text=${encodeURIComponent(
                currentValue
            )}&format=json&limit=5&apiKey=840a8882828f47b3b5562c323855442c`;

            const promise = new Promise((resolve, reject) => {
                currentPromiseReject = reject;
                fetch(url)
                    .then((response) => {
                        currentPromiseReject = null;
                        response.ok ? response.json().then(resolve) : response.json().then(reject);
                    })
                    .catch(reject);
            });

            promise
                .then((data) => {
                    currentItems = data.results || [];
                    showSuggestions(currentItems);
                })
                .catch((err) => {
                    if (!err.canceled) console.error(err);
                });
        }, DEBOUNCE_DELAY);
    });

    function showSuggestions(items) {
        closeDropDownList();

        const autocompleteItemsElement = document.createElement("div");

        // Updated Tailwind classes for proper alignment and width
        autocompleteItemsElement.setAttribute(
            "class",
            "autocomplete-items absolute z-10 bg-white shadow-md rounded-lg border border-gray-300 max-h-60 overflow-y-auto mt-1 left-0 right-0"
        );

        containerElement.style.position = "relative"; // Ensure parent container is relative for proper alignment
        containerElement.appendChild(autocompleteItemsElement);

        items.forEach((item, index) => {
            const itemElement = document.createElement("div");
            itemElement.textContent = item.formatted;

            // Add Tailwind styling to the suggestion items
            itemElement.setAttribute("class", "p-2 cursor-pointer hover:bg-gray-100");

            itemElement.addEventListener("click", () => {
                // Populate fields with the selected address components
                streetInput.value = item.street || item.name || '';
                if (cityInput) cityInput.value = item.city || '';
                if (countryInput) countryInput.value = item.country || '';

                const houseNumberInput = document.getElementById("house_number");
                const busNumberInput = document.getElementById("Apartment_number");
                const postcodeInput = document.getElementById("postcode");

                if (item.housenumber) {
                    const houseNumberPattern = /^(\d+)(\D+)?$/; // Splits numbers and letters (e.g., 123B -> "123", "B")
                    const match = houseNumberPattern.exec(item.housenumber);

                    if (match) {
                        if (houseNumberInput) houseNumberInput.value = match[1]; // The numeric part
                        if (busNumberInput) busNumberInput.value = match[2] || ''; // The non-numeric part (optional)
                    } else {
                        // Fallback if no match
                        if (houseNumberInput) houseNumberInput.value = item.housenumber;
                        if (busNumberInput) busNumberInput.value = '';
                    }
                } else {
                    if (houseNumberInput) houseNumberInput.value = '';
                    if (busNumberInput) busNumberInput.value = '';
                }

                if (postcodeInput) postcodeInput.value = item.postcode || '';

                closeDropDownList();
            });

            autocompleteItemsElement.appendChild(itemElement);
        });
    }





    function closeDropDownList() {
        const dropdown = containerElement.querySelector(".autocomplete-items");
        if (dropdown) containerElement.removeChild(dropdown);
        focusedItemIndex = -1;
    }
}

const streetInput = document.getElementById("street");
const cityInput = document.getElementById("city");
const countryInput = document.getElementById("country");
const autocompleteContainer = document.getElementById("autocomplete-container");

// Initialize autocomplete
addressAutocomplete(autocompleteContainer, streetInput, cityInput, countryInput, {
    placeholder: "Enter an address here"
});

</script>


</x-app-layout>
