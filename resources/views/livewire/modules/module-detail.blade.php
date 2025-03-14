<div>
    <x-slot name="nav_menu">
        <livewire:NavMenu />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __($current_module->name) }}
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 gap-9 mx-auto space-y-6 max-w-7xl py-12 lg:space-y-0 lg:grid-cols-3 lg:place-items-center lg:h-75-vh sm:px-6 lg:px-8">
        <p class="text-gray-800 dark:text-gray-200">Module details here. List down the courses under this module.</p>
    </div>
</div>
