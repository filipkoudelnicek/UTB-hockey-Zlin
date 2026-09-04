<?php

namespace App\Filament\Modules\PageTypes;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Support\Icons\Heroicon;

class BlogPageType
{
    public static function getSchema(): array
    {
        return [
            Section::make('Výpis aktualit')
                ->description('Aktuality se načítají automaticky z přehledu Aktuality.')
                ->icon(Heroicon::OutlinedNewspaper)
                ->iconColor('primary')
                ->schema([
                    Text::make('Na této šabloně nejsou žádná další nastavení. Pro úpravu článků přejděte do sekce Aktuality.'),
                ]),
        ];
    }
}
