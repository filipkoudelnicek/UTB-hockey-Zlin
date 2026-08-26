<?php

namespace App\Http\Middleware;

use App\Services\UrlService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $uriArr = explode('/', $request->getRequestUri());

        if (isset($uriArr[1])) {
            $locales = UrlService::getLocales();
            if (in_array($uriArr[1], $locales)) {
                app()->setLocale($uriArr[1]);
            } else {
                app()->setLocale(UrlService::getDefaultLocale());
            }

            return $next($request);
        }

        $selLang = $request->session()->get('setLang');

        if ($selLang) {
            app()->setLocale($selLang);
        } else {
            app()->setLocale(config('app.locale'));
        }

        return $next($request);
    }
}