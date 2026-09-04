@extends('layouts.app')

@section('title', data_get($page?->content, 'seo.title', 'Kontakt') ?: 'Kontakt')

@section('seo')
    <x-seo-module :seo="$page?->content['seo'] ?? []" />
@endsection

@section('content')
    {{-- Hero sekce --}}
    <x-page-header
        breadcrumb="Kontakt"
        :eyebrow="data_get($page->content, 'hero.eyebrow', 'JSME TU PRO VÁS')"
        :title="data_get($page->content, 'hero.title', 'OZVĚTE')"
        :accent="data_get($page->content, 'hero.accent', 'SE NÁM.')"
        :heading="data_get($page->content, 'hero.heading')"
        :image="\App\Services\MediaService::getMediaUrl(data_get($page->content, 'hero.image'))"
        :locale="$page->lang_locale ?? null"
    />

    {{-- Kontaktní informace & formulář --}}
    <section class="bg-paper py-16">
        <div class="w-shell mx-auto grid grid-cols-[1fr_1.4fr] gap-12 max-mobile:!grid-cols-1 max-mobile:min-w-0">
            <div class="max-mobile:min-w-0">
                <p class="mb-3 flex items-center gap-2.5 text-11 font-bold uppercase tracking-eyebrow text-wine">
                    <span class="block h-[3px] w-6 bg-current"></span>
                    {{ data_get($page->content, 'contact.eyebrow', 'KONTAKTNÍ ÚDAJE') }}
                </p>
                <h2 class="mb-8 font-condensed text-56 font-black uppercase leading-none">
                    <x-highlighted-text :value="data_get($page->content, 'contact.heading')" :title="data_get($page->content, 'contact.title', 'UTB')" :accent="data_get($page->content, 'contact.accent', 'REDBRICKS')" />
                </h2>

                <div class="flex flex-col gap-6">
                    {{-- Obecné dotazy --}}
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-wine">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,12 2,6"></polyline>
                            </svg>
                        </div>
                        <div>
                            <span class="mb-1 block text-10 font-bold uppercase tracking-label text-muted">
                                {{ data_get($page->content, 'contact.general_label', 'OBECNÉ DOTAZY') }}
                            </span>
                            <a class="block text-sm font-semibold text-wine no-underline transition-colors hover:!text-orange" href="mailto:{{ data_get($contactDetails ?? [], 'email', 'info@utbhockey.cz') }}">
                                {{ data_get($contactDetails ?? [], 'email', 'info@utbhockey.cz') }}
                            </a>
                            <a class="mt-0.5 block text-sm font-semibold text-wine no-underline transition-colors hover:!text-orange" href="tel:{{ preg_replace('/\s+/', '', data_get($contactDetails ?? [], 'phone', '+420 777 123 456')) }}">
                                {{ data_get($contactDetails ?? [], 'phone', '+420 777 123 456') }}
                            </a>
                        </div>
                    </div>

                    {{-- Marketing & Partneři --}}
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-wine">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="mb-1 block text-10 font-bold uppercase tracking-label text-muted">
                                {{ data_get($page->content, 'contact.marketing_label', 'MARKETING & PARTNEŘI') }}
                            </span>
                            <a class="block text-sm font-semibold text-wine no-underline transition-colors hover:text-orange" href="mailto:{{ data_get($contactDetails ?? [], 'marketing', 'marketing@utbhockey.cz') }}">
                                {{ data_get($contactDetails ?? [], 'marketing', 'marketing@utbhockey.cz') }}
                            </a>
                        </div>
                    </div>

                    {{-- Domácí stadion --}}
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-wine">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"></path>
                                <circle cx="12" cy="9" r="2.5"></circle>
                            </svg>
                        </div>
                        <div>
                            <span class="mb-1 block text-10 font-bold uppercase tracking-label text-muted">
                                {{ data_get($page->content, 'contact.venue_label', 'DOMÁCÍ STADION') }}
                            </span>
                            <p class="m-0 text-sm font-semibold">{!! nl2br(e(data_get($contactDetails ?? [], 'address', "CCM Aréna\nBřeznická 4068\n760 01 Zlín"))) !!}</p>
                        </div>
                    </div>

                    {{-- Úřední hodiny --}}
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-wine">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div>
                            <span class="mb-1 block text-10 font-bold uppercase tracking-label text-muted">
                                {{ data_get($page->content, 'contact.hours_label', 'ÚŘEDNÍ HODINY') }}
                            </span>
                            <p class="m-0 text-sm">{{ data_get($contactDetails ?? [], 'hours', 'Po – Pa: 9:00 – 17:00') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kontaktní formulář --}}
            <livewire:contact-form />
        </div>
    </section>

    {{-- Mapa --}}
    <section class="relative h-[420px] overflow-hidden">
        <div
            aria-label="Mapa umístění CCM Arény ve Zlíně"
            class="relative z-0 h-full w-full"
            data-leaflet-map
            data-latitude="{{ data_get($page->content, 'map.latitude', 49.21677515339962) }}"
            data-longitude="{{ data_get($page->content, 'map.longitude', 17.66014925582014) }}"
            data-map-title="{{ data_get($page->content, 'map.title', 'CCM Aréna') }}"
            data-map-address="{{ data_get($page->content, 'map.address', 'Březnická 4068, Zlín') }}"
            data-map-link-label="{{ data_get($page->content, 'map.link_label', 'OTEVŘÍT V MAPÁCH') }}"
            data-map-link-url="{{ data_get($page->content, 'map.link_url', 'https://www.google.com/maps/search/?api=1&query=CCM+Arena+Zlin') }}"
            data-map-logo="{{ \App\Services\MediaService::getMediaUrl(\App\Models\Setting::get('header_logo_media_id')) }}"
        ></div>
        <noscript>
            <a class="absolute inset-0 z-10 flex items-center justify-center bg-wine p-6 text-center font-condensed text-lg font-black uppercase text-white" href="{{ data_get($page->content, 'map.link_url', 'https://www.google.com/maps/search/?api=1&query=CCM+Arena+Zlin') }}" rel="noopener noreferrer" target="_blank">
                {{ data_get($page->content, 'map.link_label', 'OTEVŘÍT V MAPÁCH') }}
            </a>
        </noscript>
    </section>

    {{-- FAQ sekce --}}
    @if(filled(data_get($page->content, 'faq.items')))
    <section class="bg-white py-20">
        <div class="w-shell mx-auto">
            <p class="mb-3 flex items-center gap-2.5 text-11 font-bold uppercase tracking-eyebrow text-wine">
                <span class="block h-[3px] w-6 bg-current"></span>
                {{ data_get($page->content, 'faq.eyebrow', 'NEJČASTĚJŠÍ DOTAZY') }}
            </p>
            <h2 class="mb-10 font-condensed text-section-md font-black uppercase leading-none">
                {{ data_get($page->content, 'faq.title', 'CO VÁS ZAJÍMÁ') }}
            </h2>

            <div class="grid items-start gap-4 md:grid-cols-2">
                @foreach(data_get($page->content, 'faq.items', []) as $faq)
                    <details data-faq class="overflow-hidden rounded-xl border-[1.5px] border-line bg-white">
                        <summary class="flex cursor-pointer select-none items-center justify-between gap-4 px-6 py-5 font-condensed text-17 font-black uppercase focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">
                            {{ data_get($faq, 'question') }}
                            <span data-faq-icon class="inline-flex h-5 w-5 shrink-0 items-center justify-center leading-none [transform-origin:50%_50%]">
                                <svg fill="none" height="14" stroke="currentColor" stroke-linecap="round" stroke-width="4.5" viewBox="0 0 24 24" width="14">
                                    <line x1="12" x2="12" y1="7" y2="17"></line>
                                    <line x1="7" x2="17" y1="12" y2="12"></line>
                                </svg>
                            </span>
                        </summary>
                        <p data-faq-answer class="m-0 overflow-hidden px-6 pb-5 text-sm leading-relaxed text-ink/70">
                            {{ data_get($faq, 'answer') }}
                        </p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>
    @endif
@endsection
