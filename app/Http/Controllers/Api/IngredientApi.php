<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientApi extends Controller
{
    public function index()
    {

        $Ingredient = Ingredient::with(['farm.unitRelation', 'vehicle', 'typeOfPus'])->get();

        if ($Ingredient->isNotEmpty()) {
            return response()->json([
                "data" => $Ingredient->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'farm_name' => $item->farm->farm_name ?? null,
                        'unit_name' => $item->farm->unitRelation->unit_name ?? null,
                        'type_of_pus_name' => $item->typeOfPus->name_pus ?? null,
                        'vehicle_number' => $item->vehicle->vehicle_number ?? null,
                        'trip' => $item->trip,
                        'received_date' => $item->received_date,
                        'receiving_factory' => $item->receiving_factory,
                        'harvesting_date' => $item->harvesting_date,
                        'end_harvest_date' => $item->end_harvest_date,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                    ];
                }),
                "status" => 200,
                "message" => "Danh sách thông tin nguyên liệu.",
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'Không tìm thấy dữ liệu.'
            ]);
        }
    }
    public function detail($id)
    {
        $ingredient = Ingredient::with(['farm.unitRelation', 'typeOfPus', 'plantingAreas', 'vehicle', 'batches'])
            ->find($id);

        if ($ingredient) {
            $treeType = $ingredient->plantingAreas->pluck('chi_tieu')->first();
            return response()->json([
                "ingredient" => [
                    "id" => $ingredient->id,
                    "farm_name" => $ingredient->farm->farm_name,
                    'unit_name' => $ingredient->farm->unitRelation->unit_name ?? null,
                    "type_of_pus_name" => $ingredient->typeOfPus->name_pus,
                    "tree_type" => $treeType,
                    "trip" => $ingredient->trip,
                    "received_date" => $ingredient->received_date,
                    "receiving_factory" => $ingredient->receiving_factory,
                    "harvesting_date" => $ingredient->harvesting_date,
                    "end_harvest_date" => $ingredient->end_harvest_date,
                    "created_at" => $ingredient->created_at,
                    "updated_at" => $ingredient->updated_at,
                    "vehicle_number_id" => $ingredient->vehicle_number_id,
                ],
                "farm" => $ingredient->farm,
                "type_of_pus" => $ingredient->typeOfPus,
                "planting_areas" => $ingredient->plantingAreas,
                "vehicle" => $ingredient->vehicle,
                "batches" => $ingredient->batches,
                "status" => 200,
                "message" => "Thông tin nguyên liệu."
            ]);
        }
        return response()->json([
            "message" => "Không tìm thấy thông tin nguyên liệu.",
            "status" => 404,
        ]);
    }
}
