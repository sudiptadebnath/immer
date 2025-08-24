<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';
    public $timestamps = false;

    protected $fillable = [
        'scan_datetime',
        'scan_by',
        'user_id',
        'post',
        'typ',
        'location',
    ];


    // The user who was scanned
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scannedBy()
    {
        return $this->belongsTo(User::class, 'scan_by');
    }

    protected $casts = [
        'scan_datetime' => 'datetime',
    ];
}
