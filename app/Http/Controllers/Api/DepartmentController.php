<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Department;
use App\Models\DepartmentStructuralMapping;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'department.name' => 'required|string',
            'department.code' => 'required|string|unique:departments,code',
            'department.category' => 'required|string',
            'department.status' => 'required|string',
            'department.info' => 'nullable|string',
            'department.image_id' => 'nullable|string',
            'department_structural_mappings' => 'required|array',
            'department_structural_mappings.*.building_id' => 'required|exists:buildings,id',
            'department_structural_mappings.*.floor_id' => 'required|exists:floors,id',
            'department_structural_mappings.*.room_or_section_id' => 'required|exists:rooms,id',
            'department_structural_mappings.*.cabin_or_desk_id' => 'required|exists:cabins,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $deptData = $request->input('department');
            $department = Department::create($deptData);

            foreach ($request->input('department_structural_mappings') as $mapping) {
                $department->structuralMappings()->create($mapping);
            }

            DB::commit();

            return response()->json(['message' => 'Department created successfully', 'data' => $department->load('structuralMappings')], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Something went wrong', 'details' => $e->getMessage()], 500);
        }
    }

    public function edit(Request $request, $depId)
    {
        if (!$depId) {
            return response()->json([
                'status' => false,
                'errors' => 'Invalid Department Id',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'code' => 'required|string',
            'category' => 'required|string',
            'info' => 'nullable|string',
            'image_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $department = Department::where('id', $depId)  // Fix the key name accordingly
                ->update([
                    'name' => $request['name'],
                    'code' => $request['code'],
                    'category' => $request['category'],
                    'info' => $request['info'],
                    'image_id' => $request['image_id'],
                ]);

            DB::commit();

            return response()->json(['message' => 'Department updated successfully', 'data' => $department], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Something went wrong', 'details' => $e->getMessage()], 500);
        }
    }

    public function editStructure(Request $request, $depId)
    {
        if (!$depId) {
            return response()->json([
                'status' => false,
                'errors' => 'Invalid Department Id',
            ], 422);
        }
        // dd($request);
        $validator = Validator::make($request->all(), [
            '*.building_id' => 'required|integer|exists:buildings,id',
            '*.floor_id' => 'required|integer|exists:floors,id',
            '*.room_or_section_id' => 'required|integer|exists:rooms,id',
            '*.cabin_or_desk_id' => 'required|integer|exists:cabins,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $department = Department::findOrFail($depId);

        // Remove old mappings
        $department->structuralMappings()->delete();

        // Insert new mappings
        foreach ($request->all() as $mapping) {
            DepartmentStructuralMapping::create([
                'department_id'     => $department->id,
                'building_id'       => $mapping['building_id'],
                'floor_id'          => $mapping['floor_id'],
                'room_or_section_id' => $mapping['room_or_section_id'],
                'cabin_or_desk_id'  => $mapping['cabin_or_desk_id'],
            ]);
        }

        return response()->json(['message' => 'Department updated successfully', 'data' => $department->load('structuralMappings')], 202);
    }

    public function getDepartmentList(Request $request)
    {
        $searchText = $request->input('search_text', '');
        $pagination = $request->input('pagination', []);
        $startFrom = $pagination['start_from'] ?? 0;
        $pageSize = $pagination['page_size'] ?? 10;

        // Build the query with relation
        $query = Department::query();

        // Apply search filter if exists (partial match)
        if (!empty($searchText)) {
            $query->where('name', 'LIKE', '%' . $searchText . '%');
        }

        // Get total before pagination
        $total = $query->count();

        // Apply pagination
        $departments = $query
            ->skip($startFrom)
            ->take($pageSize)
            ->get();

        // Format response with dummy counts
        $searchResponse = $departments->map(function ($department) {
            $doc = Document::select('id', 'user_id', 'file_path as image_storage_url', 'mime_type', 'entity_type', 'entity_id')->where('user_id', $department->image_id)->first();
            return [
                'department' => $department,
                'image' => $doc,
                'details' => ['description' => $department->info],
            ];
        });

        return response()->json([
            'total_count'     => $total,
            'search_response' => $searchResponse,
        ]);
    }

    public function getStructure($id)
    {

        // $data = Department::with([
        //     'structuralMappings.wing:id,wing_name',
        //     'structuralMappings.building:id,building_name',
        //     'structuralMappings.floor:id,floor_name',
        //     'structuralMappings.room:id,room_name',
        //     'structuralMappings.cabin:id,cabin_name'
        // ])->where('id', $id)->first();

        $department = Department::with([
            'structuralMappings.building.wing',
            'structuralMappings.building.floors.rooms.cabinOrDesks'
        ])->where('id', $id)->first();

        if (empty($department)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Department Id',
            ], 422);
        }

        $buildings = [];
        $floors = [];
        $roomsOrSections = [];
        $cabinOrDesks = [];

        foreach ($department['structuralMappings'] as $mapping) {
            $building = $mapping->building;

            $cabinId = $mapping->cabin_or_desk_id;

            if ($building) {
                $buildings[] = [
                    'id' => $building->id,
                    'building_name' => $building->building_name,
                    'building_number' => $building->building_number,
                    'wing_name' => $building->wing?->wing_name,
                    'wing_number' => $building->wing?->wing_number,
                ];

                foreach ($building->floors as $floor) {
                    $roomIds = $floor->rooms->pluck('id')->toArray();
                    $floors[] = [
                        'id' => $floor->id,
                        'floor_name' => $floor->floor_name,
                        'floor_number' => $floor->floor_number,
                        'room_ids_list' => $roomIds,
                    ];

                    foreach ($floor->rooms as $room) {
                        $roomsOrSections[] = [
                            'id' => $room->id,
                            'floor_id' => $floor->id,
                            'room_name' => $room->room_name,
                            'room_number' => $room->room_number,
                            'section_name' => $room->section_name ?? null,
                            'section_number' => $room->section_number ?? null,
                        ];

                        foreach ($room['cabinOrDesks'] as $cab) {
                            if ($cab->id != $cabinId) continue;
                            $cabinOrDesks[] = [
                                'id' => $cab->id,
                                'building_id' => $building->id,
                                'floor_id' => $floor->id,
                                'room_id' => $room->id,
                                'cabin_name' => $cab->cabin_name,
                                'cabin_number' => $cab->cabin_number,
                            ];
                        }
                    }
                }
            }
        }

        return response()->json([
            'buildings' => $buildings,
            'floors' => $floors,
            'rooms_or_sections' => $roomsOrSections,
            'cabin_or_desks' => $cabinOrDesks,
        ]);
    }

    public function validateCabin($depId, $cabinId)
    {

        if (!$depId && !$cabinId) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Department Id or Cabin Id',
            ], 422);
        }

        $data = DepartmentStructuralMapping::where('department_id', '!=', $depId)->where('cabin_or_desk_id', $cabinId)->first();

        if ($data) {
            return true;
        } else {
            return false;
        }
    }
}
