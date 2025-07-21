<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;

    protected $table = 'user_info';

    protected $fillable = [
        'user_id',
        'employee_name',
        'employee_id',
        'adhaar_id',
        'id_proof',
        'employee_id_doc_id',
        'adhaar_id_doc_id',
        'adhaar_id_back_doc_id',
        'id_proof_doc_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
