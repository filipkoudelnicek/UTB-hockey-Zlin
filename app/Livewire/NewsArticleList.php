<?php

namespace App\Livewire;

use App\Models\Article;
use App\Models\ArticleCategory;
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
        $this->categoryId = $categoryId && ArticleCategory::query()
            ->whereKey($categoryId)
            ->where('is_filterable', true)
            ->exists()
            ? $categoryId
            : null;
        $this->resetPage();
    }

    public function paginationView(): string
    {
        return 'livewire.pagination.news';
    }

    public function render()
    {
        if ($this->categoryId && ! ArticleCategory::query()
            ->whereKey($this->categoryId)
            ->where('is_filterable', true)
            ->exists()) {
            $this->categoryId = null;
        }

        $categories = ArticleCategory::query()
            ->where('is_filterable', true)
            ->whereHas('articles', fn ($query) => $query->published()->where('lang_locale', $this->locale))
            ->orderBy('name')
            ->get();

        $articles = Article::published()
            ->where('lang_locale', $this->locale)
            ->with('categories')
            ->when($this->categoryId, fn ($query) => $query->whereHas(
                'categories',
                fn ($categoryQuery) => $categoryQuery
                    ->whereKey($this->categoryId)
                    ->where('is_filterable', true),
            ))
            ->orderByDesc('publish_time')
            ->paginate(7);

        return view('livewire.news-article-list', compact('articles', 'categories'));
    }
}
