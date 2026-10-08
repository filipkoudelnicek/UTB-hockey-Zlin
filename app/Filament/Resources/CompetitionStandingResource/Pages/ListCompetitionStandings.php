<?php

namespace App\Filament\Resources\CompetitionStandingResource\Pages;

use App\Filament\Resources\CompetitionStandingResource;
use App\Models\CompetitionSeason;
use App\Models\CompetitionStanding;
use App\Models\Team;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Validation\ValidationException;

class ListCompetitionStandings extends ListRecords
{
    protected static string $resource = CompetitionStandingResource::class;

    public function getSubheading(): ?string
    {
        return 'Skóre UTB se počítá z ligových zápasů. Ostatní týmy mají vstřelené a inkasované branky zadané ručně.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addStanding')
                ->label('Přidat tým do tabulky')
                ->icon('heroicon-o-plus')
                ->schema([
                    Select::make('competition_season_id')
                        ->label('Ročník soutěže')
                        ->options(fn () => CompetitionSeason::orderByDesc('starts_at')->pluck('name', 'id'))
                        ->default(fn () => CompetitionSeason::currentForClub(Team::club())?->id)
                        ->searchable()
                        ->required(),
                    Select::make('team_id')
                        ->label('Tým')
                        ->options(fn () => Team::active()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->live()
                        ->required(),
                    TextInput::make('wins')
                        ->label('Výhry (V)')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->required()
                        ->disabled(fn (Get $get): bool => (int) $get('team_id') === (int) Team::club()?->getKey()),
                    TextInput::make('losses')
                        ->label('Prohry (P)')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->required()
                        ->disabled(fn (Get $get): bool => (int) $get('team_id') === (int) Team::club()?->getKey()),
                    TextInput::make('goals_for')
                        ->label('VB')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(65535)
                        ->disabled(fn (Get $get): bool => (int) $get('team_id') === (int) Team::club()?->getKey()),
                    TextInput::make('goals_against')
                        ->label('IB')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(65535)
                        ->disabled(fn (Get $get): bool => (int) $get('team_id') === (int) Team::club()?->getKey()),
                    TextInput::make('points')
                        ->label('Body (B)')
                        ->numeric()
                        ->default(0)
                        ->required()
                        ->disabled(fn (Get $get): bool => (int) $get('team_id') === (int) Team::club()?->getKey()),
                ])
                ->action(function (array $data): void {
                    if (CompetitionStanding::query()
                        ->where('competition_season_id', $data['competition_season_id'])
                        ->where('team_id', $data['team_id'])
                        ->exists()) {
                        throw ValidationException::withMessages([
                            'team_id' => 'Tento tým už v tabulce vybraného ročníku je.',
                        ]);
                    }

                    if ((int) $data['team_id'] === (int) Team::club()?->getKey()) {
                        $data['wins'] = 0;
                        $data['losses'] = 0;
                        $data['points'] = 0;
                        $data['goals_for'] = null;
                        $data['goals_against'] = null;
                    }

                    CompetitionStanding::create($data);

                    Notification::make()
                        ->title('Tým byl přidán do tabulky.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
