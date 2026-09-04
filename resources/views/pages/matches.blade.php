@extends('layouts.app')

@section('title', data_get($page?->content, 'seo.title', 'Zápasy') ?: 'Zápasy')

@section('seo')
    <x-seo-module :seo="$page?->content['seo'] ?? []" />
@endsection

@section('content')
    {{-- Hero sekce --}}
    <x-page-header
        breadcrumb="Zápasy"
        :eyebrow="data_get($page->content, 'hero.eyebrow', 'ROČNÍK') . ' ' . $competitionSeason?->name"
        :title="data_get($page->content, 'hero.title', 'ZÁPASY')"
        :accent="data_get($page->content, 'hero.accent', '& VÝSLEDKY')"
        :heading="data_get($page->content, 'hero.heading')"
        :image="\App\Services\MediaService::getMediaUrl(data_get($page->content, 'hero.image'))"
        :locale="$page->lang_locale ?? null"
    />

    {{-- Rozpis zápasů --}}
    <section class="bg-paper py-16">
        <div class="w-shell mx-auto">
            {{-- Filtry --}}
            @if($matches->isNotEmpty())
            <div class="mb-10 flex flex-wrap gap-2" data-filter-group data-target="#scheduleList">
                <button
                    class="inline-flex cursor-pointer items-center rounded-lg border-2 border-wine bg-wine px-5 py-2.5 font-condensed text-sm font-black uppercase tracking-widest text-white transition-all focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3"
                    data-filter="all"
                >Všechny</button>
                <button
                    class="inline-flex cursor-pointer items-center rounded-lg border-2 border-control-line bg-transparent px-5 py-2.5 font-condensed text-sm font-black uppercase tracking-widest text-nav-ink transition-all focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3"
                    data-filter="next"
                >Nadcházející</button>
                <button
                    class="inline-flex cursor-pointer items-center rounded-lg border-2 border-control-line bg-transparent px-5 py-2.5 font-condensed text-sm font-black uppercase tracking-widest text-nav-ink transition-all focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3"
                    data-filter="past"
                >Odehrané</button>
            </div>
            @endif

            {{-- Seznam zápasů --}}
            <div class="flex flex-col gap-4" id="scheduleList">
                @forelse($matches as $match)
                    @php
                        $past = $match->status === \App\Enums\MatchStatus::Finished;
                        $category = $past ? 'past' : 'next';
                        $competitionLabel = $match->competitionSeason?->competition?->short_name ?: $match->competitionSeason?->competition?->name ?: $match->match_type->label();
                        $clubWon = $past && $clubTeam && $match->involves($clubTeam) && (($match->home_team_id === $clubTeam->id && $match->home_score > $match->away_score) || ($match->away_team_id === $clubTeam->id && $match->away_score > $match->home_score));
                    @endphp

                    <article
                        class="overflow-hidden rounded-2xl bg-white {{ $past ? 'border border-line opacity-85' : 'border border-line shadow-sm' }}"
                        data-category="{{ $category }}"
                    >
                        <div class="grid grid-cols-[120px_1fr_auto] max-mobile:!grid-cols-1">
                            {{-- Datum --}}
                            <div class="flex flex-col items-center justify-center px-5 py-6 text-white max-mobile:min-h-[7rem] max-mobile:p-[1.2rem] {{ $past ? 'bg-nav-ink' : 'bg-wine' }}">
                                <strong class="font-condensed text-52 font-black leading-none">
                                    {{ $match->played_at->format('d') }}
                                </strong>
                                <span class="text-10 font-bold uppercase tracking-widest text-center {{ $past ? 'text-white/55' : 'text-white/70' }}">
                                    {{ mb_strtoupper($match->played_at->translatedFormat('M')) }}<br>{{ $match->played_at->format('Y') }}
                                </span>
                            </div>

                            {{-- Týmy & informace --}}
                            <div class="flex flex-col justify-between p-5 max-mobile:min-w-0">
                                <div>
                                    <span class="mb-2 inline-block text-10 font-bold uppercase tracking-label {{ $past ? 'text-muted' : 'text-orange' }}">
                                        {{ $past ? 'ODEHRÁNO' : 'NADCHÁZEJÍCÍ' }} · {{ mb_strtoupper($competitionLabel) }}
                                    </span>
                                    <b class="block text-sm font-semibold text-muted">
                                        {{ $past ? '' : $match->played_at->format('H:i') . ' · ' }}{{ $match->venue?->name }}{{ $match->venue?->city ? ', ' . $match->venue->city : '' }}
                                    </b>
                                </div>

                                <div class="mt-4 flex items-center gap-4 max-mobile:flex-wrap max-mobile:gap-y-[0.8rem]">
                                    <div class="flex items-center gap-2.5">
                                        <x-team-badge :team="$match->homeTeam" />
                                        <span class="font-condensed text-base font-black uppercase">{{ $match->homeTeam->name }}</span>
                                    </div>

                                    @if($past)
                                        <div class="flex items-center gap-1.5 font-condensed text-28 font-black">
                                            <span class="{{ $match->home_team_id === $clubTeam?->id ? 'text-wine' : 'text-muted' }}">{{ $match->home_score }}</span>
                                            <span class="text-muted">:</span>
                                            <span class="{{ $match->away_team_id === $clubTeam?->id ? 'text-wine' : 'text-muted' }}">{{ $match->away_score }}</span>
                                        </div>
                                    @else
                                        <span class="font-condensed text-2xl font-black text-muted">vs</span>
                                    @endif

                                    <div class="flex items-center gap-2.5">
                                        <x-team-badge :team="$match->awayTeam" />
                                        <span class="font-condensed text-base font-black uppercase">{{ $match->awayTeam->name }}</span>
                                    </div>

                                    @if($past && $clubTeam && $match->involves($clubTeam))
                                        <span class="ml-2 rounded-full px-2.5 py-1 text-9 font-bold uppercase tracking-label {{ $clubWon ? 'bg-[rgba(34,197,94,0.15)] text-[#16a34a]' : 'bg-[rgba(220,38,38,0.12)] text-[#dc2626]' }}">
                                            {{ $clubWon ? 'VÍTĚZSTVÍ' : 'PROHRA' }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Akce --}}
                            <div class="flex flex-col items-center justify-center gap-2 border-l border-l-line p-5 max-mobile:!border-l-0 max-mobile:border-t max-mobile:border-t-line max-mobile:px-5 max-mobile:pb-5 max-mobile:pt-4">
                                @if(!$past && $match->ticket_url)
                                    <a
                                        class="inline-flex min-w-[130px] items-center justify-center rounded-lg bg-orange px-5 py-3 font-condensed text-sm font-black uppercase tracking-widest text-white no-underline shadow-[0_12px_24px_rgba(245,120,0,0.22)] transition-all hover:-translate-y-px hover:!bg-orange-hover hover:!text-white hover:shadow-[0_16px_32px_rgba(245,120,0,.42)] focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 max-mobile:w-full"
                                        href="{{ $match->ticket_url }}"
                                    >VSTUPENKY</a>
                                @elseif(!$past)
                                    <button
                                        aria-label="Vstupenky zatím nejsou k dispozici"
                                        class="inline-flex min-w-[130px] cursor-not-allowed items-center justify-center rounded-lg border border-wine/10 bg-paper-2 px-5 py-3 font-condensed text-sm font-black uppercase tracking-widest text-wine/35 shadow-none max-mobile:w-full"
                                        disabled
                                        type="button"
                                    >VSTUPENKY</button>
                                @elseif($past && $match->reportArticle)
                                    <a
                                        class="inline-flex min-w-[130px] items-center justify-center rounded-lg border-2 border-wine px-5 py-3 font-condensed text-sm font-black uppercase tracking-widest text-wine no-underline transition-all hover:bg-wine hover:text-white focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3 max-mobile:w-full"
                                        href="{{ $match->reportArticle->url }}"
                                    >REPORT</a>
                                @else
                                    <span class="min-w-[130px]"></span>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-line bg-white p-8 text-center text-sm text-muted">
                        Pro tuto sezónu zatím nejsou vložené žádné zápasy.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Tabulka soutěže --}}
    @if($selectedCompetition || $standings->isNotEmpty())
    <section class="border-t border-t-line bg-white py-16">
        <div class="w-shell mx-auto grid grid-cols-[1fr_1.5fr] items-start gap-12 max-mobile:!grid-cols-1">
            <div>
                <p class="mb-3 flex items-center gap-2.5 text-11 font-bold uppercase tracking-eyebrow text-wine">
                    <span class="block h-[3px] w-6 bg-current"></span>
                    {{ $selectedCompetition?->name ?? 'SOUTĚŽ' }}
                </p>
                <h2 class="mb-4 font-condensed text-section-lg font-black uppercase leading-none">
                    <x-highlighted-text :value="data_get($page->content, 'standings.heading')" :title="data_get($page->content, 'standings.title', 'AKTUÁLNÍ')" :accent="data_get($page->content, 'standings.accent', 'TABULKA')" accent-class="block not-italic text-orange" />
                </h2>
                <p class="mb-6 text-sm leading-relaxed text-ink/60">
                    {{ data_get($page->content, 'standings.text', 'Aktuální tabulka vybrané soutěže.') }}
                </p>
            </div>
            <x-standings-table :standings="$standings" :club-team="$clubTeam" />
        </div>
    </section>
    @endif
@endsection
