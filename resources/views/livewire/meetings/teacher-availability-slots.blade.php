<div
    x-data="{
        isMeetingDateChosen: $wire.entangle('is_meeting_date_chosen'),
        meetingDate: $wire.entangle('meeting_date'),
        meetings: [],
        timeSlots: [],
        reserveSlots() {
            const openedTimeSlots = this.timeSlots.filter(slot =>
                (!slot.id && slot.is_opened) || (slot.id && !slot.students?.length > 0)
            )

            $dispatch('updating-slots', { slots_to_update: openedTimeSlots })
        },
        toggleTimeSlot(isOpened, idx) {
            this.timeSlots[idx].is_opened = isOpened
        }
    }"
    x-init="
        $wire.on('rendered-time-slots', data => {
            meetings = data.meetings
            timeSlots = data.time_slots

            timeSlots.forEach((slot, idx) => {
                timeSlots[idx].is_opened = false
                const hasTime = meetings.find(ms => ms.start_time == slot.start_time)

                if (hasTime) {
                    timeSlots[idx] = {
                        ...timeSlots[idx],
                        id: hasTime.id,
                        route: hasTime.route,
                        is_opened: hasTime.is_opened == 1 ? true : false,
                        students: hasTime.meeting_users,
                    }
                }
            })
        })
    "
>
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
            <div class="bg-gray-200 dark:bg-gray-600 p-6">
                <p class="text-gray-800 dark:text-gray-200">You may toggle your availability starting the next day until the next 28 days based on your timezone. Times are saved in UTC but will be rendered in your respective timezone. Your current timezone is <strong>{{ Auth::user()->timezone }}</strong>. If this is not correct, please go to your <a wire:navigate class="text-green-600 dark:text-green-300 hover:underline" href="{{ route('settings.time') }}">settings and change it there</a>.</p>

                <div x-bind:class="{ 'border-b-2 border-gray-700': isMeetingDateChosen }" class="*:my-4 *:px-2">
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

                    <div>
                        <span wire:loading.flex wire:target="show_available_times_for_selected_date" class="py-3">
                            <x-loading-indicator
                                :loader_color_bg="'fill-gray-800 dark:fill-gray-200'"
                                :loader_color_spin="'fill-gray-800 dark:fill-gray-200'"
                                :text="'Showing time slots'"
                                :size="4"
                            />
                        </span>

                        <div
                            x-cloak
                            x-show="isMeetingDateChosen"
                            wire:loading.remove wire:target="show_available_times_for_selected_date"
                            class="grid grid-cols-1 gap-4 py-3 md:gap-0 lg:grid-cols-2"
                        >
                            <div class="col-span-1 self-end top-0 sticky z-10 opacity-75 bg-gray-200/100 dark:bg-gray-600/100 lg:col-span-2">
                                <span class="text-gray-600 dark:text-gray-300">Slots:</span>
                                <span class="text-gray-900 dark:text-gray-100 font-semibold text-lg">{{ $count_pending_reserved_slots }} / {{ count($time_slots) }}</span>
                            </div>

                            <template x-for="(slot, currentIndex) in timeSlots" :key="currentIndex">
                                <div
                                    x-data="{ toggleSlot: slot.is_opened }"
                                    class="flex items-center justify-between border-b border-green-800 dark:border-green-200 even:pl-0 even:lg:pl-7"
                                >
                                    <p class="text-gray-800 dark:text-gray-200">
                                        <span x-text="slot.start_time"></span> ~ <span x-text="slot.end_time"></span>
                                    </p>

                                    <div class="grid grid-cols-1 place-items-center py-2">
                                        {{-- Show this element if at least 1 student has booked this slot --}}
                                        <a
                                            x-bind:href="slot.route"
                                            x-show="slot.is_opened && slot.students"
                                            wire:navigate
                                            class="flex -space-x-2"
                                        >
                                            <template x-for="(student, index) in slot.students?.slice(0, 2)" :key="index">
                                                {{-- Blade component equivalent: <x-round-image /> --}}
                                                <span class="w-full">
                                                    <img
                                                        x-bind:alt="`${student.last_name}, ${student.first_name}`"
                                                        x-bind:src="student.profile_photo_url"
                                                        x-bind:title="`${student.last_name}, ${student.first_name} has booked this slot`"
                                                        x-bind:aria_label="`${student.last_name}, ${student.first_name} has booked this slot`"
                                                        class="size-6 rounded-full object-cover border-green-300 border-2"
                                                        loading="lazy"
                                                    />
                                                </span>
                                            </template>

                                            <div
                                                x-show="slot.students && slot.students?.length > 2"
                                                class="size-6 rounded-full object-cover border-gray-300 border-2 text-white px-1 w-full text-[0.7rem] bg-gray-800 flex items-center"
                                            >
                                                <span x-text="(slot.students?.length - 2)"></span>+
                                            </div>
                                        </a>

                                        {{-- Show the remaining elements if the slot is opened or the slot is open but no students has booked it yet --}}
                                        <input
                                            x-bind:checked="slot.is_opened"
                                            x-cloak
                                            x-show="!slot.students || slot.students?.length == 0"
                                            class="hidden checkbox-reserved-slots"
                                            type="checkbox"
                                        />

                                        <button
                                            x-cloak
                                            x-on:click="toggleSlot = !toggleSlot; toggleTimeSlot(toggleSlot, currentIndex);"
                                            x-show="!slot.students || slot.students?.length == 0"
                                            x-bind:class="{
                                                'bg-green-500 focus:ring-green-400': slot.is_opened,
                                                'bg-red-500 focus:ring-red-400': !slot.is_opened
                                            }"
                                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-offset-2 focus:outline-hidden bg-red-500 focus:ring-red-400"
                                            role="switch"
                                            title="Toggle this slot"
                                        >
                                            <span class="sr-only">Toggle slot availability</span>

                                            <span
                                                x-bind:class="{'translate-x-5': slot.is_opened, 'translate-x-0': !slot.is_opened}"
                                                class="pointer-events-none relative inline-block size-5 transform rounded-full bg-white ring-0 shadow-xs transition duration-200 ease-in-out"
                                            >
                                                <span
                                                    class="absolute inset-0 flex size-full items-center justify-center transition-opacity duration-200 ease-out"
                                                    x-bind:aria-hidden="slot.is_opened"
                                                >
                                                    <svg x-bind:class="{'text-green-600': slot.is_opened, 'text-red-600': !slot.is_opened }" class="size-3" fill="currentColor" viewBox="0 0 12 12">
                                                        <path x-bind:class="{'hidden': !slot.is_opened, 'inline-flex': slot.is_opened }" d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z" />

                                                        <path x-bind:class="{'hidden': slot.is_opened, 'inline-flex': !slot.is_opened }" d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <div
                                x-cloak
                                x-show="isMeetingDateChosen"
                                class="mx-auto py-3 col-span-1 lg:col-span-2"
                            >
                                <x-secondary-button wire:click="reserve_slot_modal" class="justify-self-start sm:justify-self-end">
                                    <span wire:loading.flex wire:target="reserve_slot_modal">
                                        <x-loading-indicator
                                            :loader_color_bg="'fill-gray-800 dark:fill-gray-200'"
                                            :loader_color_spin="'fill-gray-800 dark:fill-gray-200'"
                                            :show_text="false"
                                            :size="4"
                                        />
                                    </span>

                                    <span x-cloak class="ms-2">Update slots for this date</span>
                                </x-secondary-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal :max_width="'xl'" :toggle_show_on_click="false" wire:model="show_update_slots_confirmation_modal">
        <x-slot name="title">
            <div class="flex justify-between items-center border-b-2 border-b-gray-200">
                <h3 class="text-2xl text-gray-800 dark:text-gray-200">Update Meeting Availabilities</h3>

                <button wire:click="$toggle('show_update_slots_confirmation_modal')" class="text-xl p-2 text-gray-800 dark:text-gray-200 hover:cursor-pointer">&times;</button>
            </div>
        </x-slot>

        <x-slot name="content">
            <p>Are you sure you want to update your availabilities? Select Confirm to finalize your availabilities.</p>
        </x-slot>

        <x-slot name="footer">
            <x-button x-on:click="reserveSlots()" bg_colors="bg-green-200 dark:bg-green-600" text_colors="text-gray-800 dark:text-gray-200 mr-2">Confirm</x-button>

            <x-secondary-button wire:click="$toggle('show_update_slots_confirmation_modal')">Cancel</x-secondary-button>
        </x-slot>
    </x-confirmation-modal>
</div>
