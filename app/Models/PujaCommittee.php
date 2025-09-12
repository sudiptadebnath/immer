<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PujaCommittee extends Model
{
    protected $fillable = [
        // Puja Committee fields
        'action_area',
        'category',
        'puja_committee_name',
        'puja_committee_address',
        'secretary_name',
        'secretary_mobile',
        'chairman_name',
        'chairman_mobile',
        'proposed_immersion_date',
        'proposed_immersion_time',
        'no_of_vehicles',
        'vehicle_no',
        'team_members',
        'stat',
        'token',
    ];

    // Hide sensitive fields in JSON
    /*protected $hidden = [
        'token',
    ];*/

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($rec) {
            $rec->token = Str::random(6);
        });
    }
}
