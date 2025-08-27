<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeIdentity;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function createAdmin(Request $request)
    {dd('sdf');
        DB::transaction(function () use ($request) {
            // 1. Create role if not exists
            $role = Role::where('name', $request->employee['designation'])->first();

            // 2. Create employee
            $employee = Employee::create([
                'role_id' => $role->id,
                'designation' => $request->employee['designation'],
                'emp_code' => $request->employee['emp_code'],
                'status' => $request->employee['status'],
                'email_id' => $request->employee['email_id'],
            ]);

            // 3. Create identities
            foreach (['identity_type1', 'identity_type2'] as $typeKey) {
                if (!empty($request->employee[$typeKey])) {
                    EmployeeIdentity::create([
                        'employee_id' => $employee->id,
                        'identity_type' => $request->employee[$typeKey],
                        'identity_number' => $request->employee[str_replace('type', 'number', $typeKey)],
                        'document_ids' => json_encode($request->employee[$typeKey . '_document_ids'] ?? []),
                    ]);
                }
            }

            // 4. Create user info
            User::create([
                'employee_id' => $employee->id,
                'full_txt_name' => $request->user['full_txt_name'],
                'gender' => $request->user['gender'],
                'country_code' => $request->user['country_code'],
                'mobile_no' => $request->user['mobile_no'],
                'dob' => Carbon::createFromTimestampMs($request->user['dob']),
                'status' => $request->user['status'],
            ]);

            // 5. Handle work schedule logic via previously created tables
            // (service_day_schedules, slots) - assumed already implemented
        });

        return response()->json(['message' => 'Admin created successfully']);
    }
}
