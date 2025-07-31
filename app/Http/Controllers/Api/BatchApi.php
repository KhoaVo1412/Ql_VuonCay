<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\ApiKey;
use Illuminate\Http\Request;
use App\Models\Webmap;
use Illuminate\Support\Facades\Auth;

class BatchApi extends Controller
{

    public function index(Request $request)
    {
        $request->validate([
            'batch_code' => 'required|string',
        ]);

        $authUser = Auth::user();
        $userId = $authUser ? $authUser->id : $request->input('user_id');
        $customerId = $request->input('customer_id');

        // Tìm batch
        $batch = Batch::where('batch_code', $request->batch_code)
            ->with([
                'orderExport.contract.customer',
                'ingredients',
                'ingredients.farm.unitRelation',
                'ingredients.factory',
                'ingredients.typeOfPus',
                'ingredients.plantingAreas',
                'ingredients.vehicle',
                'testingResult'
            ])
            ->first();

        if (!$batch) {
            return response()->json(['message' => 'Không tìm thấy lô hàng này vui lòng kiểm tra lại.'], 404);
        }

        // Check quyền truy cập lô hàng
        $contract = $batch->orderExport->contract ?? null;
        $customer = $contract ? $contract->customer : null;

        if (!$customer) {
            return response()->json(['message' => 'Không xác định được khách hàng cho lô này.'], 403);
        }

        if ($authUser && !$authUser->hasRole('Admin')) {
            if ($customer->user_id !== $userId) {
                return response()->json(['message' => 'Bạn không có quyền truy cập lô hàng này.'], 403);
            }
        } elseif (!$authUser && $customerId && $customer->id != $customerId) {
            return response()->json(['message' => 'Bạn không có quyền truy cập lô hàng này.'], 403);
        }

        // Nếu hợp lệ, tiếp tục trả dữ liệu như cũ
        $ingredients = $batch->ingredients->map(function ($ingredient) {
            $treeType = $ingredient->plantingAreas->pluck('chi_tieu')->join(', ');

            return [
                'id' => $ingredient->id,
                'farm_name' => optional($ingredient->farm)->farm_name,
                'unit' => optional($ingredient->farm->unitRelation)->unit_name,
                'tree_type' => $treeType,
                'type_of_pus_id' => $ingredient->type_of_pus_id,
                'trip' => $ingredient->trip,
                'received_date' => $ingredient->received_date,
                'receiving_factory' => optional($ingredient->factory)->factory_name,
                'harvesting_date' => $ingredient->harvesting_date,
                'end_harvest_date' => $ingredient->end_harvest_date,
                'vehicle_number_id' => $ingredient->vehicle_number_id,
                'pivot' => $ingredient->pivot,
                'farm' => $ingredient->farm,
                'type_of_pus' => $ingredient->typeOfPus,
                'vehicle' => $ingredient->vehicle,
                'planting_areas' => $ingredient->plantingAreas->map(function ($plantingArea) {
                    $geoData = json_decode($plantingArea->geo, true);
                    $webmaps = optional(Webmap::first())->webmap;

                    $geoJson = [
                        "type" => "FeatureCollection",
                        "features" => [
                            [
                                "type" => "Feature",
                                "properties" => [
                                    "id_plot" => $plantingArea->ma_lo,
                                    "fid" => $plantingArea->fid,
                                    "idmap" => $plantingArea->idmap,
                                    "plot" => $plantingArea->id_plot,
                                    "plantation" => optional($plantingArea->farm)->farm_name,
                                    "planting_y" => $plantingArea->nam_trong,
                                    "area_ha" => $plantingArea->dien_tich,
                                    "tapping_y" => $plantingArea->tapping_y,
                                    "repl_time" => $plantingArea->repl_time,
                                    "find" => $plantingArea->find,
                                    "webmap" => $webmaps,
                                    "gwf" => $plantingArea->gwf,
                                    "nguon_goc_lo" => $plantingArea->nguon_goc_lo,
                                    "nguon_goc_dat" => $plantingArea->nguon_goc_dat,
                                    "hang_dat" => $plantingArea->hang_dat,
                                    "hien_trang" => $plantingArea->hien_trang,
                                    "layer" => $plantingArea->layer,
                                    "x" => $plantingArea->x,
                                    "y" => $plantingArea->y,
                                    "xa" => $plantingArea->xa,
                                    "huyen" => $plantingArea->huyen,
                                    "chuthich" => $plantingArea->chuthich,
                                    "quocgia" => $plantingArea->quoc_gia,
                                    "nhasx" => $plantingArea->nha_sx,
                                ],
                                "geometry" => [
                                    "type" => $geoData["features"][0]["geometry"]["type"] ?? "Polygon",
                                    "coordinates" => $geoData["features"][0]["geometry"]["coordinates"] ?? []
                                ]
                            ]
                        ]
                    ];

                    return [
                        'id' => $plantingArea->id,
                        'unit' => optional($plantingArea->farm->unitRelation)->unit_name,
                        'id_plot' => $plantingArea->ma_lo,
                        'planting_y' => $plantingArea->nam_trong,
                        'fid' => $plantingArea->fid,
                        'idmap' => $plantingArea->idmap,
                        'plot' => $plantingArea->id_plot,
                        'plantation' => optional($plantingArea->farm)->farm_name,
                        'area_ha' => $plantingArea->dien_tich,
                        'tapping_y' => $plantingArea->tapping_y,
                        'repl_time' => $plantingArea->repl_time,
                        'find' => $plantingArea->find,
                        'webmap' => $webmaps,
                        'geo_json' => $geoJson,
                        'gwf' => $plantingArea->gwf,
                        'nguon_goc_lo' => $plantingArea->nguon_goc_lo,
                        'nguon_goc_dat' => $plantingArea->nguon_goc_dat,
                        'hang_dat' => $plantingArea->hang_dat,
                        'hien_trang' => $plantingArea->hien_trang,
                        'layer' => $plantingArea->layer,
                        'x' => $plantingArea->x,
                        'y' => $plantingArea->y,
                        'xa' => $plantingArea->xa,
                        'huyen' => $plantingArea->huyen,
                        'chuthich' => $plantingArea->chuthich,
                        'quocgia' => $plantingArea->quoc_gia,
                        'nhasx' => $plantingArea->nha_sx,
                    ];
                }),
            ];
        });

        $testingResult = $batch->testingResult;

        return response()->json([
            'batch' => [
                'id' => $batch->id,
                'batch_code' => $batch->batch_code,
                'qr_code' => $batch->qr_code,
                'date_sx' => $batch->date_sx,
                'batch_weight' => $batch->batch_weight . " Tấn",
                'banh_weight' => $batch->banh_weight . " Kg",
                'status' => $batch->status,
                'type' => $batch->type,
                'note' => $batch->note,
                'order_export_id' => $batch->order_export_id,
                'contract' => $contract,
                'ingredients' => $ingredients,
                'testing_results' => $testingResult,
            ],
            'status' => 200,
            'success' => 'Thông tin của lô',
        ]);
    }

    public function index1(Request $request)
    {
        $request->validate([
            'batch_code' => 'required|string',
        ]);
        // $api_key = ApiKey::all();
        $batch = Batch::where('batch_code', $request->batch_code)
            ->with([
                'orderExport',
                'ingredients',
                'ingredients.farm',
                'ingredients.factory',
                'ingredients.typeOfPus',
                'ingredients.plantingAreas',
                'ingredients.vehicle',
                'testingResult'
            ])
            ->first();

        if (!$batch) {
            return response()->json(['message' => 'Batch not found'], 404);
        }

        $ingredients = $batch->ingredients->map(function ($ingredient) {
            $treeType = $ingredient->plantingAreas->pluck('chi_tieu')->join(', ');

            return [
                'id' => $ingredient->id,
                'farm_name' => $ingredient->farm ? $ingredient->farm->farm_name : null,
                'unit' => $ingredient->farm ? $ingredient->farm->unitRelation->unit_name : null,
                'tree_type' => $treeType,
                'type_of_pus_id' => $ingredient->type_of_pus_id,
                'trip' => $ingredient->trip,
                'received_date' => $ingredient->received_date,
                'receiving_factory' => $ingredient->factory ? $ingredient->factory->factory_name : null,
                'harvesting_date' => $ingredient->harvesting_date,
                'end_harvest_date' => $ingredient->end_harvest_date,
                'vehicle_number_id' => $ingredient->vehicle_number_id,
                'pivot' => $ingredient->pivot,
                'farm' => $ingredient->farm,
                'type_of_pus' => $ingredient->typeOfPus,
                'vehicle' => $ingredient->vehicle,
                'planting_areas' => $ingredient->plantingAreas->map(function ($plantingArea) {
                    $geoData = json_decode($plantingArea->geo, true);
                    // $webmapUrl = "https://horuco.maps.arcgis.com/apps/instant/basic/index.html?appid=32f13c9fbdc949b3a6f34aab686b2bde";
                    $webmaps = Webmap::first()->webmap ?? null;

                    $geoJson = [
                        "type" => "FeatureCollection",
                        "features" => [
                            [
                                "type" => "Feature",
                                "properties" => [
                                    "id_plot" => $plantingArea->ma_lo,
                                    "fid" => $plantingArea->fid,
                                    "idmap" => $plantingArea->idmap,
                                    "plot" => $plantingArea->id_plot,
                                    "plantation" => $plantingArea->farm ? $plantingArea->farm->farm_name : null,
                                    "plot" => $plantingArea->plot,
                                    "planting_y" => $plantingArea->nam_trong,
                                    'area_ha' => $plantingArea->dien_tich,
                                    'tapping_y' => $plantingArea->tapping_y,
                                    'repl_time' => $plantingArea->repl_time,
                                    'find' => $plantingArea->find,
                                    // 'webmap' => $plantingArea->webmap,
                                    // 'webmap' => $webmapUrl,
                                    'webmap' => $webmaps,
                                    'gwf' => $plantingArea->gwf,
                                    'nguon_goc_lo' => $plantingArea->nguon_goc_lo,
                                    'nguon_goc_dat' => $plantingArea->nguon_goc_dat,
                                    'hang_dat' => $plantingArea->hang_dat,
                                    'hien_trang' => $plantingArea->hien_trang,
                                    'layer' => $plantingArea->layer,
                                    'x' => $plantingArea->x,
                                    'y' => $plantingArea->y,
                                    'xa' => $plantingArea->xa,
                                    'huyen' => $plantingArea->huyen,
                                    'chuthich' => $plantingArea->chuthich,
                                    'quocgia' => $plantingArea->quoc_gia,
                                    'nhasx' => $plantingArea->nha_sx,
                                    // 'created_at' => $plantingArea->created_at,
                                    // 'updated_at' => $plantingArea->updated_at,
                                ],
                                "geometry" => [
                                    "type" => $geoData["features"][0]["geometry"]["type"] ?? "Polygon",
                                    "coordinates" => $geoData["features"][0]["geometry"]["coordinates"] ?? []
                                ]
                            ]
                        ]
                    ];

                    return [
                        'id' => $plantingArea->id,
                        'unit' => $plantingArea->farm->unitRelation->unit_name,
                        'id_plot' => $plantingArea->ma_lo,
                        'planting_y' => $plantingArea->nam_trong,
                        'fid' => $plantingArea->fid,
                        'idmap' => $plantingArea->idmap,
                        'plot' => $plantingArea->id_plot,
                        'plantation' => $plantingArea->farm ? $plantingArea->farm->farm_name : null,
                        'area_ha' => $plantingArea->dien_tich,
                        'tapping_y' => $plantingArea->tapping_y,
                        'repl_time' => $plantingArea->repl_time,
                        'find' => $plantingArea->find,
                        // 'webmap' => $plantingArea->webmap,
                        // 'webmap' => $webmapUrl,
                        'webmap' => $webmaps,
                        'geo_json' => $geoJson,
                        'gwf' => $plantingArea->gwf,
                        'nguon_goc_lo' => $plantingArea->nguon_goc_lo,
                        'nguon_goc_dat' => $plantingArea->nguon_goc_dat,
                        'hang_dat' => $plantingArea->hang_dat,
                        'hien_trang' => $plantingArea->hien_trang,
                        'layer' => $plantingArea->layer,
                        'x' => $plantingArea->x,
                        'y' => $plantingArea->y,
                        'xa' => $plantingArea->xa,
                        'huyen' => $plantingArea->huyen,
                        'chuthich' => $plantingArea->chuthich,
                        'quocgia' => $plantingArea->quoc_gia,
                        'nhasx' => $plantingArea->nha_sx,
                    ];
                }),
            ];
        });
        $contract = $batch->orderExport->contract ?? null;
        $testingResult = $batch->testingResult;

        return response()->json([
            // 'key' => ['api_key' => $api_key->token_key],
            'batch' => [
                'id' => $batch->id,
                'batch_code' => $batch->batch_code,
                'qr_code' => $batch->qr_code,
                'date_sx' => $batch->date_sx,
                'batch_weight' => $batch->batch_weight . " Tấn",
                'banh_weight' => $batch->banh_weight . " Kg",
                'status' => $batch->status,
                'type' => $batch->type,
                'note' => $batch->note,
                'order_export_id' => $batch->order_export_id,
                'contract' => $contract ? $contract : null,
                'ingredients' => $ingredients,
                'testing_results' => $testingResult ? $testingResult : null,
            ],
            'status' => 200,
            'success' => 'Thông tin của lô'
        ]);
    }
}