@extends('layouts.app')

@section('seo')
    <x-seo-module :seo="$page?->content['seo'] ?? []" :default-title="$page?->title" />
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
                    @if(filled(data_get($page->content, 'contact.general_label')) && (filled(data_get($contactDetails ?? [], 'email')) || filled(data_get($contactDetails ?? [], 'phone'))))
                    <div class="group relative flex w-fit items-start gap-4">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-wine transition-colors group-hover:bg-orange">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,12 2,6"></polyline>
                            </svg>
                        </div>
                        <div>
                            <span class="mb-1 block text-10 font-bold uppercase tracking-label text-muted">
                                {{ data_get($page->content, 'contact.general_label') }}
                            </span>
                            @if(filled(data_get($contactDetails ?? [], 'email')))
                                <a class="block text-sm font-semibold text-wine no-underline transition-colors group-hover:!text-orange after:absolute after:inset-0 after:content-['']" href="mailto:{{ data_get($contactDetails ?? [], 'email') }}">{{ data_get($contactDetails ?? [], 'email') }}</a>
                            @endif
                            @if(filled(data_get($contactDetails ?? [], 'phone')))
                                <a class="relative z-10 mt-0.5 block w-fit text-sm font-semibold text-wine no-underline transition-colors hover:!text-orange" href="tel:{{ preg_replace('/\s+/', '', data_get($contactDetails ?? [], 'phone')) }}">{{ data_get($contactDetails ?? [], 'phone') }}</a>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Marketing & Partneři --}}
                    @if(filled(data_get($page->content, 'contact.marketing_label')) && filled(data_get($contactDetails ?? [], 'marketing')))
                    <div class="group relative flex w-fit items-start gap-4">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-wine transition-colors group-hover:bg-orange">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3 11 18-5v12L3 14v-3z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.6 16.8a3 3 0 1 1-5.8-1.6"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="mb-1 block text-10 font-bold uppercase tracking-label text-muted">
                                {{ data_get($page->content, 'contact.marketing_label') }}
                            </span>
                            <a class="block text-sm font-semibold text-wine no-underline transition-colors group-hover:text-orange after:absolute after:inset-0 after:content-['']" href="mailto:{{ data_get($contactDetails ?? [], 'marketing') }}">{{ data_get($contactDetails ?? [], 'marketing') }}</a>
                        </div>
                    </div>
                    @endif

                    {{-- Domácí stadion --}}
                    @if(filled(data_get($page->content, 'contact.venue_label')) && filled(data_get($contactDetails ?? [], 'address')))
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-wine">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"></path>
                                <circle cx="12" cy="9" r="2.5"></circle>
                            </svg>
                        </div>
                        <div>
                            <span class="mb-1 block text-10 font-bold uppercase tracking-label text-muted">
                                {{ data_get($page->content, 'contact.venue_label') }}
                            </span>
                            <p class="m-0 text-sm font-semibold">{!! nl2br(e(data_get($contactDetails ?? [], 'address'))) !!}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Úřední hodiny --}}
                    @if(filled(data_get($page->content, 'contact.hours_label')) && filled(data_get($contactDetails ?? [], 'hours')))
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-wine">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div>
                            <span class="mb-1 block text-10 font-bold uppercase tracking-label text-muted">
                                {{ data_get($page->content, 'contact.hours_label') }}
                            </span>
                            <p class="m-0 text-sm">{!! nl2br(e(data_get($contactDetails ?? [], 'hours'))) !!}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Facebook --}}
                    @if(filled(data_get($page->content, 'contact.facebook_label')) && filled(data_get($contactDetails ?? [], 'facebook')))
                    <div class="group relative flex w-fit items-start gap-4">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-wine transition-colors group-hover:bg-orange">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="mb-1 block text-10 font-bold uppercase tracking-label text-muted">{{ data_get($page->content, 'contact.facebook_label') }}</span>
                            <a class="block text-sm font-semibold text-wine no-underline transition-colors group-hover:text-orange after:absolute after:inset-0 after:content-['']" href="{{ data_get($contactDetails ?? [], 'facebook') }}" target="_blank" rel="noopener noreferrer">Facebook</a>
                        </div>
                    </div>
                    @endif

                    {{-- Instagram --}}
                    @if(filled(data_get($page->content, 'contact.instagram_label')) && filled(data_get($contactDetails ?? [], 'instagram')))
                    <div class="group relative flex w-fit items-start gap-4">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-wine transition-colors group-hover:bg-orange">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                            </svg>
                        </div>
                        <div>
                            <span class="mb-1 block text-10 font-bold uppercase tracking-label text-muted">{{ data_get($page->content, 'contact.instagram_label') }}</span>
                            <a class="block text-sm font-semibold text-wine no-underline transition-colors group-hover:text-orange after:absolute after:inset-0 after:content-['']" href="{{ data_get($contactDetails ?? [], 'instagram') }}" target="_blank" rel="noopener noreferrer">Instagram</a>
                        </div>
                    </div>
                    @endif
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
