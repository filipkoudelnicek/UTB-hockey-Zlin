<?php

namespace App\Models;

use App\Services\MediaService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competition extends Model
{
    protected $fillable = ['name', 'short_name', 'slug', 'logo_media_id', 'source', 'external_id'];
    public function competitionSeasons(): HasMany { return $this->hasMany(CompetitionSeason::class); }
    public function getLogoUrlAttribute(): ?string { return MediaService::getMediaUrl($this->logo_media_id); }
}
