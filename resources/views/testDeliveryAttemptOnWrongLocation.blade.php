<x-app-layout>
    @section('title', 'Package Delivery Checker')
    @section('scripts')
        <script>
            // Extracted from Laravel
            const destinationStreet = {!! json_encode($package->destinationLocation->address->street) !!};
            const geoapifyApiKey = '{{ env('GEOAPIFY_API_KEY') }}';

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(async (position) => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;

                    const url = `https://api.geoapify.com/v1/geocode/reverse?lat=${lat}&lon=${lon}&apiKey=${geoapifyApiKey}`;

                    try {
                        const response = await fetch(url);
                        const data = await response.json();

                        if (data.features && data.features.length > 0) {
                            const result = data.features[0].properties;
                            const currentStreet = result.street || "Unknown street";
                            const fullAddress = result.formatted;

                            document.getElementById("current-address").innerText = `${fullAddress}`;

                            // Basic normalized comparison
                            if (normalize(currentStreet) === normalize(destinationStreet)) {
                                document.getElementById("match-result").innerHTML = "<strong>✅ You're at the correct delivery location.</strong>";
                            } else {
                                document.getElementById("match-result").innerHTML = "<strong>❌ Not at the delivery location. So the delivery could not be done</strong>";
                            }
                        } else {
                            document.getElementById("current-address").innerText = "Could not get address.";
                        }
                    } catch (err) {
                        console.error(err);
                        document.getElementById("current-address").innerText = "Error fetching address.";
                    }

                }, () => {
                    document.getElementById("current-address").innerText = "Geolocation permission denied.";
                });
            } else {
                document.getElementById("current-address").innerText = "Geolocation not supported.";
            }

            // Simple string normalizer to avoid case sensitivity issues
            function normalize(str) {
                return str.toLowerCase().replace(/\s+/g, '').trim();
            }
        </script>
    @endsection
    <div class="flex flex-col items-center justify-center min-h-[calc(100vh-121px)] bg-gray-100">
        <div class="bg-white border border-spacing-1 border-b-black-800 p-8 rounded shadow-md w-full max-w-xl">
            <h1 class="text-2xl font-bold mb-6">Package Delivery Checker</h1>
            <div><p>Destination Package Address:
                    {{$package->destinationLocation->address->street}} {{$package->destinationLocation->address->house_number}}
                </p>
                <div class="flex flex-col mt-4 gap-3">
                    <p>Current Address: </p>
                    <p id="current-address">Getting your current location...</p>
                    <p id="match-result"></p>
                </div>
            </div>

        </div>


    </div>



</x-app-layout>
