<x-app-layout>
    @section("pageName","Pickup Point")
    @section("meta")
        <meta name="csrf-token" content="{{ csrf_token() }}">
    @endsection
    @section("scripts")
        <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });

                // When a QR Code is scanned
                scanner.addListener('scan', function (qrData) {
                    window.location.href = "/pickup/package/" + qrData;  // Redirect to the package page
                });

                // Get Available Cameras and Start Scanner
                Instascan.Camera.getCameras().then(function (cameras) {
                    if (cameras.length > 0) {
                        // Start the first available camera
                        scanner.start(cameras[0]);
                    } else {
                        alert('No camera found!');
                    }
                }).catch(function (e) {
                    document.getElementById('preview').style.display = 'none';
                    document.getElementById('errCamera').style.display = 'block';
                    console.error('Error accessing the camera:', e);
                });

                document.getElementById('search-form').addEventListener('submit', function (e) {
                    e.preventDefault();
                    const idValue = document.getElementById('id').value.trim();
                    if (idValue) {
                        window.location.href = "/pickup/package/" + idValue;
                    } else {
                        document.getElementById('errSearch').style.display = 'block';
                    }
                    // window.location.href = "/pickup/package/" + document.getElementById('id').value;
                });
            });

        </script>

    @endsection
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800  leading-tight text-center">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!--div class="flex px-12 py-4 overflow-x-auto gap-6 mt-4 max-w-7xl justify-evenly  w-auto mx-auto">
        <div class="bg-white shadow-md w-1/3 w-auto overflow-hidden py-12 sm:px-6 lg:px-8 sm:rounded-lg flex justify-center">
            <div class="p-4 flex flex-col items-center">
                <h2 class="text-xl font-bold text-gray-800">25</h2>
                <p class="text-gray-600 mt-2">Orders</p>
            </div>
        </div>
        <div class="bg-white shadow-md w-2/3 overflow-hidden py-12 sm:px-6 lg:px-8 sm:rounded-lg flex justify-center">
            <div class="p-4 flex flex-col items-center">
                <h2 class="text-xl font-bold text-gray-800">{{count($packages)}}</h2>
                <p class="text-gray-600 mt-2">Orders</p>
            </div>
        </div>
        <div class="justify-center shadow-md bg-white w-1/3 w-auto overflow-hidden py-12 sm:px-6 lg:px-8 sm:rounded-lg flex">
            <div class="p-4 flex flex-col items-center">
                <h2 class="text-xl font-bold text-gray-800">25</h2>
                <p class="text-gray-600 mt-2">Orders</p>
            </div>
        </div>
    </div!-->

    <div class=" flex flex-col justify-center items-center align-middle  overflow-x-auto py-20 max-w-7xl sm:rounded-lg mx-auto sm:px-6 lg:px-8">
        <div class="bg-white border overflow-hidden rounded-md shadow-slate-100 shadow-lg p-8 flex w-fit flex-col gap-6 justify-center items-center ">
            <div class=" flex-wrap flex w-fit flex-col gap-x-2 gap-y-4 justify-center items-center">
                <h2 class="font-bold">Scan the package</h2>
                <span style="display: none;" class="text-red-700 px-3 py-2 rounded bg-red-200" id="errCamera">Error Accessing camera or no camera found,
                    please enter the barcode manually</span>

                <!-- Video Element for Camera -->
                <video id="preview" class="min-w-32 max-w-56 max-h-56 w-48 h-48 min-h-32 box-border border rounded-md"></video>
            </div>
            <div class="border-t-2 flex flex-col w-auto flex-wrap gap-2 justify-center items-center">
                <p class="border-t-2"></p>
                <p>Enter manually the package id or barcode</p>
                <form id="search-form" action="{{ route('pickup.package.id' ,["id" => request()->get('id') ?request()->get('id') : ' ']) }}" method="GET" class="flex gap-3 flex-col flex-wrap">

                    <div class="flex gap-2 flex-wrap">
                        <div class="relative max-w-xs">
                            <input name="id" value="" id="id" type="text" placeholder="Barcode..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.9 14.32a8 8 0 111.414-1.414l4.387 4.387a1 1 0 01-1.414 1.414l-4.387-4.387zM8 14a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <x-primary-button type="submit">
                            Search
                        </x-primary-button>
                    </div>

                    <span style="display: none;" class=" text-center text-red-700 px-3 py-2 rounded bg-red-200" id="errSearch">Non valid input has been entered</span>
                </form>
            </div>
        </div>



        <!--table class="min-w-full table-auto bg-gray-200 shadow-xl  sm:rounded-b-lg overflow-hidden">
            <div class=" bg-gray-100 overflow-hidden shadow-lg sm:rounded-t-lg flex sm:justify-between p-3">
                <div class="relative w-full max-w-xs">
                    <form action="{{route('pickup.dashboard')}}" method="GET">
                        <input name="search" value="{{request()->get('search') ? request()->get('search'): ""}}" id="search" type="text" placeholder="Search..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.9 14.32a8 8 0 111.414-1.414l4.387 4.387a1 1 0 01-1.414 1.414l-4.387-4.387zM8 14a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </form>

                </div>

                <div class="flex items-center justify-between gap-3">
                    <form action="{{route('pickup.dashboard',request()->query())}}" method="get"><x-secondary-button type="submit"><x-refresh-logo ></x-refresh-logo></x-secondary-button></form>
                    <x-secondary-button><x-filter-logo></x-filter-logo></x-secondary-button>
                </div>
            </div>
            <thead class="bg-gray-800 text-white ">
            <tr>
                <th class="py-3 px-4 text-center">#</th>
                <th class="py-3 px-4 text-center">Client Name</th>
                <th class="py-3 px-4 text-center">Barcode</th>
                <th class="py-3 px-4 text-center">Delivery Service</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-center">Actions</th>
            </tr>
            </thead>
            <tbody>
            @if(count($packages) == 0)
                <tr class="border-b last:border-b-0 text-center hover:bg-gray-50 bg-gray-50 ">
                    <td class="py-3 px-4" colspan="6">No data found</td>
                </tr>
            @else
                @foreach($packages as $package)
                    <tr class="border-b last:border-b-0 text-center hover:bg-gray-50 bg-gray-100 ">
                        <td class="py-3 px-4">{{ $package->id }}</td>
                        <td class="py-3 px-4">{{ $package->user->first_name." ".$package->user->last_name }}</td>
                        <td class="py-3 px-4">{{ $package->reference }}</td>
                        <td class="py-3 px-4">{{ $package->deliveryMethod->name }}</td>
                        <td class="py-3 px-4">{{ $package->status }}</td>
                        <td class="py-3 px-4 text-blue-500 cursor-pointer"><a href="{{route('pickup.package.id',["id" => $package->id])}}" >Show</a></td>

                    </tr>
                @endforeach
            @endif

            </tbody>

        </table>
        <div class="min-w-full flex justify-center items-center gap-6 mt-4">
            {{$packages->onEachSide(1)->links()}}
        </div!-->
    </div>

</x-app-layout>
