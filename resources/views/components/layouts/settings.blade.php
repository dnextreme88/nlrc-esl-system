<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- SEO purposes -->
        <meta name="keywords" content="Zeldan Nordic Language Review and Training Center" />
        <meta property="og:title" content="Zeldan Nordic Language Review and Training Center" />
        <meta name="og:description" content="Zeldan Nordic Language Review and Training Center" />

        <title>{{ config('app.name', 'NLRC-ESL') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <script>
            // Gets rid of white flashes on page load if the dark mode is on
            if (JSON.parse(localStorage.getItem('nlrcEslProjectIsDarkMode'))) {
                document.documentElement.classList.add('dark');
            }
        </script>

        @livewireStyles
        @vite('resources/css/app.css')
        @stack('styles')
        @stack('scripts')
    </head>

    <body x-data="window.darkModeSwitcher()" x-init="init" x-bind:class="{ 'dark': switchOn }" class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100 dark:bg-gray-800">
            @if (isset($nav_menu))
                @if (Request::routeIs('home'))
                    <div class="h-8 bg-gray-100 dark:bg-gray-800">&nbsp;</div>

                    <nav class="bg-gray-100 dark:bg-gray-800 top-0 sticky z-[1]">{{ $nav_menu }}</nav>
                @else
                    <nav class="bg-gray-100 dark:bg-gray-800">{{ $nav_menu }}</nav>
                @endif
            @endif

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                <div class="py-12">
                    <div class="w-full mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3">
                        <div class="px-10 py-20 ">
                            <nav>
                                <ul class="space-y-4 [&>*]:flex [&>*]:items-center [&>*]:gap-4">
                                    <li>
                                        <svg class="size-5 text-gray-800 dark:text-gray-200 {{ Request::routeIs('settings.user') ? 'fill-green-400 dark:fill-green-600' : '' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                        </svg>

                                        <x-nav-link wire:navigate href="{{ route('settings.user') }}" :active="Request::routeIs('settings.user')">
                                            {{ __('User') }}
                                        </x-nav-link>
                                    </li>

                                    <li>
                                        <svg class="size-5 text-gray-800 dark:text-gray-200 {{ Request::routeIs('settings.security') ? 'fill-green-400 dark:fill-green-600' : '' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                        </svg>

                                        <x-nav-link wire:navigate href="{{ route('settings.security') }}" :active="Request::routeIs('settings.security')">
                                            {{ __('Security') }}
                                        </x-nav-link>
                                    </li>
                                </ul>
                            </nav>
                        </div>

                        <div class="col-span-1 md:col-span-2 px-10 pt-20 pb-10 bg-gray-300/50 dark:bg-gray-600/50">
                            @if (Request::routeIs('settings.user'))
                                <livewire:settings.user-settings />
                            @elseif (Request::routeIs('settings.security'))
                                <livewire:settings.security-settings />
                            @else
                                <livewire:settings.settings-page />
                            @endif
                        </div>
                    </div>
                </div>
            </main>
        </div>

        @livewireScripts
        @livewireCalendarScripts
        @vite('resources/js/app.js')
        @stack('modals')
    </body>
</html>
