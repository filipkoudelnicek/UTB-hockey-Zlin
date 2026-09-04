<?php

namespace App\Actions;

use App\Models\GameMatch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UpdateMatchResultAction
{
    public function __construct(
        private readonly SynchronizeMatchStatusesAction $synchronizeMatchStatuses,
    ) {}

    public function execute(GameMatch $match, array $data): GameMatch
    {
        DB::transaction(function () use ($match, $data): void {
            $match->fill($data);

            $match->save();
        });

        $this->synchronizeMatchStatuses->execute();

        Cache::forget('homepage.data');
        return $match->refresh();
    }
}
