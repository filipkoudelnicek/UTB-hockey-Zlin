@extends('layouts.app')

@section('title', $page->title)

@section('seo')
    <x-seo-module :seo="$page->content['seo'] ?? []" />
@endsection

@section('content')
    <x-page-header
        :breadcrumb="$page->title"
        :eyebrow="data_get($page->content, 'hero.eyebrow')"
        :title="data_get($page->content, 'hero.title') ?: $page->title"
        :accent="data_get($page->content, 'hero.accent')"
        :heading="data_get($page->content, 'hero.heading')"
        :image="\App\Services\MediaService::getMediaUrl(data_get($page->content, 'hero.image'))"
        :locale="$page->lang_locale ?? null"
    />

    @if(filled(data_get($page->content, 'text')))
        <section class="bg-paper py-16 max-mobile:py-12">
            <div class="article-body mx-auto w-article text-17 leading-170 text-ink/85 [&_a]:text-orange [&_a]:underline [&_a]:underline-offset-4 [&_blockquote]:mb-8 [&_blockquote]:border-l-4 [&_blockquote]:border-orange [&_blockquote]:pl-6 [&_blockquote]:font-condensed [&_blockquote]:text-2xl [&_blockquote]:font-black [&_blockquote]:leading-tight [&_cite]:mt-3 [&_cite]:block [&_cite]:font-sans [&_cite]:text-sm [&_cite]:font-semibold [&_cite]:not-italic [&_cite]:text-muted [&_figure]:mb-8 [&_img]:block [&_img]:h-auto [&_img]:max-w-full [&_img]:rounded-2xl [&_p]:mb-6 [&_h2]:mb-5 [&_h2]:mt-10 [&_h2]:font-condensed [&_h2]:text-3xl [&_h2]:font-black [&_h2]:uppercase [&_h2]:leading-tight [&_h2]:text-wine [&_ul]:mb-6 [&_ul]:list-disc [&_ul]:pl-6 [&_ol]:mb-6 [&_ol]:list-decimal [&_ol]:pl-6">
                <x-rich-text :content="data_get($page->content, 'text')" />
            </div>
        </section>
    @endif
@endsection
