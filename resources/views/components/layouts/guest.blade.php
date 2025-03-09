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

        @filamentStyles
        @livewireStyles
        @vite('resources/css/app.css')
        @stack('styles')
        @stack('scripts')
    </head>

    <body x-data="window.darkModeSwitcher()" x-init="init" x-bind:class="{ 'dark': switchOn }" class="font-sans antialiased">
        <div class="font-sans text-gray-900 dark:text-gray-100 antialiased">
            {{ $slot }}
        </div>

        @filamentScripts
        @livewireScripts
        @livewireCalendarScripts
        @vite('resources/js/app.js')
        @stack('modals')
    </body>
</html>
