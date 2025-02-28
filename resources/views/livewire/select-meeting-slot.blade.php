<div>
    <h3 class="text-3xl text-gray-800 dark:text-gray-200">Reserve your slot</h3>

    <div class="grid grid-cols-1 md:grid-cols-3 [&>*]:my-4 [&>*]:px-2">
        <form wire:submit.prevent="show_available_times_for_selected_date">
            <x-label is_required="true" value="{{ __('Meeting Date') }}" for="meeting_date" />

            <x-select wire:model="meeting_date" name="meeting_date">
                <option value="">Select a date</option>
                @foreach ($possible_dates as $date)
                    <option value="{{ $date['db_format'] }}">{{ $date['view_format'] }}</option>
                @endforeach
            </x-select>

            @error ('meeting_date')
                <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
            @enderror

            <div>
                <x-button class="my-4 hover:cursor-pointer">
                    <span wire:loading.flex wire:target="show_available_times_for_selected_date" class="items-center">
                        <x-loading-indicator
                            :loader_color_bg="'fill-gray-200 dark:fill-gray-800'"
                            :loader_color_spin="'fill-gray-200 dark:fill-gray-800'"
                            :show_text="false"
                            :size="4"
                        />
                    </span>

                    <span class="ml-2">Show time slots</span>
                </x-button>
            </div>
        </form>

        <div class="md:col-span-2">
            <span wire:loading.flex wire:target="show_available_times_for_selected_date" class="items-center">
                <x-loading-indicator
                    :loader_color_bg="'fill-gray-200 dark:fill-gray-800'"
                    :loader_color_spin="'fill-gray-200 dark:fill-gray-800'"
                    :text="'Showing time slots'"
                    :size="4"
                />
            </span>

            <div wire:loading.remove wire:target="show_available_times_for_selected_date">
                @if ($is_meeting_date_chosen)
                    @if (count($available_meeting_slots_time) > 0)
                        <h4 class="text-lg text-gray-800 dark:text-gray-200">Available time slots for {{ \Carbon\Carbon::parse($meeting_date)->format('F j, Y') }}</h4>

                        <ul class="[&>*]:py-4">
                            @foreach ($available_meeting_slots_time as $meeting_slot_time)
                                <li class="px-4 items-center grid grid-cols-1 gap-3 sm:grid-cols-2 lg:px-2">
                                    <p>
                                        <span class="text-xl text-green-600 dark:text-green-300">&rarr;</span>
                                        <span class="text-gray-800 dark:text-gray-200">{{ $meeting_slot_time['start_time'] }} ~ {{ $meeting_slot_time['end_time'] }}</span>
                                    </p> 
                                    
                                    <x-secondary-button wire:click="reserve_slot_modal('{{ $meeting_slot_time['start_time'] }}', '{{ $meeting_slot_time['end_time'] }}')" class="justify-self-start sm:justify-self-end">Book this time</x-secondary-button>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="px-4 text-red-800 dark:text-red-200">This date has no available meeting slots.</p>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal wire:model="show_reserve_slot_confirmation_modal" :maxWidth="'xl'">
        <x-slot name="title">
            <div class="flex justify-between items-center border-b-2 border-b-gray-200">
                <h3 class="text-2xl text-gray-800 dark:text-gray-200">Confirm Reservation Slot</h3>

                <button wire:click="$toggle('show_reserve_slot_confirmation_modal')" class="text-xl p-2 text-gray-800 dark:text-gray-200">&times;</button>
            </div>
        </x-slot>

        <x-slot name="content">
            <p>Are you sure you want to book this time? Please confirm your meeting details below:</p>

            <p class="font-semibold mt-2">{{ \Carbon\Carbon::parse($meeting_date)->format('l, F, j Y') }} at {{ $start_time }} ~ {{ $end_time }}</p>
        </x-slot>

        <x-slot name="footer">
            <x-button wire:click="reserve_slot" bg_colors="bg-green-200 dark:bg-green-600" text_colors="text-gray-800 dark:text-gray-200 mr-2">Confirm</x-button>

            <x-secondary-button wire:click="$toggle('show_reserve_slot_confirmation_modal')">Cancel</x-secondary-button>
        </x-slot>
    </x-confirmation-modal>
</div>
