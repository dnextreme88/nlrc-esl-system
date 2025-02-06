{{-- NOTE: window.darkModeSwitcher() is defined on the layouts --}}

@if (isset($title))
    <div>{{ $title }}</div>
@endif

<div x-on:keydown.window.tab="switchOn = false" {{ $attributes->merge(['class' => 'flex px-4 py-2 place-content-start']) }}>
    @if (isset($left_side))
        <div>{{ $left_side }}</div>
    @endif

    <input x-bind:checked="switchOn" class="hidden" type="checkbox" name="switch" />

    <button
        x-ref="switchButton"
        x-on:click="switchOn = !switchOn; switchTheme()"
        x-bind:class="{'bg-green-400': switchOn, 'bg-slate-400': !switchOn}"
        class="relative inline-flex h-5 py-0.5 focus:outline-none rounded-full w-9"
        type="button"
    >
        <span x-bind:class="{'translate-x-[18px]': switchOn, 'translate-x-0.5': !switchOn}" class="h-4 w-4 duration-200 ease-in-out bg-white rounded-full shadow-md"></span>
    </button>

    @if (isset($right_side))
        <label
            x-on:click="$refs.switchButton.click(); $refs.switchButton.focus()"
            x-bind:class="{'text-slate-300': switchOn, 'text-slate-700': !switchOn}"
            x-bind:id="$id('switch')"
            class="ml-2 text-sm select-none"
        >
            {{ $right_side }}
        </label>
    @endif
</div>
