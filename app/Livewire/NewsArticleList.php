<?php

namespace App\Livewire;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Services\MediaService;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class NewsArticleList extends Component
{
    use WithPagination;

    public string $locale;

    public string $emptyMessage;

    public ?int $categoryId = null;

    public function mount(string $locale, string $emptyMessage): void
    {
        $this->locale = $locale;
        $this->emptyMessage = $emptyMessage;
    }

    public function selectCategory(?int $categoryId): void
    {
        $this->categoryId = $categoryId;
        $this->resetPage();
    }

    public function paginationView(): string
    {
        return 'livewire.pagination.news';
    }

    public function render()
    {
        $categories = Cache::remember(
            "news-filter-categories:{$this->locale}",
            now()->addMinutes(5),
            fn () => ArticleCategory::query()
                ->where('is_filterable', true)
                ->whereHas('articles', fn ($query) => $query->published()->where('lang_locale', $this->locale))
                ->orderBy('name')
                ->get(),
        );

        if ($this->categoryId && ! $categories->contains('id', $this->categoryId)) {
            $this->categoryId = null;
        }

        $articles = Article::published()
            ->select(['id', 'slug', 'lang_locale', 'title', 'excerpt', 'featured_media_id', 'publish_time'])
            ->where('lang_locale', $this->locale)
            ->with('categories:id,name')
            ->when($this->categoryId, fn ($query) => $query->whereHas(
                'categories',
                fn ($categoryQuery) => $categoryQuery
                    ->whereKey($this->categoryId)
                    ->where('is_filterable', true),
            ))
            ->orderByDesc('publish_time')
            ->paginate(7);

        MediaService::preload($articles->pluck('featured_media_id'));

        return view('livewire.news-article-list', compact('articles', 'categories'));
    }
}
