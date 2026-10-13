<?php

namespace App\Http\Controllers;

use App\Services\Analytics\AnalyticsTracker;
use App\Services\Analytics\BotDetector;
use App\Services\Analytics\PageToken;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;

class AnalyticsController extends Controller
{
    public function __construct(private readonly AnalyticsTracker $tracker)
    {
    }

    /**
     * Příjem zobrazení. Klient jej volá až po udělení analytického souhlasu.
     */
    public function collect(Request $request): Response
    {
        $token = $request->input('t');

        if (
            ! config('analytics.enabled')
            || ! $this->isSameOrigin($request)
            || ! is_string($token) || strlen($token) > 2048
            || BotDetector::isBot($request->userAgent())
            || BotDetector::isPreviewRequest($request)
        ) {
            return response()->noContent();
        }

        $page = PageToken::consume($token);

        if ($page === null) {
            return response()->noContent();
        }

        $cookieName = config('analytics.cookie.name');
        $visitorId = $request->cookie($cookieName);
        $isNew = ! AnalyticsTracker::isValidVisitorId($visitorId);

        if ($isNew) {
            $visitorId = AnalyticsTracker::newVisitorId();
        }

        $this->tracker->record($visitorId, $page['path'], $page['type'], $page['id']);

        $response = response()->noContent();

        if ($isNew) {
            $minutes = (int) abs(now()->diffInMinutes(now()->addMonthsNoOverflow((int) config('analytics.cookie.months'))));

            $response->withCookie(Cookie::make(
                $cookieName,
                $visitorId,
                $minutes,
                '/',
                null,
                $request->isSecure(),
                false,
                false,
                'lax',
            ));
        }

        return $response;
    }

    /**
     * Odvolání souhlasu: smaže pseudonymní data návštěvníka a analytickou cookie.
     */
    public function forget(Request $request): Response
    {
        if (! $this->isSameOrigin($request)) {
            return response()->noContent();
        }

        $cookieName = config('analytics.cookie.name');
        $visitorId = $request->cookie($cookieName);

        if (is_string($visitorId)) {
            $this->tracker->forget($visitorId);
        }

        return response()->noContent()->withCookie(Cookie::forget($cookieName));
    }

    private function isSameOrigin(Request $request): bool
    {
        $site = $request->headers->get('Sec-Fetch-Site');

        return $site === null || in_array($site, ['same-origin', 'none'], true);
    }
}
