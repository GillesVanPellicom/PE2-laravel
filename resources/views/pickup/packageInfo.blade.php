<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-center items-center gap-6">
            <form class="self-start" action="{{route('pickup.dashboard')}}">
                <x-primary-button>
                    <svg xmlns="http://www.w3.org/2000/svg" height="32px" viewBox="0 0 32 32" width="32px" fill="currentColor">
                        <path d="M25.002,16c0,0.5522-0.4473,1-1,1H9.8672l4.9629,7.4453c0.3066,0.4595,0.1826,1.0806-0.2773,1.3867 C14.3818,25.9458,14.1895,26,13.999,26c-0.3232,0-0.6406-0.1563-0.833-0.4453L6.7959,16l6.3701-9.5547 c0.3057-0.46,0.9248-0.5845,1.3867-0.2773c0.46,0.3062,0.584,0.9272,0.2773,1.3867L9.8672,15H24.002 C24.5547,15,25.002,15.4478,25.002,16z"/>
                    </svg>
                </x-primary-button>
            </form>

            <h2 class="items-center font-semibold text-xl text-black  leading-tight text-center">
                {{ __('Package Details') }}
            </h2>
        </div>

    </x-slot>
    <div class="overflow-x-auto m-10 py-12 max-w-2xl sm:rounded-lg mx-auto sm:px-6 lg:px-8 bg-gray-100  shadow-2xl">
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
                <div class="py-3 flex justify-end flex-row gap-4 flex-wrap">
                    <form class=" mt-10 flex flex-row-reverse flex-wrap gap-6" action="{{route('pickup.dashboard.setStatusPackage',["id" => $package->id])}}" method="post">
                        @csrf
                        @method('PATCH')
                        <x-primary-button name="status"  value="Delivered">Set Package Delivered</x-primary-button>
                        <x-primary-button name="status"  value="To Return">Return Package</x-primary-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
