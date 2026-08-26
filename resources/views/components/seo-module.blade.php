@isset($seo)
    @if(!empty($seo['title']))
        <meta name="title" content="{{ $seo['title'] }}">
    @endif

    @if(!empty($seo['description']))
        <meta name="description" content="{{ $seo['description'] }}">
    @endif

    @if(!empty($seo['keywords']))
        <meta name="keywords" content="{{ $seo['keywords'] }}">
    @endif

    @if(!empty($seo['og_type']))
        <meta property="og:type" content="{{ $seo['og_type'] }}">
    @endif

    @if(!empty($seo['og_title']))
        <meta property="og:title" content="{{ $seo['og_title'] }}">
    @endif

    @if(!empty($seo['og_desc']))
        <meta property="og:description" content="{{ $seo['og_desc'] }}">
    @endif

    @if (!empty($ogImageUrl))
        <meta property="og:image" content="{{ $ogImageUrl }}">
    @endif

    @if(!empty($seo['canonical']))
        <link rel="canonical" href="{{ $seo['canonical'] }}">
    @endif
@endisset
