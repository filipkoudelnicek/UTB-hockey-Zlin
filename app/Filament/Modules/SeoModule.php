<?php

namespace App\Filament\Modules;

use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Fieldset;
use Awcodes\Curator\Components\Forms\CuratorPicker;

class SeoModule
{
    public static function make(): array
    {
        return [
            Section::make('SEO a Sociální sítě')
                ->schema([
                    Fieldset::make('SEO Nastavení')
                        ->schema([
                            TextInput::make('content.seo.title')
                                ->label('Meta Titulek')
                                ->maxLength(60),

                            Textarea::make('content.seo.description')
                                ->label('Meta Popis')
                                ->rows(3)
                                ->maxLength(160),

                            TextInput::make('content.seo.canonical')
                                ->label('Kanonická URL')
                                ->placeholder('https://example.com/moje-stranka'),
                        ]),

                    Fieldset::make('Open Graph (Facebook & Sociální sítě)')
                        ->schema([
                            TextInput::make('content.seo.og_title')
                                ->label('OG Titulek')
                                ->maxLength(60),

                            TextInput::make('content.seo.og_type')
                                ->label('OG Typ'),

                            Textarea::make('content.seo.og_desc')
                                ->label('OG Popis')
                                ->rows(3)
                                ->maxLength(160),

                            CuratorPicker::make('content.seo.og_image')->label('OG Obrázek')->helperText('Doporučený rozměr: 1200x630 px'),
                        ]),
                ]),
        ];
    }
}
