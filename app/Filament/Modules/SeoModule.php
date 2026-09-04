<?php

namespace App\Filament\Modules;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Awcodes\Curator\Components\Forms\CuratorPicker;

class SeoModule
{
    public static function make(): array
    {
        return [
            Section::make('SEO a sdílení')
                ->description('Volitelné nastavení pro vyhledávače a náhled při sdílení odkazu.')
                ->collapsed()
                ->schema([
                    Tabs::make('seo')
                        ->tabs([
                            Tab::make('Vyhledávače')
                                ->icon('heroicon-o-magnifying-glass')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextInput::make('content.seo.title')
                                            ->label('Meta nadpis')
                                            ->helperText('Nadpis zobrazovaný ve výsledcích vyhledávání.')
                                            ->maxLength(60),

                                        Textarea::make('content.seo.description')
                                            ->label('Meta popisek')
                                            ->helperText('Krátký popis zobrazovaný ve výsledcích vyhledávání.')
                                            ->rows(3)
                                            ->maxLength(160),
                                    ]),

                                    TextInput::make('content.seo.canonical')
                                        ->label('Kanonická URL')
                                        ->url()
                                        ->helperText('Vyplňte jen tehdy, pokud má tato stránka používat jinou hlavní URL.')
                                        ->placeholder('https://www.example.cz/moje-stranka'),
                                ]),

                            Tab::make('Sociální sítě')
                                ->icon('heroicon-o-share')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextInput::make('content.seo.og_title')
                                            ->label('Nadpis pro sdílení')
                                            ->maxLength(60),

                                        Textarea::make('content.seo.og_desc')
                                            ->label('Popisek pro sdílení')
                                            ->rows(3)
                                            ->maxLength(160),

                                        CuratorPicker::make('content.seo.og_image')
                                            ->label('Obrázek pro sdílení')
                                            ->helperText('Doporučený rozměr: 1200 × 630 px'),
                                    ]),
                                ]),
                        ]),
                ]),
        ];
    }
}
