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
        'uid',
        'name',
        'address',
        'email',
        'mob',
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
            if (empty($user->token)) {
                $user->token = Str::random(64); 
            }
        });
    }
}
