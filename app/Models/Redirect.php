<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $fillable = [
        'from_url',
        'to_url',
        'http_code',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'http_code' => 'integer',
    ];
}
