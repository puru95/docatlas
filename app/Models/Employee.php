<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function employeeIdentity()
    {
        return $this->hasMany(EmployeeIdentity::class);
    }
}
