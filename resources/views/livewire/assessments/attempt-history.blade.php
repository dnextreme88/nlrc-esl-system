<div>
    <h3 class="text-3xl text-gray-800 dark:text-gray-200">Attempt History</h3>

    <div class="grid grid-cols-1 gap-2 md:gap-4 *:mx-2 *:my-4">
        @forelse ($student_assessments as $assessment)
            @php
                switch ($assessment['status']) {
                    case 'Failed':
                        $color_classes = 'bg-red-200 dark:bg-red-400/10 text-red-800 dark:text-red-300 ring-red-600/40 dark:ring-red-400/60';

                        break;
                    case 'Passed':
                        $color_classes = 'bg-green-200 dark:bg-green-400/10 text-green-800 dark:text-green-300 ring-green-600/40 dark:ring-green-400/60';

                        break;
                }
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-[1fr_180px]">
                {{-- TODO: REFACTOR LINK TO VIEW STUDENT ANSWERS --}}
                {{-- <a wire:navigate href="{{ route('meetings.detail', ['meeting_uuid' => $meeting['meeting_uuid']]) }}"> --}}
                <a wire:navigate href="#">
                    <x-bold-text-with-subtext
                        :text_in_bold="Helpers::parse_time_to_user_timezone($assessment->created_at)->format('M j, Y g:i A')"
                        :subtext="$assessment['score']. '%'"
                        class="transition duration-150 hover:text-green-600 dark:hover:text-green-300"
                    />
                </a>

                <x-badge :text="$assessment['status']" class="justify-self-start md:justify-self-end max-w-fit {{ $color_classes }}" />
            </div>
        @empty
            <p class="p-2 text-gray-800 dark:text-gray-200">You currently have not taken this assessment.</p>
        @endforelse
    </div>
</div>
