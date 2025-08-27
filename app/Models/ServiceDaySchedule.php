<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceDaySchedule extends Model
{
    protected $fillable = ['department_service_id', 'day_of_week', 'day_off', 'is_24_hours'];

    public function slots()
    {
        return $this->hasMany(Slot::class);
    }
}
