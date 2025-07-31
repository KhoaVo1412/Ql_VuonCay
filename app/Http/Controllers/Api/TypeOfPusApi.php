<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TypeOfPus;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class TypeOfPusApi extends Controller
{
    public function index()
    {

        $TypeOfPus = TypeOfPus::all();
        if (true) {
            return response()->json([
                "data" => $TypeOfPus,
                "status" => 200,
                "message" => " Danh sách loại mủ.",
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.'
        ]);
    }
    public function index_vhc()
    {

        $Vehicle = Vehicle::all();
        if (true) {
            return response()->json([
                "data" => $Vehicle,
                "status" => 200,
                "message" => " Danh sách Xe.",
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.'
        ]);
    }
}
