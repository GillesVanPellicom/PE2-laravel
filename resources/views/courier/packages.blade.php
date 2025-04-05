<x-courier>
    <x-slot:title>
        Packages
    </x-slot:title>
    <div>
        @php
            $users = App\Models\User::role('courier')->get();
            $employees = [];
            foreach ($users as $user) {
                $employees[] = $user->employee;
            }
            
            $employees1 = App\Models\User::role('courier')->get()->pluck('employee')->all();
        @endphp
        @foreach ($employees as $emp)
            {{ $emp }}
        @endforeach
        <br>
        <br>
        @foreach ($employees1 as $emp)
            {{ $emp }}
        @endforeach
    </div>
</x-courier>
