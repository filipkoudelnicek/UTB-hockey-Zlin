<?php

namespace App\View\Components;

use App\Models\Navigation;
use App\Services\UrlService;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HeaderMenu extends Component
{
    public array $navItems;
    public string $currentLocale;
    public string $homepageUrl;

    public function __construct()
    {
        $this->currentLocale = app()->getLocale();
        $this->homepageUrl   = UrlService::getHomepageUrl($this->currentLocale);

        $nav = Navigation::getByHandle('main', $this->currentLocale);
        $this->navItems = $nav?->items ?? [];
    }

    public function render(): View
    {
        return view('components.header.header-menu');
    }
}
