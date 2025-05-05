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
        <x-markdown-parser class="text-gray-800 dark:text-gray-200">
            {{ $current_module->description }}
        </x-markdown-parser>

        <div class="mt-10 border-2 border-gray-300 dark:border-gray-600 shadow-sm shadow-gray-600">
            @if ($current_module->units->isNotEmpty())
                <h3 class="px-4 py-2 text-2xl bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200">Units</h3>

                <dl class="px-4 divide-y divide-gray-600 py-6 space-y-4">
                    @foreach ($current_module->units as $index => $unit)
                        <x-accordion-toggle :content_classes="'indent-2 mt-4 pr-10'" :index="$index" :is_opened="'false'" :parent_classes="'my-4'">
                            <x-slot name="title">{{ $unit->name }}</x-slot>

                            <x-slot name="content">
                                <x-markdown-parser class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $unit->description }}
                                </x-markdown-parser>

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
                            </x-slot>
                        </x-accordion-toggle>
                    @endforeach
                </dl>
            @else
                <p class="p-2 text-red-900 dark:text-red-300">No units found for this module.</p>
            @endif
        </div>
    </div>
</div>
