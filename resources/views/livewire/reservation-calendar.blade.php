<div>
    <x-slot name="nav_menu">
        <x-navigation-menu />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Reservation Calendar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div x-data="{
                    showLoading: false,
                    saveReservationSlots(e) {
                        const slots = [];

                        const checkedSlots = Array.from(document.querySelectorAll('.checkbox-reserved-slots'))
                            .filter(slot => slot.checked)
                            .map(s => slots.push({
                                date: s.dataset.date,
                                start_time: s.dataset.startTime,
                                end_time: s.dataset.endTime,
                            })
                        );

                        $dispatch('saving-reservation-slots', { reserved_slots: slots });
                    }
                }"
                x-init="$wire.on('updated-reserved-slots', () => showLoading = false);"
                class="bg-gray-100 dark:bg-gray-700 overflow-hidden shadow-xl sm:rounded-lg"
            >
                <div class="grid grid-cols-1 md:grid-cols-4">
                    <div class="ms-4 mb-6 col-span-4 [&>*]:py-4">
                        <h2 class="text-4xl text-gray-800 dark:text-gray-200">Reservation Calendar</h2>

                        <p class="text-gray-800 dark:text-gray-200">Opening and closing of reservation slots can be made within the next 28 days.</p>
                    </div>

                    <button x-on:click="saveReservationSlots; showLoading = true" class="col-span-2 lg:col-span-1 text-gray-800 dark:text-gray-200 hover:cursor-pointer px-4 py-2 bg-green-200 dark:bg-green-800 my-2 mx-4 transition duration-150 hover:bg-green-400 dark:hover:bg-green-600">
                        <span x-text="showLoading ? 'Reserving' : 'Reserve selected slots'">Reserve selected slots</span>
                    </button>

                    <x-action-message class="col-span-2 lg:col-span-3 mr-4 self-center justify-self-end" on="saving-reservation-slots">
                        <x-loading-indicator
                            :loader_color_bg="'fill-gray-800 dark:fill-gray-200'"
                            :loader_color_spin="'fill-gray-800 dark:fill-gray-200'"
                            :show_text="false"
                            :size="4"
                        />
                    </x-action-message>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 space-x-3 [&>*]:px-4 [&>*]:py-3 mt-2 mb-6 items-center border-2 border-gray-300 dark:border-gray-800">
                    @if ($is_today)
                        <div class="self-start">&nbsp;</div>
                    @else
                        <div class="hover:cursor-pointer text-gray-800 dark:text-gray-200" wire:click="render_prev_seven_days">
                            <x-chevron-left :text="'Last 7 days'" :text_classes="'hover:text-gray-600 dark:hover:text-gray-400'" class="size-6" />
                        </div>
                    @endif

                    <div class="text-xl md:text-2xl flex justify-center text-gray-800 dark:text-gray-200">{{ $current_month_and_year }}</div>

                    @if ($is_max_date)
                        <div class="self-end">&nbsp;</div>
                    @else
                        <div class="hover:cursor-pointer text-gray-800 dark:text-gray-200" wire:click="render_next_seven_days">
                            <x-chevron-right :text="'Next 7 days'" :text_classes="'hover:text-gray-600 dark:hover:text-gray-400'" class="size-6" />
                        </div>
                    @endif
                </div>

                <div>
                    <span wire:loading.flex wire:target="render_prev_seven_days,render_next_seven_days" class="col-span-3 py-6 justify-center">
                        <x-loading-indicator
                            :loader_color_bg="'fill-gray-800 dark:fill-gray-200'"
                            :loader_color_spin="'fill-gray-800 dark:fill-gray-200'"
                            :text="'Loading reservations'"
                            :size="12"
                            class="gap-2"
                        />
                    </span>

                    <div class="grid grid-cols-10" wire:loading.remove wire:target="render_prev_seven_days,render_next_seven_days">
                        <div class="grid grid-cols-1 col-span-3">
                            <div class="grid grid-cols-1 text-center max-h-[105px]">
                                <h2 class="text-4xl text-gray-800 dark:text-gray-200">Slots</h2>
                                <h3 class="mt-2 text-lg text-gray-800 dark:text-gray-200">&nbsp;</h3>
                                <h3 class="mt-2 text-sm text-gray-800 dark:text-gray-200">&nbsp;</h3>
                            </div>

                            @foreach ($time_slots as $time)
                                <div class="p-2 text-gray-800 dark:text-gray-200 break-words border-t border-r-2 border-t-green-800 dark:border-t-green-200 border-r-gray-600 dark:border-r-gray-200">
                                    <span>{{ $time['start_time'] }} ~ {{ $time['end_time'] }}</span>
                                </div>
                            @endforeach
                        </div>

                        @foreach ($dates as $day => $date)
                            <div class="grid grid-cols-1">
                                @php
                                    $count_pending_reserved_slots = count(array_filter($meeting_slots, function ($val) use ($time, $date) {
                                        return $val['meeting_date'] == $date['date'] &&
                                            $val['status'] == \App\Enums\MeetingStatuses::PENDING->value &&
                                            $val['is_reserved'] == 1 &&
                                            $val['meeting_slot_users'] == null;
                                    }));
                                @endphp

                                <div class="text-center">
                                    <h2 class="text-4xl text-green-600 dark:text-green-400">{{ $day }}</h2>
                                    <h3 class="mt-2 text-lg text-gray-600 dark:text-gray-400">{{ $date['date_shorthand'] }}</h3>
                                    <h4 class="mt-2 text-sm text-gray-800 dark:text-gray-200">{{ $count_pending_reserved_slots }} / {{ count($time_slots) }}</h3>
                                </div>

                                @foreach ($time_slots as $key => $time)
                                    @php
                                        $has_existing_slot = array_values(array_filter($meeting_slots, function ($val) use ($time, $date) {
                                            return $val['meeting_date'] == $date['date'] &&
                                                $val['start_time'] == $time['start_time'] &&
                                                $val['end_time'] == $time['end_time'];
                                        }));
                                    @endphp

                                    <div
                                        x-data="{ toggleSlot: {{ $has_existing_slot && $has_existing_slot[0]['is_reserved'] == 1 ? 'true' : 'false' }} }"
                                        class="grid grid-cols-1 place-items-center py-2 border-t border-green-800 dark:border-green-200"
                                    >
                                        @if ($has_existing_slot && count($has_existing_slot[0]['meeting_slot_users']) == 0 || !$has_existing_slot)
                                            <input
                                                x-bind:checked="toggleSlot"
                                                class="hidden checkbox-reserved-slots"
                                                {{ $has_existing_slot && $has_existing_slot[0]['is_reserved'] ? 'data-meeting-slot-id="$has_existing_slot[0]["id"]"' : '' }}
                                                type="checkbox"
                                                data-date="{{ $date['date'] }}"
                                                data-start-time="{{ $time['start_time'] }}"
                                                data-end-time="{{ $time['end_time'] }}"
                                            />

                                            <button
                                                x-on:click="toggleSlot = !toggleSlot"
                                                x-bind:class="{'bg-green-500 focus:ring-green-400': toggleSlot, 'bg-red-500 focus:ring-red-400': !toggleSlot}"
                                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-offset-2 focus:outline-hidden"
                                                role="switch"
                                                aria-checked="false"
                                                type="button"
                                                title="Toggle this slot"
                                            >
                                                <span class="sr-only">Toggle slot availability</span>

                                                <span
                                                    x-bind:class="{'translate-x-5': toggleSlot, 'translate-x-0': !toggleSlot}"
                                                    class="pointer-events-none relative inline-block size-5 translate-x-0 transform rounded-full bg-white ring-0 shadow-sm transition duration-200 ease-in-out"
                                                >
                                                    <span
                                                        x-bind:class="{'opacity-0 duration-100 ease-out': toggleSlot, 'opacity-100 duration-200 ease-in': !toggleSlot}"
                                                        class="absolute inset-0 flex size-full items-center justify-center opacity-100 transition-opacity duration-200 ease-in"
                                                        aria-hidden="true"
                                                    >
                                                        <svg class="size-3 text-red-600" fill="none" viewBox="0 0 12 12">
                                                            <path d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </span>

                                                    <span
                                                        x-bind:class="{'opacity-100 duration-200 ease-in': toggleSlot, 'opacity-0 duration-100 ease-out': !toggleSlot}"
                                                        class="absolute inset-0 flex size-full items-center justify-center opacity-0 transition-opacity duration-100 ease-out"
                                                        aria-hidden="true"
                                                    >
                                                        <svg class="size-3 text-green-600" fill="currentColor" viewBox="0 0 12 12">
                                                            <path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z" />
                                                        </svg>
                                                    </span>
                                                </span>
                                            </button>
                                        @elseif ($has_existing_slot && !is_null($has_existing_slot[0]['meeting_slot_users']))
                                            <div class="flex -space-x-2">
                                                @foreach (array_slice($has_existing_slot[0]['meeting_slot_users'], 0, 2) as $student)
                                                    <img
                                                        class="size-6 rounded-full object-cover border-green-300 border-2"
                                                        src="{{ $student['profile_photo_url'] }}"
                                                        alt="{{ $student['last_name'] }}, {{ $student['first_name'] }}"
                                                        title="{{ $student['last_name'] }}, {{ $student['first_name'] }} has booked this slot"
                                                    />
                                                @endforeach

                                                {{-- TODO: ADD A LINK THAT WHEN CLICKING THE PLUS BUTTON, IT SHOULD GO TO THE BOOKED CALENDAR WITH THE ASSOCIATED TIME --}}
                                                @if (count($has_existing_slot[0]['meeting_slot_users']) > 2)
                                                    <div class="size-6 rounded-full object-cover border-gray-300 border-2 text-white px-1 w-full text-[9px] bg-gray-800 flex items-center">{{ count($has_existing_slot[0]['meeting_slot_users']) }}+</div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
