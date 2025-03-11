<div>
    <h3 class="text-3xl text-gray-800 dark:text-gray-200">Upcoming Meetings</h3>

    <div class="grid grid-cols-1 gap-2 md:gap-4 *:mx-2 *:my-4">
        @forelse ($meetings as $meeting)
            @php
                $color_classes;
                $is_teacher_role = in_array(Auth::user()->role->name, [\App\Enums\Roles::HEAD_TEACHER->value, \App\Enums\Roles::TEACHER->value]);

                switch ($meeting->status) {
                    case \App\Enums\MeetingStatuses::CANCELLED->value:
                        $color_classes = 'bg-red-200 text-red-700 ring-red-600';

                        break;
                    case \App\Enums\MeetingStatuses::PENDING->value:
                        $color_classes = 'bg-yellow-200 text-yellow-700 ring-yellow-600';

                        break;
                }
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 {{ $is_teacher_role ? 'lg:grid-cols-2' : 'sm:grid-cols-2' }}">
                <div class="col-span-1 md:col-span-2 {{ $is_teacher_role ? 'lg:col-span-1' : '' }}">
                    @if ($is_teacher_role)
                        <span class="block self-center text-center rounded-full px-4 py-2 text-xs my-2 font-medium ring-1 ring-inset min-w-[100px] max-w-[100px] {{ $color_classes }}">{{ $meeting->status }}</span>
                    @endif

                    <x-meetings.date-with-time-section
                        :classes_date="'text-2xl font-semibold'"
                        :end_time="$meeting->end_time"
                        :start_time="$meeting->start_time"
                    />
                </div>

                <div class="grid grid-cols-1 gap-2 p-4 items-center {{ $is_teacher_role ? 'border-b-2 border-gray-600 md:border-b-0 md:p-0 md:grid-cols-2 lg:gap-4 lg:self-end lg:mb-4' : 'py-0 px-2 sm:self-center sm:place-self-end' }}">
                    @if ($is_teacher_role)
                        <button wire:click="cancel_meeting_modal({{ $meeting->id }})" class="transition duration-150 rounded-md py-2 px-4 text-gray-800 dark:text-gray-200 bg-red-300 dark:bg-red-600 hover:bg-red-400 dark:hover:bg-red-700 hover:cursor-pointer {{ $meeting->status == \App\Enums\MeetingStatuses::CANCELLED->value ? 'hidden md:block md:invisible' : '' }}">Cancel</button>

                        <button wire:click="reschedule_meeting_modal({{ $meeting->id }})" class="transition duration-150 rounded-md py-2 px-2 text-gray-800 dark:text-gray-200 bg-blue-300 dark:bg-blue-600 hover:bg-blue-400 dark:hover:bg-blue-700 hover:cursor-pointer">Reschedule</button>
                    @else
                        <span class="block self-center text-center rounded-full px-4 py-2 text-xs font-medium ring-1 ring-inset min-w-[100px] max-w-[100px] {{ $color_classes }}">{{ $meeting->status }}</span>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-gray-800 dark:text-gray-200 italic">You currently have no upcoming meetings.</p>
        @endforelse
    </div>

    @if (in_array(Auth::user()->role->name, [\App\Enums\Roles::HEAD_TEACHER->value, \App\Enums\Roles::STUDENT->value, \App\Enums\Roles::TEACHER->value]))
        <x-modal wire:model="show_cancel_meeting_modal" :maxWidth="'xl'">
            <div class="my-4 mx-6">
                <div class="flex justify-between items-center border-b-2 border-b-gray-200">
                    <h3 class="text-2xl text-gray-800 dark:text-gray-200">Cancel Meeting Form</h3>

                    <button wire:click="$toggle('show_cancel_meeting_modal')" class="text-xl p-4 text-gray-800 dark:text-gray-200 hover:cursor-pointer">&times;</button>
                </div>

                <p class="my-6 text-gray-700 dark:text-gray-400"><strong>Note:</strong> Cancelling a meeting will incur a penalty.</p>

                <p class="mb-8 text-gray-700 dark:text-gray-400">Please provide us with more information by filling out the details below.</p>
            </div>

            <form wire:submit.prevent="cancel_meeting" class="my-4 mx-6">
                <input wire:model="meeting_slot_id" type="hidden" />

                <x-label is_required="true" value="{{ __('Reason') }}" for="cancel_reason" />

                <x-textarea wire:model="cancel_reason" class="placeholder-gray-700 dark:placeholder-gray-400" id="cancel_reason" placeholder="Please state your reason here" />

                @error ('cancel_reason')
                    <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
                @enderror

                <x-button class="my-4 hover:cursor-pointer">
                    <span wire:loading.flex wire:target="cancel_meeting" class="items-center">
                        <x-loading-indicator
                            :loader_color_bg="'fill-gray-200 dark:fill-gray-800'"
                            :loader_color_spin="'fill-gray-200 dark:fill-gray-800'"
                            :show_text="false"
                            :size="4"
                        />

                        <span class="ml-2">Submitting</span>
                    </span>

                    <span wire:loading.remove wire:target="cancel_meeting">Submit</span>
                </x-button>
            </form>
        </x-modal>

        <x-modal wire:model="show_reschedule_meeting_modal" :maxWidth="'xl'">
            <div class="my-4 mx-6">
                <div class="flex justify-between items-center border-b-2 border-b-gray-200">
                    <h3 class="text-2xl text-gray-800 dark:text-gray-200">Reschedule Meeting Form</h3>

                    <button wire:click="$toggle('show_reschedule_meeting_modal')" class="text-xl p-4 text-gray-800 dark:text-gray-200 hover:cursor-pointer">&times;</button>
                </div>

                <p class="my-6 text-gray-700 dark:text-gray-400"><strong>Note:</strong> Reschedule this meeting at a later date. Every meeting lasts for 30 minutes.</p>

                <p class="mb-8 text-gray-700 dark:text-gray-400">Please provide us with more information by filling out the details below.</p>
            </div>

            <form wire:submit.prevent="reschedule_meeting" class="my-4 mx-6">
                <input wire:model="meeting_slot_id" type="hidden" />

                <x-label is_required="true" value="{{ __('New Meeting Date') }}" class="my-4" for="reschedule_new_date" />

                <x-input wire:model="reschedule_new_date" class="mt-1 block w-full" type="date" id="reschedule_new_date" autocomplete="reschedule_new_date" />

                @error ('reschedule_new_date')
                    <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
                @enderror

                <x-label is_required="true" value="{{ __('New Start Time') }}" class="my-4" for="reschedule_new_start_time" />

                <x-select wire:model.live="reschedule_new_start_time" :inline_block="false" class="w-full" id="reschedule_new_start_time">
                    <option value="">Select start time</option>
                    @foreach ($start_times as $start_time)
                        <option value="{{ $start_time['start_time'] }}">{{ $start_time['start_time'] }}</option>
                    @endforeach
                </x-select>

                @error ('reschedule_new_start_time')
                    <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
                @enderror

                <x-label is_required="true" value="{{ __('Reason') }}" class="my-4" for="reschedule_reason" />

                <x-textarea wire:model="reschedule_reason" class="placeholder-gray-700 dark:placeholder-gray-400" id="reschedule_reason" placeholder="Please state your reason here" />

                @error ('reschedule_reason')
                    <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
                @enderror

                <x-button class="my-4 hover:cursor-pointer">
                    <span wire:loading.flex wire:target="reschedule_meeting" class="items-center">
                        <x-loading-indicator
                            :loader_color_bg="'fill-gray-200 dark:fill-gray-800'"
                            :loader_color_spin="'fill-gray-200 dark:fill-gray-800'"
                            :show_text="false"
                            :size="4"
                        />

                        <span class="ml-2">Submitting</span>
                    </span>

                    <span wire:loading.remove wire:target="reschedule_meeting">Submit</span>
                </x-button>
            </form>
        </x-modal>
    @endif
</div>
