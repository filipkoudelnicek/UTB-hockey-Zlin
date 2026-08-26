<?php

declare(strict_types=1);

namespace App\Services;

use Awcodes\Curator\Facades\Glide;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CuratorImageOptimizer
{
    /** A balanced WebP quality that preserves detail while reducing file size. */
    public const WEBP_QUALITY = 85;

    /** Prevent unnecessarily large uploads without ever upscaling an image. */
    public const MAX_DIMENSION = 2560;

    /** @var list<string> */
    private const OPTIMIZABLE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'bmp', 'webp'];

    /**
     * Re-encodes a raster upload as WebP and returns the media metadata that
     * must be stored with it. SVGs, GIFs, and AVIFs are deliberately excluded:
     * converting them can respectively break vectors, animation, or a format
     * that is already optimized.
     *
     * @return array{path: string, width: int, height: int, size: int, type: string, ext: string}|null
     */
    public function optimize(string $diskName, string $path, string $visibility, string $extension): ?array
    {
        if (! in_array(mb_strtolower($extension), self::OPTIMIZABLE_EXTENSIONS, true)) {
            return null;
        }

        $storage = Storage::disk($diskName);

        if (! $storage->exists($path)) {
            return null;
        }

        $image = Glide::getServer()
            ->getApi()
            ->getImageManager()
            ->read($storage->get($path))
            ->scaleDown(self::MAX_DIMENSION, self::MAX_DIMENSION);

        $optimizedPath = (string) Str::of($path)
            ->replaceLast('.' . pathinfo($path, PATHINFO_EXTENSION), '.webp');

        $storage->put(
            $optimizedPath,
            $image->toWebp(self::WEBP_QUALITY)->toString(),
            $visibility,
        );

        if ($optimizedPath !== $path) {
            $storage->delete($path);
        }

        $server = Glide::getServer();
        $server->deleteCache($path);
        $server->deleteCache($optimizedPath);

        return [
            'path' => $optimizedPath,
            'width' => $image->width(),
            'height' => $image->height(),
            'size' => $storage->size($optimizedPath),
            'type' => 'image/webp',
            'ext' => 'webp',
        ];
    }
}
