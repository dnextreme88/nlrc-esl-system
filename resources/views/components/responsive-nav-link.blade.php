@props(['active'])

@php
$extra_classes = ($active ?? false)
    ? 'border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 bg-green-100 dark:bg-green-900 focus:text-green-800 dark:focus:text-green-200 focus:bg-green-100 dark:focus:bg-green-900'
    : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-green-100 dark:hover:bg-green-900 hover:border-green-400 dark:hover:border-green-600 focus:text-green-800 dark:focus:text-green-200 focus:bg-green-100 dark:focus:bg-green-700';
@endphp

<a {{ $attributes->merge(['class' => 'block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium transition duration-150 ease-in-out ' .$extra_classes]) }}>
    {{ $slot }}
</a>
