<!-- filepath: /home/julien/PE2-Laravel/PE2-laravel/resources/views/courier/signature.blade.php -->
<x-courier>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Signature for Delivery</h1>
        <p class="text-gray-600 mb-4">Please sign below to confirm the delivery.</p>
        <canvas id="signatureCanvas" class="border border-gray-300 w-full h-64"></canvas>
        <div class="mt-4 flex space-x-4">
            <button class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 focus:outline-none" onclick="submitSignature({{ $id }})">
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
            const dataURL = canvas.toDataURL();
            fetch("{{ route('courier.submitSignature') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ signature: dataURL, package_id: packageId })
            })
            .then(response => response.json())
            .then(data => {
                console.log(data.message);
                if (data.success) {
                    alert('Signature submitted successfully!');
                    window.location.href = "{{ route('courier.route') }}"; // Redirect back to the route page
                }
            })
            .catch(error => console.error('Signature submission error:', error));
        }
    </script>
</x-courier>