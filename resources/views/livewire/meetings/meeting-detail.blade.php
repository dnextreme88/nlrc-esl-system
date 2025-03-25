<div>
    <x-slot name="nav_menu">
        <livewire:NavMenu />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Meeting Details
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 gap-8 mx-auto space-y-6 max-w-7xl px-4 py-12 sm:px-6 lg:px-8 {{ $current_meeting_slot->status == \App\Enums\MeetingStatuses::PENDING->value ? 'lg:space-y-0 lg:grid-cols-[1fr_minmax(10%,_30%)]' : '' }}">
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
                                                    :title_text="$student['student']['last_name']. ', ' .$student['student']['first_name']. ' has booked this slot'"
                                                    :src="$student['student']['profile_photo_url']"
                                                />

                                                <span class="text-gray-800 dark:text-gray-200">booked on {{ Helpers::parse_time_to_user_timezone($student['created_at'])->format('F j, Y g:i A') }}</span>
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

                {{-- TODO: TO REFACTOR TO USE ACTUAL MEETING LINK FROM DB (TO BE IMPLEMENTED AS WELL) --}}
                <p class="break-words text-gray-800 dark:text-gray-200">Join: <a href="https://meet.google.com/usg-ysnc-zks" class="text-green-600 dark:text-green-300 hover:underline" target="_blank">https://meet.google.com/usg-ysnc-zks</a></p>

                <p class="text-gray-800 dark:text-gray-200">Timezone: <strong>{{ Auth::user()->timezone }}</strong>. If this is not correct, please go to your <a wire:navigate class="text-green-600 dark:text-green-300 hover:underline" href="{{ route('settings.time') }}">settings and change it there</a>.</p>

                {{-- TODO: USE ryangjchandler/alpine-clipboard PACKAGE TO ALLOW COPYING TO CLIPBOARD --}}
                <p class="text-gray-800 dark:text-gray-200">Meeting ID: <span class="font-bold">{{ $current_meeting_slot['meeting_uuid'] }}</span></p>
            </div>
        @endif
    </div>
</div>
