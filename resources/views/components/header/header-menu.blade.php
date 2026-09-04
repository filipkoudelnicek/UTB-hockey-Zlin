<div class="bg-wine font-sans text-11 font-bold leading-9 tracking-[.13em] text-white max-mobile:hidden">
    <div class="w-shell mx-auto flex justify-between">
        <span>UNIVERZITNÍ HOKEJOVÝ KLUB ZLÍN</span>
        <a href="{{ $contactUrl }}" class="text-orange no-underline transition-colors duration-200 hover:!text-white focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">Kontaktujte nás</a>
    </div>
</div>
<header data-site-header id="top" class="sticky top-0 z-50 h-[88px] bg-white shadow-[0_1px_0_#e5e0da]">
    <div class="w-shell mx-auto flex h-full items-center gap-10">
        <a href="{{ $homepageUrl }}" class="flex items-center gap-2 font-condensed text-17 font-extrabold leading-70 tracking-[.05em] text-wine no-underline focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">
            @if($headerLogo = \App\Services\MediaService::getMediaUrl(\App\Models\Setting::get('header_logo_media_id')))
                <img src="{{ $headerLogo }}" alt="" width="54" height="62" fetchpriority="high" class="h-[62px] w-[54px] object-contain">
            @endif
            <span>UTB<br><b class="text-[22px]">REDBRICKS</b></span>
        </a>
        <button type="button" data-menu-toggle aria-expanded="false" aria-label="Otevřít navigaci" class="ml-auto hidden cursor-pointer flex-col gap-[5px] border-0 bg-transparent p-2.5 max-mobile:flex focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">
            <span class="block h-0.5 w-[25px] bg-wine"></span><span class="block h-0.5 w-[25px] bg-wine"></span><span class="block h-0.5 w-[25px] bg-wine"></span>
        </button>
        <nav data-site-nav class="ml-auto flex gap-[26px] max-mobile:absolute max-mobile:left-0 max-mobile:right-0 max-mobile:top-[88px] max-mobile:z-[100] max-mobile:!ml-0 max-mobile:hidden max-mobile:flex-col max-mobile:!gap-0 max-mobile:items-stretch max-mobile:border-t max-mobile:border-control-line max-mobile:bg-white max-mobile:pt-2 max-mobile:pb-[1.2rem] max-mobile:shadow-[0_10px_28px_rgba(0,0,0,.14)]" aria-label="Hlavní navigace">
            @foreach ($navItems as $item)
                <a href="{{ $item['url'] }}" target="{{ $item['target'] ?? '_self' }}" @if($item['active'] ?? false) aria-current="page" @endif class="relative font-condensed text-sm font-bold uppercase tracking-nav text-nav-ink no-underline transition-colors duration-200 hover:!text-wine after:absolute after:bottom-[-.7rem] after:left-0 after:h-[.18rem] after:w-0 after:bg-orange after:content-[''] after:transition-[width] after:duration-200 hover:after:w-full {{ ($item['active'] ?? false) ? 'after:!w-full' : '' }} max-mobile:border-b max-mobile:border-nav-divider max-mobile:px-6 max-mobile:py-[.9rem] max-mobile:text-base max-mobile:after:hidden focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">{{ $item['label'] }}</a>
            @endforeach
            <a href="{{ $matchesUrl }}" class="hidden items-center justify-center gap-2 bg-orange px-6 py-[.9rem] font-condensed text-base font-black uppercase tracking-nav text-white no-underline transition-all duration-200 hover:-translate-y-px hover:bg-orange-hover hover:shadow-[0_16px_32px_rgba(245,120,0,.42)] max-mobile:flex focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">Vstupenky <span class="text-[1.03rem] leading-none">›</span></a>
        </nav>
        <a href="{{ $matchesUrl }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-orange px-[18px] py-2 font-condensed text-13 font-extrabold uppercase tracking-nav text-white no-underline shadow-[0_12px_24px_rgba(245,120,0,.22)] transition-all duration-200 hover:-translate-y-px hover:bg-orange-hover hover:shadow-[0_16px_32px_rgba(245,120,0,.42)] max-mobile:hidden focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">VSTUPENKY <span class="ml-2 text-sm">›</span></a>
    </div>
</header>
