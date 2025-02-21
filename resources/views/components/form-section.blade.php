@props(['submit'])

<div {{ $attributes->merge(['class' => 'p-2']) }}>
    @if (isset($title) || isset($description))
        <x-section-title>
            @if (isset($title))
                <x-slot name="title">{{ $title }}</x-slot>
            @endif

            @if (isset($description))
                <x-slot name="description">{{ $description }}</x-slot>
            @endif
        </x-section-title>
    @endif

    <div class="mt-4 md:mt-6">
        <form wire:submit="{{ $submit }}">
            <div class="px-4 py-6 sm:p-6 {{ isset($actions) ? 'sm:rounded-tl-md sm:rounded-tr-md' : 'sm:rounded-md' }}">
                <div class="grid grid-cols-1 gap-5">
                    {{ $form }}
                </div>
            </div>

            @if (isset($actions))
                <div class="flex items-center px-4 py-6">
                    {{ $actions }}
                </div>
            @endif
        </form>
    </div>
</div>
