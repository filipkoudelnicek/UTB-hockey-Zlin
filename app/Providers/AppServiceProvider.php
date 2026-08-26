<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Page;
use App\Observers\ArticleObserver;
use App\Observers\OptimizeCuratorMedia;
use App\Observers\PageObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Models\Media as CuratorMedia;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Page::observe(PageObserver::class);
        Article::observe(ArticleObserver::class);
        CuratorMedia::observe(OptimizeCuratorMedia::class);

        // Set Curator default directory from config so uploads never have null directory
        Curator::directory(config('curator.default_directory', 'media'));
        Curator::maxSize(config('curator.max_size', 51200));
        CuratorPicker::configureUsing(function (CuratorPicker $component): void {
            $component->maxSize(config('curator.max_size', 51200));
        });
    }
}
