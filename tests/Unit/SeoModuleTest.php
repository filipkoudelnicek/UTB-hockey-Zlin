<?php

namespace Tests\Unit;

use App\View\Components\SeoModule;
use Tests\TestCase;

class SeoModuleTest extends TestCase
{
    public function test_page_title_is_used_when_seo_title_is_empty(): void
    {
        config(['app.name' => 'Test Site']);

        $component = new SeoModule([], 'Text page');

        $this->assertSame('Text page', $component->pageTitle);
        $this->assertSame('Text page | Test Site', $component->documentTitle);
    }

    public function test_custom_seo_title_replaces_page_title_without_app_name(): void
    {
        config(['app.name' => 'Test Site']);

        $component = new SeoModule(['title' => 'Custom SEO title'], 'Text page');

        $this->assertSame('Custom SEO title', $component->pageTitle);
        $this->assertSame('Custom SEO title', $component->documentTitle);
    }

    public function test_generated_article_seo_title_keeps_page_title_and_app_name(): void
    {
        config(['app.name' => 'Test Site']);

        $component = new SeoModule([
            'title' => 'Truncated article title',
            'og_auto' => ['title' => 'Truncated article title'],
        ], 'Full article title');

        $this->assertSame('Truncated article title', $component->pageTitle);
        $this->assertSame('Full article title | Test Site', $component->documentTitle);
    }
}
