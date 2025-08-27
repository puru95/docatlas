<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['branch_id', 'floor_id', 'room_name', 'room_number'];

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function cabins()
    {
        return $this->hasMany(Cabin::class);
    }

    public function cabinOrDesks()
    {
        return $this->hasMany(Cabin::class);
    }
}
