@extends('layouts.app')
@section('title', $article->title)
@section('seo')<x-seo-module :seo="$article->content['seo'] ?? []" />@endsection

@section('content')
    <x-page-header
        :title="$article->title"
        :background="$article->content['banner'] ?? null"
        :author="$article->user->name"
        :publishedAt="$article->publish_time?->format('j.n.Y')"
    />

    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @isset($article->content['body'])
            <div class="prose prose-gray max-w-none">
                {!! $article->content['body'] !!}
            </div>
        @endisset
    </section>
@endsection