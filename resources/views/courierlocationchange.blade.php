@php
    use App\Models\User;
    use App\Models\Employee;
    use App\Models\Location;
    use App\Models\RouterNodes;
    use App\Services\Router\Types\Node;
@endphp
<x-app-layout>
    @section('title', 'Change Bob\'s Location')
    <div class="flex justify-center items-center">
        <form method="POST" action="{{ route('courier.update.location') }}" class="p-5">
            @csrf
            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Change Bob's Location</button>
            </div>
        </form>
    </div>

</x-app-layout>
