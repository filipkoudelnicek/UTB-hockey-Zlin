<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchPlayerStat extends Model
{
    protected $fillable = ['match_id', 'player_id', 'team_id', 'played', 'goals', 'assists', 'plus_minus'];
    protected $casts = ['played' => 'boolean'];
    public function match(): BelongsTo { return $this->belongsTo(GameMatch::class, 'match_id'); }
    public function player(): BelongsTo { return $this->belongsTo(Player::class); }
    public function team(): BelongsTo { return $this->belongsTo(Team::class); }
}
