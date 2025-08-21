<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use App\Models\Plot;
use App\Models\Variety;
use Illuminate\Http\Request;

class CropControllerApi extends Controller
{
    public function index()
    {
        // $plots = Plot::all();
        // $varieties = Variety::all();

        $all_plants = Plant::with('plot', 'variety')->orderBy('id', 'desc')->get();
        if ($all_plants->isNotEmpty()) {

            return response()->json([
                'data' => $all_plants,
                'status' => 200,
                'message' => 'Danh sách cây trồng.',
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }
    public function detail($id)
    {
        $all_plants = Plant::with('plot', 'variety')->find($id);;

        if (!$all_plants) {
            return response()->json([
                'message' => 'Cây trồng không tồn tại.'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $all_plants,
            'message' => 'Chi tiết cây trồng.'
        ], 200);
    }
}
