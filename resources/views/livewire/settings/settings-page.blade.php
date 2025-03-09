<div>
    <x-slot name="nav_menu">
        <livewire:NavMenu />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3">
            <x-custom.settings-sidebar />

            <div class="col-span-1 md:col-span-2 px-10 pt-20 pb-10 bg-gray-300/50 dark:bg-gray-600/50">
                <p class="text-gray-800 dark:text-gray-200">Please choose a navigation to the left to configure settings.</p>
            </div>
        </div>
    </div>
</div>
