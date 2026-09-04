@extends('layouts.app')

@section('title', data_get($page?->content, 'seo.title', 'Aktuality') ?: 'Aktuality')

@section('seo')
    <x-seo-module :seo="$page?->content['seo'] ?? []" />
@endsection

@section('content')
    {{-- Hero sekce --}}
    <x-page-header
        breadcrumb="Aktuality"
        :eyebrow="data_get($page->content, 'hero.eyebrow', 'CO SE DĚJE V KLUBU')"
        :title="data_get($page->content, 'hero.title', 'AKTUALITY')"
        :accent="data_get($page->content, 'hero.accent', '& NOVINKY')"
        :heading="data_get($page->content, 'hero.heading')"
        :image="\App\Services\MediaService::getMediaUrl(data_get($page->content, 'hero.image'))"
        :locale="$page->lang_locale ?? null"
    />

    {{-- Výpis aktualit --}}
    <section class="bg-paper py-16 scroll-mt-[88px]" data-news-section>
        <div class="w-shell mx-auto">
            <livewire:news-article-list
                :locale="$page->lang_locale"
                :empty-message="data_get($page->content, 'list.empty', 'Zatím nejsou publikované žádné aktuality.')"
            />
        </div>
    </section>
@endsection
