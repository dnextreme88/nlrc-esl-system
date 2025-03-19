<div>
    <x-slot name="nav_menu">
        <livewire:NavMenu />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Notifications') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col space-y-3">
                @forelse ($user_notifications as $notification)
                    @if ($notification->type == 'announcement-sent')
                        {{-- TODO:
                            Very similar with the layout from notification-bell.blade.php
                            Why not just make it as a reusable component? (no need to make it a Livewire component)
                            The differences between the two is that the title section is not present in this view and ellipsis truncation is 4x longer here (32 * 4)
                        --}}
                        <a
                            wire:click.prevent="set_is_read('{{ $notification->id }}')"
                            wire:navigate
                            href="{{ route('announcements.detail', ['id' => $notification->data['announcement_id'], 'slug' => $notification['slug']]) }}"
                            class="inline-block w-full text-sm p-2 border-b border-gray-200 dark:border-gray-900 first-of-type:rounded-t-md
                            {{ $notification->read_at ? 'bg-gray-100/50 dark:bg-gray-800/50 hover:bg-gray-200 dark:hover:bg-gray-700' : 'bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
                        >
                            <div class="flex justify-between gap-3">
                                <div class="flex flex-1 flex-col">
                                    {{--
                                    <h2 class="dark:text-gray-200 {{ !$notification->read_at ? 'font-bold' : '' }}">{{ strip_tags(\Illuminate\Support\Str::limit($notification['title'], '24', '...')) }}</h2>
                                    --}}
                                    <p class="indent-1 text-gray-800 dark:text-gray-200">
                                        {{ strip_tags(\Illuminate\Support\Str::limit($notification['description'], '128', ' (...)')) }}
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
                    {{-- TODO: NOT YET IMPLEMENTED
                    @elseif ($notification->type == 'meetings-on-call-sent')
                        <a wire:navigate wire:click="set_is_read('{{ $notification->id }}')" href="{{ route('dashboard') }}">
                            <div class="p-2 border-b border-gray-200 hover:bg-gray-400 dark:hover:bg-gray-700 dark:border-gray-900 sm:p-4 {{ !$notification->read_at ? 'bg-gray-200 dark:bg-gray-900' : '' }}">
                                <div class="flex items-center justify-between gap-2">
                                    <h2 class="dark:text-gray-200 {{ !$notification->read_at ? 'font-bold' : '' }}">
                                        An on-call meeting is about to start in 30 minutes. Check your user dashboard for details
                                    </h2>

                                    <x-custom.unread-circle :is_read="$notification->read_at" :tooltip="'Unread announcement'" />
                                </div>

                                <small class="block mt-2 dark:text-slate-400">
                                    {{ Helpers::parse_time_to_user_timezone($notification['created_at'])->diffForHumans(\Carbon\Carbon::now()->toUserTimezone(), [
                                        'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
                                        'options' => \Carbon\Carbon::JUST_NOW | \Carbon\Carbon::NO_ZERO_DIFF | \Carbon\Carbon::ONE_DAY_WORDS
                                    ]) }}
                                </small>
                            </div>
                        </a>
                    --}}
                    @endif
                @empty
                    <p class="p-2 text-gray-800 dark:text-gray-200">You have no notifications.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
