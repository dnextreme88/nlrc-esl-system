<div>
    <h3 class="text-3xl text-gray-800 dark:text-gray-200">Announcements</h3>

    @if ($latest_announcement)
        <div class="my-2 indent-2">
            <a
                wire:click.prevent="set_is_read('{{ $latest_announcement->id }}')"
                wire:navigate
                href="{{ route('announcements.detail', ['id' => $latest_announcement->data['announcement_id'], 'slug' => $latest_announcement['slug']]) }}"
                class="flex gap-4 justify-between items-center"
                title="{{ $latest_announcement['title'] }}"
            >
                <p class="truncate overflow-hidden">
                    <span class="text-xl text-green-600 dark:text-green-300">&rarr;</span>
                    <span class="font-bold text-lg text-gray-900 dark:text-gray-100 transition duration-150 ease-in-out hover:text-green-600 dark:hover:text-green-300">{{ $latest_announcement['title'] }}</span>
                </p>

                <x-custom.unread-circle :is_read="$latest_announcement->read_at" :tooltip="'Unread announcement'" class="shrink-0" />
            </a>
        </div>

        <div class="my-4">
            <h4 class="text-xl text-gray-800 dark:text-gray-200">Recent</h4>

            <ul class="flex gap-2 flex-col mt-2 [&>*]:indent-2">
                @foreach ($recent_announcements as $recent_announcement)
                    <li>
                        <a
                            wire:click.prevent="set_is_read('{{ $recent_announcement->id }}')"
                            wire:navigate
                            href="{{ route('announcements.detail', ['id' => $recent_announcement->data['announcement_id'], 'slug' => $recent_announcement['slug']]) }}"
                            class="flex gap-4 justify-between items-center text-gray-600 dark:text-gray-300 transition duration-150 ease-in-out hover:text-green-600 dark:hover:text-green-300"
                            title="{{ $recent_announcement['title'] }}"
                        >
                            <p class="truncate overflow-hidden">{{ $recent_announcement['title'] }}</p>

                            <x-custom.unread-circle :is_read="$recent_announcement->read_at" :tooltip="'Unread announcement'" class="shrink-0" />
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="my-4">
            <p class="text-gray-800 dark:text-gray-200 px-2 italic">You currently have no announcements.</p>
        </div>
    @endif
</div>
