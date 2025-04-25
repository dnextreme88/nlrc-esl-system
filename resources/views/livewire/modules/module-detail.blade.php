<div>
    <x-slot name="nav_menu">
        <livewire:NavMenu />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __($current_module->name) }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
        <div class="max-w-none text-gray-800 dark:text-gray-200 prose dark:prose-invert">{!! Markdown::parse($current_module->description) !!}</div>

        <div class="mt-10 border-2 border-gray-300 dark:border-gray-600 shadow-sm shadow-gray-600">
            @if ($current_module->units->isNotEmpty())
                <h3 class="px-4 py-2 text-2xl bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200">Units</h3>

                <dl class="px-4 divide-y divide-gray-600 py-6 space-y-4">
                    @foreach ($current_module->units as $index => $unit)
                        <div x-data="{ isOpened: false }">
                            <dt class="my-4">
                                <button
                                    x-on:click="isOpened = !isOpened;"
                                    class="flex w-full items-start justify-between text-gray-800 dark:text-gray-200 hover:cursor-pointer group"
                                    aria-controls="faq-{{ $index }}"
                                    aria-expanded="false"
                                    type="button"
                                >
                                    <span class="text-xl font-semibold transition duration-150 group-hover:scale-105 group-hover:text-green-600 group-hover:dark:text-green-300">{{ $unit->name }}</span>
                                    <span class="ml-6 flex h-7 items-center">
                                        <svg
                                            x-bind:class="{ 'rotate-180': isOpened, 'rotate-90': !isOpened }"
                                            class="size-6 transition duration-300 ease-in-out group-hover:scale-105 group-hover:text-green-600 group-hover:dark:text-green-300"
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
                            </dt>

                            <dd
                                x-show="isOpened"
                                x-transition:enter="ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform -translate-y-2"
                                x-transition:enter-end="opacity-100 transform translate-y-0"
                                x-transition:leave="ease-in duration-300"
                                x-transition:leave-end="opacity-0 transform -translate-y-4"
                                class="transition-all indent-2 mt-4 mb-6 pr-10"
                                id="faq-{{ $index }}"
                            >
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $unit->description }}</p>

                                <a
                                    wire:navigate
                                    href="{{ route('modules.unit_detail', [
                                        'id' => $current_module->id,
                                        'slug' => $current_module->slug,
                                        'unit_id' => $unit->id,
                                        'unit_slug' => $unit->slug,
                                    ]) }}"
                                    class="block mt-6 text-green-600 dark:text-green-300"
                                >
                                    Take me to this unit &rarr;
                                </a>
                            </dd>
                        </div>
                    @endforeach
                </dl>
            @else
                <p class="p-2 text-red-900 dark:text-red-300">No units found for this module.</p>
            @endif
        </div>
    </div>
</div>
