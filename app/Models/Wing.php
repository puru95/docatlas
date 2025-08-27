<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wing extends Model
{
    protected $fillable = [
        'branch_id',
        'wing_name',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function buildings()
    {
        return $this->hasMany(Building::class);
    }
}
