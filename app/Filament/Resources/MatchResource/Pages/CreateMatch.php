<?php

namespace App\Filament\Resources\MatchResource\Pages;

use App\Actions\SynchronizeMatchStatusesAction;
use App\Filament\Resources\MatchResource;
use App\Models\Player;
use App\Models\Team;
use Filament\Resources\Pages\CreateRecord;

class CreateMatch extends CreateRecord
{
    protected static string $resource = MatchResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        MatchResource::ensureClubIsInvolved($data);

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($clubTeam = Team::club()) {
            Player::active()->each(function (Player $player) use ($clubTeam): void {
                $this->record->playerStats()->firstOrCreate(
                    ['player_id' => $player->id, 'team_id' => $clubTeam->id],
                    ['played' => false, 'goals' => 0, 'assists' => 0, 'plus_minus' => 0],
                );
            });
        }

        app(SynchronizeMatchStatusesAction::class)->execute();
    }
}
