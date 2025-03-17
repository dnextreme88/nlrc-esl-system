<x-layouts.errors>
    <h2 class="text-sm text-gray-200 sm:text-xl md:text-2xl lg:text-3xl font-chalkboard">You are not authorized to view this page! Please go back by clicking the link below.</h2>

    <a wire:navigate href="{{ route('home') }}">
        <x-secondary-button class="hover:text-green-600 dark:hover:text-green-300">
            {{ __('Go Home') }}
        </x-secondary-button>
    </a>
</x-layouts.errors>
