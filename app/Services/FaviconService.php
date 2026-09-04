<?php

declare(strict_types=1);

namespace App\Services;

use Awcodes\Curator\Facades\Glide;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class FaviconService
{
    /** Google's recommended base size for a favicon. */
    private const SIZE = 48;

    /**
     * Creates a standards-compliant ICO file at the public web root from the
     * separately uploaded favicon. ICO files can contain PNG data, which
     * preserves transparency and works in all modern browsers and Google.
     */
    public function publishFromPath(string $sourcePath): void
    {
        $storage = Storage::disk('public');

        if (! $storage->exists($sourcePath)) {
            throw new RuntimeException('Nahraný soubor favicony nebyl v úložišti nalezen.');
        }

        $ico = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION)) === 'ico'
            ? $storage->get($sourcePath)
            : $this->asIco(
                Glide::getServer()
                    ->getApi()
                    ->getImageManager()
                    ->read($storage->get($sourcePath))
                    ->cover(self::SIZE, self::SIZE)
                    ->toPng()
                    ->toString(),
            );

        if (File::put(public_path('favicon.ico'), $ico) === false) {
            throw new RuntimeException('Do public/favicon.ico nelze zapisovat. Nastavte pro PHP zápisové oprávnění k tomuto souboru.');
        }
    }

    private function asIco(string $png): string
    {
        return pack(
            'vvvCCCCvvVV',
            0,
            1,
            1,
            self::SIZE,
            self::SIZE,
            0,
            0,
            1,
            32,
            strlen($png),
            22,
        ).$png;
    }
}
