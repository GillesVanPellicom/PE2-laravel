<nav x-data="{ open: false }" class="bg-white  border-b border-gray-100 ">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex justify-center items-center w-auto">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a class="flex  justify-center justify-items-center items-center gap-4" href="{{route('welcome')}}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 " />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div id="nav" class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    @role('pickup|admin')
                        <x-nav-link :href="route('pickup.dashboard')" :active="request()->routeIs('pickup.dashboard')">
                            {{ __('Pick Up Point') }}
                        </x-nav-link>
                    @endrole
                    @role('courier|admin')
                        <x-nav-link :href="route('courier')" :active="request()->routeIs('courier')">
                            {{ __('Courier') }}
                        </x-nav-link>
                    @endrole
                    <x-nav-link :href="route('packages.send-package')" :active="request()->routeIs('packages.send-package')">
                        {{ __('Send Package') }}
                    </x-nav-link>
                    <x-nav-link :href="route('packages.company-dashboard')" :active="request()->routeIs('packages.company-dashboard')">
                        {{ __('Company Dashboard') }}
                    </x-nav-link>
                    @role('HRManager|HR|admin')
                        <x-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.index')">
                            {{ __('Employees') }}
                        </x-nav-link>
                    @endrole
                    @role('airport|admin')
                    <x-nav-link :href="route('airports')" :active="request()->routeIs('airports')">
                        {{ __('Airport') }}
                    </x-nav-link>
                    @endrole

                </div>
            </div>

            <!-- Settings Dropdown -->

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @auth()
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500  bg-white  hover:text-gray-700  focus:outline-none transition ease-in-out duration-150">

                                <div>{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
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
                @endauth
            </div>


            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400  hover:text-gray-500  hover:bg-gray-100  focus:outline-none focus:bg-gray-100  focus:text-gray-500  transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @role('pickup|admin')
            <x-responsive-nav-link :href="route('pickup.dashboard')" :active="request()->routeIs('pickup.dashboard')">
                {{ __('Pick Up Point') }}
            </x-responsive-nav-link>
            @endrole
            @role('courier|admin')
            <x-responsive-nav-link :href="route('courier')" :active="request()->routeIs('courier')">
                {{ __('Courier') }}
            </x-responsive-nav-link>
            @endrole
            <x-responsive-nav-link :href="route('packages.send-package')" :active="request()->routeIs('packages.send-package')">
                {{ __('Send Package') }}
            </x-responsive-nav-link>
            @role('HRManager|HR|admin')
            <x-responsive-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.index')">
                {{ __('Employees') }}
            </x-responsive-nav-link>
            @endrole
            @role('airport|admin')
            <x-responsive-nav-link :href="route('airports')" :active="request()->routeIs('airports')">
                {{ __('Airport') }}
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
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf

                        <x-responsive-nav-link class="cursor-pointer"
                                               onclick="event.preventDefault();
                                        this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
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
