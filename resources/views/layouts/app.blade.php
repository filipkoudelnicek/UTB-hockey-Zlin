<!DOCTYPE html>
<html class="scroll-smooth motion-reduce:scroll-auto" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ \App\Models\Setting::get('favicon_updated_at', '1') }}" sizes="any">
    {{-- Fonty používané v hlavičce a první obrazovce. --}}
    <link rel="preload" href="{{ asset('assets/fonts/barlow-condensed-700-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('assets/fonts/barlow-condensed-800-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('assets/fonts/barlow-condensed-900-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('assets/fonts/barlow-condensed-900-latin-ext.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('assets/fonts/inter-latin-variable.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('assets/fonts/inter-latin-ext-variable.woff2') }}" as="font" type="font/woff2" crossorigin>

    @yield('seo')

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if(\App\Models\Setting::get('cookie_text'))
    <link rel="stylesheet" href="/assets/css/cookies.css">
    {{-- Rozhodnutí o zobrazení lišty musí proběhnout synchronně před vykreslením, jinak by na okamžik bliklo --}}
    <script>
        (function () {
            try {
                var p = JSON.parse(localStorage.getItem('cc_prefs'));
                // Souhlas s analytikou musí odpovídat aktuální verzi, jinak se lišta zobrazí znovu.
                var fresh = p && typeof p.ts === 'number' && Date.now() - p.ts < {{ (int) config('analytics.consent_months') }} * 30.4375 * 864e5;
                if (fresh && (p.analytics === false || p.v === {{ (int) config('analytics.consent_version') }})) {
                    document.documentElement.classList.add('cc-consent-set');
                }
            } catch (e) {}
        })();
    </script>
    @endif

    {{-- Vlastní analytika: token se vydává jen pro sledované stránky a skript jej odešle až po souhlasu --}}
    <script>
        window._ccVersion = {{ (int) config('analytics.consent_version') }};
        window._ccMaxAgeMs = {{ (int) config('analytics.consent_months') }} * 30.4375 * 864e5;
        window._analyticsForgetUrl = @json(route('analytics.forget', absolute: false));
        @if(!empty($analytics))
        window._analytics = @json($analytics);
        @endif
    </script>

    {{-- GA / GTM se načtou pouze po udělení souhlasu s analytickými cookies (viz cookie consent níže) --}}
    @if(\App\Models\Setting::get('google_tag_manager_id'))
    <script>window._gtmId = '{{ \App\Models\Setting::get('google_tag_manager_id') }}';</script>
    @endif
    @if(\App\Models\Setting::get('google_analytics_id'))
    <script>window._gaId = '{{ \App\Models\Setting::get('google_analytics_id') }}';</script>
    @endif
</head>

<body class="m-0 min-h-screen bg-paper font-sans text-ink-css">
    <a href="#main" class="fixed left-[.7rem] top-[.7rem] z-[9999] -translate-y-[180%] rounded-lg bg-white px-4 py-[.7rem] font-extrabold text-wine transition-transform duration-200 focus:translate-y-0 focus-visible:outline focus-visible:outline-3 focus-visible:outline-orange focus-visible:outline-offset-3">Přeskočit na obsah</a>
    <x-header-menu />

    <main id="main">
        @yield('content')
    </main>

    <x-footer />

    {{-- Cookie consent --}}
    @includeWhen(\App\Models\Setting::get('cookie_text'), 'partials.cookie-consent')
</body>
</html>
