<div {{ $attributes->merge(['class' => 'p-2']) }}>
    @if (isset($title) || isset($description))
        <x-section-title>
            @if (isset($title))
                <x-slot name="title">{{ $title }}</x-slot>
            @endif

            @if (isset($description))
                <x-slot name="description">{{ $description }}</x-slot>
            @endif
        </x-section-title>
    @endif

    <div class="mt-4 md:mt-6">
        <div class="px-4 py-6 sm:p-6 bg-gray-200 dark:bg-gray-800 shadow-lg sm:rounded-lg lg:shadow-none lg:border-2 lg:border-gray-300 dark:lg:border-gray-600">
            {{ $content }}
        </div>
    </div>
</div>
