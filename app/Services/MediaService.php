<?php

namespace App\Services;

use Awcodes\Curator\Models\Media;

class MediaService
{
    /**
     * Získá URL obrázku podle jeho ID z Curator
     */
    public static function getMediaUrl($mediaId)
    {
        if (!$mediaId) {
            return null;
        }
        
        if (! array_key_exists($mediaId, self::$pathCache)) {
            self::$pathCache[$mediaId] = Media::find($mediaId)?->path;
        }

        $path = self::$pathCache[$mediaId];
        return $path ? '/uploads/' . ltrim($path, '/') : null;
    }

    /** @var array<int|string, string|null> */
    private static array $pathCache = [];

    /** Načte cesty k více médiím jedním dotazem, aby se při výpisu nedotazovalo po jednom. */
    public static function preload(iterable $mediaIds): void
    {
        $ids = collect($mediaIds)->filter()->unique()->reject(fn ($id) => array_key_exists($id, self::$pathCache))->values();
        if ($ids->isEmpty()) {
            return;
        }

        $found = Media::whereKey($ids)->pluck('path', 'id');
        foreach ($ids as $id) {
            self::$pathCache[$id] = $found[$id] ?? null;
        }
    }

    /**
     * Vrátí absolutní URL obrázku podle ID
     */
    public static function getMediaFullUrl($mediaId): ?string
    {
        if (!$mediaId) {
            return null;
        }

        $media = Media::find($mediaId);

        return $media ? url('/uploads/' . ltrim($media->path, '/')) : null;
    }

}