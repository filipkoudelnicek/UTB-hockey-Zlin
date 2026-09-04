<?php

namespace App\Http\Controllers;

use App\Services\PageDataService;
use App\Services\UrlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ArticleController extends Controller
{
    protected string $defaultLocale;

    public function __construct(private readonly PageDataService $pageData)
    {
        $this->defaultLocale = UrlService::getDefaultLocale();
    }

    public function showArticle(Request $request, string $articleSlug)
    {
        if (!Schema::hasTable('articles')) {
            return redirect('/admin-utb');
        }
        $locale = $request->route('locale') ?? $this->defaultLocale;
        return view('pages.article', $this->pageData->forArticle($articleSlug, $locale));
    }
}
