<x-app-layout>
    @section('title', 'Pickup Package Info')
    @section('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                document.getElementById('toReturnBtn').addEventListener("click", function () {
                    document.getElementById('setPackageToReturn').classList.toggle("hidden");
                });
                document.getElementById('toDeliveredBtn').addEventListener("click", function () {
                    document.getElementById('setPackageToDelivered').classList.toggle("hidden");
                });
                document.getElementById('cancel-btn').addEventListener("click", function () {
                    document.getElementById('setPackageToReturn').classList.toggle("hidden");
                });
                document.getElementById('cancel-btn1').addEventListener("click", function () {
                    document.getElementById('setPackageToDelivered').classList.toggle("hidden");
                });
                document.getElementById('cancel-btn2').addEventListener("click", function () {
                    document.getElementById('setPackageToReportMissing').classList.toggle("hidden");
                });
                // New button event listener
                document.getElementById('toReportMissingPackageBtn').addEventListener("click", function () {
                    document.getElementById('setPackageToReportMissing').classList.toggle("hidden");
                });
            });

        </script>
    @endsection
    <x-slot name="header">
        <div class="flex flex-row max-w-2xl items-center justify-center">
            <!-- Back Button -->
            <form action="{{route('workspace.pickup.dashboard')}}" method="get">
                <x-primary-button>
                    <svg xmlns="http://www.w3.org/2000/svg" height="32px" viewBox="0 0 32 32" width="32px" fill="currentColor">
                        <path d="M25.002,16c0,0.5522-0.4473,1-1,1H9.8672l4.9629,7.4453c0.3066,0.4595,0.1826,1.0806-0.2773,1.3867 C14.3818,25.9458,14.1895,26,13.999,26c-0.3232,0-0.6406-0.1563-0.833-0.4453L6.7959,16l6.3701-9.5547 c0.3057-0.46,0.9248-0.5845,1.3867-0.2773c0.46,0.3062,0.584,0.9272,0.2773,1.3867L9.8672,15H24.002 C24.5547,15,25.002,15.4478,25.002,16z"/>
                    </svg>
                </x-primary-button>
            </form>

            <!-- Centered Title -->
            <h2 class="font-semibold text-xl text-black leading-tight absolute left-1/2 transform -translate-x-1/2">
                {{ __('Package Details') }}
            </h2>
        </div>


    </x-slot>
    <div class="overflow-x-auto m-10 py-12 max-w-2xl sm:rounded-lg mx-auto sm:px-6 lg:px-8 bg-white  shadow-2xl">
        <div class="px-6 py-4 flex flex-col gap-6 ">
            <div class="font-bold text-xl mb-2 text-gray-800 ">Package ID: {{ request()->route('id') }}</div>
            <div class="flex flex-row justify-between gap-4">
                <x-input-label>
                    <h3 class="text-gray-700 text-base">Client Name: </h3>
                    <input type="text" name="client_fullName" value="{{$package->user->first_name ." " .$package->user->last_name}}"
                           disabled  class="w-full px-4 py-2 text-gray-700 border border-gray-400 rounded-md focus:ring-blue-500
                       focus:border-blue-500">
                </x-input-label>
                <x-input-label>
                    <h3 class="text-gray-700 text-base">Barcode: </h3>
                    <input type="text" name="client_fullName" value="{{ $package->reference }}"
                           disabled  class="w-full px-4 py-2 text-gray-700 border border-gray-400 rounded-md focus:ring-blue-500
                       focus:border-blue-500">
                </x-input-label>
            </div>
            <div class="flex flex-row justify-between gap-4">
                <x-input-label>
                    <h3 class="text-gray-700 text-base">Delivery Method: </h3>
                    <input type="text" name="client_fullName" value="{{ $package->deliveryMethod->name }}"
                           disabled  class="w-full px-4 py-2 text-gray-700 border border-gray-400 rounded-md focus:ring-blue-500
                       focus:border-blue-500">
                </x-input-label>
                <x-input-label>
                    <h3 class="text-gray-700 text-base">Status: </h3>
                    <input type="text" name="client_fullName" value="{{ $package->status }}"
                           disabled  class="w-full px-4 py-2 text-gray-700 border border-gray-400 rounded-md focus:ring-blue-500
                       focus:border-blue-500">
                </x-input-label>
            </div>

            <x-input-label>
                <h3 class="text-gray-700 text-base">Weight: </h3>
                <input type="text" name="client_fullName" value="{{ $package->weightClass->name }}"
                       disabled  class="w-full px-4 py-2 text-gray-700 border border-gray-400 rounded-md focus:ring-blue-500
                       focus:border-blue-500">
            </x-input-label>
            <div class="flex flex-col justify-stretch border-t-2 py-1">
                <x-input-label class="py-2"><h3 class="text-gray-700 text-base text-center">Receiver Info: </h3></x-input-label>
                <div class="flex flex-row justify-between gap-4 flex-wrap">
                    <x-input-label>
                        <h3 class="text-gray-700 text-base">Name: </h3>
                        <input type="text" name="client_fullName" value="{{ $package->name ." " .$package->lastName }}"
                               disabled  class="w-full px-4 py-2 text-gray-700 border border-gray-400 rounded-md focus:ring-blue-500
                       focus:border-blue-500">
                    </x-input-label>
                    <x-input-label>
                        <h3 class="text-gray-700 text-base">Email: </h3>
                        <input type="text" name="client_fullName" value="{{ $package->receiverEmail }}"
                               disabled  class="w-full px-4 py-2 text-gray-700 border border-gray-400 rounded-md focus:ring-blue-500
                       focus:border-blue-500">
                    </x-input-label>
                    <x-input-label>
                        <h3 class="text-gray-700 text-base">Phone: </h3>
                        <input type="text" name="client_fullName" value="{{ $package->receiver_phone_number }}"
                               disabled  class="w-full px-4 py-2 text-gray-700 border border-gray-400 rounded-md focus:ring-blue-500
                       focus:border-blue-500">
                    </x-input-label>
                </div>
                <div class="border-t-2 mt-6 py-3 flex justify-end flex-row gap-4 flex-wrap">
                    <div class=" mt-4 flex flex-row justify-center items-center w-full flex-wrap gap-6">

                        <x-primary-button id="toDeliveredBtn" name="status" value="Delivered">Set Package to Delivered</x-primary-button>
                        <x-primary-button id="toReturnBtn" name="status"  value="To Return">Set Package to Return </x-primary-button>
                        <x-primary-button id="toReportMissingPackageBtn" name="status"  value="Pending">Report Missing Package </x-primary-button>
                    </div>
                </div>
                <div id="setPackageToReturn" class="none hidden fixed inset-0 flex items-center justify-center bg-transparent  backdrop-blur-0">
                    <div class="p-6 bg-white rounded-lg shadow-lg">
                        <h3 class="text-lg font-bold text-gray-800">Do You want to set the package to return?</h3>
                        <div class="mt-4 flex justify-end gap-4">
                            <x-secondary-button id="cancel-btn">Cancel</x-secondary-button>
                            <form action="{{route('workspace.pickup.dashboard.setStatusPackage',["id" => $package->id])}}" method="post">
                                @csrf
                                @method('PATCH')
                                <x-primary-button name="status"  value="To Return"  type="submit">Return Package</x-primary-button>
                            </form>
                        </div>
                    </div>
                </div>
                <div id="setPackageToDelivered" class="none hidden fixed inset-0 flex items-center justify-center bg-transparent  backdrop-blur-0">
                    <div class="p-6 bg-white rounded-lg shadow-lg">
                        <h3 class="text-lg font-bold text-gray-800">Do You want to set the package delivered?</h3>
                        <div class="mt-4 flex justify-end gap-4">
                            <x-secondary-button id="cancel-btn1">Cancel</x-secondary-button>
                            <form action="{{route('workspace.pickup.dashboard.setStatusPackage',["id" => $package->id])}}" method="post">
                                @csrf
                                @method('PATCH')
                                <x-primary-button name="status"  value="Delivered" type="submit">Delivered Package</x-primary-button>
                            </form>
                        </div>
                    </div>
                </div>
                <div id="setPackageToReportMissing" class="none hidden fixed inset-0 flex items-center justify-center bg-transparent  backdrop-blur-0">
                    <div class="p-6 bg-white rounded-lg shadow-lg">
                        <h3 class="text-lg font-bold text-gray-800">Do You want to set the package delivered?</h3>
                        <div class="mt-4 flex justify-end gap-4">
                            <x-secondary-button id="cancel-btn2">Cancel</x-secondary-button>
                            <form action="{{route('workspace.pickup.dashboard.setStatusPackage',["id" => $package->id])}}" method="post">
                                @csrf
                                @method('PATCH')
                                <x-primary-button name="status"  value="Pending" type="submit">Report Missing Package</x-primary-button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
