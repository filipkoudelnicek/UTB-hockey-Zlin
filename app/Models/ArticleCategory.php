<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ArticleCategory extends Model
{
    protected $fillable = ['name'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_filterable' => 'boolean',
        ];
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_category');
    }
}
