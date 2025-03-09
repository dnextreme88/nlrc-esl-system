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
            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">{{ $current_announcement['title'] }}</h3>

            @if (!empty(trim($current_announcement['tags'])))
                <ul class="flex gap-2">
                    @foreach (explode(',', $current_announcement['tags']) as $tag)
                        <li><span class="rounded-full px-3 py-2 text-xs font-medium ring-inset ring-1 dark:ring-2 bg-green-200 dark:bg-green-400/10 text-green-800 dark:text-green-300 ring-green-600/40 dark:ring-green-400/30">{{ $tag }}</span></li>
                    @endforeach
                </ul>
            @endif

            <p class="mt-4 text-gray-600 dark:text-gray-400 text-xs sm:text-sm">Posted by {{ $current_announcement->user->first_name }} {{ $current_announcement->user->last_name }} on <span class="font-bold">{{ \Carbon\Carbon::parse($current_announcement['created_at'])->format('D, M j, Y \a\t g:i A') }}</span></p>

            @if ($current_announcement['created_at'] != $current_announcement['updated_at'])
                <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm">Updated on <span class="font-bold">{{ \Carbon\Carbon::parse($current_announcement['updated_at'])->format('D, M j, Y \a\t g:i A') }}</span></p>
            @endif

            <div class="mt-4 dark:text-gray-400">{!! Markdown::parse($current_announcement['description']) !!}</div>

            <div class="border-t border-green-800 dark:border-green-200 mt-4 pt-2 flex flex-row justify-between items-center">
                <p><a wire:navigate class="text-gray-800 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-300" href="{{ route('announcements.index') }}">&larr; Back</a></p>
            </div>
        </div>
    </div>
</div>
