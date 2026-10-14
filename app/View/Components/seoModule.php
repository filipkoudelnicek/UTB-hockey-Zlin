<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\MediaService;

class SeoModule extends Component
{
    public string $pageTitle;

    public string $documentTitle;

    public function __construct(
        public array $seo,
        public ?string $defaultTitle = null,
        public string $type = 'website',
        public ?string $robots = 'index, follow',
    ) {
        if (filled($this->robots)) {
            $directive = $this->seo['robots'] ?? null;
            $directive = in_array($directive, ['index, follow', 'noindex, follow', 'index, nofollow', 'noindex, nofollow'], true)
                ? $directive
                : 'index, follow';
            $this->robots = $directive . ', max-image-preview:large, max-snippet:-1, max-video-preview:-1';
        }

        $seoTitle = $this->seo['title'] ?? null;
        $this->pageTitle = filled($seoTitle) ? $seoTitle : (string) $this->defaultTitle;

        $autoTitle = data_get($this->seo, 'og_auto.title');
        $isGeneratedSeoTitle = filled($seoTitle) && $autoTitle === $seoTitle;
        $appendAppName = !filled($seoTitle) || $isGeneratedSeoTitle;
        $documentBaseTitle = $isGeneratedSeoTitle && filled($this->defaultTitle)
            ? $this->defaultTitle
            : $this->pageTitle;

        $this->documentTitle = trim($documentBaseTitle . ($appendAppName ? ' | ' . config('app.name') : ''));
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.seo-module')->with([
            'ogImageUrl' => MediaService::getMediaFullUrl($this->seo['og_image'] ?? null),
            'ogType' => $this->type,
        ]);
    }
}
