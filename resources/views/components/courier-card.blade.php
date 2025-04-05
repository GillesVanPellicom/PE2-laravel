@php
    $packageids = session()->get('scanned_packages', []);
    $successList = session()->get('recent_success', []);
    $packages = empty($packageids)
        ? []
        : collect(
            \App\Models\Package::whereIn('id', $packageids)
                ->orderByRaw('FIELD(id, ' . implode(',', $packageids) . ')')
                ->get(),
        )->reverse();
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

        <div style="display: none;" class="flex flex-col">
            <p class="m-2"> <span class="font-bold"> Status: </span> {{ $package->status }}</p>
            <button onclick="getPackageInfo({{ $package->id }})"
                class="bg-gray-800 hover:bg-gray-600 text-white px-4 py-2 rounded">
                Package Information
            </button>
            
            <button onclick="undoAction({{ $package->id }})" class="bg-red-700 text-white px-4 mt-2 py-2 rounded hover:bg-red-900">
                Undo Action
            </button>
        </div>
    </div>
@endforeach
