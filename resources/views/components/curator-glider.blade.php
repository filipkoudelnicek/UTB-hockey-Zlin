@props([
    'media' => null,
    'alt' => '',
    'loading' => 'lazy',
    'fetchpriority' => null,
])

@php
    $url = \App\Services\MediaService::getMediaUrl($media);
@endphp

@if($url)
    <img
        src="{{ $url }}"
        alt="{{ $alt }}"
        loading="{{ $loading }}"
        @if($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
        {{ $attributes }}
    >
@endif
