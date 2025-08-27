<?php

namespace App\Http\Controllers;

use App\Models\DepartmentService;
use App\Models\Document;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_service.service_name' => 'required|string',
            'department_service.department_id' => 'required|exists:departments,id',
            'department_service.service_cost' => 'required',
            'department_service.slot_duration_minutes' => 'required|integer',
            'department_service.status' => 'required|in:ACTIVE,INACTIVE',
            'department_service.service_type' => 'required|string',
            'week_schedule.day_schedule' => 'required|array',
            'week_schedule.day_schedule.*.day_of_week' => 'required|string',
            'week_schedule.day_schedule.*.slots' => 'array',
        ]);

        $service = DepartmentService::create($request->input('department_service'));

        foreach ($request->input('week_schedule.day_schedule', []) as $day) {
            $daySchedule = $service->servicedaySchedules()->create([
                'day_of_week' => $day['day_of_week'],
                'day_off' => $day['day_off'] ?? false,
                'is_24_hours' => $request->input('week_schedule.is_24_hours', false),
            ]);

            foreach ($day['slots'] ?? [] as $slot) {
                $daySchedule->slots()->create([
                    'start_time_utc' => $slot['start_time_utc'],
                    'end_time_utc' => $slot['end_time_utc'],
                ]);
            }
        }

        return response()->json([
            'message' => 'Department service created successfully',
            'data' => $service->load('servicedaySchedules.slots')
        ], 201);
    }

    public function getServiceList(Request $request)
    {
        $searchText = $request->input('search_text', '');
        $branchId = $request->input('branch_id');
        $pagination = $request->input('pagination', []);
        $startFrom = $pagination['start_from'] ?? 0;
        $pageSize = $pagination['page_size'] ?? 10;

        $query = DepartmentService::with('department');

        // Apply search filter if exists (partial match)
        if (!empty($searchText)) {
            $query->where('specialization_name', 'LIKE', '%' . $searchText . '%');
        }

        if (!empty($departmentId)) {
            $query->where('department_id', $departmentId);
        }

        // Get total before pagination
        $total = $query->count();

        // Apply pagination
        $services = $query
            ->skip($startFrom)
            ->take($pageSize)
            ->get();

        $searchResponse = $services->map(function ($service) {
            $doc = Document::select('id', 'user_id', 'file_path as image_storage_url', 'mime_type', 'entity_type', 'entity_id')->where('user_id', $service->image_id)->first();
            return [
                'department_service' => $service,
                'image' => $doc,
                'department' => $service->department,
            ];
        });

        return response()->json([
            'success' => true,
            'total_count'     => $total,
            'search_response' => $searchResponse,
        ]);

    }
}
