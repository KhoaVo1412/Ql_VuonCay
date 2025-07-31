<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlantingArea;
use App\Models\Webmap;
use Illuminate\Http\Request;

class PlantingAreaApi extends Controller
{
    // public function index()
    // {

    //     $PlantingArea = PlantingArea::with(['farm'])->get();
    //     if (true) {
    //         return response()->json([
    //             "data" => $PlantingArea->map(function ($item) {
    //                 return [
    //                     'id' => $item->id,
    //                     'farm_name' => $item->farm->farm_name ?? null,
    //                     'idmap' => $item->idmap ?? null,
    //                     'ma_lo' => $item->ma_lo ?? null,
    //                     'nha_sx' => $item->nha_sx,
    //                     'quoc_gia' => $item->quoc_gia,
    //                     'plot' => $item->plot,
    //                     'nam_trong' => $item->nam_trong,
    //                     'chi_tieu' => $item->chi_tieu,
    //                     'dien_tich' => $item->dien_tich,
    //                     'tapping_y' => $item->tapping_y,
    //                     'repl_time' => $item->repl_time,
    //                     'find' => $item->find,
    //                     'webmap' => $item->webmap,
    //                     'gwf' => $item->gwf,
    //                     'xa' => $item->xa,
    //                     'huyen' => $item->huyen,
    //                     'nguon_goc_lo' => $item->nguon_goc_lo,
    //                     'nguon_goc_dat' => $item->nguon_goc_dat,
    //                     'chu_thich' => $item->chu_thich,
    //                     'geo' => $item->geo,
    //                     'created_at' => $item->created_at,
    //                     'updated_at' => $item->updated_at,
    //                 ];
    //             }),
    //             "status" => 200,
    //             "message" => " Danh sách khu vực trồng.",
    //         ]);
    //     } else {
    //         return response()->json([
    //             'success' => false,
    //             'data' => 'Không tìm thấy dữ liệu.'
    //         ]);
    //     }
    // }
    public function index()
    {
        $PlantingArea = PlantingArea::with(['farm.unitRelation'])->get();

        if ($PlantingArea->isNotEmpty()) {
            return response()->json([
                "data" => $PlantingArea->map(function ($item) {
                    $geoData = json_decode($item->geo, true);
                    // $webmapUrl = "https://horuco.maps.arcgis.com/apps/instant/basic/index.html?appid=32f13c9fbdc949b3a6f34aab686b2bde";
                    $webmaps = Webmap::first()->webmap ?? null;
                    $geoJson = [
                        "type" => "FeatureCollection",
                        "features" => [
                            [
                                "type" => "Feature",
                                "properties" => [
                                    "Ten_lo" => $item->ma_lo ?? null,
                                    "NT_Doi" => $item->farm->farm_name ?? null,
                                    "Hang_dat" => $item->hang_dat ?? null,
                                    "Giong" => $item->chi_tieu ?? null,
                                    "Dien_tich" => $item->dien_tich ?? null,
                                    "Nam_trong" => $item->nam_trong ?? null,
                                    "Hien_trang" => $item->hien_trang ?? null,
                                    "X" => $geoData["features"][0]["properties"]["X"] ?? null,
                                    "Y" => $geoData["features"][0]["properties"]["Y"] ?? null
                                ],
                                "geometry" => [
                                    "type" => $geoData["features"][0]["geometry"]["type"] ?? "Polygon",
                                    "coordinates" => $geoData["features"][0]["geometry"]["coordinates"] ?? []
                                ]
                            ]
                        ]
                    ];

                    return [
                        'id' => $item->id,
                        'farm_name' => $item->farm->farm_name ?? null,
                        // 'unit' => $item->farm->unit ?? null,
                        'unit_name' => $item->farm->unitRelation->unit_name ?? null,
                        'idmap' => $item->idmap ?? null,
                        'id_plot' => $item->ma_lo ?? null,
                        'nha_sx' => $item->nha_sx,
                        'quoc_gia' => $item->quoc_gia,
                        'plot' => $item->plot,
                        'nam_trong' => $item->nam_trong,
                        'chi_tieu' => $item->chi_tieu,
                        'dien_tich' => $item->dien_tich,
                        'tapping_y' => $item->tapping_y,
                        'repl_time' => $item->repl_time,
                        'find' => $item->find,
                        // 'webmap' => $item->webmap,
                        // 'webmap' => $webmapUrl,
                        'webmap' => $webmaps,
                        'gwf' => $item->gwf,
                        'xa' => $item->xa,
                        'huyen' => $item->huyen,
                        'nguon_goc_lo' => $item->nguon_goc_lo,
                        'nguon_goc_dat' => $item->nguon_goc_dat,
                        'chu_thich' => $item->chu_thich,
                        'geo_json' => $geoJson, // Chuyển đổi sang GeoJSON
                        // 'created_at' => $item->created_at,
                        // 'updated_at' => $item->updated_at,
                    ];
                }),
                "status" => 200,
                "message" => "Danh sách khu vực trồng.",
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
        $plantingArea = PlantingArea::with('farm.unitRelation')->find($id);

        if (!$plantingArea) {
            return response()->json([
                'message' => 'Khu vực trồng không tồn tại.'
            ], 404);
        }
        $plantingArea->farm_name = $plantingArea->farm ? $plantingArea->farm->farm_name : null;
        $plantingArea->unit = $plantingArea->farm ? $plantingArea->farm->unit : null;
        $geoData = json_decode($plantingArea->geo, true);
        // $webmapUrl = "https://horuco.maps.arcgis.com/apps/instant/basic/index.html?appid=32f13c9fbdc949b3a6f34aab686b2bde";
        $webmaps = Webmap::first()->webmap ?? null;

        $geoJson = [
            "type" => "FeatureCollection",
            "features" => [
                [
                    "type" => "Feature",
                    "properties" => [
                        "Ten_lo" => $plantingArea->ma_lo ?? null,
                        "NT_Doi" => $plantingArea->farm_name ?? null,
                        "Hang_dat" => $plantingArea->hang_dat ?? null,
                        "Giong" => $plantingArea->chi_tieu ?? null,
                        "Dien_tich" => $plantingArea->dien_tich ?? null,
                        "Nam_trong" => $plantingArea->nam_trong ?? null,
                        "Hien_trang" => $plantingArea->hien_trang ?? null,
                        // "Nam_Mo_Cao" => $plantingArea->tapping_y ?? null,
                        "X" => $geoData["features"][0]["properties"]["X"] ?? null,
                        "Y" => $geoData["features"][0]["properties"]["Y"] ?? null
                    ],
                    "geometry" => [
                        "type" => $geoData["features"][0]["geometry"]["type"] ?? "Polygon",
                        "coordinates" => $geoData["features"][0]["geometry"]["coordinates"] ?? []
                    ]
                ]
            ]
        ];
        return response()->json([
            'status' => 200,
            'data' => [
                'id' => $plantingArea->id,
                'farm_name' => $plantingArea->farm_name,
                'unit_name' => $plantingArea->farm->unitRelation->unit_name,
                'idmap' => $plantingArea->idmap ?? null,
                'id_plot' => $plantingArea->ma_lo ?? null,
                'nha_sx' => $plantingArea->nha_sx,
                'quoc_gia' => $plantingArea->quoc_gia,
                'plot' => $plantingArea->plot,
                'nam_trong' => $plantingArea->nam_trong,
                'chi_tieu' => $plantingArea->chi_tieu,
                'dien_tich' => $plantingArea->dien_tich,
                'tapping_y' => $plantingArea->tapping_y,
                'repl_time' => $plantingArea->repl_time,
                'find' => $plantingArea->find,
                // 'webmap' => $plantingArea->webmap,
                // 'webmap' => $webmapUrl,
                'webmap' => $webmaps,
                'gwf' => $plantingArea->gwf,
                'xa' => $plantingArea->xa,
                'huyen' => $plantingArea->huyen,
                'nguon_goc_lo' => $plantingArea->nguon_goc_lo,
                'nguon_goc_dat' => $plantingArea->nguon_goc_dat,
                'chu_thich' => $plantingArea->chu_thich,
                'geo_json' => $geoJson, // Định dạng lại GeoJSON chuẩn
                // 'created_at' => $plantingArea->created_at,
                // 'updated_at' => $plantingArea->updated_at,
            ],
            'message' => 'Chi tiết khu vực trồng.'
        ], 200);
    }
}
