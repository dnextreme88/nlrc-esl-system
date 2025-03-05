@props([
    'classes_container' => '',
    'classes_date' => '',
    'classes_time' => '',
    'end_time',
    'start_time',
])

<div class="{{ $classes_container }}">
    {{-- TODO: We can probably add a user setting option to format dates like these --}}
    <h4 class="text-gray-800 dark:text-gray-200 {{ $classes_date }}">{{ \Carbon\Carbon::parse($start_time)->toUserTimezone()->format('M j, Y') }}</h4>

    <span class="text-sm text-gray-600 dark:text-gray-400 {{ $classes_time }}">{{ \Carbon\Carbon::parse($start_time)->toUserTimezone()->format('g:i A') }} ~ {{ \Carbon\Carbon::parse($end_time)->toUserTimezone()->format('g:i A') }}</span>
</div>
