<?php

namespace App\Support;

class HighlightedHeading
{
    public const ACCENT_OPEN = '<span data-highlight="accent">';
    public const ACCENT_CLOSE = '</span>';

    public static function fromLegacy(?string $title, ?string $accent): string
    {
        $title = trim((string) $title);
        $accent = trim((string) $accent);

        if ($accent === '') {
            return $title;
        }

        return trim($title . ' ' . self::ACCENT_OPEN . $accent . self::ACCENT_CLOSE);
    }
}
