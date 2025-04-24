<x-app-layout>
    <x-sidebar-airport>
        <div class="max-w-4xl mx-auto bg-white p-6 shadow-md rounded-lg mt-6">
            <p>Notifications</p>
            @if(isset($messages) && count($messages) > 0)
                <div class="mt-4 p-4 bg-red-100 text-red-800 rounded">
                    <ul>
                        @foreach($messages as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p>No notifications available at the moment.</p>
            @endif
        </div>
    </x-sidebar-airport>
</x-app-layout>