<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SpecializationController extends Controller
{

    public function getSpecializationList(Request $request)
    {
        $searchText = $request->input('search_text', '');
        $departmentId = $request->input('department_id');
        $pagination = $request->input('pagination', []);
        $startFrom = $pagination['start_from'] ?? 0;
        $pageSize = $pagination['page_size'] ?? 10;

        $query = Specialization::with('department');

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
        $specializations = $query
            ->skip($startFrom)
            ->take($pageSize)
            ->get();

        $searchResponse = $specializations->map(function ($specialization) {
            $doc = Document::select('id', 'user_id', 'file_path as image_storage_url', 'mime_type', 'entity_type', 'entity_id')->where('user_id', $specialization->image_id)->first();
            return [
                'specialization' => $specialization,
                'image' => $doc,
                'department' => $specialization->department,
            ];
        });

        return response()->json([
            'success' => true,
            'total_count'     => $total,
            'search_response' => $searchResponse,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->input('specialization'), [
            'image_id' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'specialization_name' => 'required|string|max:255',
            'specialization_code' => 'required|string|max:100|unique:specializations,specialization_code',
            'info' => 'nullable|string',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $specialization = Specialization::create($validator->validated());

        return response()->json([
            'message' => 'Specialization created successfully',
            'data' => $specialization->load('department')
        ], 201);
    }

    public function edit(Request $request, $specId)
    {
        $validator = Validator::make($request->all(), [
            'image_id' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'specialization_name' => 'required|string|max:255',
            'specialization_code' => 'required|string|max:100',
            'info' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $specialization = Specialization::where('id', $specId)  // Fix the key name accordingly
            ->update([
                'image_id' => $request['image_id'],
                'department_id' => $request['department_id'],
                'specialization_name' => $request['specialization_name'],
                'specialization_code' => $request['specialization_code'],
                'info' => $request['info'],
            ]);

        return response()->json([
            'message' => 'Specialization created successfully',
            'data' => $specialization
        ], 201);
    }
}
