@extends('layouts.app')

@section('title', data_get($page?->content, 'seo.title', 'O klubu') ?: 'O klubu')

@section('seo')
    <x-seo-module :seo="$page?->content['seo'] ?? []" />
@endsection

@section('content')
    {{-- Hero sekce --}}
    <x-page-header
        breadcrumb="O klubu"
        :eyebrow="data_get($page->content, 'hero.eyebrow', 'OD ROKU 2017')"
        :title="data_get($page->content, 'hero.title', 'VÍC NEŽ')"
        :accent="data_get($page->content, 'hero.accent', 'JEN HOKEJ.')"
        :heading="data_get($page->content, 'hero.heading')"
        :image="\App\Services\MediaService::getMediaUrl(data_get($page->content, 'hero.image'))"
        :locale="$page->lang_locale ?? null"
    />

    {{-- Příběh --}}
    @if(filled(data_get($page->content, 'story.eyebrow')) || filled(data_get($page->content, 'story.heading')) || filled(data_get($page->content, 'story.title')) || filled(data_get($page->content, 'story.accent')) || filled(data_get($page->content, 'story.lead')) || filled(data_get($page->content, 'story.text')) || \App\Services\MediaService::getMediaUrl(data_get($page->content, 'story.image')))
    <section class="bg-paper py-20">
        <div class="w-shell mx-auto">
            <div class="grid grid-cols-[1fr_1.4fr] items-start gap-12 max-mobile:!grid-cols-1">
                <div class="max-mobile:min-w-0">
                    <p class="mb-3 flex items-center gap-2.5 text-11 font-bold uppercase tracking-eyebrow text-wine">
                        <span class="block h-[3px] w-6 bg-current"></span>
                        {{ data_get($page->content, 'story.eyebrow', 'NÁŠ PŘÍBĚH') }}
                    </p>
                    <h2 class="mb-6 font-condensed text-section-lg font-black uppercase leading-none">
                        <x-highlighted-text :value="data_get($page->content, 'story.heading')" :title="data_get($page->content, 'story.title', 'ZE ZLÍNA.')" :accent="data_get($page->content, 'story.accent', 'PRO ZLÍN.')" accent-class="block not-italic text-orange" />
                    </h2>
                </div>
                <div class="max-mobile:min-w-0">
                    <p class="mb-5 text-lg font-semibold leading-160 text-ink/90">
                        {{ data_get($page->content, 'story.lead', 'UTB RedBricks vznikli z jednoduché myšlenky: dát studentům ve Zlíně klub, se kterým se mohou ztotožnit.') }}
                    </p>
                    <p class="text-base leading-relaxed text-ink/70">
                        {{ data_get($page->content, 'story.text', 'Od prvního tréninku jsme vyrostli v pevnou součást univerzitního života. Spojujeme studenty napříč fakultami, reprezentujeme UTB po celé republice a vytváříme komunitu, která žije hokejem i mimo stadion.') }}
                    </p>
                </div>
            </div>

            @if($storyImage = \App\Services\MediaService::getMediaUrl(data_get($page->content, 'story.image')))
                <div class="mt-12 h-[480px] overflow-hidden rounded-2xl">
                    <img
                        alt="Fanoušci na stadionu"
                        class="block h-full w-full max-w-full object-cover"
                        src="{{ $storyImage }}"
                    >
                </div>
            @endif
        </div>
    </section>
    @endif

    {{-- Milníky --}}
    @if(filled(data_get($page->content, 'milestones')))
    <section class="bg-ink py-16 text-white">
        <div class="w-shell mx-auto">
            <p class="mb-3 flex items-center gap-2.5 text-11 font-bold uppercase tracking-eyebrow text-orange">
                <span class="block h-[3px] w-6 bg-current"></span>
                {{ data_get($page->content, 'milestones_eyebrow', 'MILNÍKY') }}
            </p>
            <h2 class="mb-12 font-condensed text-section-lg font-black uppercase leading-none">
                {{ data_get($page->content, 'milestones_title', 'NAŠE CESTA') }}
            </h2>
            <div class="grid grid-cols-2 gap-8 border-t-2 border-t-orange-css/30 md:grid-cols-4 max-mobile:!grid-cols-2">
                @foreach(data_get($page->content, 'milestones', []) as $milestone)
                    <div class="pt-8 {{ !$loop->first ? 'md:border-l md:border-l-white/8 md:px-8' : 'md:pr-8' }} max-mobile:!pl-0 max-mobile:!pr-0">
                        <strong class="mb-1 block font-condensed text-48 font-black text-orange">
                            {{ data_get($milestone, 'year') }}
                        </strong>
                        <span class="mb-3 block text-10 font-bold uppercase tracking-label text-white/50">
                            {{ data_get($milestone, 'title') }}
                        </span>
                        <p class="text-sm leading-160 text-white/60">
                            {{ data_get($milestone, 'description') }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Hodnoty --}}
    @if(filled(data_get($page->content, 'values')))
    <section class="bg-paper py-20">
        <div class="w-shell mx-auto">
            <div class="mb-12 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="mb-3 flex items-center gap-2.5 text-11 font-bold uppercase tracking-eyebrow text-wine">
                        <span class="block h-[3px] w-6 bg-current"></span>
                        {{ data_get($page->content, 'values_eyebrow', 'CO NÁS DRŽÍ POHROMADĚ') }}
                    </p>
                    <h2 class="m-0 font-condensed text-section-md font-black uppercase leading-none">
                        {{ data_get($page->content, 'values_title', 'NAŠE HODNOTY') }}
                    </h2>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                @foreach(data_get($page->content, 'values', []) as $value)
                    <article class="rounded-2xl border border-line bg-white p-8">
                        <span class="mb-4 block font-condensed text-52 font-black leading-none text-orange-css/20">
                            {{ str_pad((string)$loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <h3 class="mb-4 font-condensed text-28 font-black uppercase leading-tight text-wine">
                            {{ data_get($value, 'title') }}
                        </h3>
                        <p class="text-15 leading-relaxed text-ink/70">
                            {{ data_get($value, 'text') }}
                        </p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Vedení klubu --}}
    @if(filled(data_get($page->content, 'leadership.people')))
    <section class="bg-white py-20" id="vedeni">
        <div class="w-shell mx-auto">
            <p class="mb-3 flex items-center gap-2.5 text-11 font-bold uppercase tracking-eyebrow text-wine">
                <span class="block h-[3px] w-6 bg-current"></span>
                {{ data_get($page->content, 'leadership.eyebrow', 'LIDÉ ZA TÝMEM') }}
            </p>
            <h2 class="mb-12 font-condensed text-section-md font-black uppercase leading-none">
                {{ data_get($page->content, 'leadership.title', 'VEDENÍ KLUBU') }}
            </h2>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-4">
                @foreach(data_get($page->content, 'leadership.people', []) as $person)
                    @php $personPhoto = \App\Services\MediaService::getMediaUrl(data_get($person, 'photo')); @endphp
                    <article class="group">
                        <div class="overflow-hidden rounded-2xl border border-line bg-white shadow-sm transition-all duration-300 group-hover:-translate-y-1 group-hover:shadow-lg">
                            <div class="aspect-[3/4] overflow-hidden">
                                @if($personPhoto)
                                    <img
                                        alt="{{ data_get($person, 'name') }}"
                                        class="block h-full w-full max-w-full object-cover object-top transition-transform duration-500 group-hover:scale-[1.03]"
                                        loading="lazy"
                                        src="{{ $personPhoto }}"
                                    >
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-paper-2 font-condensed text-60 font-black text-wine/20">
                                        {{ mb_substr((string) data_get($person, 'name'), 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="border-t border-t-line p-5">
                                <span class="mb-2 block text-10 font-bold uppercase tracking-stat text-muted">
                                    {{ data_get($person, 'position') }}
                                </span>
                                <h3 class="mb-3 font-condensed text-25 font-black uppercase leading-tight text-ink">
                                    {{ data_get($person, 'name') }}
                                </h3>
                                @if(data_get($person, 'email'))
                                    <a
                                        class="text-sm font-semibold text-orange no-underline transition-colors hover:text-wine"
                                        href="mailto:{{ data_get($person, 'email') }}"
                                    >{{ data_get($person, 'email') }}</a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif
@endsection
