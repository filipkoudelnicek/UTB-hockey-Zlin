<?php

namespace App\Filament\Modules;

use App\Models\Article;
use App\Models\Page;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

/**
 * Reusable link picker: internal (Page/Article by ID) or external URL.
 *
 * Usage in any Filament schema:
 *   ...LinkModule::make('content.cta'),        // inlined as array spread
 *   ...LinkModule::make('button', 'Tlačítko'),
 *
 * Inside a Repeater, use relative path:
 *   ...LinkModule::make('link'),
 *
 * Stored JSON shape:
 *   { "label": "Více", "type": "internal", "model": "page", "id": 5 }
 *   { "label": "Více", "type": "external", "url": "https://...", "target": "_blank" }
 */
class LinkModule
{
    public static function make(string $prefix, string $label = 'Odkaz', bool $withLabel = true): array
    {
        $labelField = $withLabel
            ? [
                TextInput::make("{$prefix}.label")
                    ->label('Text tlačítka')
                    ->columnSpanFull(),
            ]
            : [];

        return [
            Fieldset::make($label)
                ->schema(array_merge($labelField, [

                    Radio::make("{$prefix}.type")
                        ->label('Typ odkazu')
                        ->options([
                            'internal' => 'Interní (stránka / článek)',
                            'external' => 'Externí (URL, otevře v novém okně)',
                        ])
                        ->default('internal')
                        ->live()
                        ->inline(),

                    // --- INTERNAL ---
                    Select::make("{$prefix}.model")
                        ->label('Typ obsahu')
                        ->options([
                            'page'    => 'Stránka',
                            'article' => 'Článek',
                        ])
                        ->default('page')
                        ->live()
                        ->hidden(fn ($get) => $get("{$prefix}.type") !== 'internal'),

                    Select::make("{$prefix}.id")
                        ->label('Stránka / Článek')
                        ->options(function ($get) use ($prefix) {
                            if ($get("{$prefix}.model") === 'article') {
                                return Article::where('active', 1)
                                    ->get()
                                    ->mapWithKeys(fn ($a) => [
                                        $a->id => "[{$a->lang_locale}] {$a->title}",
                                    ]);
                            }

                            return Page::where('active', 1)
                                ->whereNotNull('full_slug')
                                ->get()
                                ->mapWithKeys(fn ($p) => [
                                    $p->id => "[{$p->lang_locale}] {$p->title}  (/{$p->full_slug})",
                                ]);
                        })
                        ->searchable()
                        ->hidden(fn ($get) => $get("{$prefix}.type") !== 'internal'),

                    // --- EXTERNAL ---
                    TextInput::make("{$prefix}.url")
                        ->label('URL')
                        ->url()
                        ->placeholder('https://')
                        ->hidden(fn ($get) => $get("{$prefix}.type") !== 'external'),
                ]))
                ->columns(2),
        ];
    }
}
