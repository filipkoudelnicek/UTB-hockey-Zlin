@props([
    'title',
    'accent' => null,
    'heading' => null,
    'eyebrow' => null,
    'breadcrumb' => null,
    'image' => null,
    'locale' => null,
])

<section class="relative flex h-[455px] items-end overflow-hidden bg-wine text-white">
    <img
        aria-hidden="true"
        class="absolute inset-0 h-full w-full object-cover object-center"
        src="{{ $image ?: asset('assets/obrazky/header.webp') }}"
        alt=""
        loading="eager"
        fetchpriority="high"
    >
    <div class="absolute inset-0 [background:linear-gradient(90deg,rgba(51,10,14,.95),rgba(82,18,24,.68)_58%,rgba(31,8,10,.22))]"></div>
    <div class="relative z-[2] w-shell mx-auto pb-16">
        @if($breadcrumb)
            <nav class="mb-10 flex items-center gap-2 text-10 font-bold uppercase tracking-label text-white/65">
                <a
                    class="text-orange no-underline transition-colors hover:text-white focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3"
                    href="{{ \App\Services\PageService::getRelativeUrlByType('homepage', $locale, '/') }}"
                >Domů</a>
                <span>/</span>
                <span>{{ $breadcrumb }}</span>
            </nav>
        @endif

        @if($eyebrow)
            <p class="mb-4 flex items-center gap-2.5 text-11 font-bold uppercase tracking-eyebrow text-orange">
                <span class="block h-[3px] w-6 bg-current"></span>
                {{ $eyebrow }}
            </p>
        @endif

        <h1 class="m-0 font-condensed text-page-title font-black uppercase leading-95">
            <x-highlighted-text :value="$heading" :title="$title" :accent="$accent" accent-class="block not-italic text-orange" />
        </h1>
    </div>
</section>
