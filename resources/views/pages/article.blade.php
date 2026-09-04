@extends('layouts.app')

@section('title', $article->plain_title)

@section('seo')
    <x-seo-module :seo="$article->content['seo'] ?? []" type="article" />
@endsection

@section('content')
    <article>
        <header
            class="relative bg-cover bg-top bg-no-repeat py-14 text-white"
            style="background-image: linear-gradient(rgba(0,0,0,.45), rgba(0,0,0,.45)), url('{{ $article->banner_image_url ?: asset('assets/obrazky/article.webp') }}')"
        >
            <div class="w-shell mx-auto">
                <nav class="mb-8 flex items-center gap-2 text-10 font-bold uppercase tracking-label text-white/55">
                    <a class="text-orange no-underline hover:text-white" href="{{ \App\Services\PageService::getRelativeUrlByType('homepage', $article->lang_locale, '/') }}">Domů</a>
                    <span>/</span>
                    <a class="text-orange no-underline hover:text-white" href="{{ \App\Services\PageService::getRelativeUrlByType('blog', $article->lang_locale, '/aktuality') }}">Aktuality</a>
                    <span>/</span>
                    <span>{{ $article->plain_title }}</span>
                </nav>

                <p class="mb-4 flex items-center gap-2.5 text-11 font-bold uppercase tracking-eyebrow text-orange">
                    <span class="block h-[3px] w-6 bg-current"></span>
                    {{ mb_strtoupper(\App\Models\Article::categoryLabel($article->category)) }}
                </p>

                <h1 class="mb-6 font-condensed text-article-title font-black uppercase leading-95">
                    <x-highlighted-text :value="$article->title" accent-class="block text-orange-css" />
                </h1>

                <div class="flex flex-wrap items-center gap-5 text-xs font-semibold uppercase tracking-nav text-white/50">
                    <span>{{ optional($article->publish_time)->format('d. m. Y') }}</span>
                    <span class="text-white/20">|</span>
                    <span>AUTOR: {{ mb_strtoupper($article->user?->name ?? 'Redakce RB') }}</span>
                </div>
            </div>
        </header>

        <div class="bg-paper py-16">
            <div class="w-article mx-auto">
                <div class="mb-8 flex items-center gap-4 max-mobile:flex-col">
                    <div class="flex shrink-0 flex-col items-center gap-3 max-mobile:flex-row">
                        <span class="text-xs font-bold uppercase tracking-label text-muted">SDÍLET</span>
                        <a aria-label="Sdílet na Facebooku" class="flex h-9 w-9 items-center justify-center rounded-full bg-facebook text-sm font-bold text-white no-underline transition-all hover:scale-110 focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 motion-reduce:!transition-none" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($article->url) }}" target="_blank" rel="noopener">f</a>
                        <a aria-label="Sdílet na X" class="flex h-9 w-9 items-center justify-center rounded-full bg-ink text-sm font-bold text-white no-underline transition-all hover:scale-110 focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 motion-reduce:!transition-none" href="https://twitter.com/intent/tweet?url={{ urlencode($article->url) }}&text={{ urlencode($article->plain_title) }}" target="_blank" rel="noopener">X</a>
                        <button aria-label="Kopírovat odkaz" class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-full border-0 bg-orange p-0 text-sm font-bold text-white transition-all hover:scale-110 focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 motion-reduce:!transition-none" data-copy-article-link="{{ $article->url }}" type="button">↗</button>
                    </div>

                    <div class="flex-1">
                        <div class="article-body text-17 leading-170 text-ink/85 [&_a]:text-orange [&_a]:underline [&_a]:underline-offset-4 [&_blockquote]:mb-8 [&_blockquote]:border-l-4 [&_blockquote]:border-orange [&_blockquote]:pl-6 [&_blockquote]:font-condensed [&_blockquote]:text-2xl [&_blockquote]:font-black [&_blockquote]:leading-tight [&_cite]:mt-3 [&_cite]:block [&_cite]:font-sans [&_cite]:text-sm [&_cite]:font-semibold [&_cite]:not-italic [&_cite]:text-muted [&_figure]:mb-8 [&_img]:block [&_img]:h-auto [&_img]:max-w-full [&_img]:rounded-2xl [&_p]:mb-6 [&_h2]:mb-5 [&_h2]:mt-10 [&_h2]:font-condensed [&_h2]:text-3xl [&_h2]:font-black [&_h2]:uppercase [&_h2]:leading-tight [&_h2]:text-wine [&_ul]:mb-6 [&_ul]:list-disc [&_ul]:pl-6 [&_ol]:mb-6 [&_ol]:list-decimal [&_ol]:pl-6">
                            @if(filled(data_get($article->content, 'body')))
                                <x-rich-text :content="data_get($article->content, 'body')" />
                            @endif
                        </div>

                        @if($nextMatch)
                            <div class="mt-8 flex flex-wrap items-center justify-between gap-4 rounded-2xl bg-wine p-6 text-white">
                                <div>
                                    <strong class="block font-condensed text-lg font-black uppercase">{{ $nextMatch->homeTeam->name }} × {{ $nextMatch->awayTeam->name }}</strong>
                                    <span class="text-sm text-white/70"><time>{{ $nextMatch->played_at->format('d. m. Y · H:i') }}</time>@if($nextMatch->venue?->name) · {{ $nextMatch->venue->name }}@endif</span>
                                </div>

                                @if(filled($nextMatch->ticket_url))
                                    <a class="inline-flex items-center gap-2 rounded-lg bg-orange px-5 py-3 font-condensed text-sm font-black uppercase tracking-widest text-white no-underline shadow-[0_12px_24px_rgba(245,120,0,0.22)] transition-all hover:-translate-y-px hover:!bg-orange-hover hover:!text-white hover:shadow-[0_16px_32px_rgba(245,120,0,.42)] focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 motion-reduce:!transition-none" href="{{ $nextMatch->ticket_url }}" target="_blank" rel="noopener">CHCI VSTUPENKY <span>›</span></a>
                                @else
                                    <a class="inline-flex items-center gap-2 rounded-lg bg-orange px-5 py-3 font-condensed text-sm font-black uppercase tracking-widest text-white no-underline shadow-[0_12px_24px_rgba(245,120,0,0.22)] transition-all hover:-translate-y-px hover:!bg-orange-hover hover:!text-white hover:shadow-[0_16px_32px_rgba(245,120,0,.42)] focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 motion-reduce:!transition-none" href="{{ \App\Services\PageService::getRelativeUrlByType('matches', $article->lang_locale, '/zapasy') }}">VŠECHNY ZÁPASY <span>›</span></a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </article>

    @if($related->isNotEmpty())
        <section class="border-t border-t-line bg-white py-16">
            <div class="w-shell mx-auto">
                <div class="mb-8 flex items-end justify-between gap-6 max-mobile:flex-col max-mobile:items-start">
                    <div>
                        <p class="mb-2 flex items-center gap-2.5 text-11 font-bold uppercase tracking-eyebrow text-wine">
                            <span class="block h-[3px] w-6 bg-current"></span>
                            ČTĚTE DÁLE
                        </p>
                        <h2 class="m-0 font-condensed text-section-sm font-black uppercase leading-none">
                            DALŠÍ ČLÁNKY
                        </h2>
                    </div>
                    <a
                        class="inline-flex items-center gap-2 border-b-2 border-b-wine pb-[0.15rem] font-condensed text-[.8rem] font-black uppercase tracking-label text-wine transition-all hover:gap-3 hover:border-orange-css hover:text-orange-css"
                        href="{{ \App\Services\PageService::getRelativeUrlByType('blog', $article->lang_locale, '/aktuality') }}"
                    >
                        VŠECHNY AKTUALITY <span class="font-condensed text-[1.03rem] leading-none">›</span>
                    </a>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    @foreach($related as $item)
                        <article class="group flex flex-col overflow-hidden rounded-2xl border border-line bg-paper">
                            <a href="{{ $item->url }}" class="flex flex-1 flex-col text-inherit no-underline focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">
                                <div class="h-[200px] overflow-hidden">
                                    <img
                                        src="{{ $item->featured_image_url ?: asset('assets/obrazky/article.webp') }}"
                                        alt="{{ $item->plain_title }}"
                                        class="block h-full w-full max-w-full object-cover transition-transform duration-500 group-hover:scale-[1.04] motion-reduce:!transition-none"
                                    >
                                </div>
                                <div class="flex flex-1 flex-col p-6">
                                    <p class="mb-2 text-10 font-bold uppercase tracking-meta text-muted">
                                        {{ optional($item->publish_time)->format('d. m. Y') }}
                                    </p>
                                    <h3 class="m-0 flex-1 font-condensed text-2xl font-black uppercase leading-tight">
                                        {{ $item->plain_title }}
                                    </h3>
                                    <span class="mt-4 inline-flex items-center gap-[0.45rem] text-[.69rem] font-black uppercase tracking-label text-orange-css [transition:color_0.2s_ease,gap_0.2s_ease] group-hover:gap-[.7rem] motion-reduce:!transition-none">
                                        ČÍST ČLÁNEK <span class="font-condensed text-[1.03rem] [transition:transform_0.2s_ease] group-hover:translate-x-1 motion-reduce:!transition-none">›</span>
                                    </span>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
