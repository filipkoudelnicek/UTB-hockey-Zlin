@extends('layouts.app')

@section('title', $code . ' — ' . $title)

@section('seo')
    <meta name="robots" content="noindex, nofollow">
@endsection

@section('content')
    <section class="relative overflow-hidden bg-paper py-20 max-mobile:py-14" aria-labelledby="error-title">
        <div class="w-shell mx-auto max-mobile:w-shell-mobile">
            <div class="relative overflow-hidden rounded-[1.7rem] border border-ink-css/8 bg-white px-8 py-14 shadow-[0_10px_24px_rgba(19,17,17,.05)] max-mobile:px-6 max-mobile:py-12">
                <span aria-hidden="true" class="pointer-events-none absolute -right-4 -top-11 font-condensed text-[clamp(10rem,24vw,19rem)] font-black leading-none tracking-[-.08em] text-wine/[.055]">
                    {{ $code }}
                </span>
                <div class="relative max-w-[42rem]">
                    <p class="mb-[1.1rem] mx-0 mt-0 inline-flex items-center gap-[0.8rem] text-[.7rem] font-extrabold uppercase tracking-micro text-orange-css">
                        <span class="block h-[0.16rem] w-[2.1rem] bg-current"></span>
                        {{ $title }}
                    </p>
                    <h1 id="error-title" class="m-0 font-condensed text-page-title font-black uppercase leading-95 tracking-[-.035em] text-wine">
                        Chyba {{ $code }}
                    </h1>
                    <p class="mb-0 mt-6 max-w-[36rem] text-[1.05rem] leading-170 text-ink-css/70">
                        {{ $message }}
                    </p>
                    <div class="mt-9 flex flex-wrap gap-4">
                        <a href="/" class="inline-flex items-center justify-center gap-3 rounded-lg bg-orange px-6 py-4 font-condensed text-[.86rem] font-black uppercase tracking-action text-white no-underline transition-all hover:-translate-y-px hover:bg-orange-hover hover:shadow-[0_16px_32px_rgba(245,120,0,.42)] focus-visible:outline focus-visible:outline-3 focus-visible:outline-wine focus-visible:outline-offset-3">
                            Na hlavní stránku <span class="text-[1.03rem] leading-none">›</span>
                        </a>
                        @if($showBack ?? false)
                            <button type="button" onclick="window.history.back()" class="cursor-pointer rounded-lg border border-wine/20 bg-white px-6 py-4 font-condensed text-[.86rem] font-black uppercase tracking-action text-wine transition-colors hover:border-wine hover:bg-wine hover:text-white focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">
                                Zpět
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
