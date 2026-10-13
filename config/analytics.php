<?php

return [
    'enabled' => env('ANALYTICS_ENABLED', true),

    'timezone' => 'Europe/Prague',

    // Verze souhlasu. Při změně rozsahu měření ji zvyšte, aby byl souhlas vyžádán znovu (viz public/assets/js/cookies.js).
    'consent_version' => 2,

    // Platnost volby v cookie liště (souhlas i odmítnutí); poté se lišta zobrazí znovu.
    'consent_months' => 12,

    'cookie' => [
        'name' => 'utb_vid',
        'months' => 6,
    ],

    // Opakované zobrazení stejné URL stejným návštěvníkem v tomto okně se nezapočítá.
    'dedupe_seconds' => 30,

    // Platnost tokenu vydaného společně se stránkou; token lze použít jen jednou.
    'token_ttl_minutes' => 360,

    'rate_limit' => [
        'per_ip_per_minute' => 60,
        'per_visitor_per_minute' => 20,
    ],

    // Surová data (pseudonymní ID návštěvníka) se po této době mažou, denní souhrny bez osobních údajů zůstávají.
    'retention_months' => 14,

    'max_path_length' => 255,
];
