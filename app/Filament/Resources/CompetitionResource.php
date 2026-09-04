<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompetitionResource\Pages;
use App\Filament\Resources\CompetitionResource\RelationManagers;
use App\Models\Competition;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CompetitionResource extends AdminResource
{
    protected static ?string $model = Competition::class;
    protected static ?string $permissionKey = 'sport.settings';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-trophy';
    protected static string|\UnitEnum|null $navigationGroup = 'Sportovní nastavení';
    protected static ?string $navigationLabel = 'Soutěže';
    protected static ?string $modelLabel = 'Soutěž';
    protected static ?string $pluralModelLabel = 'Soutěže';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make('Základní informace')->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->label('Název soutěže')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                    TextInput::make('short_name')->label('Zkratka')->maxLength(30),
                    CuratorPicker::make('logo_media_id')->label('Logo soutěže'),
                ]),
            ]),

            Section::make('Pokročilé nastavení')
                ->description('Identifikátor v URL se vytváří automaticky. Běžně jej není potřeba měnit.')
                ->schema([
                    TextInput::make('slug')
                        ->label('Identifikátor v URL')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('Vytvoří se automaticky z názvu.'),
                ])
                ->collapsed(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Název')->searchable(),
            TextColumn::make('short_name')->label('Zkratka'),
            TextColumn::make('competition_seasons_count')->counts('competitionSeasons')->label('Ročníky'),
        ])->recordActions([
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SeasonsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompetitions::route('/'),
            'create' => Pages\CreateCompetition::route('/create'),
            'edit' => Pages\EditCompetition::route('/{record}/edit'),
        ];
    }
}
