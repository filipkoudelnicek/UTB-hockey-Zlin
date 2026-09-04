<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Oficiální web UTB RedBricks se připravuje.">
    <meta name="robots" content="noindex, nofollow">
    <title>UTB RedBricks | Připravujeme</title>
    <link rel="preload" href="{{ asset('assets/fonts/barlow-condensed-800-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('assets/fonts/barlow-condensed-900-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('assets/fonts/barlow-condensed-900-latin-ext.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('assets/fonts/inter-latin-variable.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('assets/fonts/inter-latin-ext-variable.woff2') }}" as="font" type="font/woff2" crossorigin>
    <style>
        @font-face { font-family: 'Barlow Condensed'; font-style: normal; font-weight: 800; font-display: swap; src: url('{{ asset('assets/fonts/barlow-condensed-800-latin.woff2') }}') format('woff2'); }
        @font-face { font-family: 'Barlow Condensed'; font-style: normal; font-weight: 900; font-display: swap; src: url('{{ asset('assets/fonts/barlow-condensed-900-latin.woff2') }}') format('woff2'); unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD; }
        @font-face { font-family: 'Barlow Condensed'; font-style: normal; font-weight: 900; font-display: swap; src: url('{{ asset('assets/fonts/barlow-condensed-900-latin-ext.woff2') }}') format('woff2'); unicode-range: U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF; }
        @font-face { font-family: 'Inter'; font-style: normal; font-weight: 400 700; font-display: swap; src: url('{{ asset('assets/fonts/inter-latin-variable.woff2') }}') format('woff2'); unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD; }
        @font-face { font-family: 'Inter'; font-style: normal; font-weight: 400 700; font-display: swap; src: url('{{ asset('assets/fonts/inter-latin-ext-variable.woff2') }}') format('woff2'); unicode-range: U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF; }

        :root { --wine: #6a1b21; --orange: #f57800; --white: #fff; }
        * { box-sizing: border-box; }
        body { min-width: 320px; height: 100vh; margin: 0; overflow: hidden; color: var(--white); font-family: 'Inter', sans-serif; background: #211312; }
        .landing { position: relative; display: grid; min-height: 100vh; overflow: hidden; isolation: isolate; background: var(--wine); }
        .landing::before { position: absolute; z-index: -3; inset: 0; content: ''; background: url('{{ asset('assets/obrazky/maintenance.webp') }}') center / cover no-repeat; }
        .landing::after { position: absolute; z-index: -2; inset: 0; content: ''; background: linear-gradient(90deg, rgba(50, 7, 11, .97) 0%, rgba(83, 19, 23, .91) 37%, rgba(88, 25, 26, .6) 59%, rgba(17, 12, 11, .32) 100%), linear-gradient(180deg, rgba(15, 8, 8, .18) 0%, rgba(15, 8, 8, .65) 100%); }
        .content { display: flex; width: min(1170px, calc(100% - 48px)); height: 100vh; margin: 0 auto; padding: 44px 0 52px; flex-direction: column; justify-content: space-between; gap: 64px; }
        .brand { display: inline-flex; width: fit-content; align-items: center; gap: 12px; color: var(--orange); font-family: 'Barlow Condensed', sans-serif; font-size: 20px; font-weight: 800; line-height: .75; letter-spacing: .05em; text-decoration: none; }
        .brand img { width: 90px; height: 90px; object-fit: contain; background-color: white; border-radius: 50px; padding: 10px;}
        .brand b { font-size: 26px; }
        .message { max-width: 690px; }
        .eyebrow { display: flex; align-items: center; gap: 13px; margin: 0 0 18px; color: var(--orange); font-size: 11px; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; }
        .eyebrow::before { width: 34px; height: 3px; content: ''; background: currentColor; }
        h1 { max-width: 650px; margin: 0; font-family: 'Barlow Condensed', sans-serif; font-size: clamp(72px, 10vw, 144px); font-weight: 900; line-height: .9; text-transform: uppercase; }
        h1 span { color: var(--orange); }
        .intro { max-width: 460px; margin: 28px 0 0; color: rgba(255, 255, 255, .82); font-size: 17px; line-height: 1.65; }
        .socials { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 32px; }
        .social-link { display: inline-flex; min-height: 52px; align-items: center; gap: 10px; padding: 12px 18px; border: 1px solid rgba(255, 255, 255, .35); border-radius: 8px; color: var(--white); font-family: 'Barlow Condensed', sans-serif; font-size: 14px; font-weight: 900; letter-spacing: .13em; text-decoration: none; text-transform: uppercase; transition: transform .2s ease, box-shadow .2s ease, background .2s ease, color .2s ease; }
        .social-link--instagram:hover, .social-link--instagram:focus-visible { border-color: #d6295a; outline: none; background: #d6295a; transform: translateY(-2px); box-shadow: 0 8px 18px rgba(214, 41, 90, .28); }
        .social-link--facebook:hover, .social-link--facebook:focus-visible { border-color: #1877f2; outline: none; background: #1877f2; transform: translateY(-2px); box-shadow: 0 8px 18px rgba(24, 119, 242, .28); }
        .social-link svg { width: 20px; height: 20px; fill: currentColor; }
        .social-link .instagram-icon { fill: none; stroke: currentColor; stroke-width: 2; }
        .footer { display: flex; align-items: flex-end; justify-content: space-between; gap: 24px; padding-top: 24px; border-top: 1px solid rgba(255, 255, 255, .23); color: rgba(255, 255, 255, .58); font-size: 11px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }
        .footer strong { color: var(--orange); }
        @media (max-width: 760px) { .landing::before { background-position: 61% center; } .landing::after { background: linear-gradient(180deg, rgba(50, 7, 11, .75) 0%, rgba(72, 14, 18, .88) 46%, rgba(39, 8, 11, .97) 100%), linear-gradient(90deg, rgba(70, 14, 17, .78), rgba(70, 14, 17, .36)); } .content { width: min(100% - 28px, 1170px); padding: 28px 0 32px; gap: 52px; } .brand { font-size: 17px; } .brand img { width: 52px; height: 52px; } .brand b { font-size: 22px; } h1 { font-size: clamp(44px, 13vw, 64px); } .intro { margin-top: 24px; font-size: 15px; } .socials { margin-top: 28px; } .social-link { flex: 1 1 145px; justify-content: center; } .footer { align-items: flex-start; flex-direction: column; gap: 8px; font-size: 10px; } }
    </style>
</head>
<body>
    @php
        $headerLogo = \App\Services\MediaService::getMediaUrl(\App\Models\Setting::get('header_logo_media_id'));
        $instagramUrl = \App\Models\Setting::get('social_instagram');
        $facebookUrl = \App\Models\Setting::get('social_facebook');
    @endphp

    <main class="landing">
        <div class="content">
            <a class="brand" href="{{ url('/') }}" aria-label="UTB RedBricks">
                @if ($headerLogo)
                    <img src="{{ $headerLogo }}" alt="">
                @endif
                <span>UTB<br><b>REDBRICKS</b></span>
            </a>

            <section class="message" aria-labelledby="page-title">
                <p class="eyebrow">Brzy se vrátíme na led</p>
                <h1 id="page-title">Web se<br><span>připravuje.</span></h1>
                <p class="intro">Pracujeme na novém domově pro všechny fanoušky UTB RedBricks. Aktuální dění ze zákulisí sledujte na našich sítích.</p>
                <nav class="socials" aria-label="Sociální sítě">
                    @if (filled($instagramUrl))
                        <a class="social-link social-link--instagram" href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer">
                            <svg class="instagram-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.4" cy="6.6" r=".7" fill="currentColor" stroke="none"></circle></svg>
                            Instagram
                        </a>
                    @endif
                    @if (filled($facebookUrl))
                        <a class="social-link social-link--facebook" href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8h3V4.2c-.5-.1-2.2-.2-4.1-.2C9 4 6.3 6.4 6.3 10.8V14H2v4.3h4.3V24h5.2v-5.7h4.1L16.3 14h-4.8v-2.8C11.5 9.9 11.9 8 14 8Z"></path></svg>
                            Facebook
                        </a>
                    @endif
                </nav>
            </section>

            <footer class="footer">
                <span>Univerzitní hokejový klub Zlín</span>
                <span><strong>UTB RedBricks</strong> &copy; 2026</span>
            </footer>
        </div>
    </main>
</body>
</html>
