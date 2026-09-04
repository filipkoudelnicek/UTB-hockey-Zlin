<?php

namespace App\Enums;

enum CaptainRole: string
{
    case None = 'none';
    case Captain = 'captain';
    case Assistant = 'assistant';

    public function label(): string
    {
        return match ($this) {
            self::None => 'Bez role',
            self::Captain => 'Kapitán',
            self::Assistant => 'Asistent',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
