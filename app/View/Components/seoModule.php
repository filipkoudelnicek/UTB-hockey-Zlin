<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\MediaService;

class SeoModule extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public $seo)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.seo-module')->with([
        'ogImageUrl' => MediaService::getMediaFullUrl($this->seo['og_image'] ?? null),
    ]);
    }
}
