<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VenueResource\Pages;
use App\Models\Venue;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class VenueResource extends AdminResource
{
    protected static ?string $model = Venue::class;
    protected static ?string $permissionKey = 'sport.settings';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';
    protected static string|\UnitEnum|null $navigationGroup = 'Sportovní nastavení';
    protected static ?string $navigationLabel = 'Stadiony';
    protected static ?string $modelLabel = 'Stadion';
    protected static ?string $pluralModelLabel = 'Stadiony';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make('Základní informace')->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->label('Název stadionu')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                    TextInput::make('city')->label('Město'),
                    TextInput::make('address')->label('Adresa'),
                    TextInput::make('map_url')->label('Odkaz na mapu')->url(),
                ]),
            ]),
            Section::make('Pokročilé nastavení')
                ->description('Doplňující údaje pro odkaz na stadion a jeho zobrazení na mapě.')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('slug')
                            ->label('Identifikátor v URL')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Vytvoří se automaticky z názvu.'),
                        TextInput::make('latitude')->label('Zeměpisná šířka')->numeric(),
                        TextInput::make('longitude')->label('Zeměpisná délka')->numeric(),
                    ]),
                ])
                ->collapsed(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Stadion')->searchable(),
            TextColumn::make('city')->label('Město'),
        ])->recordActions([
            Actions\Action::make('map')
                ->label('Mapa')
                ->icon('heroicon-o-map-pin')
                ->color('info')
                ->url(fn (Venue $record) => $record->map_url, shouldOpenInNewTab: true)
                ->visible(fn (Venue $record): bool => filled($record->map_url)),
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVenues::route('/'),
            'create' => Pages\CreateVenue::route('/create'),
            'edit' => Pages\EditVenue::route('/{record}/edit'),
        ];
    }
}
