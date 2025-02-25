@props([
    'is_read',
    'tooltip' => 'Unread notification'
])

@if (!$is_read)
    <sup>
        <div class="inline-block h-3 w-3 scale-100 bg-red-400 rounded-full shadow-md animate-[pulse_3s_infinite]" title="{{ $tooltip }}"></div>
    </sup>
@endif
