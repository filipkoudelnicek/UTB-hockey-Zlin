@props(['media' => null, 'alt' => ''])

@php
    $url = \App\Services\MediaService::getMediaUrl($media);
@endphp

@if($url)
    <img src="{{ $url }}" alt="{{ $alt }}" loading="lazy" {{ $attributes }}>
@endif
