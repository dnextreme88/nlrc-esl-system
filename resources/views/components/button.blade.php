@props([
    'bg_colors' => 'bg-gray-800 dark:bg-gray-200 hover:bg-gray-600 dark:hover:bg-gray-400 focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300',
    'text_colors' => 'text-gray-200 dark:text-gray-800',
])

<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-hidden focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150 hover:cursor-pointer ' .$bg_colors. ' ' .$text_colors
]) }}>
    {{ $slot }}
</button>
