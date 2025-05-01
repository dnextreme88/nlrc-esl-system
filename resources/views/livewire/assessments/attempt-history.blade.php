<div>
    <h3 class="text-3xl text-gray-800 dark:text-gray-200">Attempt History</h3>

    <div class="grid grid-cols-1 gap-2 md:gap-4 *:my-4">
        @forelse ($student_assessments as $assessment)
            @php
                switch ($assessment['status']) {
                    case 'Failed':
                        $color_classes = 'bg-red-200 text-red-700 ring-red-600';

                        break;
                    case 'Passed':
                        $color_classes = 'bg-green-200 text-green-700 ring-green-600';

                        break;
                }
            @endphp

            <div class="mx-2 grid grid-cols-1 md:grid-cols-[1fr_180px]">
                {{-- TODO: Can just refactor the component it was based on: <x-meetings.date-with-time-section> as they're identical in styles --}}
                {{-- TODO: REFACTOR LINK TO VIEW STUDENT ANSWERS --}}
                {{-- <a wire:navigate href="{{ route('meetings.detail', ['meeting_uuid' => $meeting['meeting_uuid']]) }}"> --}}
                <a wire:navigate href="#">
                    <div>
                        <h4 class="transition duration-150 text-gray-800 dark:text-gray-200 hover:text-green-600 dark:hover:text-green-300 text-2xl font-semibold">{{ Helpers::parse_time_to_user_timezone($assessment->created_at)->format('M j, Y g:i A') }}</h4>

                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $assessment['score'] }}%</span>
                    </div>
                </a>

                <div class="place-self-start md:place-self-end">
                    <span class="block self-center text-center rounded-full px-4 py-2 text-xs my-2 font-medium ring-1 ring-inset min-w-[100px] max-w-[100px] {{ $color_classes }}">{{ $assessment['status'] }}</span>
                </div>
            </div>
        @empty
            <p class="p-2 text-gray-800 dark:text-gray-200">You currently have not taken this assessment.</p>
        @endforelse
    </div>
</div>
