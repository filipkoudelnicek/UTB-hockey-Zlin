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
        <div><p class="mb-5 mt-0 font-condensed text-13 font-black uppercase tracking-label text-orange">RYCHLÉ ODKAZY</p><div class="flex flex-col gap-3">@foreach (array_slice($navItems, 0, 3) as $item)<a href="{{ $item['url'] }}" class="w-fit text-sm text-white/65 no-underline transition-colors duration-200 hover:!text-orange focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">{{ $item['label'] }}</a>@endforeach</div></div>
        <div><p class="mb-5 mt-0 font-condensed text-13 font-black uppercase tracking-label text-orange">KLUB</p><div class="flex flex-col gap-3">@foreach (array_slice($navItems, 3, 3) as $item)<a href="{{ $item['url'] }}" class="w-fit text-sm text-white/65 no-underline transition-colors duration-200 hover:!text-orange focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">{{ $item['label'] }}</a>@endforeach</div></div>
        <div class="max-small:col-span-2"><p class="mb-5 mt-0 font-condensed text-13 font-black uppercase tracking-label text-orange">NAPIŠTE NÁM</p><a href="mailto:{{ \App\Models\Setting::get('site_email', 'info@utbhockey.cz') }}" class="mb-2 block w-fit text-sm text-white/65 no-underline transition-colors duration-200 hover:!text-orange focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">{{ \App\Models\Setting::get('site_email', 'info@utbhockey.cz') }}</a><span class="text-13 leading-160 text-white/40">{!! nl2br(e(\App\Models\Setting::get('site_address', 'CCM Aréna, Březnická 4068\n760 01 Zlín'))) !!}</span>
            @php($footerSocials = ['Facebook' => \App\Models\Setting::get('social_facebook'), 'Instagram' => \App\Models\Setting::get('social_instagram')])
            @if(filled($footerSocials['Facebook']) || filled($footerSocials['Instagram']))
                <div class="mt-4 flex items-center gap-3">
                    @if(filled($footerSocials['Facebook']))
                        <a href="{{ $footerSocials['Facebook'] }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="text-white/65 transition-colors duration-200 hover:!text-orange focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                        </a>
                    @endif
                    @if(filled($footerSocials['Instagram']))
                        <a href="{{ $footerSocials['Instagram'] }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="text-white/65 transition-colors duration-200 hover:!text-orange focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
    <div class="border-t border-white/10"><div class="w-shell mx-auto flex items-center justify-between py-[18px] font-sans text-10 font-bold uppercase tracking-stat text-white/35"><span>© {{ date('Y') }} UTB REDBRICKS. VŠECHNA PRÁVA VYHRAZENA.</span><a href="#" class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap text-white/35 no-underline transition-colors duration-200 hover:!text-orange focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">NAHORU <span class="text-base leading-none">↑</span></a></div></div>
</footer>
