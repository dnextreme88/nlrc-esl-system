<div>
    <x-slot name="nav_menu">
        <livewire:NavMenu />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Unit: {{ __($current_unit->name) }}
        </h2>
    </x-slot>

    <div class="grid space-y-6 max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
        <a
            wire:navigate
            href="{{ route('modules.detail', ['id' => $module_id, 'slug' => $module_slug]) }}"
            class="text-green-600 dark:text-green-300"
        >
            &larr; Back to units page
        </a>

        <h3 class="text-3xl pb-4 border-b-2 border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $current_unit->name }}</h3>

        <div class="text-base text-gray-600 dark:text-gray-400 md:indent-2">{!! Markdown::parse($current_unit->description) !!}</div>

        @if ($current_unit->unit_attachments->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-[180px_1fr]">
                <h4 class="text-2xl text-gray-800 dark:text-gray-200 py-4 md:px-2 md:py-6">Resources</h4>

                <div class="border-2 border-gray-300 dark:border-gray-600 space-y-6 md:space-y-3 p-3 md:p-6">
                    @foreach ($current_unit->unit_attachments as $attachment)
                        <div class="flex gap-2 group">
                            <svg class="size-5 transition duration-150 text-green-600 dark:text-green-400 group-hover:text-green-400 group-hover:dark:text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-label="Download resource">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                <title>Download resource</title>
                            </svg>

                            <a class="text-gray-800 dark:text-gray-200 transition duration-150 group-hover:text-green-400 group-hover:dark:text-green-600" href="{{ asset('storage/' .$attachment->file_path) }}" download>{{ $attachment->file_name }}</a>
                        </div>

                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $attachment->description }}</p>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
