<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class MaintenanceTools extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $navigationLabel = 'Údržba systému';

    protected static ?string $title = 'Údržba systému';

    protected static string|\UnitEnum|null $navigationGroup = 'Nastavení';

    protected static ?int $navigationSort = 99;

    public static function canAccess(): bool
    {
        return auth()->user()?->isRoot() ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('optimizeClear')
                ->label('Vyčistit cache')
                ->icon(Heroicon::OutlinedBolt)
                ->color('warning')
                ->requiresConfirmation()
                ->modalDescription('Vymaže optimalizační cache aplikace.')
                ->action(fn () => redirect()->to(url('/admin-utb/maintenance/optimize-clear'))),
            Action::make('migrate')
                ->label('Spustit migrace')
                ->icon(Heroicon::OutlinedCircleStack)
                ->color('danger')
                ->requiresConfirmation()
                ->modalDescription('Provede čekající změny databázového schématu. Tato akce může ovlivnit běžící web.')
                ->action(fn () => redirect()->to(url('/admin-utb/maintenance/migrate'))),
        ];
    }
}
