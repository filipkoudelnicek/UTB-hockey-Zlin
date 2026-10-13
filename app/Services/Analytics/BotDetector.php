<?php

namespace App\Services\Analytics;

use Illuminate\Http\Request;

class BotDetector
{
    private const PATTERN = '/bot|crawl|spider|slurp|scrap|fetch|monitor|uptime|probe|checker|validator|headless|phantom|lighthouse|pagespeed|gtmetrix|preview|facebookexternalhit|facebot|whatsapp|telegram|discord|slack|skype|linkedin|pinterest|twitter|embedly|quora|vkshare|curl|wget|httpie|python|java\/|go-http|okhttp|libwww|axios|node-fetch|postman|ahrefs|semrush|mj12|petal|yandex|baidu|googleother|google-|apis-google|feedly|rss/i';

    public static function isBot(?string $userAgent): bool
    {
        $userAgent = trim((string) $userAgent);

        if (strlen($userAgent) < 10) {
            return true;
        }

        return preg_match(self::PATTERN, $userAgent) === 1;
    }

    /**
     * Prefetch, prerender a vložené náhledy, které uživatel skutečně neviděl.
     */
    public static function isPreviewRequest(Request $request): bool
    {
        foreach (['Sec-Purpose', 'Purpose', 'X-Purpose', 'X-Moz'] as $header) {
            $value = strtolower((string) $request->headers->get($header));

            if ($value !== '' && preg_match('/prefetch|prerender|preview/', $value)) {
                return true;
            }
        }

        $dest = strtolower((string) $request->headers->get('Sec-Fetch-Dest'));

        return in_array($dest, ['iframe', 'frame', 'embed', 'object'], true);
    }
}
