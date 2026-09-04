<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionSeason extends Model
{
    protected $fillable = [
        'competition_id', 'name', 'status', 'starts_at', 'ends_at',
        'source', 'external_id',
    ];

    protected $casts = [
        'starts_at' => 'date', 'ends_at' => 'date',
    ];

    public function competition(): BelongsTo { return $this->belongsTo(Competition::class); }
    public function teams(): BelongsToMany { return $this->belongsToMany(Team::class)->withPivot('sort_order')->withTimestamps()->orderByPivot('sort_order'); }
    public function matches(): HasMany { return $this->hasMany(GameMatch::class); }
    public function standings(): HasMany { return $this->hasMany(CompetitionStanding::class)->orderByDesc('points')->orderBy('team_id'); }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public static function currentForClub(?Team $clubTeam = null): ?self
    {
        return static::query()
            ->with('competition')
            ->active()
            ->when($clubTeam, fn (Builder $query) => $query->whereHas('teams', fn (Builder $teamQuery) => $teamQuery->whereKey($clubTeam->id)))
            ->orderByDesc('starts_at')
            ->first()
            ?? static::query()->with('competition')->orderByDesc('starts_at')->first();
    }
}
