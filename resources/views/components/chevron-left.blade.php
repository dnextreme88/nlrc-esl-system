@props([
    'accessibility_title' => 'Chevron left icon',
    'text' => null,
    'text_classes' => null,
])

<span class="flex items-center gap-2 {{ $text ? 'justify-center md:justify-start' : '' }}">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-label="{{ $accessibility_title }}" {{ $attributes->merge(['class' => 'text-gray-800 dark:text-gray-200']) }}>
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
        <title>{{ $accessibility_title }}</title>
    </svg>

    @if ($text)
        <span class="font-semibold px-3 {{ $text_classes }}">{{ $text }}</span>
    @endif
</span>
