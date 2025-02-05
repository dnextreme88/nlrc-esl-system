@props([
    'is_required' => false,
    'value',
])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700 dark:text-gray-300']) }}>
    {{ $value ?? $slot }}

    @if ($is_required)
        <x-red-asterisk />
    @endif
</label>
