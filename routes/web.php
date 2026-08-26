<?php

use App\Models\PageRoute;
use App\Services\UrlService;
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
        Route::get('/', fn () => redirect('/admin'));
    }
} catch (\Throwable $e) {
    // DB není dostupná (první migrate apod.)
    Route::get('/', fn () => redirect('/admin'));
}

Route::fallback(fn () => abort(404));

