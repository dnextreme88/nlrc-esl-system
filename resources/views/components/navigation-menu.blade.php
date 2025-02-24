@auth
    <nav x-data="{ open: false }" class="bg-gray-100 dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <x-application-logo :link="'dashboard'" class="h-9" />
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 md:flex md:-my-px md:ms-10">
                        <x-nav-link wire:navigate href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    </div>

                    @if (in_array(Auth::user()->role->name, [\App\Enums\Roles::HEAD_TEACHER->value, \App\Enums\Roles::STUDENT->value, \App\Enums\Roles::TEACHER->value]))
                        <div class="hidden space-x-8 md:flex md:-my-px md:ms-10">
                            <x-nav-link wire:navigate href="{{ route('my-meetings') }}" :active="request()->routeIs('my-meetings')">
                                {{ __('My Meetings') }}
                            </x-nav-link>
                        </div>

                        <div class="hidden space-x-8 md:flex md:-my-px md:ms-10">
                            <x-nav-link wire:navigate href="{{ route('reservation-calendar') }}" :active="request()->routeIs('reservation-calendar')">
                                {{ __('Reservation Calendar') }}
                            </x-nav-link>
                        </div>
                    @elseif (Auth::user()->role->name == \App\Enums\Roles::ADMIN->value)
                        <div class="hidden space-x-8 md:flex md:-my-px md:ms-10">
                            <x-nav-link wire:navigate href="{{ url('/admin') }}">
                                {{ __('Admin Panel') }}
                            </x-nav-link>
                        </div>
                    @endif
                </div>

                <div class="hidden md:flex md:items-center md:ms-6">
                    <x-dark-mode-toggle>
                        <x-slot name="left_side">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 fill-green-300 dark:fill-transparent dark:text-green-400" aria-label="Toggle light mode">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                                <title>Toggle light mode</title>
                            </svg>
                        </x-slot>

                        <x-slot name="right_side">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 fill-white" aria-label="Toggle dark mode">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                                <title>Toggle dark mode</title>
                            </svg>
                        </x-slot>
                    </x-dark-mode-toggle>

                    <!-- Settings Dropdown -->
                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                    <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                        <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                    </button>
                                @else
                                    <span class="inline-flex rounded-md">
                                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                            {{ Auth::user()->name }}

                                            <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </button>
                                    </span>
                                @endif
                            </x-slot>

                            <x-slot name="content">
                                <div class="border-t border-gray-200 dark:border-gray-600"></div>

                                <!-- Account Management -->
                                <div class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400">
                                    {{ __('Manage Account') }}
                                </div>

                                {{-- TODO: Remove the Laravel Fortify views related to it --}}
                                <x-dropdown-link wire:navigate href="{{ route('settings.user') }}">
                                    {{ __('Settings') }}
                                </x-dropdown-link>

                                <div class="border-t border-gray-200 dark:border-gray-600"></div>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf

                                    <x-dropdown-link
                                        href="{{ route('logout') }}"
                                        @click.prevent="$root.submit();"
                                    >
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>

                <!-- Hamburger -->
                <div class="-me-2 flex items-center md:hidden">
                    <button
                        x-on:click="open = !open"
                        x-bind:class="{'focus:rotate-90': open}"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                    >
                        <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div :class="{'block': open, 'hidden': !open}" class="hidden md:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link wire:navigate href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            </div>

            @if (in_array(Auth::user()->role->name, [\App\Enums\Roles::HEAD_TEACHER->value, \App\Enums\Roles::STUDENT->value, \App\Enums\Roles::TEACHER->value]))
                <div class="pt-2 pb-3 space-y-1">
                    <x-responsive-nav-link wire:navigate href="{{ route('my-meetings') }}" :active="request()->routeIs('my-meetings')">
                        {{ __('My Meetings') }}
                    </x-responsive-nav-link>
                </div>

                <div class="pt-2 pb-3 space-y-1">
                    <x-responsive-nav-link wire:navigate href="{{ route('reservation-calendar') }}" :active="request()->routeIs('reservation-calendar')">
                        {{ __('Reservation Calendar') }}
                    </x-responsive-nav-link>
                </div>
            @elseif (Auth::user()->role->name == \App\Enums\Roles::ADMIN->value)
                <div class="pt-2 pb-3 space-y-1">
                    <x-responsive-nav-link wire:navigate href="{{ url('/admin') }}">
                        {{ __('Admin Panel') }}
                    </x-responsive-nav-link>
                </div>
            @endif

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                <div class="flex items-center px-4">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <div class="shrink-0 me-3">
                            <img class="size-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                        </div>
                    @endif

                    <div>
                        <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-dark-mode-toggle>
                        <x-slot name="left_side">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 fill-green-300 dark:fill-transparent dark:text-green-400" aria-label="Toggle light mode">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                                <title>Toggle light mode</title>
                            </svg>
                        </x-slot>

                        <x-slot name="right_side">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 fill-white" aria-label="Toggle dark mode">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                                <title>Toggle dark mode</title>
                            </svg>
                        </x-slot>
                    </x-dark-mode-toggle>

                    <!-- Account Management -->
                    {{-- TODO: Remove the Laravel Fortify views related to it --}}
                    <x-responsive-nav-link wire:navigate href="{{ route('settings.user') }}" :active="request()->route()->getPrefix() == '/settings'">
                        {{ __('Settings') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf

                        <x-responsive-nav-link
                            href="{{ route('logout') }}"
                            @click.prevent="$root.submit();"
                        >
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    </nav>
@else
    <nav class="flex justify-between items-center gap-3 p-4">
        <div>
            <x-application-logo class="h-9" />
        </div>

        <div>
            <a
                href="{{ route('login') }}"
                class="text-gray-900 dark:text-gray-100 hover:text-green-600 dark:hover:text-green-300 rounded-md px-3 py-2 ring-1 ring-transparent transition focus:outline-none focus-visible:ring-[#FF2D20] dark:focus-visible:ring-white"
            >
                Log in
            </a>

            <a
                href="{{ route('register') }}"
                class="text-gray-900 dark:text-gray-100 hover:text-green-600 dark:hover:text-green-300 rounded-md px-3 py-2 ring-1 ring-transparent transition focus:outline-none focus-visible:ring-[#FF2D20] dark:focus-visible:ring-white"
            >
                Register
            </a>
        </div>
    </nav>
@endauth
