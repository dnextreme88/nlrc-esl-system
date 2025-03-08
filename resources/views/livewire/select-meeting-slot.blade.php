<div>
    <h3 class="text-3xl text-gray-800 dark:text-gray-200">Reserve your slot</h3>

    <p class="mx-2 my-4 text-gray-800 dark:text-gray-200">Pick a date and a time below to schedule a meeting with a teacher. Your current timezone is <strong>{{ Auth::user()->timezone }}</strong>. If this is not correct, please go to your <a class="text-green-600 dark:text-green-300 hover:underline" href="{{ route('settings.time') }}">settings and change it there</a>.</p>

    <div class="grid grid-cols-1 lg:grid-cols-2 [&>*]:my-4 [&>*]:px-2">
        <form wire:submit.prevent="show_available_times_for_selected_date">
            <x-label is_required="true" value="{{ __('Meeting Date') }}" for="meeting_date" />

            <x-select wire:model="meeting_date" name="meeting_date">
                <option value="">Select a date</option>
                @foreach ($possible_dates as $date)
                    <option value="{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}">{{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</option>
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

        <div>
            <span wire:loading.flex wire:target="show_available_times_for_selected_date" class="items-center">
                <x-loading-indicator
                    :loader_color_bg="'fill-gray-800 dark:fill-gray-200'"
                    :loader_color_spin="'fill-gray-800 dark:fill-gray-200'"
                    :text="'Showing time slots'"
                    :size="4"
                />
            </span>

            <div wire:loading.remove wire:target="show_available_times_for_selected_date">
                @if ($is_meeting_date_chosen)
                    @if ($available_meeting_slots_time->isNotEmpty())
                        <h4 class="text-lg text-gray-800 dark:text-gray-200">Available time slots for {{ \Carbon\Carbon::parse($meeting_date)->format('F j, Y') }}</h4>

                        <ul class="[&>*]:py-4">
                            @foreach ($available_meeting_slots_time as $meeting_slot_time)
                                @php
                                    $student_already_reserved_in_slot = $meeting_slot_time->meeting_slot_users->pluck('id')
                                        ->first(fn ($user_id) => $user_id == Auth::user()->id);
                                @endphp

                                <li class="px-4 items-center grid grid-cols-1 gap-3 sm:grid-cols-2 lg:px-2">
                                    <div>
                                        <p>
                                            <span class="text-xl text-green-600 dark:text-green-300">&rarr;</span>
                                            <span class="text-gray-800 dark:text-gray-200 {{ $student_already_reserved_in_slot ? 'line-through decoration-2 decoration-green-600 dark:decoration-green-300' : '' }}">{{ \Carbon\Carbon::parse($meeting_slot_time['start_time'])->toUserTimezone()->format('h:i A') }} ~ {{ \Carbon\Carbon::parse($meeting_slot_time['end_time'])->toUserTimezone()->format('h:i A') }}</span>
                                        </p>
                                    </div>

                                    @if ($student_already_reserved_in_slot)
                                        <p class="text-base text-start text-gray-600 dark:text-gray-300 sm:text-sm sm:text-end">You already reserved this slot</p>
                                    @else
                                        <x-secondary-button
                                            wire:click="reserve_slot_modal('{{ $meeting_slot_time['start_time'] }}', '{{ $meeting_slot_time['end_time'] }}')"
                                            class="justify-self-start sm:justify-self-end"
                                        >
                                            Book this time
                                        </x-secondary-button>
                                    @endif
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

            <p class="mt-2">
                <span class="text-xl text-green-600 dark:text-green-300">&rarr;</span>
                <span class="font-semibold text-gray-600 dark:text-gray-300">{{ \Carbon\Carbon::parse($start_time)->toUserTimezone()->format('l, F, j Y') }}: {{ \Carbon\Carbon::parse($start_time)->toUserTimezone()->format('h:i A') }} ~ {{ \Carbon\Carbon::parse($end_time)->toUserTimezone()->format('h:i A') }}</span>
            </p>
        </x-slot>

        <x-slot name="footer">
            <x-button wire:click="reserve_slot" bg_colors="bg-green-200 dark:bg-green-600" text_colors="text-gray-800 dark:text-gray-200 mr-2">Confirm</x-button>

            <x-secondary-button wire:click="$toggle('show_reserve_slot_confirmation_modal')">Cancel</x-secondary-button>
        </x-slot>
    </x-confirmation-modal>
</div>
