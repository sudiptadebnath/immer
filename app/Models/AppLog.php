<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppLog extends Model
{
    // Eloquent should manage timestamps
    public $timestamps = true;

    // Tell Laravel to only use created_at
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'name', 'action', 'reaction', 'user', 'context', 'ip'
    ];

    protected $casts = [
        'context' => 'array', // store JSON as array
    ];
}
