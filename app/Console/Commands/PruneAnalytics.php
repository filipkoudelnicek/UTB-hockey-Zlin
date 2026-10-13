<?php

namespace App\Console\Commands;

use App\Services\Analytics\AnalyticsTracker;
use Illuminate\Console\Command;

class PruneAnalytics extends Command
{
    protected $signature = 'analytics:prune';

    protected $description = 'Smaže surová analytická data starší než nastavená doba uchování.';

    public function handle(AnalyticsTracker $tracker): int
    {
        $result = $tracker->prune();

        $this->info("Smazáno zobrazení: {$result['pageviews']}, návštěvníků: {$result['visitors']}.");

        return self::SUCCESS;
    }
}
