<?php

namespace App\Services\Analytics;

use Carbon\CarbonImmutable;

class AnalyticsPeriod
{
    public const LAST_7 = 'last_7';
    public const LAST_30 = 'last_30';
    public const THIS_MONTH = 'this_month';
    public const PREVIOUS_MONTH = 'previous_month';
    public const CUSTOM = 'custom';

    private const MAX_DAYS = 731;

    public function __construct(
        public readonly CarbonImmutable $from,
        public readonly CarbonImmutable $to,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::LAST_7 => 'Posledních 7 dní',
            self::LAST_30 => 'Posledních 30 dní',
            self::THIS_MONTH => 'Aktuální měsíc',
            self::PREVIOUS_MONTH => 'Předchozí měsíc',
            self::CUSTOM => 'Vlastní rozsah',
        ];
    }

    /**
     * @param  array<string, mixed>|null  $filters
     */
    public static function fromFilters(?array $filters, ?CarbonImmutable $now = null): self
    {
        $today = ($now ?? CarbonImmutable::now(config('analytics.timezone')))->startOfDay();

        switch ($filters['period'] ?? self::LAST_30) {
            case self::LAST_7:
                return new self($today->subDays(6), $today);
            case self::THIS_MONTH:
                return new self($today->startOfMonth(), $today);
            case self::PREVIOUS_MONTH:
                $previous = $today->startOfMonth()->subMonthNoOverflow();

                return new self($previous->startOfMonth(), $previous->endOfMonth()->startOfDay());
            case self::CUSTOM:
                $custom = self::customRange($filters, $today);

                if ($custom) {
                    return $custom;
                }
        }

        return new self($today->subDays(29), $today);
    }

    /**
     * @param  array<string, mixed>|null  $filters
     */
    private static function customRange(?array $filters, CarbonImmutable $today): ?self
    {
        try {
            $timezone = config('analytics.timezone');
            $from = ! empty($filters['start_date']) ? CarbonImmutable::parse($filters['start_date'], $timezone)->startOfDay() : null;
            $to = ! empty($filters['end_date']) ? CarbonImmutable::parse($filters['end_date'], $timezone)->startOfDay() : null;
        } catch (\Throwable) {
            return null;
        }

        if (! $from && ! $to) {
            return null;
        }

        $to = min($to ?? $today, $today);
        $from = $from ?? $to->subDays(29);

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to, $from];
        }

        if ($from->diffInDays($to) > self::MAX_DAYS) {
            $from = $to->subDays(self::MAX_DAYS);
        }

        return new self($from, $to);
    }

    /**
     * @return list<string>
     */
    public function dates(): array
    {
        $dates = [];

        for ($day = $this->from; $day->lessThanOrEqualTo($this->to); $day = $day->addDay()) {
            $dates[] = $day->toDateString();
        }

        return $dates;
    }
}
