<?php

namespace App\Enums;

enum MatchType: string
{
    case League = 'league';
    case Friendly = 'friendly';

    public function label(): string
    {
        return match ($this) {
            self::League => 'Liga',
            self::Friendly => 'Přátelský zápas',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }

    public function requiresCompetition(): bool
    {
        return $this === self::League;
    }
}
