<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        // Puja Committee fields
        'newtown',
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
        'vehicle_no',
        'team_members',

        // User system fields
        'password',
        'role',
        'stat',
        'token',
        'logged_at',
    ];

    // Hide sensitive fields in JSON
    protected $hidden = [
        'password',
        'token',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if ($user->role === 'u' && empty($user->token)) {
                $user->token = Str::random(64);
            }
        });
    }
}
