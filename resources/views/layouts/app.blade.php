<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/favicon.ico">

    @yield('seo')
    <title>@yield('title') | {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if(\App\Models\Setting::get('cookie_text'))
    <link rel="stylesheet" href="/assets/css/cookies.css">
    @endif

    {{-- GA / GTM se načtou pouze po udělení souhlasu s analytickými cookies (viz cookie consent níže) --}}
    @if(\App\Models\Setting::get('google_tag_manager_id'))
    <script>window._gtmId = '{{ \App\Models\Setting::get('google_tag_manager_id') }}';</script>
    @endif
    @if(\App\Models\Setting::get('google_analytics_id'))
    <script>window._gaId = '{{ \App\Models\Setting::get('google_analytics_id') }}';</script>
    @endif
</head>

<body>
    <x-header-menu />

    @yield('content')

    <x-footer />

    {{-- Cookie consent --}}
    @includeWhen(\App\Models\Setting::get('cookie_text'), 'partials.cookie-consent')
</body>
</html>
