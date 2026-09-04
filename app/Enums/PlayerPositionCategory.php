<?php

namespace App\Enums;

enum PlayerPositionCategory: string
{
    case Goalkeeper = 'goalkeeper';
    case Defender = 'defender';
    case Forward = 'forward';

    public function label(): string
    {
        return match ($this) {
            self::Goalkeeper => 'Brankář',
            self::Defender => 'Obránce',
            self::Forward => 'Útočník',
        };
    }
}
