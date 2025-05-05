@props([
    'id',
    'max_width',
    'toggle_show_on_click' => true,
])

@php
    $id = $id ?? md5($attributes->wire('model'));

    $max_width = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
    ][$max_width ?? '2xl'];
@endphp

<div
    x-data="{ show: @entangle($attributes->wire('model')) }"
    @if ($toggle_show_on_click) x-on:close.stop="show = false" @endif
    @if ($toggle_show_on_click) x-on:keydown.escape.window="show = false" @endif
    x-show="show"
    id="{{ $id }}"
    class="jetstream-modal fixed inset-0 -overflow-y-auto px-4 py-6 z-50 bg-gray-200/50 dark:bg-gray-800/50 sm:px-0"
    style="display: none;"
>
    <div
        x-show="show"
        @if ($toggle_show_on_click) x-on:click="show = false" @endif
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-100"
        x-transition:enter-end="opacity-0"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="inset-0 transform transition-all"
    >
        <div class="absolute inset-0 opacity-0 bg-gray-500 dark:bg-gray-900"></div>
    </div>

    <div
        x-show="show"
        {{-- x-trap.inert.noscroll="show" --}}
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-0 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-100 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="mb-6 rounded-lg shadow-xl transform transition-all z-50 relative bg-white dark:bg-gray-800 sm:w-full sm:mx-auto {{ $max_width }}"
    >
        {{ $slot }}
    </div>
</div>
