<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\UrlService;
use Filament\Actions\Action;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class GlobalSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public function getView(): string
    {
        return 'filament.pages.global-settings';
    }

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;
    protected static ?string $navigationLabel  = 'Nastavení webu';
    protected static string|\UnitEnum|null $navigationGroup  = 'Nastavení';
    protected static ?int    $navigationSort   = 3;

    public array $data = [];

    public function getHeaderActions(): array
    {
        return [
            Action::make('generateSitemap')
                ->label('Generovat sitemap')
                ->icon(Heroicon::OutlinedMap)
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Generovat sitemap?')
                ->modalDescription('Sitemap bude přegenerován ze všech aktivních stránek a článků a uložen do public/sitemap.xml.')
                ->modalSubmitActionLabel('Generovat')
                ->action(function () {
                    $message = UrlService::generateSitemap();
                    Notification::make()
                        ->title($message)
                        ->success()
                        ->send();
                }),
        ];
    }

    public function mount(): void
    {
        $this->form->fill(Setting::allAsArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Fieldset::make('Základní informace')
                    ->schema([
                        TextInput::make('site_name')->label('Název webu'),
                        TextInput::make('site_tagline')->label('Slogan webu'),
                        TextInput::make('site_email')->label('Kontaktní e-mail')->email(),
                        TextInput::make('site_phone')->label('Telefon'),
                        Textarea::make('site_address')->label('Adresa')->rows(2),
                        TextInput::make('site_ic')->label('IČ'),
                        TextInput::make('site_dic')->label('DIČ'),
                        TextInput::make('site_bank_account')->label('Číslo účtu'),
                    ])->columns(2),

                Fieldset::make('Sociální sítě')
                    ->schema([
                        TextInput::make('social_facebook')->label('Facebook URL')->url(),
                        TextInput::make('social_instagram')->label('Instagram URL')->url(),
                        TextInput::make('social_linkedin')->label('LinkedIn URL')->url(),
                        TextInput::make('social_twitter')->label('X (Twitter) URL')->url(),
                        TextInput::make('social_youtube')->label('YouTube URL')->url(),
                    ])->columns(2),

                Fieldset::make('Analytika a GTM')
                    ->schema([
                        TextInput::make('google_analytics_id')
                            ->label('Google Analytics ID')
                            ->placeholder('G-XXXXXXXXXX'),
                        TextInput::make('google_tag_manager_id')
                            ->label('Google Tag Manager ID')
                            ->placeholder('GTM-XXXXXXX'),
                    ])->columns(2),

                Fieldset::make('GDPR / Cookie lišta')
                    ->schema([
                        Textarea::make('cookie_text')
                            ->label('Text cookie lišty')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('cookie_policy_url')
                            ->label('Zásady cookies – URL')
                            ->url()
                            ->placeholder('/zasady-cookies'),
                        TextInput::make('privacy_policy_url')
                            ->label('Zásady ochrany osobních údajů – URL')
                            ->url()
                            ->placeholder('/ochrana-osobnich-udaju'),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Nastavení bylo uloženo.')
            ->success()
            ->send();
    }
}
