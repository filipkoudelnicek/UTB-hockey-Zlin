<?php

namespace App\Filament\Forms\Components;

use App\Support\HighlightedHeading;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Utilities\Get;

class HighlightedTextInput extends Field
{
    protected string $view = 'filament.forms.components.highlighted-text-input';

    protected ?string $legacyTitlePath = null;

    protected ?string $legacyAccentPath = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Unlike Filament's built-in inputs, this field is rendered as a
        // contenteditable element. Explicit dehydration makes sure its value
        // is included in the form data when the page is saved.
        $this->dehydrated();

        $this->helperText('Označte text a oranžovým tlačítkem barvu zapnete nebo vypnete.');

        $this->afterStateHydrated(function (HighlightedTextInput $component, mixed $state, Get $get): void {
            if (filled($state) || ! $component->legacyTitlePath) {
                return;
            }

            $component->state(HighlightedHeading::fromLegacy(
                $get($component->legacyTitlePath),
                $component->legacyAccentPath ? $get($component->legacyAccentPath) : null,
            ));
        });
    }

    public function legacy(string $titlePath, ?string $accentPath = null): static
    {
        $this->legacyTitlePath = $titlePath;
        $this->legacyAccentPath = $accentPath;

        return $this;
    }
}
