@props(['text_limit' => 0])

<div {!! $attributes->merge(['class' => 'max-w-full prose dark:prose-invert']) !!}>
    @if ($text_limit > 0)
        {!! Str::limit(app(SpatieMarkdown::class)->toHtml($slot), $text_limit, preserveWords: true) !!}
    @else
        {!! app(SpatieMarkdown::class)->toHtml($slot) !!}
    @endif
</div>
