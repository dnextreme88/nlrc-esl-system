
<div
    ondragenter="onLivewireCalendarEventDragEnter(event, '{{ $componentId }}', '{{ $day }}', '{{ $dragAndDropClasses }}');"
    ondragleave="onLivewireCalendarEventDragLeave(event, '{{ $componentId }}', '{{ $day }}', '{{ $dragAndDropClasses }}');"
    ondragover="onLivewireCalendarEventDragOver(event);"
    ondrop="onLivewireCalendarEventDrop(event, '{{ $componentId }}', '{{ $day }}', {{ $day->year }}, {{ $day->month }}, {{ $day->day }}, '{{ $dragAndDropClasses }}');"
    class="flex-1 border border-gray-200 -mt-px -ml-px"
    style="min-width: 10rem;"
>
    {{-- Wrapper for Drag and Drop --}}
    <div class="w-full h-40 overflow-auto" id="{{ $componentId }}-{{ $day }}">
        <div
            @if ($dayClickEnabled)
                wire:click="onDayClick({{ $day->year }}, {{ $day->month }}, {{ $day->day }})"
            @endif
            class="w-full h-full p-2 {{ $dayInMonth ? $isToday ? 'bg-green-100 dark:bg-green-300' : ' bg-gray-100 dark:bg-gray-300 ' : 'bg-gray-300 dark:bg-gray-500' }} flex flex-col"
        >
            {{-- Day number with number of events on that day --}}
            <div class="flex items-center">
                <p class="text-sm {{ $dayInMonth ? 'font-semibold' : 'opacity-25' }}">
                    {{ $day->format('j') }}
                </p>

                <p class="text-xs text-gray-800 dark:text-gray-800 ml-4">
                    @if ($events->isNotEmpty())
                        {{ $events->count() }} {{ Str::plural('booking', $events->count()) }}
                    @endif
                </p>
            </div>

            {{-- Events --}}
            <div class="p-2 my-2 flex-1 overflow-y-auto">
                <div class="grid grid-cols-1 grid-flow-row gap-2">
                    @foreach ($events as $event)
                        <div
                            @if ($dragAndDropEnabled)
                                draggable="true"
                            @endif
                                ondragstart="onLivewireCalendarEventDragStart(event, '{{ $event['id'] }}')"
                        >
                            @include ($eventView, ['event' => $event])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
