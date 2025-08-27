<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'code', 'category', 'status', 'info', 'image_id'];

    public function structuralMappings()
    {
        return $this->hasMany(DepartmentStructuralMapping::class);
    }

    public function specialization()
    {
        return $this->hasMany(Specialization::class);
    }

}
