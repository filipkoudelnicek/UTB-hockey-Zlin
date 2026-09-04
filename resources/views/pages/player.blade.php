@extends('layouts.app')

@section('title', $player->full_name)

@section('seo')
    @if($player->seo_description)
        <meta name="description" content="{{ $player->seo_description }}">
    @endif
    @if($player->seo_og_image_url)
        <meta property="og:image" content="{{ $player->seo_og_image_url }}">
    @endif
@endsection

@section('content')
    @php
        $portrait = $player->portrait_url ?: asset('assets/obrazky/player.webp');
        $heading = $player->profile_heading ?: 'SRDCAŘ A LÍDR.';
        $parts = preg_split('/\s+/', trim($heading));
        $last = array_pop($parts);
        $first = implode(' ', $parts);
    @endphp

    {{-- Hero profil hráče --}}
    <section class="relative overflow-hidden text-white [background:linear-gradient(150deg,#1a0608_0%,#3d0a0e_50%,#6a1b21_100%)]">
        <div class="relative z-[2] w-shell mx-auto pb-0 pt-8">
            <nav class="mb-8 flex items-center gap-2 text-10 font-bold uppercase tracking-label text-white/55">
                <a class="text-orange no-underline hover:text-white" href="{{ \App\Services\PageService::getRelativeUrlByType('homepage', null, '/') }}">Domů</a>
                <span>/</span>
                <a class="text-orange no-underline hover:text-white" href="{{ \App\Services\PageService::getRelativeUrlByType('team', null, '/tym') }}">Tým</a>
                <span>/</span>
                <span>{{ $player->full_name }}</span>
            </nav>

            <div class="grid grid-cols-[1fr_auto_1fr] items-end gap-8 max-mobile:!grid-cols-1 max-mobile:!items-stretch max-mobile:!gap-4">
                {{-- Jméno a role --}}
                <div class="pb-10 max-mobile:!pb-0 max-mobile:[order:1]">
                    <p class="mb-4 flex items-center gap-2.5 text-11 font-bold uppercase tracking-eyebrow text-orange">
                        <span class="block h-[3px] w-6 bg-current"></span>
                        {{ $player->captain_role?->value === 'captain' ? 'KAPITÁN · ' : ($player->captain_role?->value === 'assistant' ? 'ASISTENT · ' : '') }}
                        {{ mb_strtoupper($player->position?->label() ?? '') }}
                    </p>

                    <div class="relative mb-4 inline-block">
                        <strong class="block font-condensed text-180 font-black leading-82 text-white/7">
                            {{ $player->jersey_number }}
                        </strong>
                    </div>

                    <h1 class="-mt-8 m-0 font-condensed text-article-title font-black uppercase leading-88">
                        {{ mb_strtoupper($player->first_name) }}<br>
                        <em class="not-italic text-orange">{{ mb_strtoupper($player->last_name) }}</em>
                    </h1>
                </div>

                {{-- Portrét hráče --}}
                <div class="flex w-[330px] flex-col items-center justify-end self-end max-mobile:!w-full max-mobile:[order:2]">
                    <div class="relative flex h-[455px] w-full items-end justify-center max-mobile:!h-80">
                        <div class="absolute inset-x-0 bottom-0 mx-auto h-[250px] w-[250px] rounded-full [background:radial-gradient(circle,rgba(245,120,0,.18)_0%,rgba(245,120,0,0)_70%)]"></div>
                        <img
                            alt="{{ $player->full_name }}"
                            class="relative z-10 block h-full w-full max-w-full object-contain object-bottom [filter:drop-shadow(0_18px_28px_rgba(0,0,0,.28))]"
                            src="{{ $portrait }}"
                            loading="eager"
                            fetchpriority="high"
                        >
                    </div>
                </div>

                {{-- Parametry hráče --}}
                <dl class="grid grid-cols-2 gap-0 border-l border-l-white/12 pb-10 pl-8 max-mobile:!border-l-0 max-mobile:!p-0 max-mobile:border-t max-mobile:border-t-white/12 max-mobile:[order:3]">
                    @foreach([
                        ['NAROZEN', $player->date_of_birth?->format('d. m. Y') ?: '—'],
                        ['HŮL', match ($player->stick_side) { 'left' => 'Levá', 'right' => 'Pravá', default => '—' }],
                        ['VÝŠKA', $player->height ? $player->height . ' cm' : '—'],
                        ['VÁHA', $player->weight ? $player->weight . ' kg' : '—'],
                        ['POZICE', $player->position?->label() ?? '—'],
                        ['FAKULTA', $player->faculty ?: '—'],
                    ] as [$label, $value])
                        <div class="py-4 {{ $loop->index % 2 === 0 ? 'pr-4' : 'pl-4' }} {{ $loop->index < 4 ? 'border-b border-b-white/10' : '' }}">
                            <dt class="mb-1 text-9 font-bold uppercase tracking-label text-white/45">{{ $label }}</dt>
                            <dd class="m-0 font-condensed text-lg font-black text-white">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </section>

    {{-- Profil hráče --}}
    <section class="bg-paper py-16">
        <div class="w-shell mx-auto grid grid-cols-[1fr_1.6fr] gap-12 max-mobile:!grid-cols-1">
            <div class="max-mobile:min-w-0 max-mobile:[order:1]">
                <p class="mb-3 flex items-center gap-2.5 text-11 font-bold uppercase tracking-eyebrow text-wine">
                    <span class="block h-[3px] w-6 bg-current"></span>
                    PROFIL HRÁČE
                </p>
                <h2 class="m-0 font-condensed text-section-md font-black uppercase leading-none">
                    {{ mb_strtoupper($first) }}<br>
                    <em class="not-italic text-orange">{{ mb_strtoupper($last) }}</em>
                </h2>
            </div>

            <div class="max-mobile:min-w-0 max-mobile:[order:2]">
                <div class="prose mb-5 max-w-none text-lg font-semibold leading-relaxed text-ink/90">
                    {!! $player->bio ?: 'Profil hráče bude brzy doplněn.' !!}
                </div>

                @if($player->quote)
                    <blockquote class="m-0 rounded-xl border-l-4 border-l-orange bg-white p-5">
                        <p class="text-17 m-0 italic leading-relaxed text-wine-dark">„{{ $player->quote }}“</p>
                        <cite class="mt-2 block text-10 font-bold uppercase not-italic tracking-label text-muted">
                            {{ $player->quote_attribution ?: $player->full_name }}
                        </cite>
                    </blockquote>
                @endif

                @if($player->video_url)
                    <a
                        href="{{ $player->video_url }}"
                        target="_blank"
                        rel="noopener"
                        class="group relative mt-8 flex aspect-video items-center justify-center overflow-hidden rounded-2xl bg-ink"
                    >
                        <img
                            alt="Video profil {{ $player->full_name }}"
                            class="absolute inset-0 block h-full w-full max-w-full object-cover opacity-50"
                            src="{{ $portrait }}"
                        >
                        <div class="absolute inset-0 bg-ink/58"></div>
                        <span class="relative z-10 flex h-20 w-20 items-center justify-center rounded-full bg-orange transition-all group-hover:bg-orange-hover">
                            <svg class="ml-1 h-8 w-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"></path>
                            </svg>
                        </span>
                        <div class="absolute bottom-5 left-5 z-10 text-white">
                            <p class="mb-0.5 font-condensed text-xl font-black uppercase">{{ $player->full_name }} – Představení</p>
                            <p class="text-sm text-white/60">UTB RedBricks · {{ $competitionSeason?->name }}</p>
                        </div>
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- Statistiky --}}
    <section class="bg-ink py-14 text-white">
        <div class="w-shell mx-auto">
            <div class="mb-10 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="mb-2 flex items-center gap-2.5 text-11 font-bold uppercase tracking-eyebrow text-orange">
                        <span class="block h-[3px] w-6 bg-current"></span>
                        AKTUÁLNÍ ROČNÍK
                    </p>
                    <h2 class="m-0 font-condensed text-section-sm font-black uppercase leading-none">
                        STATISTIKY
                    </h2>
                </div>
                <span class="inline-block rounded-full border border-white/20 px-4 py-2 text-11 font-bold uppercase tracking-label text-white/65">
                    {{ $competitionSeason?->name }}
                </span>
            </div>

            <div class="grid grid-cols-2 border-t border-t-white/8 md:grid-cols-5 max-mobile:!grid-cols-2">
                @foreach([
                    ['games', 'ZÁPASY'],
                    ['goals', 'GÓLY'],
                    ['assists', 'ASISTENCE'],
                    ['points', 'BODŮ'],
                    ['plus_minus', '+/-'],
                ] as [$key, $label])
                    <div class="px-6 py-6 {{ !$loop->last ? 'border-r border-r-white/8' : '' }} max-mobile:!px-3 max-mobile:!py-5 max-mobile:border-b max-mobile:border-b-white/8">
                        <strong class="block font-condensed text-56 font-black leading-none {{ $key === 'plus_minus' && $stats[$key] >= 0 ? 'text-[#4ade80]' : 'text-orange' }}">
                            {{ $key === 'plus_minus' && $stats[$key] > 0 ? '+' : '' }}{{ $stats[$key] }}
                        </strong>
                        <span class="mt-1 block text-10 font-bold uppercase tracking-label text-white/45">
                            {{ $label }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Další hráči --}}
    @if($otherPlayers->isNotEmpty())
    <section class="border-t border-t-line bg-white py-16">
        <div class="w-shell mx-auto">
            <div class="mb-8 flex items-end justify-between">
                <div>
                    <p class="mb-2 flex items-center gap-2.5 text-11 font-bold uppercase tracking-eyebrow text-wine">
                        <span class="block h-[3px] w-6 bg-current"></span>
                        DALŠÍ HRÁČI
                    </p>
                    <h2 class="m-0 font-condensed text-section-sm font-black uppercase leading-none">
                        POZNEJTE TÝM
                    </h2>
                </div>
                <a
                    class="inline-flex items-center gap-2 border-b-2 border-b-wine pb-[0.15rem] font-condensed text-[.8rem] font-black uppercase tracking-label text-wine transition-all hover:gap-3 hover:border-orange-css hover:text-orange-css"
                    href="{{ \App\Services\PageService::getRelativeUrlByType('team', null, '/tym') }}"
                >
                    CELÝ TÝM <span class="font-condensed text-[1.03rem] leading-none">›</span>
                </a>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($otherPlayers->take(3) as $other)
                    <x-player-card :player="$other" :compact="true" />
                @endforeach
            </div>
        </div>
    </section>
    @endif
@endsection
