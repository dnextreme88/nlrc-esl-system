@props(['text'])

<span {{ $attributes->merge(['class' => 'block self-center text-center rounded-full px-4 py-2 text-xs font-medium ring-1 ring-inset min-w-[100px] max-w-[100px]']) }}>{{ $text }}</span>
