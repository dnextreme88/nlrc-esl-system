<div>
    <x-slot name="nav_menu">
        <livewire:NavMenu />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Modules') }}
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 gap-9 mx-auto space-y-6 max-w-7xl py-12 sm:px-6 lg:px-8 lg:space-y-0 lg:grid-cols-3 lg:place-items-center lg:h-75-vh">
        @foreach ($user_modules as $module)
            <div class="grid gap-3 p-3 transition-all duration-300 bg-gray-300/50 border-gray-300 place-items-center rounded-xl relative h-full w-full dark:border-gray-700 dark:bg-gray-600/50 lg:hover:bg-gray-400 dark:lg:hover:bg-gray-700 lg:hover:scale-110 {{ !$module->has_access ? 'opacity-60 dark:opacity-50' : '' }}">
                @if (!$module->has_access)
                    <div class="block text-sm text-red-900 dark:text-red-300 lg:hidden">You don't have access to this module yet</div>
                @endif

                <a
                    wire:navigate
                    @if ($module->has_access) href="{{ route('modules.detail', ['id' => $module->id, 'slug' => $module->slug]) }}" @endif
                    class="hidden group p-4 absolute h-full w-full rounded-xl hover:bg-gray-300/75 dark:hover:bg-gray-600/75 lg:flex lg:items-center lg:justify-center {{ $module->has_access ? 'hover:cursor-pointer' : 'hover:cursor-not-allowed' }}"
                >
                    <div class="transition duration-[500ms] text-center translate-y-full opacity-0 group-hover:opacity-100 group-hover:translate-y-0">
                        @if ($module->has_access)
                            <x-markdown-parser text_limit="100" class="text-sm text-gray-800 dark:text-gray-200">
                                {{ $module->description }}
                            </x-markdown-parser>
                        @else
                            <span class="text-sm text-red-900 dark:text-red-300">You don't have access to this module yet!</span>
                        @endif
                    </div>
                </a>

                <img src="https://i.pravatar.cc/300" class="h-52 w-52" alt="Module Image" title="Module Image" loading="lazy" /> {{-- TODO: Sample only, to be replaced --}}

                <span
                    class="text-gray-800 dark:text-gray-200 font-semibold text-2xl lg:text-base lg:text-center {{ $module->has_access ? 'hover:underline hover:cursor-pointer lg:hover:cursor-auto' : 'hover:cursor-not-allowed lg:hover:cursor-auto' }}"
                >
                    {{ $module->name }}
                </span>

                @if (str_word_count($module->description) > 200)
                    <x-markdown-parser class="place-self-start text-justify text-sm text-gray-800 dark:text-gray-200 block lg:hidden line-clamp-8">
                        {{ $module->description }}
                    </x-markdown-parser>

                    <a
                        wire:navigate
                        @if ($module->has_access) href="{{ route('modules.detail', ['id' => $module->id, 'slug' => $module->slug]) }}" @endif
                        class="inline-block mt-2 mb-3 transition duration-150 ease-in-out text-green-600 dark:text-green-300 hover:underline"
                        title="Click me to read more"
                    >
                        Read more
                    </a>
                @else
                    <x-markdown-parser class="place-self-start text-justify text-sm text-gray-800 dark:text-gray-200 block lg:hidden">
                        {{ $module->description }}
                    </x-markdown-parser>
                @endif
            </div>
        @endforeach
    </div>
</div>
