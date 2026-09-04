<?php
namespace App\Models;
use App\Services\MediaService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
class Partner extends Model {
    protected $fillable=['name','logo_media_id','website','is_active'];
    protected $casts=['is_active'=>'boolean'];
    public function scopeActive(Builder $q): Builder { return $q->where('is_active',true); }
    public function getLogoUrlAttribute(): ?string { return MediaService::getMediaUrl($this->logo_media_id); }
}
