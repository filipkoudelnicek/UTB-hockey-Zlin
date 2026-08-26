@extends('layouts.app')
@section('title', $page->title)
@section('seo')<x-seo-module :seo="$page->content['seo'] ?? []" />@endsection

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">

        @isset($page->content['title'])
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                {{ $page->content['title'] }}
            </h1>
        @endisset

        <div class="flex flex-col md:flex-row gap-10 items-start">

            @isset($page->content['image'])
                <div class="w-full md:w-2/5 shrink-0 rounded-2xl overflow-hidden shadow-md">
                    <x-curator-glider :media="$page->content['image']" class="w-full h-full object-cover" />
                </div>
            @endisset

            <div class="flex-1 min-w-0">
                @isset($page->content['text'])
                    <div class="prose prose-gray max-w-none">
                        {!! $page->content['text'] !!}
                    </div>
                @endisset

                @php
                    $button = \App\Helpers\LinkHelper::resolve($page->content['button'] ?? null);
                @endphp

                @if(!empty($button['label']))
                    <a href="{{ $button['url'] }}" target="{{ $button['target'] }}"
                       class="mt-6 inline-flex items-center gap-2 rounded-lg bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-violet-700 transition-colors">
                        {{ $button['label'] }}
                    </a>
                @endif
            </div>
        </div>

    </section>
@endsection