<?php

namespace App\Filament\Modules;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

/**
 * Reusable media helpers for Filament schemas.
 *
 * Usage:
 *   MediaModule::single('content.hero_image', 'Hlavní obrázek')
 *   MediaModule::gallery('content.gallery', 'Galerie')
 *   MediaModule::slider('content.slides', 'Slider')   // includes title/text/button per slide
 */
class MediaModule
{
    /**
     * Single CuratorPicker.
     */
    public static function single(string $field, string $label = 'Obrázek'): CuratorPicker
    {
        return CuratorPicker::make($field)->label($label);
    }

    /**
     * Repeater of images (gallery without extra meta).
     */
    public static function gallery(string $field, string $label = 'Galerie'): Repeater
    {
        return Repeater::make($field)
            ->label($label)
            ->schema([
                CuratorPicker::make('image')->label('Obrázek')->required(),
                TextInput::make('alt')->label('Popis obrázku (alt)'),
            ])
            ->columns(2)
            ->reorderable()
            ->columnSpanFull();
    }

    /**
     * Slider repeater: image + title/text + optional link button.
     * Each slide stores: { image, sub_title, title, text, button: { label, type, model, id, url } }
     */
    public static function slider(string $field, string $label = 'Slider'): Repeater
    {
        return Repeater::make($field)
            ->label($label)
            ->schema([
                CuratorPicker::make('image')->label('Obrázek')->required()->columnSpanFull(),
                TextInput::make('sub_title')->label('Podnadpis'),
                TextInput::make('title')->label('Nadpis')->required(),
                Textarea::make('text')->label('Text')->rows(3)->columnSpanFull(),
                ...LinkModule::make('button', 'Tlačítko'),
            ])
            ->columns(2)
            ->reorderable()
            ->columnSpanFull();
    }
}
