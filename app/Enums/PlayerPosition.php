<?php

namespace App\Enums;

enum PlayerPosition: string
{
    case Goalkeeper = 'G';
    case Center = 'C';
    case RightWing = 'RW';
    case LeftWing = 'LW';
    case RightDefense = 'RD';
    case LeftDefense = 'LD';

    public function label(): string
    {
        return match ($this) {
            self::Goalkeeper => 'Brankář',
            self::Center => 'Centr',
            self::RightWing => 'Pravé křídlo',
            self::LeftWing => 'Levé křídlo',
            self::RightDefense => 'Pravý obránce',
            self::LeftDefense => 'Levý obránce',
        };
    }

    public function shortLabel(): string
    {
        return $this->value;
    }

    /**
     * Broad group a specific position belongs to, used for team page listing.
     */
    public function category(): PlayerPositionCategory
    {
        return match ($this) {
            self::Goalkeeper => PlayerPositionCategory::Goalkeeper,
            self::RightDefense, self::LeftDefense => PlayerPositionCategory::Defender,
            self::Center, self::RightWing, self::LeftWing => PlayerPositionCategory::Forward,
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => "{$case->value} — {$case->label()}"])->all();
    }
}
