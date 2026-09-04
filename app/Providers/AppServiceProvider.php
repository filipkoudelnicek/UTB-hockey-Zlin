<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Page;
use App\Models\PageRoute;
use App\Observers\ArticleObserver;
use App\Observers\OptimizeCuratorMedia;
use App\Observers\PageObserver;
use App\Observers\PageRouteObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Glide\SymfonyResponseFactory;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Forms\RichEditor\AttachCuratorMediaPlugin;
use Awcodes\Curator\Models\Media as CuratorMedia;
use Filament\Forms\Components\RichEditor;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Glide::serverConfig([
            'response' => new SymfonyResponseFactory(app('request')),
            'source' => public_path('uploads'),
            'source_path_prefix' => '',
            'cache' => storage_path('app'),
            'cache_path_prefix' => '.cache',
            'max_image_size' => 2000 * 2000,
        ]);
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Page::observe(PageObserver::class);
        PageRoute::observe(PageRouteObserver::class);
        Article::observe(ArticleObserver::class);
        CuratorMedia::observe(OptimizeCuratorMedia::class);

        // Set Curator default directory from config so uploads never have null directory
        Curator::directory(config('curator.default_directory', 'media'));
        Curator::maxSize(config('curator.max_size', 51200));
        CuratorPicker::configureUsing(function (CuratorPicker $component): void {
            $component->maxSize(config('curator.max_size', 51200));
        });

        // RichEditor images must use Curator as well. This lets editors select
        // existing media and makes new uploads pass through the same WebP
        // optimization observer as every other Curator upload.
        RichEditor::configureUsing(function (RichEditor $component): void {
            $component
                ->fileAttachments(false)
                ->fileAttachmentsMaxSize(config('curator.max_size', 51200))
                ->plugins([
                    AttachCuratorMediaPlugin::make(),
                ])
                ->toolbarButtons([
                    ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                    ['h2', 'h3'],
                    ['alignStart', 'alignCenter', 'alignEnd'],
                    ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                    ['table', 'attachCuratorMedia'],
                    ['undo', 'redo'],
                ]);
        });
    }
}
