<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\ApiKey;
use App\Models\AppMap;

class HomeController extends Controller
{
    public function index()
    {

        $tokenMap = ApiKey::first()->token_key;
        $url = AppMap::first()->appMap;

        // Bước 1: Phân tích URL thành các phần
        $parsedUrl = parse_url($url);

        // Bước 2: Lấy phần query string ("id=123") và phân tách nó
        parse_str($parsedUrl['query'], $queryParams);

        // Bước 3: Lấy giá trị của tham số 'id'
        $idMap = $queryParams['id'] ?? null;

        // dd($token);
        return view('index', compact('tokenMap', 'idMap'));
    }
    // public function getBatchDetails(Request $request)
    // {
    //     $batchCode = $request->input('batch_code');

    //     $batch = Batch::where('batch_code', $batchCode)->first();

    //     if ($batch) {
    //         $testingResults = $batch->testingResults;
    //         return response()->json([
    //             'status' => 200,
    //             'batch' => $batch,
    //             'testing_results' => $testingResults
    //         ]);
    //     }

    //     return response()->json([
    //         'status' => 404,
    //         'message' => 'Không tìm thấy lô hàng'
    //     ]);
    // }

    public function search(Request $request)
    {
        $code = $request->input('code');

        $token = Auth::user()->remember_token;

        if (!$code) {
            return response()->json([
                'success' => false,
                'message' => 'Mã lô hàng không được để trống.',
            ], 400);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->get('https://app.binhlongrubber.vn/api/qr-scan', [
                'code' => $code,
            ]);

            $response2 = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->get('https://app.binhlongrubber.vn/api/test', [
                'code' => $code,
            ]);

            $data = null;
            $data_test = null;
            $data_contract = null;


            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['results'])) {
                    $contract_id = $data['results']['contract_id'];

                    $response3 = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $token,
                    ])->get('https://app.binhlongrubber.vn/api/detail-order', [
                        'id' => $contract_id,
                    ]);

                    if ($response3->successful()) {
                        $data_contract = $response3->json();
                    }
                } else {

                    return response()->json([
                        'success' => false,
                        'message' => 'Không tìm thấy kết quả',
                    ], 404);
                }
            }

            if ($response2->successful()) {
                $data_test = $response2->json();
            }

            if (!$data && !$data_test) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy dữ liệu từ API.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'data_test' => $data_test,
                'data_contract' => $data_contract,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi gọi API: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function fetchContracts()
    {
        $token = Auth::user()->remember_token;
        $listOrderResponse = Http::withHeaders([
            'token' => $token,
        ])->get('https://app.horuco.com.vn/api/contracts/list');

        if ($listOrderResponse->successful()) {
            $listOrderData = $listOrderResponse->json();
            $contracts = $listOrderData['data'] ?? [];
            return response()->json([
                'success' => true,
                'contracts' => $contracts,
                'count' => count($contracts)
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Lỗi khi gọi API danh sách hợp đồng',
        ], 500);
    }
    public function getContractDetails1(Request $request, $id)
    {
        $token = Auth::user()->remember_token;

        $listOrderResponse = Http::withHeaders([
            'token' => $token,
        ])->get('https://app.horuco.com.vn/api/contracts/list');

        if ($listOrderResponse->successful()) {
            $contract = collect($listOrderResponse->json()['data'])->firstWhere('id', $id);

            $detailOrderResponse = Http::withHeaders([
                'token' => $token,
            ])->get('https://app.horuco.com.vn/api/contracts/get', [
                'id' => $id,
            ]);

            if ($detailOrderResponse->successful()) {
                $contractDetails = $detailOrderResponse->json()['data'];

                // Lấy danh sách mã lệnh xuất hàng từ order_export_list
                $codes = collect($contractDetails['order_export_list'])->pluck('code');

                $data = [
                    'contract' => $contractDetails, // Trả về toàn bộ thông tin hợp đồng
                    'codes' => $codes, // Danh sách mã lệnh xuất hàng
                ];

                return response()->json([
                    'success' => true,
                    'msg' => 'Lấy danh sách thành công',
                    'data' => $data,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'msg' => 'Không thể lấy chi tiết hợp đồng',
                    'error' => $detailOrderResponse->body(),
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'msg' => 'Không thể lấy danh sách hợp đồng',
                'error' => $listOrderResponse->body(),
            ]);
        }
    }
    public function getContractDetails(Request $request, $id)
    {
        $token = Auth::user()->remember_token;

        $listOrderResponse = Http::withHeaders([
            'token' => $token,
        ])->get('https://app.horuco.com.vn/api/contracts/list');

        if ($listOrderResponse->successful()) {
            $contract = collect($listOrderResponse->json()['data'])->firstWhere('id', $id);

            $detailOrderResponse = Http::withHeaders([
                'token' => $token,
            ])->get('https://app.horuco.com.vn/api/contracts/get', [
                'id' => $id,
            ]);

            if ($detailOrderResponse->successful()) {
                $contractDetails = $detailOrderResponse->json()['data'];

                // Add DDS link to each batch in order exports
                $contractDetails['order_export_list'] = collect($contractDetails['order_export_list'])->map(function ($orderExport) {
                    $orderExport['batches'] = collect($orderExport['batches'])->map(function ($batch) use ($orderExport) {
                        $batch['dds_link'] = $this->getDdsLinkForBatch($orderExport['code'], $batch['id']);
                        return $batch;
                    });
                    return $orderExport;
                });

                return response()->json([
                    'success' => true,
                    'msg' => 'Lấy danh sách thành công',
                    'data' => [
                        'contract' => $contractDetails,
                        'codes' => collect($contractDetails['order_export_list'])->pluck('code'),
                    ],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'msg' => 'Không thể lấy chi tiết hợp đồng',
                    'error' => $detailOrderResponse->body(),
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'msg' => 'Không thể lấy danh sách hợp đồng',
                'error' => $listOrderResponse->body(),
            ]);
        }
    }

    public function getDdsLinkForBatch($orderExportCode, $batchId)
    {
        return route('export.dds.order', ['code' => $orderExportCode, 'batchId' => $batchId]);
    }

    // public function getContractDetails(Request $request, $id)
    // {
    //     $token = Auth::user()->remember_token;

    //     $listOrderResponse = Http::withHeaders([
    //         'token' => $token,
    //     ])->get('https://app.horuco.com.vn/api/contracts/list');

    //     if ($listOrderResponse->successful()) {

    //         $contract = collect($listOrderResponse->json()['data'])->firstWhere('id', $id);

    //         $detailOrderResponse = Http::withHeaders([
    //             'token' => $token,
    //         ])->get('https://app.horuco.com.vn/api/contracts/get', [
    //             'id' => $id,
    //         ]);

    //         if ($detailOrderResponse->successful()) {

    //             $contractDetails = $detailOrderResponse->json()['data']['contract'];
    //             $codeinfo = $detailOrderResponse->json()['data']['code'];

    //             // $codes = collect($detailOrderResponse->json()['data']['code'])
    //             //     ->flatMap(function ($contractDetail) {
    //             //         return collect($contractDetail['plots'])->pluck('code');
    //             //     });

    //             $data = [
    //                 'contract_info' => $contract,
    //                 'contract_details' => $contractDetails,
    //                 // 'codes' => $codes,
    //                 // 'codeinfo' => $codeinfo,
    //             ];

    //             return response()->json([
    //                 'success' => true,
    //                 'msg' => 'Lấy danh sách thành công',
    //                 'data' => $data,
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'success' => false,
    //                 'msg' => 'Không thể lấy chi tiết hợp đồng',
    //                 'error' => $detailOrderResponse->body(),
    //             ]);
    //         }
    //     } else {
    //         return response()->json([
    //             'success' => false,
    //             'msg' => 'Không thể lấy danh sách hợp đồng',
    //             'error' => $listOrderResponse->body(),
    //         ]);
    //     }
    // }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function map(Request $request)
    {
        $batch_code = $request->input('code');
        $token = Auth::user()->remember_token;

        if (!$batch_code) {
            return response()->json([
                'success' => false,
                'message' => 'Mã lô hàng không được để trống.',
            ], 400);
        }

        try {
            $response = Http::withHeaders([
                'token' => $token,
            ])->get('https://app.horuco.com.vn/api/export-batch', [
                'batch_code' => $batch_code,
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy dữ liệu từ API.',
                ], 404);
            }

            $data = $response->json();

            // Lấy danh sách geo_json của tất cả khu trồng
            $geoJsons = [];
            foreach ($data['batch']['ingredients'] as $ingredient) {
                foreach ($ingredient['planting_areas'] as $area) {
                    if (isset($area['geo_json'])) {
                        $geoJsons[] = $area['geo_json'];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'geo_jsons' => $geoJsons,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi gọi API: ' . $e->getMessage(),
            ], 500);
        }
    }
}
