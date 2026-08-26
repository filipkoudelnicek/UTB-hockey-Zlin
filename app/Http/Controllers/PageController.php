<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Services\MediaService;
use App\Services\UrlService;
use Illuminate\Support\Facades\Schema;

class PageController extends Controller
{
    protected $defaultLocale;

    public function __construct()
    {
        $this->defaultLocale = UrlService::getDefaultLocale();
    }
    
    public function homepage(Request $request)
    {
        if (!Schema::hasTable('pages')) {
            return redirect('/admin');
        }
        
        $locale = $request->route('locale') ?? $this->defaultLocale;
        
        try {
            $page = Page::active()
                ->where('type', 'homepage')
                ->where('lang_locale', $locale)
                ->first();
                
            if (!$page) {
                return redirect('/admin');
            }

            $template = optional($page->pageType)->template ?? 'pages.homepage';
            return view($template, ['page' => $page]);
        } catch (\Exception $e) {
            return redirect('/admin');
        }
    }
    
    public function show(Request $request, $slug = '')
    {
        if (!Schema::hasTable('pages')) {
            abort(404);
        }
        
        $locale = $request->route('locale') ?? $this->defaultLocale;
        $slug   = ltrim((string) $slug, '/');
        
        try {
            $page = Page::active()
                ->where('full_slug', $slug)
                ->where('lang_locale', $locale)
                ->first();
                
            if (!$page) {
                abort(404);
            }

            $template = optional($page->pageType)->template ?? 'pages.' . $page->type;
            return view($template, ['page' => $page]);
        } catch (\Exception $e) {
            abort(404);
        }
    }
    
    public static function getMediaUrl($mediaId)
    {
        return MediaService::getMediaUrl($mediaId);
    }
}