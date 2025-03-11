@props(['align' => 'right', 'width' => '48', 'content_classes' => 'py-1 bg-white dark:bg-gray-700', 'dropdown_classes' => ''])

@php
$alignment_classes = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    'none', 'false' => '',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    '60' => 'w-60',
    '64' => 'w-64',
    default => 'w-48',
};
@endphp

<div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 mt-2 rounded-md shadow-lg {{ $width }} {{ $alignment_classes }} {{ $dropdown_classes }}"
        style="display: none;"
        @click="open = false"
    >
        <div class="rounded-md ring-1 ring-gray-300 dark:ring-gray-600 {{ $content_classes }}">
            {{ $content }}
        </div>
    </div>
</div>
