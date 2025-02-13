@props(['disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'block min-h-[100px] w-full border-green-300 dark:border-green-700 bg-gray-200 dark:bg-gray-300 text-gray-800 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-500 rounded-md shadow-sm disabled:bg-green-100 disabled:text-gray-500 dark:disabled:bg-green-700 dark:disabled:text-gray-400 placeholder:italic placeholder:text-gray-400 dark:placeholder:text-gray-500']) !!}>
    {{ $slot }}
</textarea>
