<?php

use App\Models\PageRoute;
use App\Services\UrlService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Sitemap — vždy statická
|--------------------------------------------------------------------------
*/
Route::get('/sitemap.xml', function () {
    try {
        return UrlService::buildSitemap()->toResponse(request());
    } catch (\Exception $e) {
        abort(500, 'Sitemap generation failed.');
    }
});

/*
|--------------------------------------------------------------------------
| Dynamické routes z DB (spravováno přes admin → Route Builder)
|--------------------------------------------------------------------------
*/
try {
    if (Schema::hasTable('page_routes')) {
        PageRoute::loadRoutes();
    } else {
        // Fresh install — přesměruj do administrace
        Route::get('/', fn () => redirect('/admin-utb'));
    }
} catch (\Throwable $e) {
    Log::error('Failed to load database page routes.', [
        'exception' => $e,
    ]);

    // DB není dostupná (první migrate apod.)
    Route::get('/', fn () => redirect('/admin-utb'));
}

Route::fallback(function () {
    Log::warning('No application route matched the request.', [
        'method' => request()->method(),
        'path' => request()->path(),
        'url' => request()->fullUrl(),
    ]);

    abort(404);
});
