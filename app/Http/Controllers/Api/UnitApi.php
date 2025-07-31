<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Units;
use Illuminate\Http\Request;

class UnitApi extends Controller
{
    public function index()
    {
        $units = Units::all();
        if ($units->isNotEmpty()) {
            $responseData = $units->map(function ($unit) {
                return [
                    'id' => $unit->id,
                    'unit_name' => $unit->unit_name,
                ];
            });
            return response()->json([
                'data' => $responseData,
                'status' => 200,
                'message' => 'Danh sách đơn vị.',
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }
}
