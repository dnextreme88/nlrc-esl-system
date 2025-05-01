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
        <h3 class="pb-3 text-3xl text-gray-800 dark:text-gray-200">{{ $current_unit->name }}</h3>

        <x-markdown-parser class="mt-4 indent-2 text-gray-800 dark:text-gray-200">
            {{ $current_unit->description }}
        </x-markdown-parser>

        @if ($current_unit->unit_attachments->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-[180px_1fr]">
                <h4 class="text-2xl text-gray-800 dark:text-gray-200 py-4 md:px-2 md:py-6">Resources</h4>

                <div class="border-2 border-gray-300 dark:border-gray-600 space-y-6 md:space-y-3 p-3 md:p-6">
                    @foreach ($current_unit->unit_attachments as $attachment)
                        <a class="group" href="{{ asset('storage/' .$attachment->file_path) }}" download>
                            <svg class="me-3 inline-block size-5 transition duration-150 text-green-600 dark:text-green-400 group-hover:text-green-400 group-hover:dark:text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-label="Download resource">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                <title>Download resource</title>
                            </svg>

                            <span class="text-gray-800 dark:text-gray-200 transition duration-150 group-hover:text-green-400 group-hover:dark:text-green-600">{{ $attachment->file_name. $attachment->file_type }}</span>
                        </a>

                        <p class="mt-2 mb-4 text-sm text-gray-600 dark:text-gray-400">{{ $attachment->description }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <h3 class="pb-3 text-3xl text-gray-800 dark:text-gray-200">Assessments</h3>

        <div class="indent-2 {{ $assessments->count() > 0 ? 'border-2 border-gray-300 dark:border-gray-600 space-y-6 md:space-y-3 py-3 px-4 md:py-5' : '' }}">
            @forelse ($assessments as $units_assessment)
                <a
                    wire:navigate
                    href="{{ route('assessments.detail', [
                        'id' => $units_assessment->assessment->id,
                        'slug' => $units_assessment->assessment->slug,
                        'unit_id' => $current_unit->id
                    ]) }}"
                    class="flex justify-between group flex-col md:flex-row md:items-center"
                >
                    <div class="indent-2">
                        <span class="text-green-600 dark:text-green-300 mr-2">&rarr;</span>
                        <span class="transition duration-150 text-gray-800 dark:text-gray-200 group-hover:text-green-600 group-hover:dark:text-green-300">
                            {{ $units_assessment->assessment->title }}
                        </span>

                        <div class="relative text-sm mt-1 pb-3 text-gray-600 dark:text-gray-400">
                            <span class="block md:hidden"> {{-- Always visible on small screens --}}
                                {{ $units_assessment->assessment->description }}
                            </span>

                            <span class="absolute opacity-0 translate-y-2 transition-all duration-300 ease-in-out hidden md:block group-hover:opacity-100 group-hover:translate-y-0"> {{-- Hidden & animated on md+ screens --}}
                                {{ $units_assessment->assessment->description }}
                            </span>
                        </div>
                    </div>

                    <x-badge :text="$units_assessment->assessment->type" class="self-start max-w-fit bg-green-200 dark:bg-green-400/10 text-green-800 dark:text-green-300 ring-green-600/40 dark:ring-green-400/60" />
                </a>
            @empty
                <p class="text-gray-600 dark:text-gray-400 md:indent-2">This unit has no active assessments.</p>
            @endforelse
        </div>

        <div class="border-t border-green-800 dark:border-green-200 mt-4 pt-2 flex flex-row justify-between items-center">
            <a wire:navigate class="text-gray-800 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-300" href="{{ route('modules.detail', ['id' => $module_id, 'slug' => $module_slug]) }}">&larr; Back</a>
        </div>
    </div>
</div>
