<x-app-layout>
    <div class=" mt-96 flex flex-col sm:justify-center items-center bg-gray-100">
        <div class="flex flex-col items-center justify-center gap-3">
            <h1 class="font-bold text-4xl text-gray-800">
                @yield('message')
            </h1>
            <div class="flex flex-row justify-center items-center gap-3">
                <a href="{{route('welcome')}}" class="text-blue-500 text-xl hover:text-blue-700">Home Page</a>
                <p class="text-gray-400">|</p>
                <a href="javascript:history.back()" class="text-blue-500 text-xl hover:text-blue-700">Go back to previously page</a>
            </div>

        </div>
    </div>


</x-app-layout>
