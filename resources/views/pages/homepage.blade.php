@extends('layouts.app')

@section('title', data_get($page?->content, 'seo.title') ?: $page?->title)

@section('seo')
    <x-seo-module :seo="$page?->content['seo'] ?? []" />
@endsection

@section('content')
    {{-- Hero sekce s nejbližším zápasem --}}
    @if(filled(data_get($page->content, 'hero.image')) || filled(data_get($page->content, 'hero.eyebrow')) || filled(data_get($page->content, 'hero.heading')) || filled(data_get($page->content, 'hero.title')) || filled(data_get($page->content, 'hero.accent')) || filled(data_get($page->content, 'hero.text')) || filled(data_get($page->content, 'hero.cta_label')) || $nextMatch)
    <section class="relative flex min-h-[44rem] items-center overflow-hidden text-white max-mobile:min-h-[37rem]">
        @if(filled(data_get($page->content, 'hero.image')))
            <x-curator-glider
                :media="data_get($page->content, 'hero.image')"
                alt=""
                loading="eager"
                fetchpriority="high"
                class="absolute inset-0 h-full w-full object-cover object-center opacity-64 [filter:grayscale(0.18)_brightness(0.92)_saturate(0.8)] [transform:scale(1.03)]"
            />
        @endif
        <div aria-hidden="true" class="absolute inset-0 [background:linear-gradient(_90deg,rgba(60,11,16,0.97)_0%,rgba(92,22,27,0.94)_30%,rgba(124,53,38,0.74)_46%,rgba(161,150,143,0.46)_62%,rgba(232,228,224,0.18)_100%_)]"></div>
        <div aria-hidden="true" class="absolute inset-0 opacity-80 [background:repeating-linear-gradient(_118deg,transparent_0_110px,rgba(245,120,0,0.08)_111px_113px,transparent_115px_180px_)] [transform:skewX(-11deg)_scale(1.18)]"></div>

        <div class="relative z-[2] mx-auto my-0 grid w-shell grid-cols-[minmax(0,1.2fr)_minmax(260px,400px)] items-end gap-10 pb-12 pt-[5.5rem] max-tablet:grid-cols-1 max-tablet:pb-24 max-tablet:pt-20 max-mobile:w-shell-mobile">
            <div>
                @if(filled(data_get($page->content, 'hero.eyebrow')) || $competitionSeason?->name)
                <p class="mb-[1.1rem] mx-0 mt-0 inline-flex items-center gap-[0.8rem] text-[.7rem] font-extrabold uppercase tracking-micro text-orange-css">
                    <span class="block h-[0.16rem] w-[2.1rem] bg-current"></span>
                    {{ data_get($page->content, 'hero.eyebrow') }} {{ $competitionSeason?->name }}
                </p>
                @endif
                @if(filled(data_get($page->content, 'hero.heading')) || filled(data_get($page->content, 'hero.title')) || filled(data_get($page->content, 'hero.accent')))
                <h1 class="mb-[1.2rem] mx-0 mt-0 font-condensed text-118 font-black uppercase leading-90 text-white max-mobile:text-hero-mobile">
                    <x-highlighted-text :value="data_get($page->content, 'hero.heading')" :title="data_get($page->content, 'hero.title')" :accent="data_get($page->content, 'hero.accent')" accent-class="block not-italic text-orange-css" />
                </h1>
                @endif
                @if(filled(data_get($page->content, 'hero.text')))
                <p class="mb-[2.3rem] mx-0 mt-0 max-w-[28rem] text-[1.08rem] leading-170 text-white/82">
                    {{ data_get($page->content, 'hero.text') }}
                </p>
                @endif
                @if(filled(data_get($page->content, 'hero.cta_label')))
                <div class="flex">
                    <a
                        class="inline-flex items-center justify-center gap-[0.8rem] rounded-lg border-0 bg-orange-css px-6 py-4 font-condensed text-[.86rem] font-black uppercase tracking-action text-white transition-all hover:-translate-y-px hover:bg-orange-hover hover:[box-shadow:0_16px_32px_rgba(245,120,0,.42)]"
                        href="{{ \App\Services\PageService::getRelativeUrlByType('matches', $page->lang_locale ?? null, '/zapasy') }}"
                    >
                        {{ data_get($page->content, 'hero.cta_label') }} <span class="font-condensed text-[1.03rem] leading-none">›</span>
                    </a>
                </div>
                @endif
            </div>

            <aside
                aria-label="Nejbližší zápas"
                class="w-[min(100%,22rem)] justify-self-end rounded-[1.2rem] border border-white/20 bg-white/8 px-[1.2rem] pb-4 pt-[1.2rem] shadow-[0_20px_34px_rgba(0,0,0,0.08)] [backdrop-filter:blur(7px)] max-tablet:justify-self-start max-mobile:w-full"
            >
                <span class="mb-[0.7rem] block text-[.62rem] font-extrabold uppercase tracking-micro text-white/70">
                    NEJBLIŽŠÍ ZÁPAS
                </span>

                @if($nextMatch)
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div class="flex-1 text-left">
                            <span class="block font-condensed text-[clamp(1.8rem,2vw,2.4rem)] font-black uppercase leading-90 text-white">
                                {{ $nextMatch->homeTeam->short_name ?: $nextMatch->homeTeam->name }}
                            </span>
                        </div>
                        <span class="font-condensed text-[2.4rem] font-black text-wine">VS</span>
                        <div class="flex-1 text-right">
                            <span class="block font-condensed text-[clamp(1.8rem,2vw,2.4rem)] font-black uppercase leading-90 text-white">
                                {{ $nextMatch->awayTeam->short_name ?: $nextMatch->awayTeam->name }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-[0.8rem] rounded-[0.9rem] border border-white/8 bg-[rgba(24,18,19,0.32)] px-[0.9rem] pb-[0.7rem] pt-[0.9rem]">
                        <div class="mb-[0.3rem] flex justify-between gap-4 text-[.68rem] font-extrabold uppercase tracking-action text-white/68">
                            <span>{{ $nextMatch->played_at->format('d. m. Y') }}</span>
                            <span>{{ $nextMatch->played_at->format('H:i') }}</span>
                        </div>
                        <p class="m-0 text-[.85rem] text-white/75">
                            {{ $nextMatch->venue?->name }}{{ $nextMatch->venue?->city ? ', ' . $nextMatch->venue->city : '' }}
                        </p>
                    </div>
                @endif

                @if(filled(data_get($page->content, 'matches.detail_label')))
                <a
                    class="inline-flex items-center gap-2 border-b-2 border-b-transparent pb-[0.15rem] text-[.64rem] font-black uppercase tracking-micro text-wine transition-all hover:gap-3 hover:border-b-wine hover:text-white max-mobile:!text-white"
                    href="{{ \App\Services\PageService::getRelativeUrlByType('matches', $page->lang_locale ?? null, '/zapasy') }}"
                >
                    {{ data_get($page->content, 'matches.detail_label') }} <span class="font-condensed text-[1.03rem] leading-none">›</span>
                </a>
                @endif
            </aside>
        </div>
    </section>
    @endif

    {{-- Zápasy (nadcházející a minulý) --}}
    @if($nextMatch || $lastMatch || filled(data_get($page->content, 'matches.eyebrow')) || filled(data_get($page->content, 'matches.title')) || filled(data_get($page->content, 'matches.all_label')))
    <section class="px-0 py-20" id="zapasy">
        <div class="mx-auto my-0 w-shell">
            <div class="mb-8 flex items-end justify-between gap-6 max-tablet:flex-col max-tablet:items-start">
                <div>
                    @if(filled(data_get($page->content, 'matches.eyebrow')))
                    <p class="mb-[1.1rem] mx-0 mt-0 inline-flex items-center gap-[0.8rem] text-[.7rem] font-extrabold uppercase tracking-micro text-wine">
                        <span class="block h-[0.16rem] w-[2.1rem] bg-current"></span>
                        {{ data_get($page->content, 'matches.eyebrow') }}
                    </p>
                    @endif
                    @if(filled(data_get($page->content, 'matches.title')))
                    <h2 class="m-0 font-condensed text-section-title font-black uppercase leading-none text-ink-css max-mobile:text-section-title-mobile">
                        {{ data_get($page->content, 'matches.title') }}
                    </h2>
                    @endif
                </div>
                @if(filled(data_get($page->content, 'matches.all_label')))
                <a
                    class="inline-flex items-center gap-2 border-b-2 border-b-wine pb-[0.15rem] font-condensed text-[.8rem] font-black uppercase tracking-label text-wine transition-all hover:gap-3 hover:border-orange-css hover:text-orange-css"
                    href="{{ \App\Services\PageService::getRelativeUrlByType('matches', $page->lang_locale ?? null, '/zapasy') }}"
                >
                    {{ data_get($page->content, 'matches.all_label') }} <span class="font-condensed text-[1.03rem] leading-none">›</span>
                </a>
                @endif
            </div>

            <div class="grid grid-cols-[minmax(0,2fr)_minmax(0,1fr)] gap-[1.4rem] max-tablet:grid-cols-1">
                {{-- Karta dalšího zápasu --}}
                @if($nextMatch)
                    <article class="flex flex-col overflow-hidden rounded-[1.7rem] border border-paper-2 bg-wine px-[1.6rem] pb-0 pt-[1.6rem] text-white shadow-[0_10px_24px_rgba(19,17,17,0.05)]">
                        <div class="flex items-center justify-between gap-4 text-[.64rem] font-extrabold uppercase tracking-eyebrow text-white/58">
                            <span>{{ $nextMatch->competitionSeason?->competition?->name ?: $nextMatch->match_type->label() }}</span>
                            <time>{{ $nextMatch->played_at->format('d. m. Y · H:i') }}</time>
                        </div>
                        <div class="flex flex-1 items-center justify-between gap-4 px-0 pb-[0.8rem] pt-[1.2rem]">
                            <div class="flex flex-1 flex-col items-center justify-center gap-[0.6rem] text-center">
                                <x-team-badge :team="$nextMatch->homeTeam" large class="border border-white/12 bg-white" />
                                <div>
                                    <span class="mb-[0.2rem] block text-[.68rem] uppercase tracking-eyebrow text-white/64">{{ $nextMatch->homeTeam->short_name }}</span>
                                    <strong class="block font-condensed text-2xl font-black uppercase text-white">{{ $nextMatch->homeTeam->name }}</strong>
                                </div>
                            </div>
                            <span class="font-condensed text-[2.7rem] font-black text-white/38">VS</span>
                            <div class="flex flex-1 flex-col items-center justify-center gap-[0.6rem] text-center">
                                <x-team-badge :team="$nextMatch->awayTeam" large class="border border-white/12 bg-white" />
                                <div>
                                    <span class="mb-[0.2rem] block text-[.68rem] uppercase tracking-eyebrow text-white/64">{{ $nextMatch->awayTeam->short_name }}</span>
                                    <strong class="block font-condensed text-2xl font-black uppercase text-white">{{ $nextMatch->awayTeam->name }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-4 border-t border-t-white/16 px-0 pb-[1.1rem] pt-4 text-[.8rem] text-white/72">
                            <span>{{ $nextMatch->venue?->name }}{{ $nextMatch->venue?->city ? ', ' . $nextMatch->venue->city : '' }}</span>
                            @if(filled($nextMatch->ticket_url))
                                <a
                                    class="inline-flex items-center gap-2 border-b-2 border-b-transparent pb-[0.15rem] font-condensed text-[.72rem] font-black uppercase tracking-meta text-orange-css transition-all hover:gap-3 hover:border-b-orange-css hover:text-white"
                                    href="{{ $nextMatch->ticket_url }}"
                                    target="_blank"
                                    rel="noopener"
                                >
                                    VSTUPENKY <span class="font-condensed text-[1.03rem] leading-none">›</span>
                                </a>
                            @endif
                        </div>
                    </article>
                @endif

                {{-- Karta minulého zápasu --}}
                @if($lastMatch)
                    @php
                        $clubWon = $clubTeam && (($lastMatch->home_team_id === $clubTeam->id && $lastMatch->home_score > $lastMatch->away_score) || ($lastMatch->away_team_id === $clubTeam->id && $lastMatch->away_score > $lastMatch->home_score));
                        $isDraw = $lastMatch->home_score === $lastMatch->away_score;
                        $venueLabel = implode(', ', array_filter([$lastMatch->venue?->name, $lastMatch->venue?->city]));
                    @endphp
                    <article class="flex flex-col justify-start overflow-hidden rounded-[1.7rem] border border-paper-2 bg-white/72 px-[1.6rem] pb-0 pt-[1.4rem] shadow-[0_10px_24px_rgba(19,17,17,0.05)]">
                        <div class="flex items-center justify-between gap-4 text-[.64rem] font-extrabold uppercase tracking-eyebrow text-muted">
                            <span>MINULÝ ZÁPAS</span>
                            <time>{{ $lastMatch->played_at->format('d. m. Y') }}</time>
                        </div>
                        <div class="flex flex-1 items-center justify-center gap-4 px-0 pb-4 pt-6">
                            <div class="flex-1 text-center">
                                <x-team-badge :team="$lastMatch->homeTeam" class="mx-auto mb-2 h-[2.6rem] w-[2.6rem]"/>
                                <span class="mb-[0.3rem] block text-xs font-semibold uppercase tracking-stat text-muted">{{ $lastMatch->homeTeam->short_name }}</span>
                                <strong class="font-condensed text-score leading-90 {{ $lastMatch->home_team_id === $clubTeam?->id ? 'text-wine' : 'text-ink-css' }}">
                                    {{ $lastMatch->home_score }}
                                </strong>
                            </div>
                            <span class="mb-[0.3rem] block font-condensed text-xs font-semibold uppercase tracking-stat text-muted">:</span>
                            <div class="flex-1 text-center">
                                <x-team-badge :team="$lastMatch->awayTeam" class="mx-auto mb-2 h-[2.6rem] w-[2.6rem]" />
                                <span class="mb-[0.3rem] block text-xs font-semibold uppercase tracking-stat text-muted">{{ $lastMatch->awayTeam->short_name }}</span>
                                <strong class="font-condensed text-score leading-90 {{ $lastMatch->away_team_id === $clubTeam?->id ? 'text-wine' : 'text-ink-css' }}">
                                    {{ $lastMatch->away_score }}
                                </strong>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-4 border-t border-t-ink/8 px-0 pb-[1.1rem] pt-4 text-[.8rem] text-muted">
                            <span>{{ $venueLabel }}</span>
                            @if($lastMatch->home_score !== null && $lastMatch->away_score !== null && $clubTeam)
                                <span class="rounded-full px-2.5 py-1 text-9 font-bold uppercase tracking-label {{ $clubWon ? 'bg-[rgba(34,197,94,0.15)] text-[#16a34a]' : ($isDraw ? 'bg-[rgba(245,120,0,0.14)] text-orange-css' : 'bg-[rgba(220,38,38,0.12)] text-[#dc2626]') }}">
                                    {{ $clubWon ? 'VÍTĚZSTVÍ' : ($isDraw ? 'REMÍZA' : 'PROHRA') }}
                                </span>
                            @elseif($lastMatch->reportArticle)
                                <a href="{{ $lastMatch->reportArticle->url }}" class="font-condensed text-11 font-black uppercase tracking-label text-wine hover:text-orange">REPORT</a>
                            @endif
                        </div>
                    </article>
                @endif
            </div>
        </div>
    </section>
    @endif

    {{-- Sociální sítě --}}
    @php
        $instagramPosts = collect(data_get($socialFeed ?? [], 'instagram.posts', []));
        $featuredInstagramPost = $instagramPosts->first();
        $smallInstagramPost = $instagramPosts->skip(1)->first();
        $facebookPost = data_get($socialFeed ?? [], 'facebook.posts.0');
        $featuredImageUrl = data_get($featuredInstagramPost, 'image_url');
        $smallImageUrl = data_get($smallInstagramPost, 'image_url');
    @endphp
    @if($featuredInstagramPost || $facebookPost || $smallInstagramPost || filled(data_get($page->content, 'social.featured_image')) || filled(data_get($page->content, 'social.featured_text')) || filled(data_get($page->content, 'social.facebook_text')) || filled(data_get($page->content, 'social.small_image')) || filled(data_get($page->content, 'social.small_text')) || filled(data_get($page->content, 'social.title')))
    <section class="bg-ink-css px-0 py-20 text-white" id="social">
        <div class="mx-auto my-0 w-shell">
            <div class="mb-3 flex items-end justify-between gap-6 max-tablet:flex-col max-tablet:items-start">
                <div>
                    @if(filled(data_get($page->content, 'social.eyebrow')))
                    <p class="mb-[1.1rem] mx-0 mt-0 inline-flex items-center gap-[0.8rem] text-[.7rem] font-extrabold uppercase tracking-micro text-orange-css">
                        <span class="block h-[0.16rem] w-[2.1rem] bg-current"></span>
                        {{ data_get($page->content, 'social.eyebrow') }}
                    </p>
                    @endif
                    @if(filled(data_get($page->content, 'social.heading')) || filled(data_get($page->content, 'social.title')) || filled(data_get($page->content, 'social.accent')))
                    <h2 class="m-0 font-condensed text-section-title font-black uppercase leading-none text-white max-mobile:text-section-title-mobile">
                        <x-highlighted-text :value="data_get($page->content, 'social.heading')" :title="data_get($page->content, 'social.title')" :accent="data_get($page->content, 'social.accent')" accent-class="block not-italic text-orange-css" />
                    </h2>
                    @endif
                </div>
                <div class="flex flex-wrap gap-[0.8rem]">
                    @if(filled(data_get($socialLinks ?? [], 'instagram')))
                    <a
                        class="inline-flex min-h-[2.9rem] items-center justify-center rounded-[0.7rem] border border-white/20 px-4 py-[0.7rem] font-condensed text-[.74rem] font-black uppercase tracking-eyebrow text-white [transition:background_0.3s_ease,border-color_0.3s_ease,color_0.3s_ease,transform_0.3s_ease,box-shadow_0.3s_ease] hover:-translate-y-0.5 hover:border-instagram hover:bg-instagram hover:shadow-[0_8px_18px_rgba(214,41,90,.28)] focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 motion-reduce:!transition-none"
                        href="{{ data_get($socialLinks ?? [], 'instagram') }}"
                        target="_blank"
                        rel="noopener"
                    >Instagram</a>
                    @endif
                    @if(filled(data_get($socialLinks ?? [], 'facebook')))
                    <a
                        class="inline-flex min-h-[2.9rem] items-center justify-center rounded-[0.7rem] border border-white/20 px-4 py-[0.7rem] font-condensed text-[.74rem] font-black uppercase tracking-eyebrow text-white [transition:background_0.3s_ease,border-color_0.3s_ease,color_0.3s_ease,transform_0.3s_ease,box-shadow_0.3s_ease] hover:-translate-y-0.5 hover:border-facebook hover:bg-facebook hover:shadow-[0_8px_18px_rgba(24,119,242,.28)] focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 motion-reduce:!transition-none"
                        href="{{ data_get($socialLinks ?? [], 'facebook') }}"
                        target="_blank"
                        rel="noopener"
                    >Facebook</a>
                    @endif
                </div>
            </div>

            @php
                $hasFeaturedCard = $featuredInstagramPost || filled(data_get($page->content, 'social.featured_image')) || filled(data_get($page->content, 'social.featured_text'));
            @endphp
            <div class="grid grid-cols-[minmax(0,1.5fr)_minmax(240px,1fr)] gap-[1.4rem] max-tablet:grid-cols-1">
                {{-- Hlavní příspěvek --}}
                @if($hasFeaturedCard)
                <article class="relative h-full overflow-hidden rounded-[1.4rem] border border-white/8 bg-[#1d1c1b] after:absolute after:inset-0 after:content-[''] after:[background:linear-gradient(180deg,rgba(21,19,19,.1)_0%,rgba(21,19,19,.8)_100%)]">
                    @if(filled($featuredImageUrl))
                        <img
                            src="{{ $featuredImageUrl }}"
                            alt=""
                            class="absolute inset-0 block h-full w-full max-w-full object-cover"
                        >
                    @elseif(filled(data_get($page->content, 'social.featured_image')))
                        <x-curator-glider
                            :media="data_get($page->content, 'social.featured_image')"
                            alt="Tým oslavuje na ledě"
                            class="absolute inset-0 block h-full w-full max-w-full object-cover"
                        />
                    @endif
                    <div class="relative z-[1] flex h-full flex-col justify-end p-[1.6rem]">
                        <div class="mb-[0.9rem] flex items-center justify-between gap-[0.8rem] text-[.64rem] font-extrabold uppercase tracking-meta text-white/55">
                            @if(filled(data_get($featuredInstagramPost, 'caption')) || filled(data_get($page->content, 'social.featured_network')))
                            <span class="rounded-full border border-white/12 bg-instagram px-[0.6rem] py-[0.3rem] text-white">
                                {{ $featuredInstagramPost ? 'Instagram' : data_get($page->content, 'social.featured_network') }}
                            </span>
                            @endif
                            @if(filled(data_get($featuredInstagramPost, 'time_label')) || filled(data_get($page->content, 'social.featured_time')))<time class="text-orange-css">{{ data_get($featuredInstagramPost, 'time_label') ?: data_get($page->content, 'social.featured_time') }}</time>@endif
                        </div>
                        <p class="mb-4 mx-0 mt-0 max-w-[34rem] text-[1.05rem] leading-160 text-white/90">
                            {{ data_get($featuredInstagramPost, 'caption') ?: data_get($page->content, 'social.featured_text') }}
                        </p>
                        <div class="flex flex-wrap gap-4 text-[.64rem] font-bold uppercase tracking-nav text-white/45">
                            @if(filled(data_get($featuredInstagramPost, 'stats')))
                                @foreach(data_get($featuredInstagramPost, 'stats') as $stat)
                                    <span>{{ $stat }}</span>
                                @endforeach
                            @elseif(filled(data_get($page->content, 'social.featured_stats')))
                                @foreach(explode(' · ', data_get($page->content, 'social.featured_stats')) as $stat)
                                    @if(filled($stat))<span>{{ $stat }}</span>@endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </article>
                @endif

                {{-- Postranní příspěvky --}}
                @php
                    $hasFacebookCard = $facebookPost || filled(data_get($page->content, 'social.facebook_time')) || filled(data_get($page->content, 'social.facebook_text')) || filled(data_get($page->content, 'social.facebook_link_label'));
                    $hasSmallCard = $smallInstagramPost || filled(data_get($page->content, 'social.small_image')) || filled(data_get($page->content, 'social.small_text'));
                @endphp
                <div class="flex h-full flex-col gap-[1.4rem]">
                    @if($hasFacebookCard)
                    <article class="relative flex-1 overflow-hidden rounded-[1.4rem] border border-white/8 bg-[#191716] px-[1.3rem] pb-[1.1rem] pt-[1.4rem]">
                        <div class="mb-[0.9rem] flex items-center justify-between gap-[0.8rem] text-[.64rem] font-extrabold uppercase tracking-meta text-white/55">
                            <span class="rounded-full border border-white/12 bg-facebook px-[0.6rem] py-[0.3rem] text-white">Facebook</span>
                            @if(filled(data_get($facebookPost, 'time_label')) || filled(data_get($page->content, 'social.facebook_time')))<time class="text-orange-css">{{ data_get($facebookPost, 'time_label') ?: data_get($page->content, 'social.facebook_time') }}</time>@endif
                        </div>
                        <p class="mb-4 mx-0 mt-2 whitespace-pre-line text-white/72 leading-158">
                            {{ data_get($facebookPost, 'message') ?: data_get($page->content, 'social.facebook_text') }}
                        </p>
                        <a
                            class="inline-flex items-center gap-2 border-b-2 border-b-transparent pb-[0.15rem] text-[.64rem] font-black uppercase leading-none tracking-meta text-orange-css [transition:color_0.2s_ease,border-color_0.2s_ease,gap_0.2s_ease] hover:gap-3 hover:border-b-orange-css hover:text-white focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 motion-reduce:!transition-none"
                            href="{{ data_get($facebookPost, 'permalink') ?: '#' }}"
                            @if(filled(data_get($facebookPost, 'permalink'))) target="_blank" rel="noopener" @endif
                        >
                            {{ data_get($page->content, 'social.facebook_link_label') }} <span class="font-condensed text-[1.03rem] leading-none">›</span>
                        </a>
                    </article>
                    @endif

                    @if($hasSmallCard)
                    <article class="relative min-h-[13.4rem] flex-1 overflow-hidden rounded-[1.4rem] border border-white/8 bg-[#1d1c1b] after:absolute after:inset-0 after:content-[''] after:[background:linear-gradient(180deg,rgba(21,19,19,.2)_0%,rgba(21,19,19,.9)_100%)]">
                        @if(filled($smallImageUrl))
                            <img
                                src="{{ $smallImageUrl }}"
                                alt=""
                                class="absolute inset-0 block h-full w-full max-w-full object-cover"
                            >
                        @elseif(filled(data_get($page->content, 'social.small_image')))
                            <x-curator-glider
                                :media="data_get($page->content, 'social.small_image')"
                                alt="Hokejka a puk"
                                class="absolute inset-0 block h-full w-full max-w-full object-cover"
                            />
                        @endif
                        <div class="absolute inset-[auto_0_0_0] z-[1] block h-auto flex-col justify-end px-[1.2rem] pb-4 pt-[1.2rem]">
                            @if($smallInstagramPost || filled(data_get($page->content, 'social.featured_network')))
                            <span class="mb-2 inline-block w-fit rounded-full border border-white/12 bg-instagram px-[0.6rem] py-[0.3rem] text-[.64rem] font-extrabold uppercase tracking-meta text-white">
                                {{ $smallInstagramPost ? 'Instagram' : data_get($page->content, 'social.featured_network') }}
                            </span>
                            @endif
                            <p class="m-0 text-white/85 leading-normal">
                                {{ data_get($smallInstagramPost, 'caption') ?: data_get($page->content, 'social.small_text') }}
                            </p>
                        </div>
                    </article>
                    @endif
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Tým sekce --}}
    @if($promoPlayers->isNotEmpty() || filled(data_get($page->content, 'team.heading')) || filled(data_get($page->content, 'team.title')) || filled(data_get($page->content, 'team.accent')) || filled(data_get($page->content, 'team.text')) || filled(data_get($page->content, 'team.cta_label')))
    <section class="relative flex items-center overflow-hidden [background:linear-gradient(90deg,rgba(76,18,23,0.96)_0%,rgba(98,33,30,0.96)_20%,rgba(119,80,73,0.9)_42%,rgba(224,219,212,0.85)_100%)] max-md-exact:min-h-[34rem] max-md-exact:flex-col max-md-exact:justify-between max-md-exact:px-0 max-md-exact:pb-0 max-md-exact:pt-16" id="tym">
        <div aria-hidden="true" class="absolute inset-0 [background:linear-gradient(90deg,rgba(79,15,20,0.88)_0%,rgba(96,30,28,0.82)_32%,rgba(122,89,82,0.28)_55%,rgba(230,224,215,0.18)_100%)]"></div>
        <div class="relative z-[1] mx-auto my-0 w-shell px-0 pb-16 pt-20 text-white max-md-exact:w-shell-mobile max-md-exact:px-0 max-md-exact:pb-8 max-md-exact:pt-12">
            @if(filled(data_get($page->content, 'team.eyebrow')))
            <p class="mb-[1.1rem] mx-0 mt-0 inline-flex items-center gap-[0.8rem] text-[.7rem] font-extrabold uppercase tracking-micro text-orange-css">
                <span class="block h-[0.16rem] w-[2.1rem] bg-current"></span>
                {{ data_get($page->content, 'team.eyebrow') }}
            </p>
            @endif
            @if(filled(data_get($page->content, 'team.heading')) || filled(data_get($page->content, 'team.title')) || filled(data_get($page->content, 'team.accent')))
            <h2 class="m-0 font-condensed text-band font-black uppercase leading-90 text-white max-mobile:text-section-title-mobile">
                <x-highlighted-text :value="data_get($page->content, 'team.heading')" :title="data_get($page->content, 'team.title')" :accent="data_get($page->content, 'team.accent')" accent-class="block not-italic text-orange-css" />
            </h2>
            @endif
            @if(filled(data_get($page->content, 'team.text')))
            <p class="mb-[2.1rem] mt-[1.6rem] mx-0 max-w-[32rem] text-[1.05rem] leading-160 text-white/90 max-md-exact:max-w-full max-md-exact:text-[.98rem]">
                {{ data_get($page->content, 'team.text') }}
            </p>
            @endif
            @if(filled(data_get($page->content, 'team.cta_label')))
            <a
                class="inline-flex items-center justify-center gap-[0.8rem] rounded-lg border border-white/35 bg-white px-6 py-4 font-condensed text-[.86rem] font-black uppercase tracking-action text-ink-css [transition:transform_0.2s_ease,box-shadow_0.2s_ease,background_0.2s_ease,color_0.2s_ease] hover:-translate-y-px hover:shadow-[0_16px_32px_rgba(0,0,0,.28)] focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 motion-reduce:!transition-none"
                href="{{ \App\Services\PageService::getRelativeUrlByType('team', $page->lang_locale ?? null, '/tym') }}"
            >
                {{ data_get($page->content, 'team.cta_label') }} <b>›</b>
            </a>
            @endif
        </div>

        @php
            $promoSlides = $promoPlayers->values()->map(fn ($promoPlayer, $i) => [
                'image' => $promoPlayer->portrait_url ?: asset('assets/obrazky/player.webp'),
                'number' => (string) $promoPlayer->jersey_number,
                'position' => $promoPlayer->position?->shortLabel() ?: '',
            ]);
            $firstPromoSlide = $promoSlides->first();
            $firstPromoDigits = $firstPromoSlide ? str_split((string) $firstPromoSlide['number']) : [];
            $firstPromoNumberLayout = count($firstPromoDigits) > 1
                ? 'justify-center gap-[clamp(1rem,2.4vw,2.5rem)] max-md-exact:gap-[0.6rem]'
                : 'justify-center translate-x-[25%] max-md-exact:translate-x-0';
        @endphp
        @if($firstPromoSlide)
        <div
            class="absolute bottom-0 right-[clamp(3rem,8vw,10rem)] z-[1] flex h-[clamp(22rem,34vw,38rem)] w-[clamp(23rem,32vw,37rem)] items-end justify-center overflow-visible [transition:opacity_0.42s_ease] max-md-exact:relative max-md-exact:bottom-auto max-md-exact:right-auto max-md-exact:mx-auto max-md-exact:mb-0 max-md-exact:mt-4 max-md-exact:h-[19rem] max-md-exact:w-[min(100%,20rem)] motion-reduce:!transition-none"
            data-player-promo-target
            aria-label="Představení hráčů"
        >
            {{-- VÍNOVÝ VNĚJŠÍ OBRYS ČÍSLA --}}
            <div
                data-player-promo-number
                class="pointer-events-none absolute inset-0 z-0 flex items-end {{ $firstPromoNumberLayout }} overflow-visible font-condensed text-[clamp(10.8rem,45vw,45rem)] font-black leading-70 tracking-normal text-transparent [-webkit-text-stroke:7px_#6a1b21] [transform:translate(3%,-12%)_scaleX(1.04)] [transform-origin:center_bottom] max-md-exact:text-[25rem] max-md-exact:[transform:translateY(-10%)]"
            >
                @foreach($firstPromoDigits as $digit)
                    <span>{{ $digit }}</span>
                @endforeach
            </div>

            {{-- JEMNÁ ORANŽOVÁ VÝPLŇ ČÍSLA --}}
            <div
                data-player-promo-number
                class="pointer-events-none absolute inset-0 z-[1] flex items-end {{ $firstPromoNumberLayout }} overflow-visible font-condensed text-[clamp(10.8rem,45vw,45rem)] font-black leading-70 tracking-normal text-orange-css/15 [transform:translate(3%,-12%)_scaleX(1.04)] [transform-origin:center_bottom] max-md-exact:text-[25rem] max-md-exact:text-orange-css/30 max-md-exact:[transform:translateY(-10%)]"
            >
                @foreach($firstPromoDigits as $digit)
                    <span>{{ $digit }}</span>
                @endforeach
            </div>

            {{-- HRÁČ --}}
            <img
                data-player-promo-image
                src="{{ $firstPromoSlide['image'] }}"
                alt=""
                class="relative z-[2] h-[82%] w-full max-w-full object-contain object-bottom"
            >
        </div>
        @if($promoSlides->count() > 1)
            <script type="application/json" data-player-promo>{!! $promoSlides->toJson() !!}</script>
        @endif
        @endif
    </section>
    @endif

    {{-- Klub sekce --}}
    @if(filled(data_get($page->content, 'club.eyebrow')) || filled(data_get($page->content, 'club.heading')) || filled(data_get($page->content, 'club.title')) || filled(data_get($page->content, 'club.accent')) || filled(data_get($page->content, 'club.text')) || filled(data_get($page->content, 'club.stats')) || filled(data_get($page->content, 'club.cta_label')))
    <section class="relative bg-paper px-0 py-20 max-mobile:bg-paper" id="klub">
        <div class="relative z-[1] mx-auto my-0 grid min-h-[28rem] w-shell grid-cols-[minmax(0,1.05fr)_minmax(0,1.2fr)] items-center gap-10 max-tablet:grid-cols-1 max-mobile:min-h-auto max-mobile:grid-cols-1 max-mobile:gap-8">
            <div class="pr-4 text-ink-css">
                <p class="mb-[1.1rem] mx-0 mt-0 inline-flex items-center gap-[0.8rem] text-[.7rem] font-extrabold uppercase tracking-micro text-orange-css">
                    <span class="block h-[0.16rem] w-[2.1rem] bg-current"></span>
                    {{ data_get($page->content, 'club.eyebrow') }}
                </p>
                @if(filled(data_get($page->content, 'club.heading')) || filled(data_get($page->content, 'club.title')) || filled(data_get($page->content, 'club.accent')))
                <h2 class="m-0 font-condensed text-promo font-black uppercase leading-80 text-ink-css max-mobile:text-section-title-mobile">
                    <x-highlighted-text :value="data_get($page->content, 'club.heading')" :title="data_get($page->content, 'club.title')" :accent="data_get($page->content, 'club.accent')" accent-class="block text-orange-css" />
                </h2>
                @endif
                @if(filled(data_get($page->content, 'club.cta_label')))
                <a
                    class="mt-[1.6rem] inline-flex items-center justify-center gap-[0.8rem] rounded-lg border border-wine/20 bg-wine px-6 py-4 font-condensed text-[.86rem] font-black uppercase tracking-action text-white [transition:transform_0.2s_ease,box-shadow_0.2s_ease,background_0.2s_ease,color_0.2s_ease] hover:-translate-y-px hover:bg-wine-hover hover:shadow-[0_12px_28px_rgba(106,27,33,.32)] focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 motion-reduce:!transition-none"
                    href="{{ \App\Services\PageService::getRelativeUrlByType('about', $page->lang_locale ?? null, '/klub') }}"
                >
                    {{ data_get($page->content, 'club.cta_label') }} <span class="font-condensed text-[1.03rem] leading-none">›</span>
                </a>
                @endif
            </div>

            <div class="flex min-h-72 flex-col justify-center pt-4 text-ink-css/80 max-mobile:min-h-auto max-mobile:pt-0">
                @if(filled(data_get($page->content, 'club.text')))
                <p class="mb-8 mx-0 mt-0 max-w-[35rem] text-[1.15rem] leading-160 text-ink-css/80">
                    {{ data_get($page->content, 'club.text') }}
                </p>
                @endif
                <div class="grid grid-cols-3 gap-0 border-t border-t-ink-css/14">
                    @php
                        $clubStats = collect(data_get($page->content, 'club.stats') ?: [])
                            ->filter(fn ($stat) => filled(data_get($stat, 'value')) || filled(data_get($stat, 'label')))
                            ->take(3);
                    @endphp
                    @foreach($clubStats as $stat)
                        <div class="min-h-32 px-[0.6rem] pb-0 pt-5 {{ !$loop->last ? 'border-r border-r-ink-css/14' : 'border-r-0' }}">
                            @if(filled(data_get($stat, 'value')))
                            <strong class="block font-condensed text-stat leading-none text-wine">
                                {{ data_get($stat, 'value') }}
                            </strong>
                            @endif
                            @if(filled(data_get($stat, 'label')))
                            <span class="block mt-[0.6rem] text-[.7rem] font-extrabold tracking-promo uppercase text-ink-css/70">
                                {{ data_get($stat, 'label') }}
                            </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Tabulka ligy --}}
    @if(filled($competitionSeason?->competition?->name) || filled(data_get($page->content, 'standings.heading')) || filled(data_get($page->content, 'standings.title')) || filled(data_get($page->content, 'standings.accent')) || filled(data_get($page->content, 'standings.text')) || filled(data_get($page->content, 'standings.cta_label')) || $standings->isNotEmpty())
    <section class="bg-wine px-0 py-20">
        <div class="mx-auto my-0 grid w-shell grid-cols-[minmax(0,1fr)_minmax(0,1.5fr)] items-center gap-8 max-tablet:grid-cols-1">
            <div class="max-w-[26rem]">
                <p class="mb-[1.1rem] mx-0 mt-0 inline-flex items-center gap-[0.8rem] text-[.7rem] font-extrabold uppercase tracking-micro text-white/80">
                    <span class="block h-[0.16rem] w-[2.1rem] bg-current"></span>
                    {{ $competitionSeason?->competition?->name }}
                </p>
                @if(filled(data_get($page->content, 'standings.heading')) || filled(data_get($page->content, 'standings.title')) || filled(data_get($page->content, 'standings.accent')))
                <h2 class="m-0 font-condensed text-section-title font-black uppercase leading-none text-white max-mobile:text-section-title-mobile">
                    <x-highlighted-text :value="data_get($page->content, 'standings.heading')" :title="data_get($page->content, 'standings.title')" :accent="data_get($page->content, 'standings.accent')" accent-class="block not-italic text-orange-css" />
                </h2>
                @endif
                @if(filled(data_get($page->content, 'standings.text')))
                <p class="mb-[1.8rem] mx-0 mt-0 text-[.97rem] leading-170 text-white/80">
                    {{ data_get($page->content, 'standings.text') }}
                </p>
                @endif
                @if(filled(data_get($page->content, 'standings.cta_label')))
                <a
                    class="inline-flex items-center gap-2 border-b-2 border-b-wine pb-[0.15rem] font-condensed text-[.8rem] font-black uppercase tracking-label text-orange-css transition-all hover:gap-3 hover:border-orange-css hover:text-white"
                    href="{{ \App\Services\PageService::getRelativeUrlByType('matches', $page->lang_locale ?? null, '/zapasy') }}"
                >
                    {{ data_get($page->content, 'standings.cta_label') }} <span class="font-condensed text-[1.03rem] leading-none">›</span>
                </a>
                @endif
            </div>

            <div class="rounded-2xl overflow-hidden shadow-lg">
                <table class="w-full max-mobile:w-full max-mobile:table-fixed border-collapse">
                    <thead>
                        <tr class="bg-ink text-white">
                            <th class="py-4 px-4 text-left font-bold uppercase max-mobile:!px-[0.3rem] max-mobile:whitespace-nowrap max-mobile:!w-[1.8rem] text-10 tracking-label text-white/50 w-9">#</th>
                            <th class="py-4 px-4 text-left font-bold uppercase max-mobile:!px-[0.3rem] max-mobile:whitespace-nowrap max-mobile:overflow-hidden max-mobile:text-ellipsis text-10 tracking-label text-white/50">TÝM</th>
                            <th class="py-4 px-4 text-center font-bold uppercase max-mobile:!px-[0.3rem] max-mobile:whitespace-nowrap max-mobile:!w-[1.9rem] text-10 tracking-label text-white/50 w-[42px]">Z</th>
                            <th class="py-4 px-4 text-center font-bold uppercase max-mobile:!px-[0.3rem] max-mobile:whitespace-nowrap max-mobile:!w-[1.9rem] text-10 tracking-label text-white/50 w-[42px]">V</th>
                            <th class="py-4 px-4 text-center font-bold uppercase max-mobile:!px-[0.3rem] max-mobile:whitespace-nowrap max-mobile:!w-[1.9rem] text-10 tracking-label text-white/50 w-[42px]">P</th>
                            <th class="py-4 px-4 text-center font-bold uppercase max-mobile:!px-[0.3rem] max-mobile:whitespace-nowrap max-mobile:!w-[1.9rem] text-10 tracking-label text-white/50 w-[42px]">B</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach($standings->take(5) as $row)
                            @php
                                $team = $row->team ?? ($row['team'] ?? null);
                                $teamId = $row->team_id ?? ($row['team_id'] ?? null);
                                $isClub = $clubTeam && (int)$clubTeam->id === (int)$teamId;
                                $get = fn($key) => data_get($row, $key);
                            @endphp
                            <tr class="border-b border-b-line {{ $isClub ? 'bg-[rgba(245,120,0,0.05)] font-bold' : '' }}">
                                <td class="px-4 py-3.5 text-sm {{ $isClub ? 'font-black text-orange' : 'font-bold text-muted' }} max-mobile:!w-[1.8rem] max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]">
                                    {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-3.5 max-mobile:overflow-hidden max-mobile:text-ellipsis max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]">
                                    <div class="flex items-center gap-3 max-mobile:min-w-0 max-mobile:gap-[0.35rem]">
                                        <x-team-badge :team="$team" />
                                        <span class="{{ $isClub ? 'font-black text-wine' : 'font-semibold' }} text-sm max-mobile:overflow-hidden max-mobile:text-ellipsis">
                                            {{ $team?->name ?? data_get($row, 'team_name') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center text-sm max-mobile:!w-[1.9rem] max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]">{{ $get('games_played') }}</td>
                                <td class="px-4 py-3.5 text-center text-sm max-mobile:!w-[1.9rem] max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]">{{ $get('wins') }}</td>
                                <td class="px-4 py-3.5 text-center text-sm max-mobile:!w-[1.9rem] max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]">{{ $get('losses') }}</td>
                                <td class="px-4 py-3.5 text-center text-sm font-black {{ $isClub ? 'text-orange' : 'text-wine' }} max-mobile:!w-[1.9rem] max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]">
                                    {{ $get('points') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    @endif

    {{-- Aktuality sekce --}}
    @if($articles->isNotEmpty() || filled(data_get($page->content, 'news.eyebrow')) || filled(data_get($page->content, 'news.heading')) || filled(data_get($page->content, 'news.title')) || filled(data_get($page->content, 'news.accent')) || filled(data_get($page->content, 'news.all_label')))
    <section class="bg-paper px-0 py-20" id="novinky">
        <div class="mx-auto my-0 w-shell">
            <div class="mb-10 flex items-end justify-between gap-6 max-tablet:flex-col max-tablet:items-start">
                <div>
                    <p class="mb-[1.1rem] mx-0 mt-0 inline-flex items-center gap-[0.8rem] text-[.7rem] font-extrabold uppercase tracking-micro text-wine">
                        <span class="block h-[0.16rem] w-[2.1rem] bg-current"></span>
                        {{ data_get($page->content, 'news.eyebrow') }}
                    </p>
                    @if(filled(data_get($page->content, 'news.heading')) || filled(data_get($page->content, 'news.title')) || filled(data_get($page->content, 'news.accent')))
                    <h2 class="m-0 font-condensed text-section-title font-black uppercase leading-none text-ink-css max-mobile:text-section-title-mobile">
                        <x-highlighted-text :value="data_get($page->content, 'news.heading')" :title="data_get($page->content, 'news.title')" :accent="data_get($page->content, 'news.accent')" accent-class="block text-orange-css" />
                    </h2>
                    @endif
                </div>
                @if(filled(data_get($page->content, 'news.all_label')))
                <a
                    class="inline-flex items-center gap-2 border-b-2 border-b-wine pb-[0.15rem] font-condensed text-[.8rem] font-black uppercase tracking-label text-wine transition-all hover:gap-3 hover:border-orange-css hover:text-orange-css"
                    href="{{ \App\Services\PageService::getRelativeUrlByType('blog', $page->lang_locale ?? null, '/aktuality') }}"
                >
                    {{ data_get($page->content, 'news.all_label') }} <span class="font-condensed text-[1.03rem] leading-none">›</span>
                </a>
                @endif
            </div>

            <div class="grid grid-cols-3 gap-[1.3rem] max-tablet:grid-cols-1">
                @foreach($articles as $article)
                    <article class="group relative flex cursor-pointer flex-col overflow-hidden rounded-3xl border border-ink-css/8 bg-white shadow-[0_10px_18px_rgba(19,17,17,0.03)] transition-all duration-500 ease-out hover:-translate-y-[5px] hover:shadow-[0_20px_36px_rgba(19,17,17,.10)] motion-reduce:!transition-none">
                        <div class="h-[13.2rem] overflow-hidden">
                            @if(filled($article->featured_media_id))
                                <x-curator-glider
                                    :media="$article->featured_media_id"
                                    :alt="$article->plain_title"
                                    class="block h-full w-full max-w-full object-cover transition-transform duration-500 group-hover:scale-[1.04] motion-reduce:!transition-none"
                                />
                            @else
                                <img
                                    alt=""
                                    class="block h-full w-full max-w-full object-cover transition-transform duration-500 group-hover:scale-[1.04] motion-reduce:!transition-none"
                                    src="{{ asset('assets/obrazky/article.webp') }}"
                                >
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col px-[1.2rem] pb-[1.1rem] pt-[1.3rem]">
                            <p class="mb-[0.7rem] mx-0 mt-0 text-[.62rem] font-extrabold uppercase tracking-meta text-muted">
                                {{ optional($article->publish_time)->format('d. m. Y') }} · {{ mb_strtoupper($article->category) }}
                            </p>
                            <h3 class="m-0 flex-1 font-condensed text-[1.7rem] uppercase leading-[1.05] tracking-[-.04em] text-ink-css">
                                {{ $article->plain_title }}
                            </h3>
                            <a
                                class="mt-4 inline-flex items-center gap-[0.45rem] text-[.66rem] font-black uppercase tracking-meta text-orange-css [transition:gap_0.2s] hover:gap-[.7rem] after:absolute after:inset-0 after:content-[''] focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 motion-reduce:!transition-none"
                                href="{{ $article->url }}"
                            >
                                {{ data_get($page->content, 'news.read_label') }} <span>›</span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Partneři klubu --}}
    @php $partnersWithLogo = $partners->filter(fn ($partner) => filled($partner->logo_media_id)); @endphp
    @if($partnersWithLogo->isNotEmpty())
    <section class="bg-white py-12">
        <div class="mx-auto max-w-7xl px-6">
            <p class="mb-8 text-center text-xs font-bold uppercase tracking-[0.2em] text-wine">
                {{ data_get($page->content, 'partners.title') }}
            </p>
            <div aria-label="Partneři klubu" class="flex items-center gap-[0.8rem] max-partner:gap-[0.35rem]">
                <button
                    aria-label="Předchozí partner"
                    class="grid h-[2.4rem] w-[2.4rem] flex-[0_0_2.4rem] cursor-pointer place-items-center rounded-full border border-control-line bg-white p-0 text-wine hover:border-wine hover:bg-wine hover:text-white"
                    id="partnersPrev"
                    type="button"
                >
                    <svg aria-hidden="true" class="block h-4 w-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>

                <div class="min-w-0 flex-1 overflow-hidden">
                    <div class="flex items-center transition-transform duration-500" id="partnersSliderTrack">
                        @foreach($partnersWithLogo as $partner)
                            <div class="flex h-36 min-w-0 flex-[0_0_20%] items-center justify-center px-4 py-0 max-partner:h-32 max-partner:basis-full">
                                <a href="{{ $partner->website ?: '#' }}" @if(filled($partner->website)) target="_blank" rel="noopener" @endif>
                                    <x-curator-glider
                                        :media="$partner->logo_media_id"
                                        :alt="$partner->name"
                                        class="block h-28 max-h-32 w-auto max-w-full object-contain max-partner:max-h-28"
                                    />
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button
                    aria-label="Další partner"
                    class="grid h-[2.4rem] w-[2.4rem] flex-[0_0_2.4rem] cursor-pointer place-items-center rounded-full border border-control-line bg-white p-0 text-wine hover:border-wine hover:bg-wine hover:text-white"
                    id="partnersNext"
                    type="button"
                >
                    <svg aria-hidden="true" class="block h-4 w-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>
    </section>
    @endif
@endsection
