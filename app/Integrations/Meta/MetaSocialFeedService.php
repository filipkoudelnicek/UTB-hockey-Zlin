<?php

namespace App\Integrations\Meta;

use Carbon\CarbonImmutable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaSocialFeedService
{
    /**
     * Return only the fields rendered by the homepage social cards.
     *
     * Failed or incomplete API responses deliberately become empty feeds. The
     * Blade template then continues to render its existing CMS content.
     */
    public function homepageFeed(): array
    {
        if (! $this->isConfigured()) {
            return $this->emptyFeed();
        }

        try {
            return Cache::remember(
                'meta.social.homepage-feed',
                now()->addSeconds((int) config('services.meta.cache_ttl', 900)),
                fn (): array => $this->fetchHomepageFeed(),
            );
        } catch (\Throwable $exception) {
            Log::warning('Meta social feed could not be loaded.', [
                'exception' => $exception::class,
            ]);

            return $this->emptyFeed();
        }
    }

    private function fetchHomepageFeed(): array
    {
        return [
            'instagram' => $this->instagramFeed(),
            'facebook' => $this->facebookFeed(),
        ];
    }

    private function instagramFeed(): array
    {
        $instagramAccountId = (string) config('services.meta.instagram_account_id');

        if ($instagramAccountId === '') {
            return ['posts' => []];
        }

        $response = $this->client()->get($this->endpoint("{$instagramAccountId}/media"), $this->parameters([
            'fields' => 'caption,media_type,media_url,thumbnail_url,timestamp,like_count,comments_count',
            'limit' => 2,
        ]));

        if (! $response->successful()) {
            $this->logApiFailure('instagram_media', $response->status());

            return ['posts' => []];
        }

        $posts = collect($response->json('data', []))
            ->map(fn (array $post): ?array => $this->normalizeInstagramPost($post))
            ->filter()
            ->values()
            ->all();

        return compact('posts');
    }

    private function facebookFeed(): array
    {
        $facebookPageId = (string) config('services.meta.facebook_page_id');

        if ($facebookPageId === '') {
            return ['posts' => []];
        }

        $response = $this->client()->get($this->endpoint("{$facebookPageId}/posts"), $this->parameters([
            'fields' => 'message,permalink_url,created_time',
            'limit' => 1,
        ]));

        if (! $response->successful()) {
            $this->logApiFailure('facebook_posts', $response->status());

            return ['posts' => []];
        }

        $posts = collect($response->json('data', []))
            ->map(fn (array $post): ?array => $this->normalizeFacebookPost($post))
            ->filter()
            ->values()
            ->all();

        return compact('posts');
    }

    private function normalizeInstagramPost(array $post): ?array
    {
        $imageUrl = ($post['media_type'] ?? null) === 'VIDEO'
            ? ($post['thumbnail_url'] ?? null)
            : ($post['media_url'] ?? null);

        if (! filled($post['caption'] ?? null) && ! filled($imageUrl)) {
            return null;
        }

        return [
            'image_url' => $imageUrl,
            'caption' => $post['caption'] ?? null,
            'time_label' => $this->timeLabel($post['timestamp'] ?? null),
            'stats' => array_values(array_filter([
                isset($post['like_count']) ? "{$post['like_count']} lajků" : null,
                isset($post['comments_count']) ? "{$post['comments_count']} komentářů" : null,
            ])),
        ];
    }

    private function normalizeFacebookPost(array $post): ?array
    {
        if (! filled($post['message'] ?? null)) {
            return null;
        }

        return [
            'message' => $post['message'] ?? null,
            'permalink' => $post['permalink_url'] ?? null,
            'time_label' => $this->timeLabel($post['created_time'] ?? null),
        ];
    }

    private function client(): PendingRequest
    {
        return Http::acceptJson()
            ->timeout(5)
            ->retry(1, 200);
    }

    private function endpoint(string $path): string
    {
        return sprintf(
            'https://graph.facebook.com/%s/%s',
            config('services.meta.graph_version', 'v25.0'),
            ltrim($path, '/'),
        );
    }

    private function parameters(array $parameters): array
    {
        return [
            ...$parameters,
            'access_token' => (string) config('services.meta.access_token'),
        ];
    }

    private function timeLabel(?string $timestamp): ?string
    {
        if (! filled($timestamp)) {
            return null;
        }

        try {
            return CarbonImmutable::parse($timestamp)
                ->locale(config('app.locale'))
                ->diffForHumans();
        } catch (\Throwable) {
            return null;
        }
    }

    private function isConfigured(): bool
    {
        return filled(config('services.meta.access_token'))
            && (filled(config('services.meta.instagram_account_id')) || filled(config('services.meta.facebook_page_id')));
    }

    private function emptyFeed(): array
    {
        return [
            'instagram' => ['posts' => []],
            'facebook' => ['posts' => []],
        ];
    }

    private function logApiFailure(string $resource, int $status): void
    {
        Log::warning('Meta Graph API returned an unsuccessful response.', compact('resource', 'status'));
    }
}
