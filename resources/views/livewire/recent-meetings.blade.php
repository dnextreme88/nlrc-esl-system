<div>
    <h3 class="text-3xl text-gray-800 dark:text-gray-200">Recent Meetings</h3>

    <div class="grid grid-cols-1 gap-2 md:gap-4 *:my-4">
        @forelse ($meetings as $meeting)
            @php
                $color_classes;
                $is_teacher_role = in_array(Auth::user()->role->name, [\App\Enums\Roles::HEAD_TEACHER->value, \App\Enums\Roles::TEACHER->value]);

                switch ($meeting->status) {
                    case \App\Enums\MeetingStatuses::CANCELLED->value:
                        $color_classes = 'bg-red-200 text-red-700 ring-red-600';

                        break;
                    case \App\Enums\MeetingStatuses::COMPLETED->value:
                        $color_classes = 'bg-green-200 text-green-700 ring-green-600';

                        break;
                    case \App\Enums\MeetingStatuses::NO_SHOW->value:
                        $color_classes = 'bg-gray-200 text-gray-700 ring-gray-600';

                        break;
                }
            @endphp

            <div class="mx-2 grid grid-cols-1 md:grid-cols-[1fr_180px]">
                <a wire:navigate href="{{ route('meetings.detail', ['meeting_uuid' => $meeting['meeting_uuid']]) }}">
                    <x-bold-text-with-subtext
                        :text_in_bold="Helpers::parse_time_to_user_timezone($meeting->start_time)->format('M j, Y')"
                        :subtext="Helpers::parse_time_to_user_timezone($meeting->start_time)->format('g:i A'). ' ~ ' .Helpers::parse_time_to_user_timezone($meeting->end_time)->format('g:i A')"
                        :subtext_classes="'transition duration-150 hover:text-green-600 dark:hover:text-green-300 opacity-50 dark:opacity-25'"
                        class="transition duration-150 text-base hover:text-green-600 dark:hover:text-green-300 opacity-50 dark:opacity-25"
                    />
                </a>

                <x-badge :text="$meeting->status" class="opacity-50 dark:opacity-25 place-self-start md:place-self-end {{ $color_classes }}" />
            </div>
        @empty
            <p class="p-2 text-gray-800 dark:text-gray-200">You currently have no recent meetings.</p>
        @endforelse
    </div>
</div>
