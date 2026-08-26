<?php

declare(strict_types=1);

namespace App\Observers;

use App\Services\CuratorImageOptimizer;
use Awcodes\Curator\Models\Media;

class OptimizeCuratorMedia
{
    public function __construct(private readonly CuratorImageOptimizer $optimizer) {}

    /**
     * Curator's uploader has already persisted the temporary upload when this
     * runs. Updating its metadata here covers both the single and multi-upload
     * flows before the media record is inserted.
     */
    public function creating(Media $media): void
    {
        $this->optimize($media);
    }

    /**
     * File replacement keeps the existing media record. Reprocess a new
     * non-WebP image after Curator has moved it into its final location.
     */
    public function updating(Media $media): void
    {
        if ($media->isDirty('ext') && $media->ext !== 'webp') {
            $this->optimize($media);
        }
    }

    private function optimize(Media $media): void
    {
        if (blank($media->disk) || blank($media->path) || blank($media->ext)) {
            return;
        }

        $optimized = $this->optimizer->optimize(
            $media->disk,
            $media->path,
            $media->visibility ?? config('curator.default_visibility', 'public'),
            $media->ext,
        );

        if ($optimized === null) {
            return;
        }

        $media->fill($optimized);
    }
}
