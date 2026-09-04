<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MatchPlayerStatResource\Pages;
use App\Models\MatchPlayerStat;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MatchPlayerStatResource extends AdminResource
{
    protected static ?string $model = MatchPlayerStat::class;
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
                TextColumn::make('match.played_at')->label('Datum')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('player.last_name')->label('Hráč')->formatStateUsing(fn ($state, $record) => $record->player?->full_name ?? '—')->searchable(),
                TextColumn::make('team.short_name')->label('Tým'),
                IconColumn::make('played')->label('Nastoupil')->boolean(),
                TextColumn::make('goals')->label('G')->alignCenter(),
                TextColumn::make('assists')->label('A')->alignCenter(),
                TextColumn::make('points')->label('Body')->alignCenter()->getStateUsing(fn (MatchPlayerStat $record) => $record->goals + $record->assists),
                TextColumn::make('plus_minus')->label('+/-')->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('player_id')->label('Hráč')->relationship('player', 'last_name')->searchable()->preload(),
                SelectFilter::make('team_id')->label('Tým')->relationship('team', 'name')->searchable()->preload(),
            ])
            ->defaultSort('match_id', 'desc')
            ->recordActions([])
            ->toolbarActions([]);
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
