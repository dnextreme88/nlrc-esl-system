<div class="p-6 bg-white shadow-lg rounded-lg shadow-gray-300 dark:shadow-gray-800">
    <div class="flex justify-between items-center mb-4">
        @if ($show_previous)
            <button wire:click="previous_month" class="px-2 py-1 bg-gray-200 rounded hover:cursor-pointer">&larr;</button>
        @else
            <div></div>
        @endif

        <h2 class="text-lg font-semibold">{{ $calendar_start->format('F Y') }}</h2>

        @if ($show_next)
            <button wire:click="next_month" class="px-2 py-1 bg-gray-200 rounded hover:cursor-pointer">&rarr;</button>
        @else
            <div></div>
        @endif
    </div>

    <div class="grid grid-cols-7 text-center">
        @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
            <div class="py-1 bg-green-100 dark:bg-green-300 text-gray-800 font-semibold">{{ $day }}</div>
        @endforeach

        {{-- Show previous month's days --}}
        @foreach ($prev_days as $prev_day)
            <div class="border p-2 text-gray-400 text-sm hover:cursor-not-allowed">{{ $prev_day }}</div>
        @endforeach

        {{-- Show current month's days --}}
        @foreach ($current_month_days as $month_day)
            <div
                @if ($month_day['date_in_range'] && $month_day['has_slots'])
                    wire:click="show_times('{{ $month_day['parsed_date'] }}')"
                    title="View slots for this date"
                    aria-label="View slots for this date"
                @endif

                class="p-2 text-sm text-center
                    @if (array_key_exists('is_today', $month_day))
                        border border-gray-400 bg-yellow-200 dark:bg-yellow-300 font-semibold
                    @else
                        @if ($month_day['date_in_range'])
                            border border-gray-400

                            @if ($month_day['has_slots'])
                                bg-green-300 dark:bg-green-400/75 font-semibold hover:bg-green-200 dark:hover:bg-green-300 hover:cursor-pointer transition duration-300
                            @endif
                        @else
                            bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-100 hover:cursor-not-allowed
                        @endif
                    @endif
                "
            >
                {{ $month_day['day'] }}
            </div>
        @endforeach
    </div>
</div>
