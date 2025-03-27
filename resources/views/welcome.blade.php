<x-app-layout>
    <div class="bg-gray-100 min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white p-6 rounded-lg shadow-md">
            <h1 class="text-3xl font-bold text-center text-gray-800 mb-4">Homepage</h1>
            <p class="text-center text-gray-600 mb-6">This is the homepage.</p>

            @auth
                <form action="{{ route('auth.logout') }}" method="POST" class="mb-6">
                    @csrf
                    <button type="submit"
                        class="w-full bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition">
                        Sign Out
                    </button>
                </form>
                <a href="{{ route('customers') }}"
                    class="block text-center text-blue-500 hover:underline hover:text-blue-600">
                    Customers
                </a>
            @endauth

            @guest
                <div class="flex flex-col space-y-4">
                    <a href="{{ route('auth.login') }}"
                        class="w-full bg-blue-500 text-white py-2 px-4 text-center rounded-md hover:bg-blue-600 transition">
                        Login
                    </a>
                    <a href="{{ route('auth.register') }}"
                        class="w-full bg-green-500 text-white py-2 px-4 text-center rounded-md hover:bg-green-600 transition">
                        Register
                    </a>
                </div>
            @endguest
        </div>
    </div>
</x-app-layout>

