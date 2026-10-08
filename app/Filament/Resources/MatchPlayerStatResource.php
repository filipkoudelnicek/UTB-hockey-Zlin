<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MatchPlayerStatResource\Pages;
use App\Models\Competition;
use App\Models\CompetitionSeason;
use App\Models\Player;
use App\Models\Team;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MatchPlayerStatResource extends AdminResource
{
    protected static ?string $model = Player::class;

    protected static ?string $permissionKey = 'reports.view';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string|\UnitEnum|null $navigationGroup = 'Přehledy';

    protected static ?string $navigationLabel = 'Statistiky hráčů';

    protected static ?string $modelLabel = 'Statistika hráče';

    protected static ?string $pluralModelLabel = 'Statistiky hráčů';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Hráč')
                    ->searchable(['players.first_name', 'players.last_name'])
                    ->sortable(['players.last_name', 'players.first_name']),
                TextColumn::make('competition_names')->label('Soutěž')->placeholder('Přátelské zápasy'),
                TextColumn::make('season_names')->label('Sezóna')->placeholder('—'),
                TextColumn::make('games')->label('Zápasy')->alignCenter()->sortable(),
                TextColumn::make('goals')->label('G')->alignCenter()->sortable(),
                TextColumn::make('assists')->label('A')->alignCenter()->sortable(),
                TextColumn::make('points')->label('Body')->alignCenter()->sortable(),
                TextColumn::make('plus_minus')->label('+/-')->alignCenter()->sortable(),
            ])
            ->filters([
                SelectFilter::make('id')
                    ->label('Hráč')
                    ->options(fn () => Player::query()->orderBy('last_name')->orderBy('first_name')->get()->mapWithKeys(fn (Player $player): array => [$player->id => $player->full_name]))
                    ->searchable()
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['value'] ?? null),
                        fn (Builder $query): Builder => $query->where('players.id', $data['value']),
                    )),
                SelectFilter::make('competition_id')
                    ->label('Soutěž')
                    ->options(fn () => Competition::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['value'] ?? null),
                        fn (Builder $query): Builder => $query->where('competitions.id', $data['value']),
                    )),
                SelectFilter::make('competition_season_id')
                    ->label('Sezóna')
                    ->options(fn () => CompetitionSeason::query()
                        ->with('competition')
                        ->orderByDesc('starts_at')
                        ->get()
                        ->mapWithKeys(fn (CompetitionSeason $season): array => [
                            $season->id => ($season->competition?->name ? $season->competition->name.' — ' : '').$season->name,
                        ]))
                    ->searchable()
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['value'] ?? null),
                        fn (Builder $query): Builder => $query->where('competition_seasons.id', $data['value']),
                    )),
                SelectFilter::make('team_id')
                    ->label('Tým')
                    ->options(fn () => Team::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['value'] ?? null),
                        fn (Builder $query): Builder => $query->where('match_player_stats.team_id', $data['value']),
                    )),
            ])
            ->defaultSort('goals', 'desc')
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        return Player::query()->withAggregatedMatchStats();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMatchPlayerStats::route('/'),
        ];
    }
}
