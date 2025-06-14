<div>
    <x-slot name="nav_menu">
        <livewire:NavMenu />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Meeting Details
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 gap-8 mx-auto space-y-6 max-w-7xl px-4 py-12 sm:px-6 lg:px-8 {{ $current_meeting_slot->status == \App\Enums\MeetingStatuses::PENDING->value ? 'lg:space-y-0 lg:grid-cols-[1fr_minmax(10%,_36%)]' : '' }}">
        <div class="w-full py-4 mt-4">
            <h3 class="text-2xl text-center font-semibold mb-6 text-gray-800 dark:text-gray-200">Tracker</h1>

            <div class="flex flex-col md:grid grid-cols-[100px_1fr] text-gray-50">
                @foreach ($meeting_updates as $key => $meeting_update)
                    @php
                        $is_last_element = $key === array_key_last($meeting_updates);
                    @endphp

                    <div class="flex gap-6 md:contents">
                        <div class="relative md:mx-auto">
                            <div class="h-full w-6 flex items-center justify-center">
                                <div class="h-full w-1 pointer-events-none {{ $is_last_element ? 'bg-green-600 dark:bg-green-300' : 'bg-gray-600 dark:bg-gray-300 opacity-25' }}"></div>
                            </div>

                            <div class="size-6 absolute top-1/2 -mt-2 rounded-full
                                @if ($is_last_element)
                                    bg-green-600 dark:bg-green-300

                                    @if ($current_meeting_slot->status == \App\Enums\MeetingStatuses::PENDING->value)
                                        animate-pulse
                                    @endif
                                @else
                                    bg-gray-600 dark:bg-gray-300 opacity-25
                                @endif
                                "
                            >
                            </div>
                        </div>

                        <div class="p-4 rounded-xl my-4 mr-auto shadow-md w-full {{ $is_last_element ? 'bg-green-200 dark:bg-green-800' : 'bg-blue-200 dark:bg-blue-800' }}">
                            <h3 class="font-semibold text-lg mb-1 text-gray-800 dark:text-gray-200">{{ $meeting_update['headline'] }}</h3>

                            <p class="leading-tight text-sm text-gray-600 dark:text-gray-400">
                                @if ($meeting_update['order'] == 2)
                                    @if (in_array(Auth::user()->role->name, [\App\Enums\Roles::HEAD_TEACHER->value, \App\Enums\Roles::TEACHER->value]))
                                        @foreach ($meeting_update['sub_text'] as $student)
                                            <div class="flex space-y-2 gap-3">
                                                <x-round-image
                                                    :alt_text="$student['student']['last_name']. ', ' .$student['student']['first_name']"
                                                    :src="$student['student']['profile_photo_url']"
                                                    :title_text="$student['student']['last_name']. ', ' .$student['student']['first_name']. ' has booked this slot'"
                                                />

                                                <span class="self-center text-sm text-gray-800 dark:text-gray-200">booked on {{ Helpers::parse_time_to_user_timezone($student['created_at'])->format('F j, Y g:i A') }}</span>
                                            </div>
                                        @endforeach
                                    @else
                                        {{ $meeting_update['sub_text'] }}
                                    @endif
                                @else
                                    {{ $meeting_update['sub_text'] }}
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if ($current_meeting_slot->status == \App\Enums\MeetingStatuses::PENDING->value)
            <div class="p-4 mt-10 flex flex-col space-y-6 border-2 border-gray-300 dark:border-gray-600 shadow-md shadow-gray-500">
                <h3 class="flex justify-center gap-2 mb-12">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="fill-green-300 size-4" aria-label="Calendar icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        <title>Calendar icon</title>
                    </svg>

                    <span class="text-2xl font-semibold text-gray-800 dark:text-gray-200">{{ Helpers::parse_time_to_user_timezone($current_meeting_slot['start_time'])->format('F j, Y') }}</span>
                </h3>

                <p class="text-gray-800 dark:text-gray-200">Time: <span class="font-bold">{{ Helpers::parse_time_to_user_timezone($current_meeting_slot['start_time'])->format('g:i A') }} ~ {{ Helpers::parse_time_to_user_timezone($current_meeting_slot['end_time'])->format('g:i A') }}</span></p>

                @if ($current_meeting_slot['meeting_link'])
                    <p class="break-words text-gray-800 dark:text-gray-200">
                        <span>Join: <a href="{{ $current_meeting_slot['meeting_link'] }}" class="text-green-600 dark:text-green-300 hover:underline" target="_blank">{{ $current_meeting_slot['meeting_link'] }}</a></span>

                        <sup x-on:click="$dispatch('copied-link-to-clipboard'); $clipboard('{{ $current_meeting_slot['meeting_link'] }}');" class="p-1 inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 transition duration-150 fill-green-300 hover:cursor-pointer" aria-label="Copy to clipboard">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                                <title>Copy to clipboard</title>
                            </svg>
                        </sup>
                    </p>
                @endif

                <p class="text-gray-800 dark:text-gray-200">
                    <span class="block">Timezone: <span class="font-bold">{{ Auth::user()->timezone }}</span></span>

                    <small class="text-gray-600 dark:text-gray-400">If this is not correct, please go to your <a wire:navigate class="text-green-600 dark:text-green-300 hover:underline" href="{{ route('settings.time') }}">settings and change it there</a>.</small>
                </p>

                <p class="text-gray-800 dark:text-gray-200">Meeting ID: <span class="font-bold">{{ $current_meeting_slot['meeting_uuid'] }}</span></p>
            </div>

            @if (in_array(Auth::user()->role->name, [\App\Enums\Roles::HEAD_TEACHER->value, \App\Enums\Roles::TEACHER->value]))
                <div class="p-4 mt-10 flex flex-col space-y-6 border-2 border-gray-300 dark:border-gray-600 lg:col-span-2">
                    <x-form-section submit="update_meeting_details">
                        <x-slot name="title">
                            {{ __('Update your meeting details') }}
                        </x-slot>

                        <x-slot name="description">
                            {{ __('Set a meeting link that you and your students will use.') }}
                        </x-slot>

                        <x-slot name="form">
                            <div class="col-span-6">
                                <x-label is_required="true" value="Meeting Link" for="meeting_link" />

                                <x-input wire:model="meeting_link" class="mt-1 block w-full" type="text" id="meeting_link" />

                                <small class="text-gray-600 dark:text-gray-400">You may place any Google Meet or Zoom links</small>

                                <x-input-error class="mt-2" for="meeting_link" />
                            </div>
                        </x-slot>

                        <x-slot name="actions">
                            <x-button wire.loading.attr="disabled" class="my-4 hover:cursor-pointer">
                                <span wire:loading.flex wire:target="update_meeting_details" class="items-center">
                                    <x-loading-indicator
                                        :loader_color_bg="'fill-gray-200 dark:fill-gray-800'"
                                        :loader_color_spin="'fill-gray-200 dark:fill-gray-800'"
                                        :show_text="true"
                                        :text="'Saving'"
                                        :text_color="'text-gray-200 dark:text-gray-800'"
                                        :size="4"
                                    />
                                </span>

                                <span wire:loading.remove wire:target="update_meeting_details">Save</span>
                            </x-button>
                        </x-slot>
                    </x-form-section>
                </div>
            @endif
        @endif
    </div>
</div>
