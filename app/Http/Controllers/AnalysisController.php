<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalysisController extends Controller
{
    use ApiResponse;

    public function getMedicinesData(Request $request)
    {

        try {

            $query = $request['search_text'];

            if (!$query) {
                return $this->error('Search query is required.', 400);
            }

            $data = DB::table('medicines')->select('*')
                ->where('name', 'like', "%$query%")
                // ->orWhere('salt', 'like', "%$query%")
                // ->orWhere('category', 'like', "%$query%")
                ->get();

            $responseData = [
                "total_count" => count($data),
                "search_response" => $data
            ];

            return $this->success($responseData, 'Users fetched successfully');
        } catch (\Throwable $e) {

            $this->reportException(__CLASS__ . "/" . __FUNCTION__, $e->getMessage());
        }
    }

    public function getMedicinesDetails(Request $request)
    {

        try {

            $request->validate([
                'id' => 'required',
            ]);

            $id = $request['id'];

            if (!$id) {
                return $this->error('Search query is required.', 400);
            }

            $data = DB::table('medicines')->select('id', 'name')
                // ->where('name', 'like', "%$query%")
                // ->orWhere('salt', 'like', "%$query%")
                // ->orWhere('category', 'like', "%$query%")
                ->get();

            $responseData = [
                "total_count" => count($data),
                "search_response" => $data
            ];

            return $this->success($responseData, 'Users fetched successfully');
        } catch (\Throwable $e) {

            $this->reportException(__CLASS__ . "/" . __FUNCTION__, $e->getMessage());
        }
    }
}
