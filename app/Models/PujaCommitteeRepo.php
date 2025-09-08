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
        'action_area_id',
        'puja_category_id',
        'name',
        'puja_address',
        'view_order',
    ];
    /**
     * Each PujaCommitteeRepo belongs to one ActionArea.
     */
    public function actionArea()
    {
        return $this->belongsTo(ActionArea::class, 'action_area_id');
    }

    /**
     * Each PujaCommitteeRepo belongs to one PujaCategorie.
     */
    public function pujaCategory()
    {
        return $this->belongsTo(PujaCategorie::class, 'puja_category_id');
    }	
}
