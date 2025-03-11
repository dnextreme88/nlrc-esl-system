<div>
    <x-slot name="nav_menu">
        <livewire:NavMenu />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Toggle Meeting Availability') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                x-data="{
                    showLoading: false,
                    {{--
                    dateTomorrow: new Date($wire.time_in_user_timezone_tomorrow),
                    nextMonth() {
                        $refs.prevButton.classList.toggle('hidden');
                        $refs.nextButton.classList.toggle('hidden');
                        this.dateTomorrow.setMonth(this.dateTomorrow.getMonth() + 1);
                        this.renderCalendar(false);
                    },
                    prevMonth() {
                        $refs.prevButton.classList.toggle('hidden');
                        $refs.nextButton.classList.toggle('hidden');

                        this.dateTomorrow.setMonth(this.dateTomorrow.getMonth() - 1);
                        this.renderCalendar(true);
                    },
                    renderCalendar(isCurrentMonth = true) {
                        console.log('LOG: Rendering calendar...');
                        const calendar = document.getElementById('calendar');

                        let currentDate = this.dateTomorrow;
                        let startHighlight = new Date(currentDate);
                        startHighlight.setDate(currentDate.getDate() + 1);
                        let endHighlight = new Date(currentDate);
                        endHighlight.setDate(currentDate.getDate() + 1 + 28);

                        calendar.innerHTML = '';
                        var month = currentDate.getMonth();
                        var year = currentDate.getFullYear();
                        $refs.monthYear.textContent = `${currentDate.toLocaleString('default', { month: 'long' })} ${year}`;

                        var firstDay = new Date(year, month, 1).getDay();
                        var daysInMonth = !isCurrentMonth ? new Date(year, month - 1, 0).getDate() : new Date(year, month + 1, 0).getDate();
                        var prevMonthDays = new Date(year, month, 0).getDate();
                        var totalCells = 42; // 6 weeks x 7 days grid

                        let calendarDays = [];

                        // Fill days from previous month
                        for (let i = firstDay - 1; i >= 0; i--) {
                            calendarDays.push(`<div class='text-gray-400 p-2 text-center border rounded-sm'>${prevMonthDays - i}</div>`);
                        }

                        // Fill days from current month
                        for (let day = 1; day <= daysInMonth; day++) {
                            var markup = '';

                            if (
                                (isCurrentMonth && this.dateTomorrow.getDate() < day) ||
                                (!isCurrentMonth && endHighlight.getDate() > day)
                            ) {
                                markup = `<div class='p-2 text-center border rounded-sm cursor-pointer hover:bg-blue-200'>${day}</div>`;
                            } else {
                                markup = `<div class='text-gray-400 p-2 text-center border rounded-sm'>${day}</div>`
                            }

                            calendarDays.push(markup);
                        }

                        // Fill days from next month
                        let remainingCells = totalCells - calendarDays.length;
                        let nextMonthSelectable = new Date(year, month + 1, 1) <= endHighlight;

                        for (let i = 1; i <= remainingCells; i++) {
                            calendarDays.push(`<div class='text-gray-400 p-2 text-center border rounded-sm'>${i}</div>`);
                        }

                        calendar.innerHTML = calendarDays.join('');

                        // Enable next button if there are selectable days in next month
                        $refs.nextButton.disabled = !nextMonthSelectable;
                        $refs.nextButton.classList.toggle('opacity-50', $refs.nextButton.disabled);
                    },
                    --}}
                    saveReservationSlots(e) {
                        const slots = [];

                        const checkedSlots = Array.from(document.querySelectorAll('.checkbox-reserved-slots'))
                            .filter(slot => slot.checked || slot.dataset.meetingSlotId != 0)
                            .map(s => slots.push({
                                start_time: s.dataset.startTime,
                                end_time: s.dataset.endTime,
                                is_opened: s.checked,
                            })
                        );

                        $dispatch('saving-reservation-slots', { reserved_slots: slots });
                    }
                }"
                x-init="
                    $wire.on('updated-reserved-slots', () => showLoading = false);

                    {{-- renderCalendar(true); --}}
                "
                class="bg-gray-200 dark:bg-gray-600 p-6"
            >
                <p class="text-gray-800 dark:text-gray-200">You may toggle your availability starting the next day until the next 28 days based on your timezone. Times are saved in UTC but will be rendered in your respective timezone. Your current timezone is <strong>{{ Auth::user()->timezone }}</strong>. If this is not correct, please go to your <a wire:navigate class="text-green-600 dark:text-green-300 hover:underline" href="{{ route('settings.time') }}">settings and change it there</a>.</p>

                <div class="*:my-4 *:px-2 {{ $is_meeting_date_chosen ? 'border-b-2 border-gray-700' : '' }}">
                    <form wire:submit.prevent="show_available_times_for_selected_date">
                        <div>
                            <x-label is_required="true" value="{{ __('Meeting Date') }}" for="meeting_date" />

                            @error ('meeting_date')
                                <span class="hidden md:block">&nbsp;</span>
                            @enderror
                        </div>

                        <div>
                            <x-select wire:model="meeting_date" name="meeting_date">
                                <option value="">Select a date</option>
                                @foreach ($possible_dates as $date)
                                    <option value="{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}">{{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</option>
                                @endforeach
                            </x-select>

                            @error ('meeting_date')
                                <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="items-center col-span-1 md:col-span-2">
                            <x-button class="my-4 hover:cursor-pointer">
                                <span wire:loading.flex wire:target="show_available_times_for_selected_date">
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

                    {{--
                    <div class="p-4 bg-white shadow-lg rounded-lg">
                        <div class="flex justify-between items-center mb-4">
                            <button
                                x-ref="prevButton"
                                x-on:click="prevMonth"
                                class="hidden p-2 bg-gray-300 rounded-sm hover:bg-gray-400"
                                id="prev"
                            >&larr;</button>

                            <h2
                                x-ref="monthYear"
                                class="text-lg font-semibold"
                                id="month-year"
                            >
                            </h2>

                            <button
                                x-ref="nextButton"
                                x-on:click="nextMonth"
                                class="p-2 bg-gray-300 rounded-sm hover:bg-gray-400"
                                id="next"
                            >&rarr;</button>
                        </div>

                        <div class="grid grid-cols-7 gap-2 text-center font-semibold">
                            <div>Sun</div>
                            <div>Mon</div>
                            <div>Tue</div>
                            <div>Wed</div>
                            <div>Thu</div>
                            <div>Fri</div>
                            <div>Sat</div>
                        </div>

                        <div id="calendar" class="grid grid-cols-7 gap-2 mt-2"></div>
                    </div>
                    --}}

                    <div>
                        <span wire:loading.flex wire:target="show_available_times_for_selected_date" class="py-3">
                            <x-loading-indicator
                                :loader_color_bg="'fill-gray-800 dark:fill-gray-200'"
                                :loader_color_spin="'fill-gray-800 dark:fill-gray-200'"
                                :text="'Showing time slots'"
                                :size="4"
                            />
                        </span>

                        <div wire:loading.remove wire:target="show_available_times_for_selected_date" class="grid grid-cols-1 gap-4 py-3 lg:grid-cols-2">
                            @if ($is_meeting_date_chosen)
                                <div class="col-span-1 self-end top-0 sticky z-10 opacity-75 bg-gray-200/100 dark:bg-gray-600/100 lg:col-span-2">
                                    <span class="text-gray-600 dark:text-gray-300">Slots:</span>
                                    <span class="text-gray-900 dark:text-gray-100 font-semibold text-lg">{{ $count_pending_reserved_slots }} / {{ count($time_slots) }}</span>
                                </div>

                                <div class="left">
                                    @foreach (array_slice($time_slots, 0, 24) as $time_slot_left_side)
                                        @php
                                            $has_existing_slot = $meeting_slots->first(function ($val) use ($time_slot_left_side) {
                                                return \Carbon\Carbon::parse($val['start_time'])->toUserTimezone()->format('H:i:s') == $time_slot_left_side['start_time'];
                                            });
                                        @endphp

                                        <div class="flex items-center justify-between border-b border-green-800 dark:border-green-200">
                                            <p class="text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::parse($time_slot_left_side['start_time'])->format('h:i A') }} ~ {{ \Carbon\Carbon::parse($time_slot_left_side['end_time'])->format('h:i A') }}</p>

                                            <div
                                                x-data="{ toggleSlot: {{ $has_existing_slot && $has_existing_slot['is_opened'] == 1 ? 'true' : 'false' }} }"
                                                class="grid grid-cols-1 place-items-center py-2"
                                            >
                                                @if ($has_existing_slot && $has_existing_slot['meeting_slot_users']->isNotEmpty())
                                                    @php
                                                        $as_array = $has_existing_slot['meeting_slot_users']->toArray();
                                                    @endphp

                                                    <div class="flex -space-x-2">
                                                        @foreach (array_slice($as_array, 0, 2) as $student)
                                                            <img
                                                                class="size-6 rounded-full object-cover border-green-300 border-2"
                                                                src="{{ $student['profile_photo_url'] }}"
                                                                alt="{{ $student['last_name'] }}, {{ $student['first_name'] }}"
                                                                title="{{ $student['last_name'] }}, {{ $student['first_name'] }} has booked this slot"
                                                            />
                                                        @endforeach

                                                        {{-- TODO: ADD A LINK THAT WHEN CLICKING THE PLUS BUTTON, IT SHOULD GO TO THE BOOKED CALENDAR WITH THE ASSOCIATED TIME --}}
                                                        @if (count($as_array) > 2)
                                                            <div class="size-6 rounded-full object-cover border-gray-300 border-2 text-white px-1 w-full text-[9px] bg-gray-800 flex items-center">{{ count($as_array) - 2 }}+</div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <input
                                                        x-bind:checked="toggleSlot"
                                                        class="hidden checkbox-reserved-slots"
                                                        type="checkbox"
                                                        data-meeting-slot-id="{{ $has_existing_slot ? $has_existing_slot['id'] : '0' }}"
                                                        data-start-time="{{ $meeting_date. ' ' .$time_slot_left_side['start_time'] }}"
                                                        data-end-time="{{ $meeting_date. ' ' .$time_slot_left_side['end_time'] }}"
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
                                                            class="pointer-events-none relative inline-block size-5 translate-x-0 transform rounded-full bg-white ring-0 shadow-xs transition duration-200 ease-in-out"
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
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="right">
                                    @foreach (array_slice($time_slots, 24) as $time_slot_right_side)
                                        @php
                                            $has_existing_slot = $meeting_slots->first(function ($val) use ($time_slot_right_side) {
                                                return \Carbon\Carbon::parse($val['start_time'])->toUserTimezone()->format('H:i:s') == $time_slot_right_side['start_time'];
                                            });
                                        @endphp

                                        <div class="flex items-center justify-between border-b border-green-800 dark:border-green-200">
                                            <p class="text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::parse($time_slot_right_side['start_time'])->format('h:i A') }} ~ {{ \Carbon\Carbon::parse($time_slot_right_side['end_time'])->format('h:i A') }}</p>

                                            <div
                                                x-data="{ toggleSlot: {{ $has_existing_slot && $has_existing_slot['is_opened'] == 1 ? 'true' : 'false' }} }"
                                                class="grid grid-cols-1 place-items-center py-2"
                                            >
                                                @if ($has_existing_slot && $has_existing_slot['meeting_slot_users']->isNotEmpty())
                                                    @php
                                                        $as_array = $has_existing_slot['meeting_slot_users']->toArray();
                                                    @endphp

                                                    <div class="flex -space-x-2">
                                                        @foreach (array_slice($as_array, 0, 2) as $student)
                                                            <img
                                                                class="size-6 rounded-full object-cover border-green-300 border-2"
                                                                src="{{ $student['profile_photo_url'] }}"
                                                                alt="{{ $student['last_name'] }}, {{ $student['first_name'] }}"
                                                                title="{{ $student['last_name'] }}, {{ $student['first_name'] }} has booked this slot"
                                                            />
                                                        @endforeach

                                                        {{-- TODO: ADD A LINK THAT WHEN CLICKING THE PLUS BUTTON, IT SHOULD GO TO THE BOOKED CALENDAR WITH THE ASSOCIATED TIME --}}
                                                        @if (count($as_array) > 2)
                                                            <div class="size-6 rounded-full object-cover border-gray-300 border-2 text-white px-1 w-full text-[9px] bg-gray-800 flex items-center">{{ count($as_array) - 2 }}+</div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <input
                                                        x-bind:checked="toggleSlot"
                                                        class="hidden checkbox-reserved-slots"
                                                        type="checkbox"
                                                        data-meeting-slot-id="{{ $has_existing_slot ? $has_existing_slot['id'] : '0' }}"
                                                        data-start-time="{{ $meeting_date. ' ' .$time_slot_right_side['start_time'] }}"
                                                        data-end-time="{{ $meeting_date. ' ' .$time_slot_right_side['end_time'] }}"
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
                                                            class="pointer-events-none relative inline-block size-5 translate-x-0 transform rounded-full bg-white ring-0 shadow-xs transition duration-200 ease-in-out"
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
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mx-auto py-3 col-span-1 lg:col-span-2">
                                    <x-secondary-button
                                        x-on:click="saveReservationSlots; showLoading = true"
                                        class="justify-self-start sm:justify-self-end"
                                    >
                                        Update slots for this date
                                    </x-secondary-button>
                                </div>
                            {{--
                                @if (count($available_meeting_slots_time) > 0)
                                    <h4 class="text-lg text-gray-800 dark:text-gray-200">Available time slots for {{ \Carbon\Carbon::parse($meeting_date)->format('F j, Y') }}</h4>

                                    <ul class="*:py-4">
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
                                @endif --}}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
