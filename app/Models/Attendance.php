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
        'puja_committee_id',
        'typ',
    ];

    public function pujaCommittee()
    {
        return $this->belongsTo(PujaCommittee::class, 'puja_committee_id');
    }

    public function scannedBy()
    {
        return $this->belongsTo(User::class, 'scan_by');
    }

    protected $casts = [
        'scan_datetime' => 'datetime',
    ];
}
