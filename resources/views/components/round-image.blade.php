@props([
    'alt_text' => null,
    'src',
    'title_text' => null,
])

<span>
    <img
        class="size-6 rounded-full object-cover border-green-300 border-2"
        src="{{ $src }}"
        loading="lazy"
        @if ($alt_text)
            alt="{{ $alt_text }}"
        @endif

        @if ($title_text)
            title="{{ $title_text }}"
            aria-label="{{ $title_text }}"
        @endif
    />
</span>