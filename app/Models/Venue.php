<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    protected $fillable = ['name', 'slug', 'address', 'city', 'latitude', 'longitude', 'map_url', 'source', 'external_id'];
    protected $casts = ['latitude' => 'decimal:7', 'longitude' => 'decimal:7'];
    public function matches(): HasMany { return $this->hasMany(GameMatch::class); }
}
