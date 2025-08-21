<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plot;
use App\Models\Webmap;
use Illuminate\Http\Request;

class PlotControllerApi extends Controller
{
    public function index()
    {
        $plots = Plot::all();
        if ($plots->isNotEmpty()) {

            return response()->json([
                'data' => $plots,
                'status' => 200,
                'message' => 'Danh sách lô.',
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }
    public function detail($id)
    {
        $plot = Plot::find($id);

        if (!$plot) {
            return response()->json([
                'message' => 'Lô không tồn tại.'
            ], 404);
        }
        // $plot->farm_name = $plot->farm ? $plot->farm->farm_name : null;
        // $plot->unit = $plot->farm ? $plot->farm->unit : null;
        $geoData = json_decode($plot->mapJs, true);
        // $webmapUrl = "https://horuco.maps.arcgis.com/apps/instant/basic/index.html?appid=32f13c9fbdc949b3a6f34aab686b2bde";
        $webmaps = Webmap::first()->webmap ?? null;

        $mapJs = [
            "type" => "FeatureCollection",
            "features" => [
                [
                    "type" => "Feature",
                    "properties" => [
                        "Ten_lo" => $plot->plotName ?? null,
                        // "NT_Doi" => $plot->farm_name ?? null,
                        "Hang_dat" => $plot->hang_dat ?? null,
                        "Giong" => $plot->chi_tieu ?? null,
                        "Dien_tich" => $plot->plotArea ?? null,
                        "Nam_trong" => $plot->year ?? null,
                        "Hien_trang" => $plot->hien_trang ?? null,
                        // "Nam_Mo_Cao" => $plot->tapping_y ?? null,
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
                'id' => $plot->id,
                // 'farm_name' => $plot->farm_name,
                // 'unit_name' => $plot->farm->unitRelation->unit_name,
                'idmap' => $plot->idmap ?? null,
                'id_plot' => $plot->plotCode ?? null,
                // 'nha_sx' => $plot->nha_sx,
                // 'quoc_gia' => $plot->quoc_gia,
                // 'plot' => $plot->plot,
                'nam_trong' => $plot->year,
                'chi_tieu' => $plot->chi_tieu,
                'dien_tich' => $plot->plotArea,
                'tapping_y' => $plot->tapping_y,
                'repl_time' => $plot->repl_time,
                'find' => $plot->find,
                // 'webmap' => $plot->webmap,
                // 'webmap' => $webmapUrl,
                'webmap' => $webmaps,
                'gwf' => $plot->gwf,
                'xa' => $plot->xa,
                'huyen' => $plot->huyen,
                'nguon_goc_lo' => $plot->nguon_goc_lo,
                'nguon_goc_dat' => $plot->nguon_goc_dat,
                'chu_thich' => $plot->chu_thich,
                'mapJs' => $mapJs, // Định dạng lại GeoJSON chuẩn
                // 'created_at' => $plot->created_at,
                // 'updated_at' => $plot->updated_at,
            ],
            'message' => 'Chi tiết lô.'
        ], 200);
    }
}
