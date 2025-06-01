<div
    @if ($eventClickEnabled)
        wire:click.stop="onEventClick('{{ $event['id']  }}')"
    @endif
    class="bg-gray-200 rounded-lg border py-2 px-3 shadow-md cursor-pointer transition duration-300 hover:bg-green-100 dark:hover:bg-green-300"
>
    <a wire:navigate href="{{ route('meetings.detail', ['meeting_uuid' => $event['meeting']['meeting_uuid']]) }}">
        <p class="text-sm font-medium">
            {{ $event['title'] ?? 'No title' }}
        </p>

        <p class="mt-2">
            @if ($event['description'])
                <div class="flex -space-x-2">
                    @if (Helpers::is_teacher_role())
                        @foreach (array_slice($event['description'], 0, 2) as $student_image)
                            <x-round-image :src="$student_image" />
                        @endforeach

                        @if (count($event['description']) > 2)
                            <div class="size-6 rounded-full object-cover border-gray-300 border-2 text-white px-1 w-full text-[9px] bg-gray-800 flex items-center basis-[24px]">
                                {{ count(array_slice($event['description'], 2)) }}+
                            </div>
                        @endif
                    @elseif (Helpers::is_student_role())
                        <x-round-image :src="$event['description']" />
                    @endif
                </div>
            @else
                <span>No description</span>
            @endif
        </p>
    </a>
</div>
