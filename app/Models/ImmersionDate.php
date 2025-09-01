<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImmersionDate extends Model
{
    use HasFactory;

    protected $table = 'puja_immersion_dates'; 
    public $timestamps = false;

    protected $fillable = [
        'name',
        'idate',
    ];
    protected $casts = [
        'idate' => 'date',
    ];    
}
