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
    </head>

    <body x-data="window.darkModeSwitcher()" x-init="init" x-bind:class="{ 'dark': switchOn }" class="font-sans antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center pt-6 space-y-10 sm:pt-0 bg-gray-300">
            <div>
                <x-application-logo class="mt-12 size-18" />
            </div>

            <div
                class="bg-no-repeat bg-transparent dark:bg-gray-800 text-center overflow-hidden flex flex-col items-center max-h-[75vh] w-full md:max-w-7xl sm:rounded-lg justify-start md:justify-center p-[12.5%] md:px-[6.25%] md:py-[25%] lg:p-[12.5%] space-y-8 md:space-y-12"
                style="background-image: url('{{ asset('images/bg-error-pages.webp') }}'); background-size: 100% 100%;"
            >
                {{ $slot }}
            </div>
        </div>

        @filamentScripts
        @livewireScriptConfig
        @vite('resources/js/app.js')
    </body>
</html>
