<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    /**
     * User-agent fragmenty typické pro boty / crawlery.
     */
    private const BOT_PATTERNS = [
        'bot', 'crawl', 'spider', 'slurp', 'search', 'facebook', 'twitter',
        'google', 'bing', 'yahoo', 'baidu', 'duckduck', 'yandex', 'semrush',
        'ahrefsbot', 'mj12bot', 'dotbot', 'petalbot', 'bytespider',
        'headlesschrome', 'phantom', 'wget', 'curl', 'python-requests',
        'go-http-client', 'apache-httpclient', 'java/', 'axios',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Zaznamenat pouze GET požadavky na front-end
        if (
            $request->isMethod('GET') &&
            ! $request->is('admin/*') &&
            ! $request->is('admin') &&
            ! $request->ajax() &&
            $response->isSuccessful()
        ) {
            $userAgent = strtolower($request->userAgent() ?? '');
            $isBot     = $this->isBot($userAgent);

            // Boty neukládáme – pouze reálné návštěvy
            if (! $isBot) {
                PageView::create([
                    'path'       => $request->path() === '/' ? '/' : '/' . $request->path(),
                    'session_id' => $request->session()->getId(),
                    'is_bot'     => false,
                    'created_at' => now(),
                ]);
            }
        }

        return $response;
    }

    private function isBot(string $userAgent): bool
    {
        if (empty($userAgent)) {
            return true;
        }

        foreach (self::BOT_PATTERNS as $pattern) {
            if (str_contains($userAgent, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
