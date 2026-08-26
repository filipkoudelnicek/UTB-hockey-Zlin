<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Services\UrlService;
use Illuminate\Support\Facades\Schema;

class ArticleController extends Controller
{
    protected $defaultLocale;

    public function __construct()
    {
        $this->defaultLocale = UrlService::getDefaultLocale();
    }
    
    public function showArticle(Request $request, $articleSlug)
    {
        if (!Schema::hasTable('articles')) {
            return redirect('/admin');
        }
        
        $locale = $request->route('locale') ?? $this->defaultLocale;
        
        try {
            $article = Article::published()
                ->where('slug', $articleSlug)
                ->where('lang_locale', $locale)
                ->firstOrFail();
            
            return view('pages.article-detail', compact('article'));
        } catch (\Exception $e) {
            abort(404);
        }
    }
}