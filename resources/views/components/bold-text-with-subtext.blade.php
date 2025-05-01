@props([
    'text_in_bold',
    'subtext',
    'subtext_classes' => null,
])

<h3 {{ $attributes->merge(['class' => 'text-gray-800 dark:text-gray-200 text-2xl font-semibold']) }}>{{ $text_in_bold }}</h3>

<span class="text-sm text-gray-600 dark:text-gray-400 {{ $subtext_classes }}">{{ $subtext }}</span>
