<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Variety;
use Illuminate\Http\Request;

class SeedGardenControllerApi extends Controller
{
    public function index()
    {
        $seedgardens = Variety::all();
        if ($seedgardens->isNotEmpty()) {

            return response()->json([
                'data' => $seedgardens,
                'status' => 200,
                'message' => 'Danh sách vườn giống.',
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }
    public function detail($id)
    {
        $Variety = Variety::find($id);

        if (!$Variety) {
            return response()->json([
                'message' => 'Vường giống không tồn tại.'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $Variety,
            'message' => 'Chi tiết vườn giống.'
        ], 200);
    }
}
