<?php

namespace App\View\Components;

use App\Models\Navigation;
use App\Services\PageService;
use App\Services\NavigationService;
use App\Services\UrlService;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HeaderMenu extends Component
{
    public array $navItems;
    public string $currentLocale;
    public string $homepageUrl;
    public string $contactUrl;
    public string $matchesUrl;

    public function __construct()
    {
        $this->currentLocale = app()->getLocale();
        $this->homepageUrl = PageService::getRelativeUrlByType('homepage', $this->currentLocale, UrlService::getHomepageUrl($this->currentLocale));
        $this->contactUrl = PageService::getRelativeUrlByType('contact', $this->currentLocale, '/kontakt');
        $this->matchesUrl = PageService::getRelativeUrlByType('matches', $this->currentLocale, '/zapasy');
        $nav = Navigation::getByHandle('main', $this->currentLocale);
        $this->navItems = array_map(function (array $item): array {
            $item['active'] = $this->isCurrentUrl($item['url'] ?? null, ($item['type'] ?? null) === 'page');

            return $item;
        }, NavigationService::resolveItems($nav?->items ?? []));
    }

    public function render(): View
    {
        return view('components.header.header-menu');
    }

    private function isCurrentUrl(?string $url, bool $isPageLink): bool
    {
        if (blank($url)) {
            return false;
        }

        $parts = parse_url($url);

        if ($parts === false) {
            return false;
        }

        if (! $isPageLink && isset($parts['host']) && $parts['host'] !== request()->getHost()) {
            return false;
        }

        $targetPath = '/' . trim($parts['path'] ?? $url, '/');
        $currentPath = '/' . trim(request()->path(), '/');

        return $targetPath === $currentPath;
    }
}
