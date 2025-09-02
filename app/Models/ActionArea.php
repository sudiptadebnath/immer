<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionArea extends Model
{
    use HasFactory;

    protected $table = 'action_areas'; 
    public $timestamps = false;

    protected $fillable = [
        'name',
        'view_order',
    ];
}
