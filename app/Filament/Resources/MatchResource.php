<?php

namespace App\Filament\Resources;

use App\Enums\MatchType;
use App\Actions\SynchronizeMatchStatusesAction;
use App\Filament\Resources\MatchResource\Pages;
use App\Models\Article;
use App\Models\CompetitionSeason;
use App\Models\GameMatch;
use App\Models\Player;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MatchResource extends AdminResource
{
    protected static ?string $model = GameMatch::class;
    protected static ?string $permissionKey = 'sport.matches';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-play-circle';
    protected static string|\UnitEnum|null $navigationGroup = 'Běžná správa';
    protected static ?string $navigationLabel = 'Zápasy';
    protected static ?string $modelLabel = 'Zápas';
    protected static ?string $pluralModelLabel = 'Zápasy';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make('Zápas')->schema([
                Grid::make(2)->schema([
                    Select::make('match_type')
                        ->label('Typ zápasu')
                        ->options(MatchType::options())
                        ->default(MatchType::League->value)
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, $get, $set): void {
                            if ($state === MatchType::Friendly->value) {
                                $set('competition_season_id', null);

                                return;
                            }

                            if (! $get('competition_season_id')) {
                                $set('competition_season_id', CompetitionSeason::currentForClub(Team::club())?->id);
                            }
                        }),
                    Select::make('competition_season_id')
                        ->label('Ročník soutěže')
                        ->options(fn () => CompetitionSeason::orderByDesc('starts_at')->pluck('name', 'id'))
                        ->default(fn () => CompetitionSeason::currentForClub(Team::club())?->id)
                        ->searchable()
                        ->required(fn (Get $get) => $get('match_type') === MatchType::League->value)
                        ->nullable()
                        ->helperText('U přátelského zápasu může zůstat prázdný.'),
                    DateTimePicker::make('played_at')->label('Datum a čas')->seconds(false)->required(),
                    TextInput::make('ticket_url')->label('Vstupenky URL')->url(),
                ]),
                Grid::make(2)->schema([
                    Grid::make(1)->schema([
                    Select::make('home_team_id')
                        ->label('Domácí')
                        ->options(fn () => Team::active()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->different('away_team_id')
                        ->live()
                        ->afterStateUpdated(fn ($state, $set) => $set('venue_id', Team::find($state)?->home_venue_id)),
                        TextInput::make('home_score')->label('Skóre domácí')->numeric()->minValue(0)->nullable(),
                    ]),
                    Grid::make(1)->schema([
                        Select::make('away_team_id')->label('Hosté')->options(fn () => Team::active()->orderBy('name')->pluck('name', 'id'))->searchable()->required()->different('home_team_id'),
                        TextInput::make('away_score')->label('Skóre hosté')->numeric()->minValue(0)->nullable(),
                    ]),
                ]),
                Grid::make(2)->schema([
                    Select::make('venue_id')
                        ->label('Stadion')
                        ->options(fn () => Venue::orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->nullable()
                        ->visible(fn (Get $get): bool => filled($get('home_team_id'))),
                    Select::make('report_article_id')->label('Report článek')->options(fn () => Article::orderByDesc('publish_time')->get()->mapWithKeys(fn (Article $article) => [$article->id => $article->plain_title]))->searchable()->nullable(),
                ]),
            ])->columns(1),

            Section::make('Statistiky hráčů')
                ->description('Jednotlivé zápasové statistiky. Souhrnné statistiky se z nich dopočítají automaticky.')
                ->schema([
                    Select::make('player_stats_search')
                        ->label('Najít hráče v soupisce')
                        ->placeholder('Napište jméno hráče')
                        ->options(fn () => Player::query()->orderBy('last_name')->orderBy('first_name')->get()->pluck('full_name', 'id'))
                        ->searchable()
                        ->live()
                        ->dehydrated(false)
                        ->afterStateUpdated(function ($state, Get $get, Set $set): void {
                            if (! $state) {
                                return;
                            }

                            $playerStats = collect($get('playerStats') ?? []);

                            if (! $playerStats->contains(fn (array $stat): bool => (int) ($stat['player_id'] ?? 0) === (int) $state)) {
                                return;
                            }

                            $set('playerStats', $playerStats
                                ->sortByDesc(fn (array $stat): bool => (int) ($stat['player_id'] ?? 0) === (int) $state)
                                ->all());
                        })
                        ->helperText('Vybraný hráč se přesune na začátek soupisky.'),
                    Repeater::make('playerStats')
                        ->relationship()
                        ->label('Hráči')
                        ->default(fn (): array => Player::active()
                            ->orderBy('last_name')
                            ->orderBy('first_name')
                            ->get()
                            ->map(fn (Player $player): array => [
                                'player_id' => $player->id,
                                'team_id' => Team::club()?->id,
                                'played' => false,
                                'goals' => 0,
                                'assists' => 0,
                                'plus_minus' => 0,
                            ])
                            ->all())
                        ->addActionLabel('Přidat hráče')
                        ->reorderable(false)
                        ->schema([
                            Select::make('player_id')
                                ->label('Hráč')
                                ->options(fn () => Player::active()->orderBy('last_name')->get()->pluck('full_name', 'id'))
                                ->searchable()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->required(),
                            Hidden::make('team_id')->default(fn () => Team::club()?->id)->required(),
                            Toggle::make('played')->label('Nastoupil')->default(false),
                            TextInput::make('goals')->label('G')->numeric()->default(0),
                            TextInput::make('assists')->label('A')->numeric()->default(0),
                            TextInput::make('plus_minus')->label('+/-')->numeric()->default(0),
                        ])
                        ->columns(5)
                        ->itemLabel(function (array $state): string {
                            if (! isset($state['player_id'])) {
                                return 'Nový hráč';
                            }

                            return Player::find($state['player_id'])?->full_name ?? 'Nový hráč';
                        }),
                ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('played_at', 'desc')
            ->columns([
                TextColumn::make('played_at')->label('Datum')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('homeTeam.short_name')->label('Domácí'),
                TextColumn::make('home_score')->label('Skóre')->formatStateUsing(fn ($state, $record) => $record->home_score !== null ? $record->home_score.' : '.$record->away_score : '—'),
                TextColumn::make('awayTeam.short_name')->label('Hosté'),
                TextColumn::make('match_type')
                    ->label('Typ')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label() ?? $state)
                    ->color(fn ($state): string => $state === MatchType::League ? 'info' : 'gray'),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label() ?? $state)
                    ->color(fn ($state): string => match ($state?->value ?? $state) {
                        'live' => 'danger',
                        'finished' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('competitionSeason.name')->label('Soutěž')->placeholder('Mimo soutěž'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ]);
    }

    public static function ensureClubIsInvolved(array $data): void
    {
        $clubTeamId = Team::club()?->id;

        if ($clubTeamId && ! in_array((int) $clubTeamId, [
            (int) ($data['home_team_id'] ?? 0),
            (int) ($data['away_team_id'] ?? 0),
        ], true)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'home_team_id' => 'Jeden z týmů zápasu musí být UTB RedBricks.',
                'away_team_id' => 'Jeden z týmů zápasu musí být UTB RedBricks.',
            ]);
        }
    }

    public static function getEloquentQuery(): Builder
    {
        app(SynchronizeMatchStatusesAction::class)->execute();

        return parent::getEloquentQuery()
            ->when(Team::club(), fn (Builder $query, Team $clubTeam): Builder => $query->where(
                fn (Builder $matchQuery): Builder => $matchQuery
                    ->where('home_team_id', $clubTeam->id)
                    ->orWhere('away_team_id', $clubTeam->id),
            ));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMatches::route('/'),
            'create' => Pages\CreateMatch::route('/create'),
            'edit' => Pages\EditMatch::route('/{record}/edit'),
        ];
    }
}
