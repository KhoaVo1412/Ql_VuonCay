<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\OrderExport;
use App\Models\PlantingArea;
use Illuminate\Http\Request;

class GeojsonController extends Controller
{
    // public function index(Request $request)
    // {
    //     $batch_code = $request->query('batch_code');
    //     $batch = null;
    //     $coordinates = collect();
    //     $geojsons = collect();
    //     $geojsonCollection = [
    //         "type" => "FeatureCollection",
    //         "features" => []
    //     ];

    //     $ma_lo = $request->query('ma_lo');
    //     $plantingarea = null;

    //     if ($batch_code) {
    //         $batch = Batch::where('batch_code', $batch_code)->first();

    //         if ($batch) {
    //             $coordinates = $batch->ingredients->flatMap(function ($ingredient) {
    //                 return collect($ingredient->plantingAreas)->map(function ($area) {
    //                     return ['x' => $area->x, 'y' => $area->y];
    //                 });
    //             });

    //             $geojsons = $batch->ingredients->flatMap(function ($ingredient) {
    //                 return $ingredient->plantingAreas->pluck('geo');
    //             });
    //         }
    //     } elseif ($ma_lo) {
    //         $plantingarea = PlantingArea::where('ma_lo', $ma_lo)->first();

    //         if ($plantingarea) { // Không dùng isNotEmpty()
    //             $coordinates = [
    //                 ['x' => $plantingarea->x, 'y' => $plantingarea->y]
    //             ];

    //             $geojsons = [$plantingarea->geo]; // Đưa vào mảng nếu chỉ có một giá trị
    //         }
    //     }

    //     foreach ($geojsons as $geo) {
    //         $decoded = json_decode($geo, true);
    //         if (isset($decoded['features'])) {
    //             $geojsonCollection['features'] = array_merge($geojsonCollection['features'], $decoded['features']);
    //         }
    //     }

    //     // Chuyển sang JSON
    //     $jsonContent = json_encode($geojsonCollection, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    //     return view("geojson.index", compact('batch', 'coordinates', 'geojsons', 'plantingarea', 'jsonContent'));
    // }



    public function index(Request $request)
    {
        $batch_code = $request->query('batch_code');
        $batch = null;
        $coordinates = collect();
        $mergedGeoJSON = collect();

        $ma_lo = $request->query('ma_lo');
        $plantingarea = null;

        $orderexport_id = $request->query('orderexport_id');

        $orderexport = null;


        if ($batch_code) {
            $batch = Batch::where('batch_code', $batch_code)->first();

            if ($batch) {
                // dd($batch->ingredients);
                $coordinates = $batch->ingredients->flatMap(function ($ingredient) {
                    return collect($ingredient->plantingAreas)->flatMap(function ($area) {

                        $geojsonString = $area->geo;

                        preg_match('/"coordinates"\s*:\s*(\[\[\[.*?\]\]\])/', $geojsonString, $matches);

                        if (!empty($matches[1])) {
                            $coordinatesString = $matches[1];

                            $coordinatesString = preg_replace_callback('/\d+\.\d+/', function ($match) {
                                return '"' . $match[0] . '"';
                            }, $coordinatesString);

                            $geojsonString = preg_replace('/"coordinates"\s*:\s*\[\[\[.*?\]\]\]/', '"coordinates": ' . $coordinatesString, $geojsonString);

                            $geojson = json_decode($geojsonString, true);
                            // dd($geojson);
                            return collect($geojson['features'])->map(function ($feature) {
                                $coordinates = collect($feature['geometry']['coordinates']);
                                $type = $feature['geometry']['type'];
                                // dd($type);
                                $flattenLevel = $type == 'Polygon' ? 1 : 2;
                                return collect($coordinates)
                                    ->flatten($flattenLevel) // Loại bỏ cấp lồng [[[]]] → [[]]
                                    ->map(fn($point) => [
                                        'x' => $point[0],
                                        'y' => $point[1],
                                    ]);
                            });
                        }
                        return collect();

                    });
                })->toArray();

                // dd($coordinates);
                $geojsons = $batch->ingredients->flatMap(function ($ingredient) {
                    return collect($ingredient->plantingAreas)->map(function ($area) {
                        $geojson = json_decode($area->geo, true);

                        // Kiểm tra xem JSON có đúng cấu trúc không
                        if (!$geojson || !isset($geojson['features'])) {
                            return null; // Bỏ qua nếu không hợp lệ
                        }

                        return $geojson['features'];
                    })->filter()->flatten(1); // Lọc bỏ giá trị null và làm phẳng danh sách
                });
                // dd( $geojsons);
                // Tạo FeatureCollection hợp nhất
                $mergedGeoJSON = [
                    "type" => "FeatureCollection",
                    "features" => $geojsons->values()->all()
                ];

                // dd($mergedGeoJSON);

            }
        } elseif ($ma_lo) {
            $plantingarea = PlantingArea::where('ma_lo', $ma_lo)->first();
            // dd($plantingarea);
            if ($plantingarea) {
                // dd($plantingarea->geo);
                $coordinates = collect([$plantingarea])->flatMap(function ($area) {

                    $geojsonString = $area->geo;

                    preg_match('/"coordinates"\s*:\s*(\[\[\[.*?\]\]\])/', $geojsonString, $matches);

                    if (!empty($matches[1])) {
                        $coordinatesString = $matches[1];

                        $coordinatesString = preg_replace_callback('/\d+\.\d+/', function ($match) {
                            return '"' . $match[0] . '"';
                        }, $coordinatesString);

                        $geojsonString = preg_replace('/"coordinates"\s*:\s*\[\[\[.*?\]\]\]/', '"coordinates": ' . $coordinatesString, $geojsonString);
                        // dd($geojsonString);
                        $geojson = json_decode($geojsonString, true);
                        // dd($geojson['features']);
                        return collect($geojson['features'])->map(function ($feature) {
                            $coordinates = collect($feature['geometry']['coordinates']);
                            $type = $feature['geometry']['type'];
                            $flattenLevel = $type == 'Polygon' ? 1 : 2;
                            return $coordinates->flatten($flattenLevel)->map(fn($point) => [
                                'x' => $point[0],
                                'y' => $point[1],
                            ]);
                        });
                    }
                    return collect();
                })->toArray();
                // dd($coordinates);

                // $coordinates = [
                //     ['x' => $plantingarea->x, 'y' => $plantingarea->y]
                // ];

                // $geojsons = [$plantingarea->geo]; // Đưa vào mảng nếu chỉ có một giá trị

                $geojsons = collect([$plantingarea])->flatMap(function ($area) {
                    $geojson = json_decode($area->geo, true);

                    // Kiểm tra xem JSON có đúng cấu trúc không
                    if (!$geojson || !isset($geojson['features'])) {
                        return null; // Bỏ qua nếu không hợp lệ
                    }
                    // dd($geojson['features']);
                    return $geojson['features'];
                })->filter();
                // dd( $geojsons);
                // Tạo FeatureCollection hợp nhất
                $mergedGeoJSON = [
                    "type" => "FeatureCollection",
                    "features" => $geojsons->values()->all()
                ];
            }


        } elseif ($orderexport_id) {

            $orderexport = OrderExport::where('id', $orderexport_id)->first();


            // dd($plantingarea);
            if ($orderexport) {
                // dd($plantingarea->geo);
                $coordinates = $orderexport->batches->flatMap(function ($batch) {
                    return $batch->ingredients->flatMap(function ($ingredient) {

                        return collect($ingredient->plantingAreas)->flatMap(function ($area) {

                            $geojsonString = $area->geo;

                            preg_match('/"coordinates"\s*:\s*(\[\[\[.*?\]\]\])/', $geojsonString, $matches);

                            if (!empty($matches[1])) {
                                $coordinatesString = $matches[1];

                                $coordinatesString = preg_replace_callback('/\d+\.\d+/', function ($match) {
                                    return '"' . $match[0] . '"';
                                }, $coordinatesString);

                                $geojsonString = preg_replace('/"coordinates"\s*:\s*\[\[\[.*?\]\]\]/', '"coordinates": ' . $coordinatesString, $geojsonString);

                                $geojson = json_decode($geojsonString, true);
                                // dd($geojson);
                                return collect($geojson['features'])->map(function ($feature) {
                                    $coordinates = collect($feature['geometry']['coordinates']);
                                    $type = $feature['geometry']['type'];
                                    // dd($type);
                                    $flattenLevel = $type == 'Polygon' ? 1 : 2;
                                    return collect($coordinates)
                                        ->flatten($flattenLevel) // Loại bỏ cấp lồng [[[]]] → [[]]
                                        ->map(fn($point) => [
                                            'x' => $point[0],
                                            'y' => $point[1],
                                        ]);
                                });
                            }
                            return collect();

                        });
                    })->toArray();
                });


                // dd($coordinates);

                $geojsons = $orderexport->batches->flatMap(function ($batch) {
                    return $batch->ingredients->flatMap(function ($ingredient) {
                        return collect($ingredient->plantingAreas)->map(function ($area) {
                            $geojson = json_decode($area->geo, true);

                            // Kiểm tra xem JSON có đúng cấu trúc không
                            if (!$geojson || !isset($geojson['features'])) {
                                return null; // Bỏ qua nếu không hợp lệ
                            }

                            return $geojson['features'];
                        })->filter()->flatten(1); // Lọc bỏ giá trị null và làm phẳng danh sách
                    });

                });
                // dd( $geojsons);
                // Tạo FeatureCollection hợp nhất
                $mergedGeoJSON = [
                    "type" => "FeatureCollection",
                    "features" => $geojsons->values()->all()
                ];

            }

        }


        return view("geojson.index", compact('batch', 'coordinates', 'mergedGeoJSON', 'plantingarea', 'orderexport'));
    }

    public function downloadGeojson(Request $request)
    {
        // Lấy batch_code hoặc ma_lo từ request
        $batchCode = $request->query('batch_code');
        $maLo = $request->query('ma_lo');
        $orderexportCode = $request->query('code');

        // Khởi tạo danh sách rỗng cho PlantingAreas
        $plantingAreas = collect();

        // Nếu có batch_code, lấy tất cả PlantingAreas từ batch
        if ($batchCode) {
            $batch = Batch::where('batch_code', $batchCode)->first();

            if (!$batch) {
                abort(404, "Không tìm thấy Batch với mã: $batchCode");
            }

            // Lấy tất cả PlantingAreas liên quan đến batch
            $plantingAreas = $batch->ingredients->flatMap->plantingAreas;
        }

        // Nếu có ma_lo, lấy trực tiếp PlantingArea theo mã lô
        if ($maLo) {
            $plantingArea = PlantingArea::where('ma_lo', $maLo)->first();

            if (!$plantingArea) {
                abort(404, "Không tìm thấy PlantingArea với mã lô: $maLo");
            }

            // Thêm vào danh sách PlantingAreas
            $plantingAreas->push($plantingArea);
        }

        if ($orderexportCode) {
            $orderexport = OrderExport::where('code', $orderexportCode)->first();

            if (!$orderexport) {
                abort(404, "Không tìm thấy Lệnh xuất hàng với mã: $orderexport");
            }

            // Lấy tất cả PlantingAreas liên quan đến batch

            $plantingAreas = $orderexport->batches
                ->flatMap(fn($batch) => $batch->ingredients)
                ->flatMap(fn($ingredient) => $ingredient->plantingAreas);
        }


        // Kiểm tra nếu không có dữ liệu
        if ($plantingAreas->isEmpty()) {
            abort(404, "Không có dữ liệu GeoJSON để tải xuống.");
        }

        $geojsonCollection = [
            "type" => "FeatureCollection",
            "features" => []
        ];

        foreach ($plantingAreas as $plantingArea) {
            $geo = json_decode($plantingArea->geo, true); // Chuyển từ JSON string sang mảng PHP
            if (isset($geo['features'])) {
                $geojsonCollection['features'] = array_merge($geojsonCollection['features'], $geo['features']);
            }
        }

        $jsonContent = json_encode($geojsonCollection, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // Xác định tên file theo batch_code hoặc ma_lo
        // $fileName = $batchCode ? "{$batchCode}.geojson" : "{$maLo}.geojson" : "{$orderexportCode}.geojson";
        if ($batchCode) {
            $fileName = "{$batchCode}.geojson";
        } elseif ($maLo) {
            $fileName = "{$maLo}.geojson";
        } elseif ($orderexportCode) {
            $fileName = "{$orderexportCode}.geojson";
        }

        // Trả về file để tải xuống
        return response()->streamDownload(function () use ($jsonContent) {
            echo $jsonContent;
        }, $fileName, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}