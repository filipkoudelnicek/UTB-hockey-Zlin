<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\MediaService;
use App\Services\PageDataService;
use App\Services\UrlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PageController extends Controller
{
    protected string $defaultLocale;

    public function __construct(private readonly PageDataService $pageData)
    {
        $this->defaultLocale = UrlService::getDefaultLocale();
    }
    
    public function homepage(Request $request)
    {
        if (!Schema::hasTable('pages')) {
            return redirect('/admin-utb');
        }
        
        $locale = $request->route('locale') ?? $this->defaultLocale;
        
        try {
            $page = Page::active()
                ->where('type', 'homepage')
                ->where('lang_locale', $locale)
                ->first();
                
            if (!$page) {
                return redirect('/admin-utb');
            }

            return $this->render($page, $request);
        } catch (\Throwable $e) {
            return redirect('/admin-utb');
        }
    }
    
    public function show(Request $request, ...$routeParameters)
    {
        if (!Schema::hasTable('pages')) {
            abort(404);
        }
        
        $locale = $request->route('locale') ?? $this->defaultLocale;
        $fullSlug = trim($request->path(), '/');

        if ($locale !== $this->defaultLocale) {
            if ($fullSlug === $locale) {
                $fullSlug = '';
            } elseif (str_starts_with($fullSlug, $locale . '/')) {
                $fullSlug = substr($fullSlug, strlen($locale) + 1);
            }
        }
        
        try {
            $page = Page::active()
                ->where('full_slug', $fullSlug)
                ->where('lang_locale', $locale)
                ->first();
                
            if (!$page) {
                abort(404);
            }

            return $this->render($page, $request);
        } catch (\Throwable $e) {
            abort(404);
        }
    }

    private function render(Page $page, Request $request)
    {
        $template = optional($page->pageType)->template ?? 'pages.' . $page->type;

        return view($template, array_merge(
            ['page' => $page],
            $this->pageData->forPage($page, $request),
        ));
    }
    
    public static function getMediaUrl($mediaId)
    {
        return MediaService::getMediaUrl($mediaId);
    }
}
