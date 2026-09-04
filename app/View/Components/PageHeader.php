<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PageHeader extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $accent = null,
        public ?string $heading = null,
        public ?string $eyebrow = null,
        public ?string $breadcrumb = null,
        public ?string $image = null,
        public ?string $locale = null,
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.page-header');
    }
}
