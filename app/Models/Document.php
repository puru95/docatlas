<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'file_name',
        'file_path',
        'mime_type',
        'entity_type',
        'entity_id',
    ];
}
