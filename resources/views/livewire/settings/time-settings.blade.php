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
                <x-form-section submit="update_timezone_settings">
                    <x-slot name="title">
                        {{ __('Timezone') }}
                    </x-slot>

                    <x-slot name="description">
                        {{ __('Set your timezone. This will affect the times set on various areas of the website.') }}
                    </x-slot>

                    <x-slot name="form">
                        <div class="col-span-6">
                            <x-label is_required="true" value="Timezone" for="timezone" />

                            <x-select :inline_block="false" wire:model.live="timezone" class="w-full" id="timezone">
                                <option value="">Select a timezone to use on this website</option>
                                @foreach ($timezones_list as $timezone)
                                    <option value="{{ $timezone }}">{{ $timezone }}</option>
                                @endforeach
                            </x-select>

                            <small class="text-gray-600 dark:text-gray-400">{{ $current_time }}</small>

                            <x-input-error class="mt-2" for="timezone" />
                        </div>

                        <x-action-message class="block me-3" on="timezone-settings-updated">
                            {{ __('Timezone settings updated.') }}
                        </x-action-message>
                    </x-slot>

                    <x-slot name="actions">
                        <x-button wire.loading.attr="disabled" class="my-4 hover:cursor-pointer">
                            <span wire:loading.flex wire:target="update_timezone_settings" class="items-center">
                                <x-loading-indicator
                                    :loader_color_bg="'fill-gray-200 dark:fill-gray-800'"
                                    :loader_color_spin="'fill-gray-200 dark:fill-gray-800'"
                                    :show_text="false"
                                    :size="4"
                                />

                                <span class="ml-2">Saving</span>
                            </span>

                            <span wire:loading.remove wire:target="update_timezone_settings">Save</span>
                        </x-button>
                    </x-slot>
                </x-form-section>
            </div>
        </div>
    </div>
</div>
