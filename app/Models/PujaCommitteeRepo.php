<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PujaCommitteeRepo extends Model
{
    use HasFactory;

    protected $table = 'puja_committies_repo'; 
    public $timestamps = false;

    protected $fillable = [
        'name',
        'view_order',
    ];
}
