<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppLog extends Model
{
    protected $fillable = [
        'name', 'action', 'reaction', 'user', 'context', 'ip'
    ];

    protected $casts = [
        'context' => 'array', // store JSON as array
    ];
}
