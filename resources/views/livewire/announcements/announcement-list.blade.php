<div>
    <x-slot name="nav_menu">
        <livewire:NavMenu />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Announcement') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (count($announcements) > 0)
                @foreach ($announcements as $announcement)
                    <div class="mb-4 mt-2 shadow-lg shadow-gray-300 dark:shadow-gray-600 first:mt-4">
                        <div class="flex justify-between gap-3 p-2 bg-gray-200 dark:bg-gray-600 sm:p-4">
                            <div class="flex flex-1 flex-col">
                                <h2 class="dark:text-gray-400 {{ !$announcement->read_at ? 'font-bold' : '' }}">
                                    <a
                                        wire:navigate
                                        wire:click="set_is_read('{{ $announcement->id }}')"
                                        href="{{ route('announcements.detail', ['id' => $announcement->data['announcement_id'], 'slug' => $announcement['slug']]) }}"
                                        class="text-gray-800 hover:text-green-600 dark:text-gray-200 dark:hover:text-green-300 transition duration-150 ease-in-out"
                                    >
                                        {{ $announcement['title'] }}
                                    </a>
                                </h2>

                                <p class="text-gray-600 dark:text-gray-400 text-sm">Posted by {{ \App\Models\User::find($announcement->data['user_id'])->name }} on <span class="font-bold">{{ Helpers::parse_time_to_user_timezone($announcement->data['created_at'])->format('D, F j, Y \a\t h:i A') }}</span></p>
                            </div>

                            <div class="flex items-center">
                                <x-custom.unread-circle :is_read="$announcement->read_at" :tooltip="'Unread announcement'" />
                            </div>
                        </div>

                        <div class="p-2 sm:p-4">
                            @if (!empty(trim($announcement['tags'])))
                                <ul class="flex gap-2">
                                    @foreach (explode(',', $announcement['tags']) as $tag)
                                        <li>
                                            <x-badge :text="$tag" class="max-w-fit bg-green-200 dark:bg-green-400/10 text-green-800 dark:text-green-300 ring-green-600/40 dark:ring-green-400/60" />
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            @if (str_word_count($announcement['description']) > 200)
                                <x-markdown-parser class="mt-4 indent-2 text-justify text-gray-800 dark:text-gray-200 line-clamp-8">
                                    {{ $announcement['description'] }}
                                </x-markdown-parser>

                                <a
                                    wire:navigate
                                    wire:click="set_is_read('{{ $announcement->id }}')"
                                    href="{{ route('announcements.detail', ['id' => $announcement->data['announcement_id'], 'slug' => $announcement['slug']]) }}"
                                    class="inline-block mt-2 mb-3 transition duration-150 ease-in-out text-green-600 dark:text-green-300 hover:underline"
                                    title="Click me to read more"
                                >
                                    Read more
                                </a>
                            @else
                                <x-markdown-parser class="mt-4 indent-2 text-justify text-gray-800 dark:text-gray-200">
                                    {{ $announcement['description'] }}
                                </x-markdown-parser>
                            @endif
                        </div>
                    </div>
                @endforeach

                {{ $announcements->withQueryString()->links() }}
            @else
                <p class="text-gray-800 dark:text-gray-200">No announcements found.</p>
            @endif
        </div>
    </div>
</div>
