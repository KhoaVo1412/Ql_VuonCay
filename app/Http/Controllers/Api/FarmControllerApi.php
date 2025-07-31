<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use Illuminate\Http\Request;

class FarmControllerApi extends Controller
{
    public function index()
    {
        $farms = Farm::with('unitRelation')->get();
        if ($farms->isNotEmpty()) {
            $responseData = $farms->map(function ($farm) {
                return [
                    'id' => $farm->id,
                    'farm_code' => $farm->farm_code,
                    'farm_name' => $farm->farm_name,
                    'unit_name' => $farm->unitRelation->unit_name,
                ];
            });
            return response()->json([
                'data' => $responseData,
                'status' => 200,
                'message' => 'Danh sách nông trường.',
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }
}
