@extends('layouts.app')

@section('title', data_get($page?->content, 'seo.title', 'Tým') ?: 'Tým')

@section('seo')
    <x-seo-module :seo="$page?->content['seo'] ?? []" />
@endsection

@section('content')
    {{-- Hero sekce --}}
    <x-page-header
        breadcrumb="Tým"
        :eyebrow="data_get($page->content, 'hero.eyebrow', 'A-TÝM') . ' ' . $competitionSeason?->name"
        :title="data_get($page->content, 'hero.title', 'NAŠE')"
        :accent="data_get($page->content, 'hero.accent', 'SESTAVA')"
        :heading="data_get($page->content, 'hero.heading')"
        :image="\App\Services\MediaService::getMediaUrl(data_get($page->content, 'hero.image'))"
        :locale="$page->lang_locale ?? null"
    />

    {{-- Sestava dle pozic --}}
    @if($players->isNotEmpty())
    <section class="bg-paper py-16">
        <div class="w-shell mx-auto">
            @php
                $sections = collect([
                    ['goalkeeper', '01', 'BRANKÁŘI', false],
                    ['defender', '02', 'OBRÁNCI', true],
                    ['forward', '03', 'ÚTOČNÍCI', true],
                ])->filter(fn (array $section) => $groups[$section[0]]->isNotEmpty())->values();
            @endphp

            @foreach($sections as [$key, $number, $title, $compact])
                <div class="flex items-center gap-5 {{ $loop->first ? 'mb-8' : 'mb-8 mt-16' }}">
                    <span class="font-condensed text-60 font-black leading-none text-orange-css/18">{{ $number }}</span>
                    <h2 class="m-0 font-condensed text-42 font-black uppercase leading-none text-wine">{{ $title }}</h2>
                    <div class="h-0.5 flex-1 [background:linear-gradient(90deg,#ede9e3,transparent)]"></div>
                </div>

                <div class="grid grid-cols-1 {{ $compact ? 'gap-5 sm:grid-cols-2 lg:grid-cols-4' : 'gap-6 md:grid-cols-3' }}">
                    @foreach($groups[$key] as $teamPlayer)
                        <x-player-card :player="$teamPlayer" :compact="$compact" />
                    @endforeach
                </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Vedení & Realizační tým CTA --}}
    @if(filled(data_get($page->content, 'leadership.eyebrow')) || filled(data_get($page->content, 'leadership.heading')) || filled(data_get($page->content, 'leadership.title')) || filled(data_get($page->content, 'leadership.accent')) || filled(data_get($page->content, 'leadership.cta_label')))
    <section class="py-16 text-white [background:linear-gradient(90deg,rgba(51,10,14,.95),rgba(82,18,24,.68)_58%,rgba(31,8,10,.22))]">
        <div class="w-shell mx-auto flex flex-wrap items-center justify-between gap-8">
            <div>
                <p class="mb-3 flex items-center gap-2.5 text-11 font-bold uppercase tracking-eyebrow text-orange">
                    <span class="block h-[3px] w-6 bg-current"></span>
                    {{ data_get($page->content, 'leadership.eyebrow', 'LIDÉ V ZÁZEMÍ') }}
                </p>
                <h2 class="m-0 font-condensed text-section-sm font-black uppercase leading-none">
                    <x-highlighted-text :value="data_get($page->content, 'leadership.heading')" :title="data_get($page->content, 'leadership.title', 'POZNEJTE VEDENÍ')" :accent="data_get($page->content, 'leadership.accent', 'A REALIZAČNÍ TÝM')" />
                </h2>
            </div>
            <a
                class="inline-flex w-fit items-center gap-2 self-center rounded-lg border-2 border-white/30 px-6 py-4 font-condensed text-[.86rem] font-black uppercase tracking-action text-white no-underline transition-all hover:-translate-y-px hover:bg-white/10 hover:shadow-[0_10px_24px_rgba(18,14,13,.14)]"
                href="{{ \App\Services\PageService::getRelativeUrlByType('about', $page->lang_locale ?? null, '/klub') }}#vedeni"
            >
                {{ data_get($page->content, 'leadership.cta_label', 'VEDENÍ KLUBU') }} <span class="font-condensed text-[1.03rem] leading-none">›</span>
            </a>
        </div>
    </section>
    @endif
@endsection
