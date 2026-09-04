@props([
    'value' => null,
    'title' => null,
    'accent' => null,
    'accentClass' => 'text-orange',
])

@php
    $source = filled($value)
        ? (string) $value
        : \App\Support\HighlightedHeading::fromLegacy($title, $accent);
    $rendered = e($source);
    $rendered = preg_replace(
        '/&lt;span data-highlight=&quot;accent&quot;&gt;([\s\S]*?)&lt;\/span&gt;/',
        '<span class="' . $accentClass . '">$1</span>',
        $rendered,
    );
@endphp

{!! $rendered !!}
