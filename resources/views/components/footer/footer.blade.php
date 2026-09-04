<footer class="bg-ink text-white">
    <div class="w-shell mx-auto grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-10 pt-16 pb-12 max-small:grid-cols-2 max-small:gap-x-5 max-small:gap-y-8">
        <div class="max-small:col-span-2">
            <a href="{{ $homepageUrl }}" class="mb-5 inline-flex w-fit items-center gap-2 font-condensed text-17 font-extrabold leading-70 text-white no-underline transition-colors duration-200 hover:!text-orange focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">
                @if($footerLogo = \App\Services\MediaService::getMediaUrl(\App\Models\Setting::get('footer_logo_media_id')))
                    <img src="{{ $footerLogo }}" alt="" width="64" height="75" class="h-[75px] w-16 object-contain">
                @endif
                <span>UTB<br><b class="text-[22px]">REDBRICKS</b></span>
            </a>
            <p class="mt-3 mb-0 text-sm leading-160 text-white/55">Hokejový klub Univerzity Tomáše Bati ve Zlíně.</p>
        </div>
        <div><h4 class="mb-5 mt-0 font-condensed text-13 font-black uppercase tracking-label text-orange">RYCHLÉ ODKAZY</h4><div class="flex flex-col gap-3">@foreach (array_slice($navItems, 0, 3) as $item)<a href="{{ $item['url'] }}" class="w-fit text-sm text-white/65 no-underline transition-colors duration-200 hover:!text-orange focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">{{ $item['label'] }}</a>@endforeach</div></div>
        <div><h4 class="mb-5 mt-0 font-condensed text-13 font-black uppercase tracking-label text-orange">KLUB</h4><div class="flex flex-col gap-3">@foreach (array_slice($navItems, 3, 3) as $item)<a href="{{ $item['url'] }}" class="w-fit text-sm text-white/65 no-underline transition-colors duration-200 hover:!text-orange focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">{{ $item['label'] }}</a>@endforeach</div></div>
        <div class="max-small:col-span-2"><h4 class="mb-5 mt-0 font-condensed text-13 font-black uppercase tracking-label text-orange">NAPIŠTE NÁM</h4><a href="mailto:{{ \App\Models\Setting::get('site_email', 'info@utbhockey.cz') }}" class="mb-2 block w-fit text-sm text-white/65 no-underline transition-colors duration-200 hover:!text-orange focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">{{ \App\Models\Setting::get('site_email', 'info@utbhockey.cz') }}</a><span class="text-13 leading-160 text-white/40">{!! nl2br(e(\App\Models\Setting::get('site_address', 'CCM Aréna, Březnická 4068\n760 01 Zlín'))) !!}</span></div>
    </div>
    <div class="border-t border-white/10"><div class="w-shell mx-auto flex items-center justify-between py-[18px] font-sans text-10 font-bold uppercase tracking-stat text-white/35"><span>© {{ date('Y') }} UTB REDBRICKS. VŠECHNA PRÁVA VYHRAZENA.</span><a href="#" class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap text-white/35 no-underline transition-colors duration-200 hover:!text-orange focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">NAHORU <span class="text-base leading-none">↑</span></a></div></div>
</footer>
