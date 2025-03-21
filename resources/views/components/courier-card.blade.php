@php
    $packageids = session()->get('scanned_packages', []);
    $packages = empty($packageids) ? [] : collect(\App\Models\Package::whereIn('id', $packageids)
        ->orderByRaw('FIELD(id, ' . implode(',', $packageids) . ')')
        ->get())->reverse();
@endphp

@foreach ($packages as $package)
    <div class="p-2 mb-2 min-w-80 w-full border-solid shadow-xl border-gray-200 border-2 rounded-md">
        <div onclick="toggleDetails(this)" class="reference-info relative flex justify-between items-center">
            <p>
                <span class="font-semibold">Ref:</span>
                <span> #{{ $package->reference }} </span>
            </p>
            <i class="fa-solid fa-angle-left icon"></i>
        </div>

        <div style="display: none;" class="flex">
            <p>I have no idea what to put here.</p>
        </div>
    </div>
@endforeach
