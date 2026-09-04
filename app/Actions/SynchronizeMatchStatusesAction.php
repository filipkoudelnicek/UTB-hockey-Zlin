<?php

namespace App\Actions;

use App\Models\GameMatch;
use Illuminate\Support\Facades\Cache;

class SynchronizeMatchStatusesAction
{
    /** Dopočítá stav pouze z času začátku zápasu. Výsledky do stavu nezasahují. */
    public function execute(): int
    {
        $now = now();
        $changed = 0;

        GameMatch::query()->orderBy('id')->each(function (GameMatch $match) use ($now, &$changed): void {
            $status = $match->automaticStatus($now);

            if ($match->status === $status) {
                return;
            }

            $match->forceFill(['status' => $status])->saveQuietly();
            $changed++;
        });

        if ($changed > 0) {
            Cache::forget('homepage.data');
        }

        return $changed;
    }
}
