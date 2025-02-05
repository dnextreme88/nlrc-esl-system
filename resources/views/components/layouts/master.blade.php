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

        @livewireStyles
        @vite('resources/css/app.css')
        @stack('styles')
        @stack('scripts')
    </head>

    <body class="font-sans antialiased">
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
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
        @vite('resources/js/app.js')
        @stack('modals')
    </body>
</html>
