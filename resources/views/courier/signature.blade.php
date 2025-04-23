<x-courier>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Signature for Delivery</h1>
        <p class="text-gray-600 mb-4">Please sign below to confirm the delivery.</p>
        <canvas id="signatureCanvas" class="border border-gray-300 w-full h-64"></canvas>
        <div class="mt-4 flex space-x-4">
            <button class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 focus:outline-none" onclick='submitSignature(@json($id))'>
                Submit
            </button>
            <button class="px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 focus:outline-none" onclick="clearCanvas()">
                Clear
            </button>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('signatureCanvas');
        const ctx = canvas.getContext('2d');
        let drawing = false;

        canvas.addEventListener('mousedown', () => drawing = true);
        canvas.addEventListener('mouseup', () => drawing = false);
        canvas.addEventListener('mousemove', draw);

        function draw(event) {
            if (!drawing) return;
            const rect = canvas.getBoundingClientRect();
            const x = event.clientX - rect.left;
            const y = event.clientY - rect.top;
            ctx.lineTo(x, y);
            ctx.stroke();
        }

        function clearCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        function submitSignature(packageId) {
            // Call the same deliver route as the "Deliver at Home" button
            const deliverUrl = "{{ route('courier.deliver', ['id' => ':id']) }}".replace(':id', packageId);

            fetch(deliverUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    // Uncomment this when the `isSignature` field exists in the database
                    // isSignature: true,
                }),
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json(); // Parse the JSON response
                })
                .then(data => {
                    console.log(data.message);
                    if (data.success) {
                        alert('Package delivered successfully!');
                        window.location.href = "{{ route('courier.route') }}"; // Redirect back to the route page
                    } else {
                        alert(`Error: ${data.message}`);
                    }
                })
                .catch(error => {
                    console.error('Delivery error:', error);
                    alert('An error occurred while delivering the package.');
                });
        }
    </script>
</x-courier>