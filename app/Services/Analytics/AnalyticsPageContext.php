<?php

namespace App\Services\Analytics;

use App\Models\Article;
use App\Models\Page;
use App\Models\Player;
use Illuminate\Http\Request;

class AnalyticsPageContext
{
    /**
     * Údaje pro klientský skript. Vrací null pro stránky, které se nesledují
     * (přihlášený uživatel administrace, boti, náhledy, ne-GET požadavky).
     *
     * @param  array<string, mixed>  $viewData
     * @return array{token: string, collect: string, forget: string, version: int}|null
     */
    public static function build(Request $request, array $viewData): ?array
    {
        if (
            ! config('analytics.enabled')
            || ! $request->isMethod('GET')
            || $request->ajax()
            || $request->user() !== null
            || BotDetector::isBot($request->userAgent())
            || BotDetector::isPreviewRequest($request)
        ) {
            return null;
        }

        $path = '/'.ltrim(rawurldecode($request->path()), '/');
        $path = $path === '/' ? $path : rtrim($path, '/');

        if (! AnalyticsTracker::isTrackablePath($path)) {
            return null;
        }

        [$type, $id] = match (true) {
            ($viewData['article'] ?? null) instanceof Article => ['article', $viewData['article']->getKey()],
            ($viewData['player'] ?? null) instanceof Player => ['player', $viewData['player']->getKey()],
            ($viewData['page'] ?? null) instanceof Page => ['page', $viewData['page']->getKey()],
            default => [null, null],
        };

        return [
            'token' => PageToken::issue($path, $type, $id),
            'collect' => route('analytics.collect', absolute: false),
            'forget' => route('analytics.forget', absolute: false),
            'version' => (int) config('analytics.consent_version'),
        ];
    }
}
