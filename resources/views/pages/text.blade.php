@extends('layouts.app')
@section('title', $page->title)
@section('seo')<x-seo-module :seo="$page->content['seo'] ?? []" />@endsection

@section('content')
    <x-page-header :title="$page->title" />

    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @isset($page->content['text'])
            <div class="prose prose-gray max-w-none">
                {!! $page->content['text'] !!}
            </div>
        @endisset
    </section>
@endsection