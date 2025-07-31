<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Batch;
use App\Models\BatchIngredients;
use App\Models\Farm;
use App\Models\Ingredient;
use App\Models\PlantingArea;
use App\Models\TypeOfPus;
use App\Models\Vehicle;
use App\Models\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userFarms = $user->farms()->with('Unrelation')->get();
        $farmName = $userFarms->isEmpty() ? Farm::all() : $userFarms;
        // dd($farmName);
        $query = BatchIngredients::leftJoin('batches', 'batch_ingredient.batch_id', '=', 'batches.id')
            ->leftJoin('ingredients', 'batch_ingredient.ingredient_id', '=', 'ingredients.id')
            ->leftJoin('vehicles', 'ingredients.vehicle_number_id', '=', 'vehicles.id')
            ->leftJoin('factory', 'ingredients.received_factory_id', '=', 'factory.id')
            ->leftJoin('farm', 'ingredients.farm_id', '=', 'farm.id')
            ->leftJoin('units', 'farm.unit_id', '=', 'units.id')
            ->select(
                'batch_ingredient.*',
                'batches.id as batch_id',
                'batches.date_sx',
                'batches.batch_code',
                'batches.banh_weight',
                'batches.batch_weight',
                'vehicles.vehicle_number',
                'factory.factory_name',
                'ingredients.trip',
                'ingredients.received_date',
                'ingredients.trip',
                'farm.farm_name as farm_name',
                'farm.unit',
                'units.unit_name as unit_name',
            );

        // if ($request->has('farm_filter') && $request->input('farm_filter') != '') {
        //     $query->where('farm.farm_name', $request->input('farm_filter'));
        // }

        if ($request->has('farm_filter') && $request->input('farm_filter') != '') {
            list($farm_name, $unit_name) = explode('|', $request->input('farm_filter'));

            $query->where('farm.farm_name', $farm_name);

            if ($unit_name) {
                $query->where('units.unit_name', $unit_name);
            }
        }

        if ($request->has('year') && $request->year != '') {
            $query->whereYear('batches.date_sx', $request->year);
        }
        if ($request->has('month') && $request->month != '') {
            $query->whereMonth('batches.date_sx', $request->month);
        }

        $all_batches_ingredients = $query->orderBy('id', 'desc')->get();
        $groupedData = [];

        foreach ($all_batches_ingredients as $item) {
            // Ghép key để gom nhóm
            $key = $item->batch_code . '-' . $item->date_sx . '-' . $item->banh_weight . '-' . $item->factory_name;

            // Nếu chưa có nhóm thì tạo mới
            if (!isset($groupedData[$key])) {
                $groupedData[$key] = [
                    'id' => $item->batch_id,
                    'batch_code' => $item->batch_code,
                    'date_sx' => $item->date_sx,
                    'banh_weight' => $item->banh_weight,
                    'batch_weight' => $item->batch_weight,
                    'factory_name' => $item->factory_name,
                    'data' => [] // Đây là mảng để chứa nhiều dòng liên quan
                ];
            }

            // Thêm dữ liệu con vào `data[]`
            $groupedData[$key]['data'][] = [
                'id' => $item->id,
                'farm_name' => $item->farm_name,
                'unit_name' => $item->unit_name,
                'vehicle_number' => $item->vehicle_number,
                'trip' => $item->trip,
                'received_date' => $item->received_date,
            ];
        }

        $groupedData = collect(array_values($groupedData));

        if ($request->ajax()) {
            return DataTables::of($groupedData)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row['id'] . '" data-id="' . $row['id'] . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->addColumn('batch_id', function ($row) {
                    return $row['batch_code'];
                })
                ->addColumn('date_sx', function ($row) {
                    // return $row->date_sx;
                    return Carbon::parse($row['date_sx'])->format('d/m/Y');
                })
                ->addColumn('banh_weight', function ($row) {
                    return $row['banh_weight'] ?? '';
                })
                ->addColumn('batch_weight', function ($row) {
                    return $row['batch_weight'] ?? '';
                })
                ->addColumn('factory_name', function ($row) {
                    return $row['factory_name'] ?? '';
                })
                // ->addColumn('farm_name', function ($row) {
                //     return $row->farm_name ?? '';
                // })
                // // ->editColumn('unit', function ($row) {
                // //     return $row->unit;
                // // })
                // ->addColumn('unit', function ($row) {
                //     return $row->unit_name ?? '';
                // })
                // ->addColumn('vehicle_number', function ($row) {
                //     return $row->vehicle_number ?? '';
                // })
                // ->addColumn('trip', function ($row) {
                //     return $row->trip;
                // })
                // ->addColumn('received_date', function ($row) {
                //     return Carbon::parse($row->received_date)->format('d/m/Y');
                // })

                ->addColumn('data', function ($row) {

                    $tableContent = collect($row['data'])->map(function ($item, $index) {
                        // $editUrl = '/edit-batches/' . $item['id']; // Hoặc $item['id'] nếu mỗi dòng con có id riêng
                        // $deleteUrl = '/delete-batches/' . $item['id']; // Tương tự
                        // $modalId = 'deleteModal_' . $item['id'] . '_' . $index;
                        return '
                            <tr>
                                <td class="text-start">' . ($item['farm_name'] ?? '') . '</td>
                                <td class="text-start">' . ($item['unit_name'] ?? '') . '</td>
                                <td class="text-start">' . ($item['vehicle_number'] ?? '') . '</td>
                                <td class="text-center">' . ($item['trip'] ?? '') . '</td>
                                <td class="text-start">' . (\Carbon\Carbon::parse($item['received_date'])->format('d/m/Y')) . '</td>
           
                                </tr>';
                    })->implode('');

                    return '
                        <div class="table-container">
                            <table class="nested-table table table-sm table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center">Nông Trường</th>
                                        <th class="text-center">Đơn Vị</th>
                                        <th class="text-center">Số Xe</th>
                                        <th class="text-center">Số Chuyến</th>
                                        <th class="text-center">Ngày Tiếp Nhận</th>
                                    </tr>
                                </thead>
                                <tbody>' . $tableContent . '</tbody>
                            </table>
                        </div>';
                })

                ->addColumn('action', function ($row) {
                    $action = '
                        <div>
                            <a href="/edit-batches/' . $row['id'] . '" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <!--<a class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal' . $row['id'] . '">
                                <i class="fas fa-trash-alt"></i>
                            </a>-->
                        </div>
                        <div class="modal fade" id="deleteModal' . $row['id'] . '" tabindex="-1" aria-labelledby="deleteModalLabel' . $row['id'] . '" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel' . $row['id'] . '">Xác Nhận Xóa</h5>
                                        <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Bạn có chắc chắn có muốn xóa thông tin lô này không ?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/delete-batches/' . $row['id'] . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'stt', 'batch_code', 'date_sx', 'banh_weight', 'batch_weight', 'factory_name', 'data', 'action'])
                ->make(true);
        }
        return view('batch.batches_detail.all_batchs', compact('farmName'));
    }

    public function add(Request $request)
    {
        $ingredients = Ingredient::with('typeOfPus', 'vehicle', 'farm.unitRelation')
            // ->whereNotIn('id', function ($query) {
            //     $query->select('ingredient_id')->from('batch_ingredient');
            // })
            ->get();
        $batchIdsWithIngredients = DB::table('batch_ingredient')->pluck('batch_id')->toArray();

        $batches = Batch::whereNotIn('id', $batchIdsWithIngredients)->get();

        // $ingredients = Ingredient::with('typeOfPus', 'vehicle', 'farm')->get();
        // dd($batchIdsWithIngredients, $batches);
        return view('batch.batches_detail.add', compact('batchIdsWithIngredients', 'ingredients', 'batches'));
    }
    private function getTypeOfPusId($typeOfPusName)
    {
        $typeOfPus = TypeOfPus::where('name_pus', $typeOfPusName)->firstOrFail();
        return $typeOfPus->id;
    }

    private function getFarmId($farmName)
    {
        $farm = Farm::where('farm_name', $farmName)->firstOrFail();
        return $farm->id;
    }

    private function getVehicleId($vehicleNumber)
    {
        $vehicle = Vehicle::where('vehicle_number', $vehicleNumber)->firstOrFail();
        return $vehicle->id;
    }
    private function getFactoryId($factoryName)
    {
        $factory = Factory::where('factory_name', $factoryName)->firstOrFail();
        return $factory->id;
    }
    // public function save(Request $request)
    // {
    //     try {
    //         $today = Carbon::today()->startOfDay();
    //         $validatedData = $request->validate([
    //             'batch_code.*' => 'required|exists:batches,id',
    //             'date_sx.*' => 'required|date_format:d/m/Y',
    //             'batch_weight' => 'required|numeric',
    //             'banh_weight' => 'required|numeric',
    //             'batches.*.ingredients.*.type_of_pus' => 'required|string',
    //             'batches.*.ingredients.*.received_date' => 'required|date_format:d/m/Y',
    //             'batches.*.ingredients.*.vehicle' => 'required|string',
    //             'batches.*.ingredients.*.trip' => 'required|string',
    //         ]);

    //         $batchCodes = $request->input('batch_code');
    //         $dates = array_map(function ($date) {
    //             return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
    //         }, $request->input('date_sx'));
    //         $batchesData = $request->input('batches');

    //         foreach ($dates as $index => $date) {
    //             if (Carbon::parse($date)->startOfDay()->greaterThan($today)) {
    //                 return back()->with(['error' => "Ngày sản xuất không được lớn hơn ngày hiện tại."]);
    //             }
    //         }
    //         foreach ($batchCodes as $index => $batchId) {
    //             $batch = Batch::updateOrCreate(
    //                 ['id' => $batchId],
    //                 [
    //                     'date_sx' => $dates[$index],
    //                     'batch_weight' => $request->batch_weight,
    //                     'banh_weight' => $request->banh_weight,
    //                 ]
    //             );

    //             // Process ingredients for this batch
    //             if (isset($batchesData[$index]['ingredients'])) {
    //                 foreach ($batchesData[$index]['ingredients'] as $ingredientIndex => $ingredientData) {
    //                     // Get the type of pus ID from the provided type of pus name
    //                     $typeOfPusId = $this->getTypeOfPusId($ingredientData['type_of_pus']);

    //                     // Get the vehicle ID by searching the vehicle number
    //                     $vehicleId = $this->getVehicleId($ingredientData['vehicle']);

    //                     // Get trip value from the input
    //                     $trip = $ingredientData['trip'];

    //                     // Create or find the ingredient
    //                     $ingredient = Ingredient::firstOrCreate([
    //                         'type_of_pus_id' => $typeOfPusId,
    //                         'received_date' => Carbon::createFromFormat('d/m/Y', $ingredientData['received_date'])->format('Y-m-d'),
    //                         'vehicle_number_id' => $vehicleId,
    //                         'trip' => $trip,
    //                     ]);

    //                     // Attach ingredient to batch (without detaching previous associations)
    //                     $batch->ingredients()->syncWithoutDetaching([$ingredient->id]);
    //                 }
    //             } else {
    //                 Log::warning("Không tìm thấy nguyên liệu cho batch index: $index");
    //             }
    //         }

    //         // Return success message after processing
    //         return redirect()->route('batches.index')->with('message', 'Kết nối nguyên liệu thành công!');
    //     } catch (\Throwable $th) {
    //         // Catch any exception and return failure message
    //         return redirect()->back()->with('error', 'Kết nối nguyên liệu thất bại!')->with('errorDetails', $th->getMessage());
    //     }
    // }
    public function save(Request $request)
    {
        try {
            // $today = Carbon::now('Asia/Ho_Chi_Minh')->startOfDay();

            $today = Carbon::today()->startOfDay();

            $batchCodes = $request->input('batch_code');
            // $dates = $request->input('date_sx');
            $dates = array_map(function ($date) {
                return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            }, $request->input('date_sx'));
            $batchesData = $request->input('batches');
            // dd($batchCodes);
            foreach ($dates as $index => $date) {
                // $parsedDate = Carbon::parse($date, 'Asia/Ho_Chi_Minh')->startOfDay();
                // if ($parsedDate->greaterThan($today)) {
                //     return back()->with(['error' => "Ngày sản xuất không được lớn hơn ngày hiện tại."])->withInput();
                // }
                if (Carbon::parse($date)->startOfDay()->greaterThan($today)) {
                    return back()->with(['error' => "Ngày sản xuất không được lớn hơn ngày hiện tại."])->withInput();
                }
            }
            foreach ($batchCodes as $index => $batchId) {
                $batch = Batch::updateOrCreate(
                    ['id' => $batchId],
                    [
                        'date_sx' => $dates[$index],
                        'batch_weight' => $request->batch_weight,
                        'banh_weight' => $request->banh_weight,
                    ]
                );
                // dd($batchesData[0]['ingredients']);
                if (isset($batchesData[0]['ingredients'])) {
                    foreach ($batchesData[0]['ingredients'] as $ingredientIndex => $ingredientData) {
                        $typeOfPusId = $this->getTypeOfPusId($ingredientData['type_of_pus']);
                        // $farmId = $this->getFarmId($ingredientData['farm']);
                        // dd($ingredientData);
                        $vehicleId = $this->getVehicleId($ingredientData['vehicle']);
                        // $factoryId = $this->getFactoryId($ingredientData['received_factory']);

                        $ingredient = Ingredient::where([
                            ['type_of_pus_id', $typeOfPusId],
                            ['received_date', Carbon::createFromFormat('d/m/Y', $ingredientData['received_date'])->format('Y-m-d')],
                            ['farm_id', $ingredientData['farm_ids']],
                            ['vehicle_number_id', $vehicleId],
                            ['trip', $ingredientData['trip']],
                        ])->first();
                        if (!$ingredient) {
                            return redirect()->back()->with('error', 'Không tìm thấy nguyên liệu!')->withInput();
                        }
                        $batch->ingredients()->syncWithoutDetaching([$ingredient->id]);
                        $farmName = optional($ingredient->farm)->farm_name ?? 'Chưa có dữ liệu';
                        $typeName = optional($ingredient->typeOfPus)->name_pus ?? 'Chưa có dữ liệu';

                        ActionHistory::create([
                            'user_id' => Auth::id(),
                            'action_type' => 'Tạo',
                            'model_type' => 'Danh Sách Lô Hàng',
                            'details' => "Kết nối nguyên liệu: $typeName | Nông trường: $farmName | Chuyến: {$ingredient->trip} | Ngày nhận: {$ingredient->received_date} → vào Lô ID: {$batch->id} | Ngày SX: {$batch->date_sx}",
                        ]);
                    }
                } else {
                    Log::warning("Không tìm thấy nguyên liệu cho batch index: $index");
                }
            }

            return redirect()->route('batches.index')->with('message', 'Kết nối nguyên liệu thành công!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Kết nối nguyên liệu thất bại!')->withInput();
        }
    }
    // public function edit($id)
    // {
    //     $batch = Batch::with(['ingredients.typeOfPus', 'ingredients.vehicle', 'ingredients.farm'])
    //         ->findOrFail($id);
    //     // if (
    //     //     ($batch->status ?? 0) == 1 ||
    //     //     !empty($batch->testingResult)
    //     // ) {
    //     //     return redirect()->back()->with('error', 'Lô hàng đã được kiểm nghiệm. Không thể chỉnh sửa!');
    //     // }
    //     $relatedIngredients = BatchIngredients::with(['ingredient.typeOfPus', 'ingredient.vehicle', 'ingredient.farm'])
    //         ->where('batch_id', $batch->id)
    //         // ->whereNotIn('ingredient_id', function ($query) {
    //         //     $query->select('ingredient_id')->from('batch_ingredient');
    //         // })
    //         ->get();
    //     $ingredients = Ingredient::with('typeOfPus', 'vehicle', 'farm.unitRelation')
    //         ->whereNotIn('id', function ($query) {
    //             $query->select('ingredient_id')->from('batch_ingredient');
    //         })
    //         ->get();
    //     $vehicles = Vehicle::all();
    //     $farms = Farm::all();
    //     $typeOfPus = TypeOfPus::all();

    //     return view('batch.batches_detail.edit', compact(
    //         'batch',
    //         'relatedIngredients',
    //         'ingredients',
    //         'vehicles',
    //         'farms',
    //         'typeOfPus'
    //     ));
    // }
    public function edit($id)
    {
        $batch = Batch::with(['ingredients.typeOfPus', 'ingredients.vehicle', 'ingredients.farm'])
            ->findOrFail($id);

        $relatedIngredients = BatchIngredients::with(['ingredient.typeOfPus', 'ingredient.vehicle', 'ingredient.farm'])
            ->where('batch_id', $batch->id)
            ->get();
        $ingredients = Ingredient::with('typeOfPus', 'vehicle', 'farm.unitRelation')
            // ->whereNotIn('id', function ($query) {
            //     $query->select('ingredient_id')->from('batch_ingredient');
            // })
            ->get();

        $vehicles = Vehicle::all();
        $farms = Farm::all();
        $typeOfPus = TypeOfPus::all();

        $batches = Batch::all();
        $batchIdsWithIngredients = $relatedIngredients->pluck('ingredient_id')->toArray();

        return view('batch.batches_detail.edit', compact(
            'batch',
            'relatedIngredients',
            'ingredients',
            'vehicles',
            'farms',
            'typeOfPus',
            'batches',
            'batchIdsWithIngredients'
        ));
    }

    public function update(Request $request, $id)
    {
        try {
            $today = Carbon::today();
            $dates = is_array($request->date_sx) ? $request->date_sx : [$request->date_sx];

            // foreach ($dates as $date) {
            //     if (Carbon::parse($date)->greaterThan($today)) {
            //         return back()->with(['error' => "Ngày sản xuất không được lớn hơn ngày hiện tại."]);
            //     }
            // }
            foreach ($dates as $date) {
                $parsedDate = Carbon::createFromFormat('d/m/Y', $date)->startOfDay();
                // dd($today);
                if ($parsedDate->greaterThan($today)) {
                    return back()->with(['error' => "Ngày sản xuất không được lớn hơn ngày hiện tại."]);
                }
            }

            $batch = Batch::findOrFail($request->batch_id);
            $oldBatch = $batch->replicate();
            if (
                ($batch->status ?? 0) == 1 ||
                !empty($batch->testingResult)
            ) {
                return redirect()->back()->with('error', 'Lô hàng đã được kiểm nghiệm. Không thể chỉnh sửa!');
            }
            $batch->update([
                'date_sx' => Carbon::createFromFormat('d/m/Y', $request->date_sx)->format('Y-m-d'),
                'batch_weight' => $request->batch_weight,
                'banh_weight' => $request->banh_weight,
            ]);
            if ($request->has('deleted_ingredient_ids') && !empty($request->deleted_ingredient_ids)) {
                BatchIngredients::where('batch_id', $batch->id)
                    ->whereIn('ingredient_id', $request->deleted_ingredient_ids)
                    ->delete();
            }

            $typeOfPusMap = TypeOfPus::pluck('id', 'name_pus');
            // $farmMap = Farm::pluck('id', 'farm_name');
            $vehicleMap = Vehicle::pluck('id', 'vehicle_number');

            $newIngredientIds = [];
            foreach ($request->type_of_pus_ids as $index => $typeOfPusName) {
                $typeOfPusId = $typeOfPusMap[$typeOfPusName] ?? null;
                // $farmId = $farmMap[$request->farm_ids[$index]] ?? null;
                $farmId = $request->farm_ids[$index] ?? null;
                $vehicleId = $vehicleMap[$request->vehicle_ids[$index]] ?? null;
                $receivedDate = Carbon::createFromFormat('d/m/Y', $request->received_dates[$index])->format('Y-m-d');
                $trip = $request->batches[$index];

                $ingredient = Ingredient::where([
                    'type_of_pus_id' => $typeOfPusId,
                    'received_date' => $receivedDate,
                    'farm_id' => $farmId,
                    'vehicle_number_id' => $vehicleId,
                    'trip' => $trip,
                ])->first();

                if (!$ingredient) {
                    // $ingredient = Ingredient::create([
                    //     'type_of_pus_id' => $typeOfPusId,
                    //     'received_date' => $receivedDate,
                    //     'farm_id' => $farmId,
                    //     'vehicle_number_id' => $vehicleId,
                    //     'trip' => $trip,
                    // ]);

                    return back()->with(['error' => "Không tìm thấy nguyên liệu với thông tin đã cung cấp!"]);
                }

                $newIngredientIds[] = $ingredient->id;
            }
            $oldIngredients = $batch->ingredients()->with(['farm', 'typeOfPus'])->get();
            $oldIngredientList = $oldIngredients->map(function ($item) {
                return "ID: {$item->id}, Farm: " . ($item->farm->farm_name ?? 'Chưa có dữ liệu') . ", Loại mủ: " . ($item->typeOfPus->name_pus ?? 'Chưa có dữ liệu');
            })->implode(' | ');

            $newIngredients = Ingredient::whereIn('id', $newIngredientIds)->with(['farm', 'typeOfPus'])->get();
            $newIngredientList = $newIngredients->map(function ($item) {
                return "ID: {$item->id}, Farm: " . ($item->farm->farm_name ?? 'Chưa có dữ liệu') . ", Loại mủ: " . ($item->typeOfPus->name_pus ?? 'Chưa có dữ liệu');
            })->implode(' | ');
            $oldDate = Carbon::parse($oldBatch->date_sx)->format('d/m/Y');
            $newDate = Carbon::parse($batch->date_sx)->format('d/m/Y');
            $batch->ingredients()->sync($newIngredientIds);

            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'Cập Nhật',
                'model_type' => 'Danh Sách Lô Hàng',
                'details' => "Cập nhật lô ID: {$batch->id}\n" .
                    "- Ngày SX: {$oldDate} ➝ {$newDate}\n" .
                    "- Trọng lượng lô: {$oldBatch->batch_weight}tấn ➝ {$request->batch_weight}tấn\n" .
                    "- Trọng lượng bánh: {$oldBatch->banh_weight}kg ➝ {$request->banh_weight}kg\n" .
                    "- Nguyên liệu cũ: {$oldIngredientList}\n" .
                    "- Nguyên liệu mới: {$newIngredientList}",
            ]);

            return redirect()->route('batches.index')->with('message', 'Cập nhật mã lô và nguyên liệu thành công!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Cập nhật thất bại. Kiểm tra lại thông tin! ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        $batches = Batch::find($id);
        $batches->ingredients()->detach();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $batch = Batch::whereIn('id', $request->ids)
            ->get();

        $batchWithCheckedStatus = $batch->filter(function ($data) {
            return $data->status == 1; // Kiểm tra trạng thái của lô hàng
        });

        if ($batchWithCheckedStatus->isNotEmpty()) {
            $batchCodes = $batchWithCheckedStatus->pluck('batch_code')->unique()->implode(', ');

            return response()->json([
                'message' => 'Mã lô ' . $batchCodes . ' đã có kết quả kiểm nghiệm, vui lòng không xóa kết nối Thông tin nguyên liệu',
                'failed_ids' => $batchWithCheckedStatus->pluck('id')
            ], 400);
        }

        $batches = Batch::whereIn('id', $request->ids)->get();

        foreach ($batches as $batch) {
            $ingredientIds = $batch->ingredients()->pluck('ingredients.id')->toArray();

            $batch->ingredients()->detach();

            // Ghi log từng nguyên liệu được detach khỏi batch
            foreach ($ingredientIds as $ingredientId) {
                ActionHistory::create([
                    'user_id' => Auth::id(),
                    'action_type' => 'Xóa',
                    'model_type' => 'Danh Sách Lô Hàng',
                    'details' => "Gỡ nguyên liệu ID: $ingredientId khỏi lô ID: {$batch->id} | Mã lô: {$batch->batch_code}",
                ]);
            }
        }

        return response()->json([
            'message' => 'Xóa thành công thông tin đã chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
}
