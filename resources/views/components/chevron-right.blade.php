@props([
    'accessibility_title' => 'Chevron right icon',
    'text' => null,
    'text_classes' => null,
])

<span class="flex items-center gap-2 {{ $text ? 'justify-end' : '' }}">
    @if ($text)
        <span class="font-semibold px-3 {{ $text_classes }}">{{ $text }}</span>
    @endif

    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-label="{{ $accessibility_title }}" {{ $attributes->merge(['class' => 'text-gray-800 dark:text-gray-200']) }}>
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        <title>{{ $accessibility_title }}</title>
    </svg>
</span>
