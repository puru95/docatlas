<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    protected $fillable = [
        'branch_id',
        'wing_name',
        'building_name',
        'building_number',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function wing()
    {
        return $this->belongsTo(Wing::class);
    }

    public function floors()
    {
        return $this->hasMany(Floor::class);
    }
}
