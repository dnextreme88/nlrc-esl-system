<x-layouts.master>
    <x-slot name="nav_menu">
        <livewire:NavMenu />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="p-6 lg:p-8 bg-gradient-to-r from-green-300 to-gray-200 dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-600 dark:via-green-700 border-b border-gray-200 dark:border-gray-700">
        <x-application-logo class="block h-12 w-auto" />

        <h1 class="mt-8 text-2xl font-medium text-gray-800 dark:text-gray-200">
            Welcome, {{ ucfirst(strtolower(Auth::user()->first_name)) }}!
        </h1>
    </div>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-2 overflow-hidden md:gap-4 lg:grid-cols-2 [&>*]:p-4 [&>*]:sm:rounded-lg">
                @if (Auth::user()->role->name == \App\Enums\Roles::STUDENT->value)
                    <div class="bg-gray-200 dark:bg-gray-600 col-span-2">
                        <livewire:select-meeting-slot />
                    </div>
                @endif

                @if (in_array(Auth::user()->role->name, [\App\Enums\Roles::HEAD_TEACHER->value, \App\Enums\Roles::STUDENT->value, \App\Enums\Roles::TEACHER->value]))
                    <div class="bg-gray-200 dark:bg-gray-600">
                        <livewire:upcoming-meetings />
                    </div>

                    <div class="bg-gray-200 dark:bg-gray-600">
                        <livewire:recent-meetings />
                    </div>
                @endif

                <div class="bg-gray-200 dark:bg-gray-600">
                    <livewire:announcements.mini-announcements />
                </div>
            </div>
        </div>
    </div>
</x-layouts.master>
