<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    protected $fillable = ['service_day_schedule_id', 'start_time_utc', 'end_time_utc'];

    public function daySchedule()
    {
        return $this->belongsTo(ServiceDaySchedule::class);
    }

}
