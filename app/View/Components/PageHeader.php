<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Awcodes\Curator\Models\Media;

class PageHeader extends Component
{
    public string $title;
    public ?string $background = null;
    public ?string $author = null;
    public ?string $publishedAt = null;

    /**
     * Create a new component instance.
     */
    public function __construct(string $title, $background = null, $author = null, $publishedAt = null)
    {
        $this->title = $title;
        $this->author = $author;
        $this->publishedAt = $publishedAt;

        if ($background) {
            $media = Media::find($background);
            $this->background = $media ? '/storage/' . $media->path : null;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.header.page-header');
    }
}
