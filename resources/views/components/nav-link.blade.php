@props(['active'])

@php
$extra_classes = ($active ?? false)
    ? 'border-green-400 dark:border-green-600 focus:border-green-700'
    : 'border-transparent hover:text-green-600 dark:hover:text-green-300 hover:border-green-400 dark:hover:border-green-500 focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700';
@endphp

<a {{ $attributes->merge(['class' => 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 text-gray-900 dark:text-gray-100 transition duration-150 ease-in-out ' .$extra_classes]) }}>
    {{ $slot }}
</a>
