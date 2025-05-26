<div>
    <h3 class="text-3xl text-gray-800 dark:text-gray-200">Reserve your slot</h3>

    <p class="mx-2 my-4 text-gray-800 dark:text-gray-200">Select a date from the calendar and pick a slot to schedule a meeting with a teacher. Your current timezone is <strong>{{ Auth::user()->timezone }}</strong>. If this is not correct, please go to your <a wire:navigate class="text-green-600 dark:text-green-300 hover:underline" href="{{ route('settings.time') }}">settings and change it there</a>.</p>

    <div
        x-data="{
            availableMeetings: $wire.entangle('available_meetings'),
            isLoading: $wire.entangle('is_loading'),
        }"
        x-init="$wire.on('show-times-for-date', () => isLoading = true)"
        class="grid grid-cols-1 gap-2 lg:grid-cols-2 *:my-4 *:px-2"
    >
        <livewire:Calendar :dates="$possible_dates" />

        <div>
            <span x-show="isLoading" class="items-center">
                <x-loading-indicator
                    :loader_color_bg="'fill-gray-800 dark:fill-gray-200'"
                    :loader_color_spin="'fill-gray-800 dark:fill-gray-200'"
                    :text="'Showing time slots'"
                    :size="4"
                />
            </span>

            <div x-show="!isLoading && availableMeetings?.length != 0">
                @if (count($available_meetings) > 0)
                    <h4 class="text-lg text-gray-800 dark:text-gray-200">Available time slots for {{ $meeting_date }}</h4>
                @endif

                <ul class="*:py-4">
                    @foreach ($available_meetings as $meeting_time)
                        <li class="px-4 items-center grid grid-cols-1 gap-3 sm:grid-cols-2 lg:px-2">
                            <div>
                                <p>
                                    <span class="text-xl text-green-600 dark:text-green-300">&rarr;</span>
                                    <span class="text-gray-800 dark:text-gray-200 {{ $meeting_time['is_student_in_slot'] ? 'line-through decoration-2 decoration-green-600 dark:decoration-green-300' : '' }}">{{ $meeting_time['time'] }}</span>
                                </p>
                            </div>

                            @if ($meeting_time['is_student_in_slot'])
                                <p class="text-base text-start text-gray-600 dark:text-gray-300 sm:text-sm sm:text-end">You already reserved this slot</p>
                            @else
                                <x-secondary-button
                                    wire:click="reserve_slot_modal('{{ $meeting_time['start_time'] }}', '{{ $meeting_time['end_time'] }}')"
                                    class="justify-self-start sm:justify-self-end"
                                >
                                    <span
                                        wire:loading.flex wire:target="reserve_slot_modal('{{ $meeting_time['start_time'] }}', '{{ $meeting_time['end_time'] }}')"
                                        class="items-center"
                                    >
                                        <x-loading-indicator
                                            :loader_color_bg="'fill-gray-800 dark:fill-gray-200'"
                                            :loader_color_spin="'fill-gray-800 dark:fill-gray-200'"
                                            :show_text="false"
                                            :size="4"
                                        />
                                    </span>

                                    <span class="ms-2">Book this time</span>
                                </x-secondary-button>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <x-confirmation-modal :max_width="'xl'" :toggle_show_on_click="false" wire:model="show_reserve_slot_confirmation_modal">
        <x-slot name="title">
            <div class="flex justify-between items-center border-b-2 border-b-gray-200">
                <h3 class="text-2xl text-gray-800 dark:text-gray-200">Confirm Reservation Slot</h3>

                <button wire:click="$toggle('show_reserve_slot_confirmation_modal')" class="text-xl p-2 text-gray-800 dark:text-gray-200 hover:cursor-pointer">&times;</button>
            </div>
        </x-slot>

        <x-slot name="content">
            <p>Are you sure you want to book this time? Please confirm your meeting details below:</p>

            <p class="mt-2">
                <span class="text-xl text-green-600 dark:text-green-300">&rarr;</span>
                <span class="font-semibold text-gray-600 dark:text-gray-300">{{ Helpers::parse_time_to_user_timezone($start_time)->format('l, F, j Y') }}: {{ Helpers::parse_time_to_user_timezone($start_time)->format('h:i A') }} ~ {{ Helpers::parse_time_to_user_timezone($end_time)->format('h:i A') }}</span>
            </p>
        </x-slot>

        <x-slot name="footer">
            <x-button wire:click="reserve_slot" bg_colors="bg-green-200 dark:bg-green-600" text_colors="text-gray-800 dark:text-gray-200 mr-2">Confirm</x-button>

            <x-secondary-button wire:click="$toggle('show_reserve_slot_confirmation_modal')">Cancel</x-secondary-button>
        </x-slot>
    </x-confirmation-modal>
</div>
