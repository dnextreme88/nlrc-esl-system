<div>
    <x-slot name="nav_menu">
        <livewire:NavMenu />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Meetings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-100 dark:bg-gray-700 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-4">
                    <div class="ms-4 mb-6 col-span-4 *:py-4">
                        <h2 class="text-4xl text-gray-800 dark:text-gray-200">My Meetings</h2>

                        <p class="text-gray-800 dark:text-gray-200">View your calendar here. You may cancel upcoming meetings or re-schedule them</p>
                    </div>

                    @if ($is_student_role || $is_teacher_role)
                        <div class="ms-4 mb-6 col-span-4 *:py-4">
                            <livewire:upcoming-meetings />
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 space-x-3 *:px-4 *:py-3 mt-2 mb-6 items-center border-2 border-gray-300 dark:border-gray-800">
                    <div class="hover:cursor-pointer text-gray-800 dark:text-gray-200" wire:click="render_prev_month">
                        <x-chevron-left :text="'Previous month'" :text_classes="'hover:text-gray-600 dark:hover:text-gray-400'" class="size-6" />
                    </div>

                    <div class="text-xl md:text-2xl flex justify-center text-gray-800 dark:text-gray-200">{{ $current_month->format('F') }} {{ $current_year }}</div>

                    <div class="hover:cursor-pointer text-gray-800 dark:text-gray-200" wire:click="render_next_month">
                        <x-chevron-right :text="'Next month'" :text_classes="'hover:text-gray-600 dark:hover:text-gray-400'" class="size-6" />
                    </div>
                </div>

                @if ($is_student_role || $is_teacher_role)
                    <livewire:my-meetings-calendar
                        :drag-and-drop-enabled="false"
                        :key="$current_year.$current_month->format('n')"
                        initialYear="{{ $current_year }}"
                        initialMonth="{{ $current_month->format('n') }}"
                    />
                @endif
            </div>
        </div>
    </div>
</div>
