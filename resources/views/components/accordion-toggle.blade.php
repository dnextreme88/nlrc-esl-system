@props([
    'content_classes' => '',
    'header_classes' => '',
    'index' => 0,
    'is_opened' => false,
    'parent_classes' => '',
])

<div x-data="{ isOpened: {{ $is_opened }} }" class="{{ $parent_classes }}">
    <button
        x-on:click="isOpened = !isOpened;"
        class="flex w-full items-center justify-between text-gray-800 dark:text-gray-200 hover:cursor-pointer group {{ $header_classes }}"
        aria-controls="toggle-{{ $index }}"
        aria-expanded="false"
        type="button"
    >
        @if (isset($title))
            <div class="text-xl font-semibold transition duration-150 group-hover:text-green-600 group-hover:dark:text-green-300">{{ $title }}</div>
        @endif

        <span class="flex h-7 items-center">
            <svg
                x-bind:class="{ 'rotate-180': isOpened, 'rotate-90': !isOpened }"
                class="size-6 transition duration-300 ease-in-out group-hover:text-green-600 group-hover:dark:text-green-300"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                aria-hidden="true"
                data-slot="icon"
            >
                {{-- Plus icon --}}
                <path x-bind:class="{'hidden': isOpened, 'inline-flex': !isOpened }" stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                {{-- Minus icon --}}
                <path x-bind:class="{'hidden': !isOpened, 'inline-flex': isOpened }" stroke-linecap="round" stroke-linejoin="round" d="M18 12H6" />
            </svg>
        </span>
    </button>

    <dd
        x-show="isOpened"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="ease-in duration-300"
        x-transition:leave-end="opacity-0 transform -translate-y-4"
        class="transition-all mb-6 {{ $content_classes }}"
        id="toggle-{{ $index }}"
    >
        {{ $content }}
    </dd>
</div>
