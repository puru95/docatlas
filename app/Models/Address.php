<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    protected $fillable = [
        'entity_id',
        'entity_type',
        'zip',
        'city',
        'state_or_province',
        'street',
        'plot',
        'block',
        'country_code',
        'country',
        'lat',
        'lon'
    ];

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }
}
