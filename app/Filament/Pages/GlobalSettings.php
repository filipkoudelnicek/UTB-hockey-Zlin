<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Models\Team;
use App\Services\FaviconService;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;

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
    protected static ?int    $navigationSort   = 4;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermission('website.settings') ?? false;
    }

    public array $data = [];

    public function mount(): void
    {
        $data = Setting::allAsArray();
        $data['contact_form_email'] ??= $data['site_email'] ?? config('mail.from.address');

        $this->form->fill($data);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Fieldset::make('Základní informace')
                    ->schema([
                        TextInput::make('site_name')->label('Název webu'),
                        TextInput::make('site_email')->label('Kontaktní e-mail')->email(),
                        TextInput::make('contact_form_email')
                            ->label('E-mail pro kontaktní formulář')
                            ->email()
                            ->required()
                            ->helperText('Na tuto adresu budou chodit zprávy odeslané z formuláře na webu.'),
                        TextInput::make('marketing_email')->label('Marketingový e-mail')->email(),
                        TextInput::make('site_phone')->label('Telefon'),
                        Textarea::make('site_address')->label('Adresa')->rows(2),
                        TextInput::make('office_hours')->label('Provozní doba'),
                    ])->columns(2),

                Fieldset::make('Přístupnost webu')
                    ->schema([
                        Toggle::make('maintenance_mode')
                            ->label('Zobrazovat veřejnosti stránku „Web se připravuje“')
                            ->helperText('Po zapnutí se na každé veřejné URL zobrazí přípravná stránka. Administrace a přihlášení zůstávají dostupné; přihlášený administrátor vidí web standardně.')
                            ->default(false),
                    ]),

                Fieldset::make('Vizuální identita')
                    ->schema([
                        CuratorPicker::make('header_logo_media_id')
                            ->label('Logo v hlavičce')
                            ->helperText('Logo se na webu nezobrazí, dokud jej nevyberete.'),
                        CuratorPicker::make('footer_logo_media_id')
                            ->label('Logo v patičce')
                            ->helperText('Logo se na webu nezobrazí, dokud jej nevyberete.'),
                        FileUpload::make('favicon_path')
                            ->label('Favicon')
                            ->disk('public')
                            ->directory('site')
                            ->visibility('public')
                            ->acceptedFileTypes([
                                'image/x-icon',
                                'image/vnd.microsoft.icon',
                                'image/ico',
                                'image/png',
                                'image/jpeg',
                                'image/webp',
                            ])
                            ->maxSize(1024)
                            ->helperText('Nahrajte ICO, PNG nebo WebP do 1 MB. Při uložení se vytvoří public/favicon.ico pro Google i všechny prohlížeče.'),
                    ])->columns(3),

                Fieldset::make('Sportovní nastavení')
                    ->schema([
                        Select::make('club_team_id')
                            ->label('Domácí klub webu')
                            ->options(fn (): array => \Illuminate\Support\Facades\Schema::hasTable('teams')
                                ? Team::query()->orderBy('name')->pluck('name', 'id')->all()
                                : [])
                            ->searchable()
                            ->helperText('Tým, jehož pohledem se vybírají zápasy, soupiska a statistiky na veřejném webu.'),
                    ]),

                Fieldset::make('Sociální sítě')
                    ->schema([
                        TextInput::make('social_facebook')->label('Facebook URL')->url(),
                        TextInput::make('social_instagram')->label('Instagram URL')->url(),
                    ])->columns(2),

                Fieldset::make('Analytika a GTM')
                    ->schema([
                        TextInput::make('google_analytics_id')
                            ->label('Google Analytics ID')
                            ->placeholder('G-XXXXXXXXXX'),
                        TextInput::make('google_tag_manager_id')
                            ->label('Google Tag Manager ID')
                            ->placeholder('GTM-XXXXXXX'),
                        Placeholder::make('analytics_hint')
                            ->label('Důležité')
                            ->content('Pokud máte Google Analytics nastavené jako tag přímo v Google Tag Manageru, vyplňte pouze GTM ID. Vyplnění obou polí by mohlo měření návštěvnosti započítat dvakrát.')
                            ->columnSpanFull(),
                    ])->columns(2),

                Fieldset::make('GDPR / Cookie lišta')
                    ->schema([
                        Textarea::make('cookie_text')
                            ->label('Text cookie lišty')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('cookie_policy_url')
                            ->label('Zásady cookies – odkaz')
                            ->placeholder('/zasady-cookies'),
                        TextInput::make('privacy_policy_url')
                            ->label('Zásady ochrany osobních údajů – odkaz')
                            ->placeholder('/ochrana-osobnich-udaju'),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $previousFaviconPath = Setting::get('favicon_path');
        $faviconPath = $data['favicon_path'] ?? null;
        $faviconChanged = (string) $faviconPath !== (string) $previousFaviconPath;

        if ($faviconChanged && filled($faviconPath)) {
            try {
                app(FaviconService::class)->publishFromPath($faviconPath);
            } catch (\Throwable $exception) {
                report($exception);

                Notification::make()
                    ->title('Favicon se nepodařilo uložit.')
                    ->body($exception->getMessage())
                    ->danger()
                    ->send();

                return;
            }
        }

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        if ($faviconChanged) {
            Setting::set('favicon_updated_at', (string) now()->getTimestamp());

            if (
                filled($previousFaviconPath)
                && $previousFaviconPath !== $faviconPath
                && str_starts_with($previousFaviconPath, 'site/')
            ) {
                Storage::disk('public')->delete($previousFaviconPath);
            }
        }

        Notification::make()
            ->title('Nastavení bylo uloženo.')
            ->success()
            ->send();
    }
}
