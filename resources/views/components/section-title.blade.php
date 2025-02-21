<div class="md:col-span-1 flex justify-between">
    <div class="px-4 sm:px-0">
        @if (isset($title))
            <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ $title }}</h3>
        @endif

        @if (isset($description))
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $description }}</p>
        @endif
    </div>

    <div class="px-4 sm:px-0">
        {{ $aside ?? '' }}
    </div>
</div>
