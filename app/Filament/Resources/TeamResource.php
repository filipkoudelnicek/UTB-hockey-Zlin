<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Models\CompetitionStanding;
use App\Models\GameMatch;
use App\Models\MatchPlayerStat;
use App\Models\Team;
use App\Models\Venue;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class TeamResource extends AdminResource
{
    protected static ?string $model = Team::class;
    protected static ?string $permissionKey = 'sport.settings';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    protected static string|\UnitEnum|null $navigationGroup = 'Sportovní nastavení';
    protected static ?string $navigationLabel = 'Týmy';
    protected static ?string $modelLabel = 'Tým';
    protected static ?string $pluralModelLabel = 'Týmy';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make('Základní informace')->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->label('Název týmu')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                    TextInput::make('short_name')->label('Zkratka týmu'),
                    Select::make('home_venue_id')
                        ->label('Domácí stadion')
                        ->options(fn () => Venue::orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->helperText('Při výběru tohoto týmu jako domácího se stadion v zápase doplní automaticky.'),
                    Toggle::make('is_active')
                        ->label('Zobrazovat v nabídce')
                        ->helperText('Vypnutý tým se nenabízí při běžné správě zápasů a soupisek.')
                        ->default(true),
                ]),
                Grid::make(2)->schema([
                    CuratorPicker::make('logo_media_id')->label('Logo týmu'),
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
            ImageColumn::make('logo')
                ->label('')
                ->getStateUsing(fn (Team $record) => $record->logo_url ? url($record->logo_url) : null)
                ->imageSize(32)
                ->square()
                ->extraImgAttributes(['class' => 'object-contain']),
            TextColumn::make('name')->label('Tým')->searchable()->sortable(),
            TextColumn::make('short_name')->label('Zkratka'),
            IconColumn::make('is_active')->boolean()->label('Aktivní'),
        ])->recordActions([
            Actions\EditAction::make(),
            static::makeDeleteAction(),
        ]);
    }

    public static function makeDeleteAction(): Actions\DeleteAction
    {
        return Actions\DeleteAction::make()
            ->before(function (Team $record, Actions\DeleteAction $action): void {
                $dependencies = [];

                if ($record->competitionSeasons()->exists()) {
                    $dependencies[] = 'soutěžních ročnících';
                }

                if (GameMatch::query()
                    ->where('home_team_id', $record->getKey())
                    ->orWhere('away_team_id', $record->getKey())
                    ->exists()) {
                    $dependencies[] = 'zápasech';
                }

                if (CompetitionStanding::query()->where('team_id', $record->getKey())->exists()) {
                    $dependencies[] = 'tabulce soutěže';
                }

                if (MatchPlayerStat::query()->where('team_id', $record->getKey())->exists()) {
                    $dependencies[] = 'statistikách hráčů';
                }

                if ($dependencies === []) {
                    return;
                }

                Notification::make()
                    ->title('Tým nelze smazat.')
                    ->body('Tým je stále použit v ' . implode(', ', $dependencies) . '. Deaktivujte jej místo mazání nebo nejdříve odstraňte tyto vazby.')
                    ->danger()
                    ->send();

                $action->halt();
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
