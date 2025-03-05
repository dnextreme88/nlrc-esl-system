<div>
    <h3 class="text-3xl text-gray-800 dark:text-gray-200">Recent Meetings</h3>

    <div class="grid grid-cols-1 gap-2 md:gap-4 [&>*]:mx-2 [&>*]:my-4">
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

            <div class="grid grid-cols-1 md:grid-cols-2">
                <x-meetings.date-with-time-section
                    :classes_container="'opacity-50 dark:opacity-25'"
                    :classes_date="'text-base'"
                    :end_time="$meeting->end_time"
                    :start_time="$meeting->start_time"
                />

                <div class="opacity-50 dark:opacity-25 place-self-end">
                    <span class="block self-center text-center rounded-full px-4 py-2 text-xs my-2 font-medium ring-1 ring-inset min-w-[100px] max-w-[100px] {{ $color_classes }}">{{ $meeting->status }}</span>
                </div>
            </div>
        @empty
            <p class="text-gray-800 dark:text-gray-200 italic">You currently have no recent meetings.</p>
        @endforelse
    </div>
</div>
