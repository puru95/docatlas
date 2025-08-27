<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['name', 'image_id'];

    public function address()
    {
        return $this->morphOne(Address::class, 'entity');
    }

    public function weekSchedule()
    {
        return $this->hasOne(WeekSchedule::class);
    }

    public function building()
    {
        return $this->hasOne(Building::class);
    }

    public function wing()
    {
        return $this->hasOne(Wing::class);
    }
}
