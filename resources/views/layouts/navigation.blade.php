@php use Illuminate\Support\Facades\Auth; @endphp
<nav x-data="{ open: false }" class="bg-white  border-b border-gray-100 ">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex justify-center items-center w-auto">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a class="flex  justify-center justify-items-center items-center gap-4" href="{{route('welcome')}}">
                        <x-application-logo class="block w-auto text-gray-800 "/>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div id="nav" class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('packages.send-package')"
                                :active="request()->routeIs('packages.send-package')">
                        {{ __('Send a parcel') }}
                    </x-nav-link>
                    <x-nav-link :href="route('track-parcel')" :active="request()->routeIs('track-parcel')">
                        {{ __('Track a parcel') }}
                    </x-nav-link>


                </div>
                <!-- Existing nav links -->

                <!-- Google App Launcher Button -->

            </div>

            <!-- Settings Dropdown -->

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @if(Auth::check() && Auth::user()->roles->isNotEmpty())
                    <div x-data="{ open: false }" class="relative ml-2">
                        <!-- Button: Google Apps Style -->
                        <button @click="open = !open"
                                class="w-10 h-10 rounded-full flex items-center justify-center hover:bg-gray-200 focus:outline-none"
                                aria-label="Open App Menu">
                            <span class="material-symbols-outlined text-2xl">apps</span>
                        </button>
                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" x-transition
                             class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl ring-1 ring-black ring-opacity-5 z-50 p-4"
                             style="display: none;"
                        >
                            <div class="grid grid-cols-3 gap-4 ">

                                @role('pickup|admin')
                                <a href="{{ route('workspace.pickup.dashboard') }}"
                                   class="flex flex-col items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 {{ request()->routeIs('pickup.dashboard') ? 'bg-blue-100' : '' }}">
                                    <span class="material-symbols-outlined text-3xl mb-1">storefront</span>
                                    <span class="text-xs font-medium">Pick Up Point</span>
                                </a>
                                @endrole

                                @role('courier|admin')
                                <a href="{{ route('workspace.courier') }}"
                                   class="flex flex-col items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 {{ request()->routeIs('courier') ? 'bg-blue-100' : '' }}">
                                    <span class="material-symbols-outlined text-3xl mb-1">local_shipping</span>
                                    <span class="text-xs font-medium">Courier</span>
                                </a>
                                @endrole

                                @role('HRManager|HR|admin')
                                <a href="{{ route('workspace.employees.index') }}"
                                   class="flex flex-col items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 {{ request()->routeIs('employees.index') ? 'bg-blue-100' : '' }}">
                                    <span class="material-symbols-outlined text-3xl mb-1">group</span>
                                    <span class="text-xs font-medium">Employees</span>
                                </a>
                                @endrole

                                @role('airport|admin')
                                <a href="{{ route('workspace.airports') }}"
                                   class="flex flex-col items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 {{ request()->routeIs('airports') ? 'bg-blue-100' : '' }}">
                                    <span class="material-symbols-outlined text-3xl mb-1">flight</span>
                                    <span class="text-xs font-medium">Airport</span>
                                </a>
                                @endrole

                                @role('DCManager|admin')
                                <a href="{{ route('workspace.dispatcher.index') }}"
                                   class="flex flex-col items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 {{ request()->routeIs('dispatcher.index') ? 'bg-blue-100' : '' }}">
                                    <span class="material-symbols-outlined text-3xl mb-1">hub</span>
                                    <span class="text-xs font-medium">Dispatcher</span>
                                </a>
                                @endrole
                                @role('courier|DCManager|admin')
                                <a href="{{ route('workspace.stranded-packages') }}"
                                   class="flex flex-col items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 {{ request()->routeIs('packages.stranded') ? 'bg-blue-100' : '' }}">
                                    <span class="material-symbols-outlined text-3xl mb-1">inventory_2</span>
                                    <span class="text-xs font-medium">Stranded Packages</span>
                                </a>
                                @endrole

                                @role('business_client|admin')
                                <a href="{{ route('packages.company-dashboard') }}"
                                   class="flex flex-col items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 {{ request()->routeIs('packages.company-dashboard') ? 'bg-blue-100' : '' }}">
                                    <span class="material-symbols-outlined text-3xl mb-1">dashboard</span>
                                    <span class="text-xs font-medium">Company Dashboard</span>
                                </a>
                                <a href="{{ route('manage-invoices') }}"
                                   class="flex flex-col items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 {{ request()->routeIs('manage-invoices') ? 'bg-blue-100' : '' }}">
                                    <span class="material-symbols-outlined text-3xl mb-1">receipt_long</span>
                                    <span class="text-xs font-medium">Manage Invoices</span>
                                </a>
                                @endrole

                            </div>
                        </div>
                    </div>
                @endif
                @if(auth()->check())
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500  bg-white  hover:text-gray-700  focus:outline-none transition ease-in-out duration-150">

                                @if(auth()->check() && auth()->user()->isCompany)
                                    {{ auth()->user()->company_name }}
                                @else
                                    {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                                @endif

                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link href="{{ route('profile') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="{{route('packages.mypackages')}}">
                                {{ __('My Packages') }}
                            </x-dropdown-link>

                            @if((!auth()->user()->hasPermissionTo('business_client.view') && auth()->user()->getRoleNames()->isNotEmpty()) || auth()->user()->hasPermissionTo('*'))
                                <x-dropdown-link href="{{ route('workspace.index') }}">
                                    {{ __('Workspace') }}
                                </x-dropdown-link>
                            @endif
                            @if(auth()->user()->hasPermissionTo('business_client.view') && auth()->user()->getRoleNames()->isNotEmpty())
                                <x-dropdown-link href="{{ route('packages.company-dashboard') }}">
                                    {{ __('Company Dashboard') }}
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('invoices.myinvoices') }}">
                                    {{ __('My Invoices') }}
                                </x-dropdown-link>
                            @endif

                            <x-dropdown-link href="{{route('tickets.nytickets')}}">
                                {{ __('Support') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('auth.logout') }}">
                                @csrf
                                <x-dropdown-link class="cursor-pointer"
                                                 onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center gap-1">
                        <x-nav-link :href="route('auth.login')" :active="request()->routeIs('auth.login')">
                            {{ __('Login') }}
                        </x-nav-link>
                        <span class="text-gray-500">|</span>
                        <x-nav-link :href="route('auth.register')" :active="request()->routeIs('auth.register')">
                            {{ __('Register') }}
                        </x-nav-link>
                    </div>
                @endif
            </div>


            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400  hover:text-gray-500  hover:bg-gray-100  focus:outline-none focus:bg-gray-100  focus:text-gray-500  transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(Route::is('workspace.*'))
                @role('pickup|admin')
                <x-responsive-nav-link :href="route('workspace.pickup.dashboard')"
                                       :active="request()->routeIs('pickup.dashboard')">
                    {{ __('Pick Up Point') }}
                </x-responsive-nav-link>
                @endrole
                @role('courier|admin')
                <x-responsive-nav-link :href="route('workspace.courier')" :active="request()->routeIs('courier')">
                    {{ __('Courier') }}
                </x-responsive-nav-link>
                @endrole
                @role('HRManager|HR|admin')
                <x-responsive-nav-link :href="route('workspace.employees.index')"
                                       :active="request()->routeIs('employees.index')">
                    {{ __('Employees') }}
                </x-responsive-nav-link>
                @endrole
                @role('airport|admin')
                <x-responsive-nav-link :href="route('workspace.airports')" :active="request()->routeIs('airports')">
                    {{ __('Airport') }}
                </x-responsive-nav-link>
                @endrole
                @role('DCManager|admin')
                <x-responsive-nav-link :href="route('workspace.dispatcher.index')"
                                       :active="request()->routeIs('dispatcher.index')">
                    {{ __('Dispatcher') }}
                </x-responsive-nav-link>
                @endrole
            @endif
            <x-responsive-nav-link :href="route('packages.send-package')"
                                   :active="request()->routeIs('packages.send-package')">
                {{ __('Send a parcel') }}
            </x-responsive-nav-link>
            @role('business_client|admin')
            <x-responsive-nav-link :href="route('packages.company-dashboard')"
                                   :active="request()->routeIs('packages.company-dashboard')">
                {{ __('Company Dashboard') }}
            </x-responsive-nav-link>
            @endrole
        </div>

        <!-- Responsive Settings Options -->

        <div class="pt-4 pb-1 border-t border-gray-200 ">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 "></div>
                <div class="font-medium text-sm text-gray-500"></div>
            </div>
            @auth()
                <x-responsive-nav-link href="{{ route('profile') }}">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{route('packages.mypackages')}}">
                    {{ __('My Packages') }}
                </x-responsive-nav-link>

                @if(!auth()->user()->hasPermissionTo('business_client.view') && auth()->user()->getRoleNames()->isNotEmpty())
                    <x-responsive-nav-link href="{{ route('workspace.index') }}">
                        {{ __('Workspace') }}
                    </x-responsive-nav-link>
                @elseif(auth()->user()->hasPermissionTo('business_client.view') && auth()->user()->getRoleNames()->isNotEmpty())
                    <x-responsive-nav-link href="{{ route('packages.company-dashboard') }}">
                        {{ __('Company Dashboard') }}
                    </x-responsive-nav-link>
                @endif
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <x-responsive-nav-link class="cursor-pointer"
                                           onclick="event.preventDefault();
                                                this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            @else
                <div class="mt-1 space-y-1">
                    <x-responsive-nav-link :href="route('auth.login')" :active="request()->routeIs('auth.login')">
                        {{ __('Login') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('auth.register')" :active="request()->routeIs('auth.register')">
                        {{ __('Register') }}
                    </x-responsive-nav-link>
                </div>
            @endauth
        </div>

    </div>
</nav>
