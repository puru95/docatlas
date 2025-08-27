<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentStructuralMapping extends Model
{
    protected $fillable = ['department_id', 'building_id', 'floor_id', 'room_or_section_id', 'cabin_or_desk_id'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function wing()
    {
        return $this->belongsTo(Wing::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function cabin()
    {
        return $this->belongsTo(Cabin::class, 'cabin_or_desk_id');
    }
}
