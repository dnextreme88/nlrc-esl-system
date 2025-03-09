@props(['disabled' => false, 'inline_block' => true])

<div class="relative {{ $inline_block ? ' inline-block' : ''}}">
    <select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-xs appearance-none disabled:bg-gray-100 dark:disabled:bg-gray-700 disabled:text-gray-400 dark:disabled:text-gray-500']) !!}>
        {{ $slot }}
    </select>
</div>
