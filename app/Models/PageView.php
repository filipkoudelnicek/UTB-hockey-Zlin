<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PageView extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'path',
        'session_id',
        'is_bot',
        'created_at',
    ];

    protected $casts = [
        'is_bot' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Pouze skutečné návštěvy (žádní boti).
     */
    public function scopeReal(Builder $query): Builder
    {
        return $query->where('is_bot', false);
    }

    /**
     * Počet zobrazení a unikátních návštěvníků za posledních N dní, seskupeno po dnech.
     *
     * @return array{labels: string[], pageviews: int[], visitors: int[]}
     */
    public static function dailyStats(int $days = 30): array
    {
        $from = now()->subDays($days - 1)->startOfDay();

        $rows = static::real()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as pageviews, COUNT(DISTINCT session_id) as visitors')
            ->where('created_at', '>=', $from)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels    = [];
        $pageviews = [];
        $visitors  = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date       = now()->subDays($i)->format('Y-m-d');
            $labels[]   = now()->subDays($i)->format('d.m.');
            $pageviews[] = (int) ($rows[$date]->pageviews ?? 0);
            $visitors[]  = (int) ($rows[$date]->visitors ?? 0);
        }

        return ['labels' => $labels, 'pageviews' => $pageviews, 'visitors' => $visitors];
    }
}
