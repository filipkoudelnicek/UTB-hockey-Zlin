<?php

namespace App\Livewire;

use App\Services\UrlService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class GenerateSitemapButton extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public function generateSitemapAction(): Action
    {
        return Action::make('generateSitemap')
            ->label('Generovat sitemap')
            ->icon('heroicon-o-map')
            ->color('gray')
            ->requiresConfirmation()
            ->modalHeading('Generovat sitemap?')
            ->modalDescription('Sitemap bude přegenerován ze všech aktivních stránek a článků.')
            ->modalSubmitActionLabel('Generovat')
            ->action(function (): void {
                $this->generate();
            });
    }

    public function generate(): void
    {
        abort_unless(auth()->user()?->hasPermission('website.settings'), 403);

        Notification::make()
            ->title(UrlService::generateSitemap())
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('livewire.generate-sitemap-button');
    }
}
