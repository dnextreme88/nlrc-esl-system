<div
    @if ($eventClickEnabled)
        wire:click.stop="onEventClick('{{ $event['id']  }}')"
    @endif
    class="bg-white rounded-lg border py-2 px-3 shadow-md cursor-pointer"
>
    <p class="text-sm font-medium">
        {{ $event['title'] ?? 'No title' }}
    </p>

    <p class="mt-2 text-xs">
        @if ($event['description'])
            <div class="flex -space-x-2">
                @if (in_array(Auth::user()->role->name, [\App\Enums\Roles::HEAD_TEACHER->value, \App\Enums\Roles::TEACHER->value]))
                    @foreach (array_slice($event['description'], 0, 2) as $student_image)
                        <img
                            class="size-6 rounded-full object-cover border-green-300 border-2"
                            src="{{ $student_image }}"
                            alt="Student image"
                            title="Student image"
                        />
                    @endforeach

                    @if (count($event['description']) > 2)
                        <div class="size-6 rounded-full object-cover border-gray-300 border-2 text-white px-1 w-full text-[9px] bg-gray-800 flex items-center basis-[24px]">
                            {{ count(array_slice($event['description'], 2)) }}+
                        </div>
                    @endif
                @elseif (Auth::user()->role->name == \App\Enums\Roles::STUDENT->value)
                    <img
                        class="size-6 rounded-full object-cover border-green-300 border-2"
                        src="{{ $event['description'] }}"
                        alt="Teacher image"
                        title="Teacher image"
                    />
                @endif
            </div>
        @else
            <span>No description</span>
        @endif
    </p>
</div>
