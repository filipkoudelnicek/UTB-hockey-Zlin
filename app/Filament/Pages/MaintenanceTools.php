<?php

namespace App\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Throwable;

class MaintenanceTools extends Page
{
    protected string $view = 'filament.pages.maintenance-tools';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $navigationLabel = 'Údržba systému';

    protected static ?string $title = 'Údržba systému';

    protected static string|\UnitEnum|null $navigationGroup = 'Nastavení';

    protected static ?int $navigationSort = 99;

    public string $commandResultTitle = '';

    public string $commandResultOutput = '';

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
                ->action(function (): void {
                    try {
                        $exitCode = Artisan::call('optimize:clear');
                        $this->showCommandResult(
                            $exitCode === 0
                                ? 'Optimalizační cache byla vyčištěna'
                                : 'Cache se nepodařilo vyčistit',
                            Artisan::output(),
                            $exitCode === 0,
                        );
                    } catch (Throwable $exception) {
                        Log::error('Optimize clear through maintenance tools failed.', [
                            'exception' => $exception,
                        ]);

                        $this->showCommandResult(
                            'Cache se nepodařilo vyčistit',
                            'Příkaz se na serveru nepodařilo spustit.',
                            false,
                        );
                    }
                }),
            Action::make('migrate')
                ->label('Spustit migrace')
                ->icon(Heroicon::OutlinedCircleStack)
                ->color('danger')
                ->requiresConfirmation()
                ->modalDescription('Provede čekající změny databázového schématu. Tato akce může ovlivnit běžící web.')
                ->action(function (): void {
                    try {
                        $exitCode = Artisan::call('migrate', ['--force' => true]);
                        $this->showCommandResult(
                            $exitCode === 0
                                ? 'Databázové migrace byly úspěšně spuštěny'
                                : 'Spuštění databázových migrací skončilo chybou',
                            Artisan::output(),
                            $exitCode === 0,
                        );
                    } catch (Throwable $exception) {
                        Log::error('Database migrations through maintenance tools failed.', [
                            'exception' => $exception,
                        ]);

                        $this->showCommandResult(
                            'Migrace se nepodařilo spustit',
                            'Příkaz se na serveru nepodařilo spustit.',
                            false,
                        );
                    }
                }),
        ];
    }

    private function showCommandResult(string $title, string $output, bool $successful): void
    {
        $this->commandResultTitle = $title;
        $this->commandResultOutput = trim($output);

        Notification::make()
            ->title($title)
            ->color($successful ? 'success' : 'danger')
            ->send();

        $this->dispatch('open-modal', id: 'maintenance-command-result');
    }
}
