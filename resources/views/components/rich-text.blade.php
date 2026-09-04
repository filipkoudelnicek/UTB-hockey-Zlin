@props(['content' => null])

@php
    $content = preg_replace(
        '~<p\b[^>]*>(?:\s|&nbsp;|<br\s*/?>)*</p>~iu',
        '<br>',
        (string) $content,
    );
@endphp

{!! $content !!}
