@props([
    'user_notifications',
    'user_notifications_unread_count' => 0,
    'user_notifications_unread_count_is_overlap' => false
])

<x-dropdown align="right" width="64">
    <x-slot name="trigger">
        <button class="flex items-center pt-0.5 cursor-pointer">
            <x-heroicon-o-bell class="h-6 w-auto text-gray-900 hover:fill-gray-300 dark:text-gray-300 dark:hover:fill-gray-600" />

            <sup>
                <span
                    class="inline-flex py-0.5 px-1 rounded-full max-h-8 max-w-8 min-h-4 min-w-4 items-center justify-center relative end-2 {{ $user_notifications_unread_count > 0 ? 'bg-red-800 dark:bg-red-200 text-gray-100 dark:text-gray-900' : 'bg-gray-800 dark:bg-gray-200 text-gray-200 dark:text-gray-800' }}"
                >
                    {{ $user_notifications_unread_count }}{{ $user_notifications_unread_count_is_overlap ? '+' : '' }}
                </span>
            </sup>
        </button>
    </x-slot>

    <x-slot name="content">
        @forelse ($user_notifications as $notification)
            @if ($notification->type == 'announcement-sent')
                <a
                    wire:click.prevent="set_is_read('{{ $notification->id }}')"
                    wire:navigate
                    href="{{ route('announcements.detail', ['id' => $notification->data['announcement_id'], 'slug' => $notification['slug']]) }}"
                    class="inline-block w-full text-sm p-2 border-b border-gray-200 dark:border-gray-900 first-of-type:rounded-t-md
                    {{ $notification->read_at ? 'bg-gray-100/50 dark:bg-gray-800/50 hover:bg-gray-200 dark:hover:bg-gray-700' : 'bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
                >
                    <div class="flex justify-between gap-3">
                        <div class="flex flex-1 flex-col">
                            <h2 class="dark:text-gray-200 {{ !$notification->read_at ? 'font-bold' : '' }}">{{ strip_tags(\Illuminate\Support\Str::limit($notification['title'], '24', '...')) }}</h2>
                            <p class="indent-1 text-gray-800 dark:text-gray-200">
                                {{ strip_tags(\Illuminate\Support\Str::limit($notification['description'], '32', ' (...)')) }}
                            </p>

                            <small class="mt-3 dark:text-gray-400">
                                {{ Helpers::parse_time_to_user_timezone($notification['created_at'])->diffForHumans(\Carbon\Carbon::now()->toUserTimezone(), [
                                    'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
                                    'options' => \Carbon\Carbon::JUST_NOW | \Carbon\Carbon::NO_ZERO_DIFF | \Carbon\Carbon::ONE_DAY_WORDS
                                ]) }}
                            </small>
                        </div>

                        <div class="flex items-center">
                            <x-custom.unread-circle :is_read="$notification->read_at" :tooltip="'Unread announcement'" />
                        </div>
                    </div>
                </a>
            {{-- TODO: TO ADD NOTIFICATIONS FOR MEETINGS ON CALL
            @elseif ($notification->type == 'meetings-on-call-sent')
                <div class="flex flex-1 flex-col right">
                    <a wire:navigate wire:click.prevent="set_is_read('{{ $notification->id }}')" href="{{ route('dashboard') }}">
                        <h2 class="dark:text-white {{ !$notification->read_at ? 'font-bold' : '' }}">An on-call meeting is about to start in 30 minutes. Check your dashboard for details</h2>

                        <small class="mt-2 dark:text-gray-400">
                            {{ Helpers::parse_time_to_user_timezone($notification['created_at'])->diffForHumans(\Carbon\Carbon::now()->toUserTimezone(), [
                                'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
                                'options' => \Carbon\Carbon::JUST_NOW | \Carbon\Carbon::NO_ZERO_DIFF | \Carbon\Carbon::ONE_DAY_WORDS
                            ]) }}
                        </small>
                    </a>
                </div>
            --}}
            @endif
        @empty
            <p class="p-2 text-sm text-gray-800 dark:text-gray-200">You have no notifications.</p>
        @endforelse

        @if (count($user_notifications) > 0)
            <a wire:navigate href="{{ route('notifications') }}" class="block p-2 text-center text-sm text-gray-800 hover:text-green-600 dark:text-gray-200 dark:hover:text-green-300 transition duration-150 ease-in-out">See more notifications</a>
        @endif
    </x-slot>
</x-dropdown>
