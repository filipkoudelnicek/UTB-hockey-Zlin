<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompetitionStandingResource\Pages;
use App\Models\CompetitionStanding;
use App\Models\Team;
use Filament\Actions;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CompetitionStandingResource extends AdminResource
{
    protected static ?string $model = CompetitionStanding::class;
    protected static ?string $permissionKey = 'reports.view';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static string|\UnitEnum|null $navigationGroup = 'Přehledy';
    protected static ?string $navigationLabel = 'Tabulka';
    protected static ?string $modelLabel = 'Řádek tabulky';
    protected static ?string $pluralModelLabel = 'Tabulka';
    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('competitionSeason.name')->label('Ročník')->sortable()->searchable(),
                TextColumn::make('rank')->label('#')->alignCenter(),
                TextColumn::make('team.name')->label('Tým')->searchable(),
                TextColumn::make('games_played')->label('Z')->alignCenter(),
                self::numberColumn('wins', 'V'),
                self::numberColumn('losses', 'P'),
                self::numberColumn('points', 'B'),
            ])
            ->filters([
                SelectFilter::make('competition_season_id')
                    ->label('Ročník soutěže')
                    ->relationship('competitionSeason', 'name'),
            ])
            ->defaultSort(fn (Builder $query): Builder => $query
                ->orderByDesc('points')
                ->orderBy('team_id'))
            ->recordClasses(fn (CompetitionStanding $record): ?string => $record->team_id === Team::club()?->id
                ? 'border-s-4 border-primary-500 bg-primary-500/10'
                : null)
            ->recordActions([
                Actions\DeleteAction::make(),
            ])
            ->toolbarActions([]);
    }

    private static function numberColumn(string $name, string $label): TextInputColumn
    {
        return TextInputColumn::make($name)
            ->label($label)
            ->type('number')
            ->rules(['required', 'integer', 'min:0'])
            ->extraInputAttributes(['min' => 0])
            ->alignCenter();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompetitionStandings::route('/'),
        ];
    }
}
