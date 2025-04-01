
<x-app-layout>
    @section('title', 'Receiving Packages')
    <x-slot name="header">
        <div class="flex flex-row max-w-2xl items-center justify-center">
            <!-- Back Button -->

            <form action="{{route('pickup.dashboard')}}" method="get">
                <x-primary-button>
                    <svg xmlns="http://www.w3.org/2000/svg" height="32px" viewBox="0 0 32 32" width="32px" fill="currentColor">
                        <path d="M25.002,16c0,0.5522-0.4473,1-1,1H9.8672l4.9629,7.4453c0.3066,0.4595,0.1826,1.0806-0.2773,1.3867 C14.3818,25.9458,14.1895,26,13.999,26c-0.3232,0-0.6406-0.1563-0.833-0.4453L6.7959,16l6.3701-9.5547 c0.3057-0.46,0.9248-0.5845,1.3867-0.2773c0.46,0.3062,0.584,0.9272,0.2773,1.3867L9.8672,15H24.002 C24.5547,15,25.002,15.4478,25.002,16z"/>
                    </svg>
                </x-primary-button>
            </form>

            <!-- Centered Title -->
            <h2 class="font-semibold text-xl text-black leading-tight absolute left-1/2 transform -translate-x-1/2">
                {{ __('Receiving Packages') }}
            </h2>
        </div>

    </x-slot>
    <div class="py-12">
        <div class="max-w-5xl  mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <table class="min-w-full table-auto bg-gray-200 shadow-xl  sm:rounded-b-lg overflow-hidden">
                        <div class=" bg-gray-50 overflow-hidden shadow-lg sm:rounded-t-lg flex items-center sm:justify-between p-3">
                            <h2 class=" text-base font-semibold flex-grow w-full">List of packages that have passed one week of waiting: </h2>
                            <div class="flex justify-between items-center gap-3">
                                <form action="{{route('pickup.dashboard.receiving-packages')}}" method="get">
                                    <x-secondary-button type="submit"><x-refresh-logo ></x-refresh-logo></x-secondary-button>
                                </form>
                            </div>
                        </div>
                        <thead class="bg-gray-800 text-white ">
                        <tr>
                            <th class="py-3 px-4 text-center">Package ID</th>
                            <th class="py-3 px-4 text-center">Barcode</th>
                            <th class="py-3 px-4 text-center">Created on</th>
                            <th class="py-3 px-4 text-center">Updated on </th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($packages) == 0)
                            <tr class="border-b last:border-b-0 text-center hover:bg-gray-50 bg-gray-50 ">
                                <td class="py-3 px-4" colspan="6">You are not expecting any package for today :)</td>
                            </tr>
                        @else
                            @foreach($packages as $package)
                                <tr class="border-b last:border-b-0 text-center hover:bg-gray-50 bg-gray-100 ">
                                    <td class="py-3 px-4">{{ $package->id }}</td>
                                    <td class="py-3 px-4">{{ $package->reference }}</td>
                                    <td class="py-3 px-4">{{ \Carbon\Carbon::parse($package->created_at)->format('Y-m-d H:i') }}</td>
                                    <td class="py-3 px-4">{{ \Carbon\Carbon::parse($package->updated_at)->format('Y-m-d H:i') }}</td>
                                    <td class="py-3 px-4 text-blue-500 cursor-pointer"><a href="{{route('pickup.package.id',["id" => $package->id])}}" >Show</a></td>

                                </tr>
                            @endforeach
                        @endif

                        </tbody>

                    </table>
                    <div class="min-w-full flex justify-center items-center gap-6 mt-4">
                        {{$packages->onEachSide(1)->links()}}
                    </div>
                    <div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
