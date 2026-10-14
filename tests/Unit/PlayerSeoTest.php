<?php

namespace Tests\Unit;

use App\Models\Player;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class PlayerSeoTest extends TestCase
{
    public function test_seo_defaults_are_generated_from_player_name_bio_and_portrait(): void
    {
        $player = new Player([
            'first_name' => 'Jan',
            'last_name' => 'Novák',
            'bio' => '<p>Útočník týmu UTB RedBricks.</p>',
            'portrait_media_id' => 12,
        ]);

        $this->assertSame([
            'title' => null,
            'description' => 'Útočník týmu UTB RedBricks.',
            'og_title' => 'Jan Novák',
            'og_desc' => 'Útočník týmu UTB RedBricks.',
            'og_image' => 12,
        ], $player->seoWithDefaults());
    }

    public function test_manually_filled_seo_fields_override_generated_values(): void
    {
        $player = new Player([
            'first_name' => 'Jan',
            'last_name' => 'Novák',
            'bio' => '<p>Automatický popis.</p>',
            'seo_title' => 'Vlastní SEO titulek',
            'seo_description' => 'Vlastní SEO popis.',
            'seo_og_title' => 'Vlastní OG nadpis',
            'seo_og_description' => 'Vlastní OG popis.',
            'seo_og_media_id' => 34,
            'portrait_media_id' => 12,
        ]);

        $this->assertSame([
            'title' => 'Vlastní SEO titulek',
            'description' => 'Vlastní SEO popis.',
            'og_title' => 'Vlastní OG nadpis',
            'og_desc' => 'Vlastní OG popis.',
            'og_image' => 34,
        ], $player->seoWithDefaults());
    }

    public function test_player_seo_component_renders_open_graph_metadata(): void
    {
        config(['app.name' => 'Test Site']);

        $player = new Player([
            'first_name' => 'Jan',
            'last_name' => 'Novák',
            'bio' => '<p>Útočník týmu UTB RedBricks.</p>',
        ]);

        $html = Blade::render(
            '<x-seo-module :seo="$seo" :default-title="$title" type="profile" />',
            ['seo' => $player->seoWithDefaults(), 'title' => $player->full_name],
        );

        $this->assertStringContainsString('<title>Jan Novák | Test Site</title>', $html);
        $this->assertStringContainsString('<meta property="og:type" content="profile">', $html);
        $this->assertStringContainsString('<meta property="og:title" content="Jan Novák">', $html);
        $this->assertStringContainsString('<meta property="og:description" content="Útočník týmu UTB RedBricks.">', $html);
    }
}
