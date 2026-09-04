<?php

namespace App\Livewire;

use App\Models\Article;
use Livewire\Component;
use Livewire\WithPagination;

class NewsArticleList extends Component
{
    use WithPagination;

    public string $locale;

    public string $emptyMessage;

    public string $category = 'all';

    public function mount(string $locale, string $emptyMessage): void
    {
        $this->locale = $locale;
        $this->emptyMessage = $emptyMessage;
    }

    public function selectCategory(string $category): void
    {
        $this->category = array_key_exists($category, Article::categoryOptions()) ? $category : 'all';
        $this->resetPage();
    }

    public function paginationView(): string
    {
        return 'livewire.pagination.news';
    }

    public function render()
    {
        $categoryOptions = Article::categoryOptions();
        $availableCategoryKeys = Article::published()
            ->where('lang_locale', $this->locale)
            ->whereIn('category', array_keys($categoryOptions))
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $categories = collect($categoryOptions)
            ->filter(fn (string $label, string $key): bool => $availableCategoryKeys->contains($key));

        $articles = Article::published()
            ->where('lang_locale', $this->locale)
            ->when($this->category !== 'all', fn ($query) => $query->where('category', $this->category))
            ->orderByDesc('publish_time')
            ->paginate(7);

        return view('livewire.news-article-list', compact('articles', 'categories'));
    }
}
