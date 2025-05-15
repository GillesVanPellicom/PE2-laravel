@php
    use App\Models\User;
    use App\Models\Employee;
    use App\Models\Location;
    use App\Models\RouterNodes;
    use App\Services\Router\Types\Node;
@endphp
<x-app-layout>
    <form method="POST" action="{{ route('courier.update.location') }}" class="p-5">
        @csrf
        <div>
            <label for="courier" class="block mb-2">Select Courier:</label>
            <select id="courier" name="employee" class="border rounded px-3 py-2 w-full">
                @php
                    $couriers = User::role('courier')->get()->pluck('employee')->all();
                @endphp
                @foreach($couriers as $courier)
                    <option value="{{ $courier->id }}">{{ $courier->user->first_name . " " . $courier->user->last_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mt-4">
            <label for="location" class="block mb-2">Select Location:</label>
            <select id="location" name="location" class="border rounded px-3 py-2 w-full">
                @php
                    $locations = [];
                    $locs = Location::all();
                    foreach($locs as $location){
                        $locations[] = Node::fromId($location->id);
                    }
                    $locs = RouterNodes::all();
                    foreach($locs as $location){
                        $locations[] = Node::fromId($location->id);
                    }
                @endphp
                @foreach($locations as $location)
                    <option value="{{ $location->getID() }}">{{ $location->getDescription() }}</option>
                @endforeach
            </select>
        </div>
        <div class="mt-6">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Change Location</button>
        </div>
    </form>
</x-app-layout>