{{-- wire:ignore prevents refreshing this component so the active classes are not lost --}}
<div class="py-2 mx-4 sm:mx-0 mb-4 border-2 border-gray-300 dark:border-gray-600 md:mb-0 md:px-10 md:py-20 md:border-0" wire:ignore>
    <nav>
        <ul class="flex space-x-4 px-3 md:px-0 md:space-y-6 md:flex-col md:space-x-0 [&>*]:flex [&>*]:items-center [&>*]:gap-4">
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

            <li>
                <svg class="size-5 text-gray-800 dark:text-gray-200 {{ Request::routeIs('settings.time') ? 'fill-green-400 dark:fill-green-600' : '' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>

                <x-nav-link wire:navigate href="{{ route('settings.time') }}" :active="Request::routeIs('settings.time')">
                    {{ __('Time') }}
                </x-nav-link>
            </li>
        </ul>
    </nav>
</div>
