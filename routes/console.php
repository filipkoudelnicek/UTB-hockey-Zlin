<?php

use App\Actions\SynchronizeMatchStatusesAction;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(fn () => app(SynchronizeMatchStatusesAction::class)->execute())->everyMinute();
Schedule::command('analytics:prune')->dailyAt('03:15')->timezone('Europe/Prague');
