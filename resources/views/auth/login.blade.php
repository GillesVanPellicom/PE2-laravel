<x-app-layout >
    @section('title', 'Login')
    <div class="flex items-center justify-center min-h-[calc(100vh-121px)] bg-gray-100">

        <form action="{{ route('auth.authenticate') }}" method="POST" class="bg-white p-8 rounded shadow-md w-full max-w-md">
            @csrf
            <h1 class="text-2xl font-bold mb-6">Please sign in</h1>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email address <span class="text-red-500">*</span></label>
                <input type="email" value="{{ old('email') }}" id="email" name="email" placeholder="name@example.com" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('email')
                <div class="text-red-500 text-sm mt-1">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="mb-4 relative">
                <label for="password" class="block text-sm font-medium text-gray-700">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password" placeholder="Password" 
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm pr-10">
                <span id="toggle-password" class="absolute top-[70%] right-3 -translate-y-[50%] flex items-center cursor-pointer">
                    <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-.274.857-.68 1.664-1.196 2.392M15.536 15.536A5.002 5.002 0 0112 17a5.002 5.002 0 01-3.536-1.464M8.464 8.464A5.002 5.002 0 0112 7c1.38 0 2.63.56 3.536 1.464" />
                    </svg>
                </span>
                @error('password')
                <div class="text-red-500 text-sm mt-1">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" value="remember-me" class="form-checkbox">
                    <span class="ml-2 text-sm text-gray-700">Remember me</span>
                </label>
            </div>
            
            <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Sign in</button>
            <div class="flex justify-between items-center mt">
                <a href="{{ route('welcome') }}" class="text-indigo-600 hover:text-indigo-900">Return to homepage</a>
                <a href="{{ route('auth.register') }}" class="text-indigo-600 hover:text-indigo-900">Register</a>
            </div>
        </form>

    </div>
    <script>
    document.getElementById('toggle-password').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');

        // Toggle password visibility
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('text-gray-500');
            eyeIcon.classList.add('text-indigo-500'); // Change color to indicate visibility
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('text-indigo-500');
            eyeIcon.classList.add('text-gray-500'); // Revert color
        }
    });
</script>
</x-app-layout>
