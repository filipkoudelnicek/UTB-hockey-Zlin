<?php

namespace Tests\Unit;

use App\Observers\OptimizeCuratorMedia;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Tests\TestCase;

class CuratorImageOptimizerTest extends TestCase
{
    public function test_it_converts_each_uploaded_photo_to_a_resized_webp(): void
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('Image optimization requires the GD extension.');
        }

        Storage::fake('public');

        foreach (['campaign.jpg', 'product.png'] as $filename) {
            $this->storeLargeImage($filename);

            $media = new Media([
                'disk' => 'public',
                'visibility' => 'public',
                'path' => "media/{$filename}",
                'ext' => pathinfo($filename, PATHINFO_EXTENSION),
            ]);

            app(OptimizeCuratorMedia::class)->creating($media);

            $this->assertSame('webp', $media->ext);
            $this->assertSame('image/webp', $media->type);
            $this->assertLessThanOrEqual(2560, $media->width);
            $this->assertLessThanOrEqual(2560, $media->height);
            $this->assertTrue(Storage::disk('public')->exists($media->path));
            $this->assertFalse(Storage::disk('public')->exists("media/{$filename}"));
            $this->assertSame('RIFF', substr(Storage::disk('public')->get($media->path), 0, 4));
        }
    }

    private function storeLargeImage(string $filename): void
    {
        $image = ImageManager::gd()->create(3000, 1000)->fill('ff0000');
        $encoded = str_ends_with($filename, '.png')
            ? $image->toPng()->toString()
            : $image->toJpeg(100)->toString();

        Storage::disk('public')->put("media/{$filename}", $encoded);
    }
}
