@extends('layouts.app')
@section('title', $page->title)
@section('seo')<x-seo-module :seo="$page->content['seo'] ?? []" />@endsection

@section('content')
    <x-page-header :title="$page->title" />

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-start">

            @if(isset($page->content['title']) || isset($page->content['text']))
                <div>
                    @isset($page->content['title'])
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $page->content['title'] }}</h2>
                    @endisset
                    @isset($page->content['text'])
                        <div class="prose prose-gray max-w-none">
                            {!! $page->content['text'] !!}
                        </div>
                    @endisset
                </div>
            @endif

            <div>
                <livewire:contact-form />
            </div>

        </div>
    </section>
@endsection