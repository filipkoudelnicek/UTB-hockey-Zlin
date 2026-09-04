<?php

namespace Tests\Feature;

use App\Integrations\Meta\MetaSocialFeedService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MetaSocialFeedServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::forget('meta.social.homepage-feed');
        config()->set('services.meta.access_token', 'test-access-token');
        config()->set('services.meta.instagram_account_id', 'instagram-account');
        config()->set('services.meta.facebook_page_id', 'facebook-page');
        config()->set('services.meta.graph_version', 'v25.0');
    }

    public function test_it_normalizes_only_the_data_needed_by_the_homepage_cards(): void
    {
        Http::fake(function (Request $request) {
            return match (true) {
                str_ends_with(parse_url($request->url(), PHP_URL_PATH), '/instagram-account/media') => Http::response([
                    'data' => [
                        [
                            'caption' => 'Výhra doma!',
                            'media_type' => 'IMAGE',
                            'media_url' => 'https://cdn.example.test/ig-1.jpg',
                            'timestamp' => '2026-08-30T10:00:00+0000',
                            'like_count' => 42,
                            'comments_count' => 3,
                        ],
                        [
                            'caption' => 'Video z kabiny',
                            'media_type' => 'VIDEO',
                            'thumbnail_url' => 'https://cdn.example.test/ig-2.jpg',
                            'timestamp' => '2026-08-29T10:00:00+0000',
                        ],
                    ],
                ]),
                str_ends_with(parse_url($request->url(), PHP_URL_PATH), '/facebook-page/posts') => Http::response([
                    'data' => [[
                        'message' => 'Děkujeme fanouškům!',
                        'permalink_url' => 'https://www.facebook.com/posts/fb-1',
                        'created_time' => '2026-08-30T09:00:00+0000',
                    ]],
                ]),
                default => Http::response([], 404),
            };
        });

        $feed = app(MetaSocialFeedService::class)->homepageFeed();

        $this->assertSame('Výhra doma!', data_get($feed, 'instagram.posts.0.caption'));
        $this->assertSame('https://cdn.example.test/ig-2.jpg', data_get($feed, 'instagram.posts.1.image_url'));
        $this->assertSame(['42 lajků', '3 komentářů'], data_get($feed, 'instagram.posts.0.stats'));
        $this->assertSame('Děkujeme fanouškům!', data_get($feed, 'facebook.posts.0.message'));
        $this->assertSame('https://www.facebook.com/posts/fb-1', data_get($feed, 'facebook.posts.0.permalink'));
        Http::assertSent(fn (Request $request): bool => str_ends_with(parse_url($request->url(), PHP_URL_PATH), '/instagram-account/media')
            && $request['fields'] === 'caption,media_type,media_url,thumbnail_url,timestamp,like_count,comments_count');
        Http::assertSent(fn (Request $request): bool => str_ends_with(parse_url($request->url(), PHP_URL_PATH), '/facebook-page/posts')
            && $request['fields'] === 'message,permalink_url,created_time');
        Http::assertSentCount(2);
    }

    public function test_it_uses_the_cache_and_does_not_call_meta_again(): void
    {
        Http::fake([
            'https://graph.facebook.com/*' => Http::response(['data' => []]),
        ]);

        $service = app(MetaSocialFeedService::class);
        $service->homepageFeed();
        $service->homepageFeed();

        Http::assertSentCount(2);
    }

    public function test_it_returns_an_empty_feed_without_configuration_or_after_an_api_error(): void
    {
        config()->set('services.meta.access_token', null);
        Http::fake();

        $this->assertSame([
            'instagram' => ['posts' => []],
            'facebook' => ['posts' => []],
        ], app(MetaSocialFeedService::class)->homepageFeed());
        Http::assertNothingSent();

        config()->set('services.meta.access_token', 'test-access-token');
        Http::fake([
            'https://graph.facebook.com/*' => Http::response(['error' => ['message' => 'Expired token']], 400),
        ]);
        Cache::forget('meta.social.homepage-feed');

        $this->assertSame([
            'instagram' => ['posts' => []],
            'facebook' => ['posts' => []],
        ], app(MetaSocialFeedService::class)->homepageFeed());
    }
}
