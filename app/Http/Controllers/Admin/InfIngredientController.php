<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Log;
use App\Exports\IngredientExport;
use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\Ingredient;
use App\Models\PlantingArea;
use App\Models\TypeOfPus;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\IngredientsImport;
use App\Models\ActionHistory;
use App\Models\Factory;
use App\Models\Units;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Complex\Exception;

class InfIngredientController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userFarms = $user->farms()->with('unitRelation')->get();
        $farmName = $userFarms->isEmpty() ? Farm::all() : $userFarms;
        $query = Ingredient::with([
            'typeOfPus:id,name_pus',
            'vehicle:id,vehicle_number,unit_id',
            'factory:id,factory_name',
            'farm:id,farm_name,unit_id',
            'farm.unitRelation:id,unit_name',
        ]);

        if (!$userFarms->isEmpty()) {
            $query->whereIn('farm_id', $userFarms->pluck('id'));
        }

        if ($request->farm_id) {
            list($farm_name, $unit_name) = explode('|', $request->farm_id);

            $query->whereHas('farm', function ($q) use ($farm_name) {
                $q->where('farm_name', $farm_name);
            });

            if ($unit_name) {
                $query->whereHas('farm.unitRelation', function ($q) use ($unit_name) {
                    $q->where('unit_name', $unit_name);
                });
            }
        }

        // Kiểm tra và lọc theo số xed
        if ($request->vehicle_number) {
            if ($request->vehicle_number) {
                $query->whereHas('vehicle', function ($q) use ($request) {
                    $q->where('vehicle_number', $request->vehicle_number);
                });
            }
        }

        if ($request->day) {
            $query->whereDay('ingredients.received_date', $request->day);
        }

        if ($request->month) {
            $query->whereMonth('ingredients.received_date', $request->month);
        }

        if ($request->year) {
            $query->whereYear('ingredients.received_date', $request->year);
        }
        // $vehicleNumbers = Vehicle::select('vehicle_number')->distinct()->pluck('vehicle_number');
        $vehicleNumbersQuery = Vehicle::select('vehicle_number')->distinct();
        $nguyenlieu = Farm::join('units', 'farm.unit_id', '=', 'units.id');

        if (!$userFarms->isEmpty()) {
            $unitIds = $userFarms->pluck('unit_id');
            $vehicleNumbersQuery->whereIn('unit_id', $unitIds);
            $nguyenlieu->whereIn('farm.id', $userFarms->pluck('id'));
        }

        $vehicleNumbers = $vehicleNumbersQuery->pluck('vehicle_number');



        $ingredients = $nguyenlieu
            ->select('farm.farm_name', 'units.unit_name')
            ->distinct()
            ->get();

        $defaultFarm = null;
        if ($ingredients->count() === 1) {
            $defaultFarm = $ingredients->first()->farm_name . '|' . $ingredients->first()->unit_name;
        }
        $farmIds = $userFarms->pluck('id');
        $farms = Farm::whereIn('id', $farmIds)->get();

        $all_ingredients = $query->orderBy('ingredients.id', 'desc')->get();
        // dd($all_ingredients);
        if ($request->ajax()) {
            return DataTables::of($all_ingredients)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })

                ->addColumn('farm_id', function ($row) {
                    return $row->farm->farm_name ?? '';
                })
                ->addColumn('unit', function ($row) {
                    return $row->farm->unitRelation->unit_name ?? '';
                })

                ->addColumn('type_of_pus_id', function ($row) {
                    return $row->typeOfPus->name_pus ?? '';
                })

                ->addColumn('vehicle_number_id', function ($row) {
                    return $row->vehicle->vehicle_number ?? '';
                })

                ->addColumn('trip', function ($row) {
                    return $row->trip ?? '';
                })
                ->addColumn('received_date', function ($row) {
                    // return $row->received_date;
                    return Carbon::parse($row->received_date)->format('d/m/Y');
                })
                ->addColumn('received_factory_id', function ($row) {
                    return $row->factory->factory_name ?? 'Chưa Có';
                })

                ->addColumn('harvesting_date', function ($row) {
                    // return $row->harvesting_date;
                    return Carbon::parse($row->harvesting_date)->format('d/m/Y');
                })
                ->addColumn('end_harvest_date', function ($row) {
                    // return $row->end_harvest_date;
                    return Carbon::parse($row->end_harvest_date)->format('d/m/Y');
                })

                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-ingredients/' . $row->id . '" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                           <!-- <a class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal' . $row->id . '">
                                <i class="fas fa-trash-alt"></i>
                            </a> -->
                        </div>
                        <div class="modal fade" id="deleteModal' . $row->id . '" tabindex="-1" aria-labelledby="deleteModalLabel' . $row->id . '" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel' . $row->id . '">Xác Nhận Xóa</h5>
                                        <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Bạn có chắc chắn muốn xóa thông tin nguyên liệu này không ?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/delete-ingredients/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'unit', 'type_of_pus_id', 'vehicle_number_id', 'farm_id', 'trip', 'received_date', 'received_factory_id', 'harvesting_date', 'end_harvest_date', 'action'])
                ->make(true);
        }
        // dd($farmName);
        return view('infingredients.all_ingredients', compact('farmName', 'vehicleNumbers', 'ingredients', 'defaultFarm', 'farms'));
    }
    public function add(Request $request)
    {
        $user = Auth::user();
        $userFarms = $user->farms()->with('unitRelation')->get();
        $units = Units::where('status', 'Hoạt động')->get();

        if ($userFarms->isEmpty()) {
            $farm = Farm::where('status', 'Hoạt động')->get();
            $vehicles = Vehicle::where('status', 'Hoạt động')->get();
            $units = Units::where('status', 'Hoạt động')->get();
            $singleUnit = null;
            $singleFarm = null;
        } else {
            $userFarms->load('unitRelation');
            $units = $userFarms->flatMap(function ($farm) {
                return $farm->unitRelation ? [$farm->unitRelation] : [];
            })->unique('id')->values();

            $singleUnit = $units->count() == 1 ? $units->first() : null;

            $farm = $userFarms;
            $vehicles = Vehicle::whereIn('unit_id', $userFarms->pluck('unit_id'))->where('status', 'Hoạt động')->get();

            $singleFarm = $userFarms->count() == 1 ? $userFarms->first() : null;
        }

        $typeofpus = TypeOfPus::where('status', '=', 'Hoạt động')->get();
        $plantingAreas = PlantingArea::all();
        $factory = Factory::where('status', '=', 'Hoạt động')->get();
        $singleFactory = $factory->count() == 1 ? $factory->first() : null;

        return view('infingredients.add', compact('singleFactory', 'singleUnit', 'singleFarm', 'units', 'factory', 'vehicles', 'farm', 'typeofpus', 'plantingAreas'));
    }
    public function getPlantingAreasByFarm1(Request $request)
    {
        $farmId = $request->farm_id;
        $ingredientId = $request->ingredientId;

        // Log::info('ingredientId: ' . $ingredientId);
        if (empty($farmId)) {
            return response()->json(['error' => 'Farm ID không hợp lệ'], 400);
        }

        $farm = Farm::find($farmId);
        if (!$farm) {
            return response()->json(['error' => 'Nông trường không tồn tại'], 404);
        }

        $dataRequest = $request->data_request;
        // Log::info('dataRequest:', $request->data_request);
        $query = PlantingArea::where('farm_id', $farmId);
        $receivedDate = $dataRequest[0]['receivedDate'] ?? null;
        $vehicle = $dataRequest[0]['vehicle'] ?? null;
        $trip = $dataRequest[0]['trip'] ?? null;
        if (!$ingredientId) {
            $plantingAreas = $query->whereNotIn('id', function ($subQuery) use ($receivedDate, $vehicle, $trip) {
                $subQuery->select('planting_area_id')
                    ->from('ingredient_plantingarea')
                    ->join('ingredients', 'ingredient_plantingarea.ingredient_id', '=', 'ingredients.id');

                // Chỉ thêm điều kiện nếu các giá trị tồn tại
                if ($receivedDate) {
                    $subQuery->where('received_date', Carbon::createFromFormat('d/m/Y', $receivedDate)->format('Y-m-d'));
                }
                if ($vehicle) {
                    $subQuery->where('vehicle_number_id', $vehicle);
                }
                if ($trip) {
                    $subQuery->where('trip', $trip);
                }
            })->get();
        } else {
            $ingredient = Ingredient::with('plantingAreas', 'vehicle', 'farm.unitRelation')->find($ingredientId);
            $currentPlantingAreaIds = $ingredient->plantingAreas->pluck('id')->toArray();
            // Log::info('Danh sách khu vực trồng của ingredient:', $currentPlantingAreaIds);
            $plantingAreas = $query->whereNotIn('id', function ($subQuery) use ($receivedDate, $vehicle, $trip, $currentPlantingAreaIds) {
                $subQuery->select('planting_area_id')
                    ->from('ingredient_plantingarea')
                    ->join('ingredients', 'ingredient_plantingarea.ingredient_id', '=', 'ingredients.id');

                if ($receivedDate) {
                    $subQuery->where('received_date', Carbon::createFromFormat('d/m/Y', $receivedDate)->format('Y-m-d'));
                }
                if ($vehicle) {
                    $subQuery->where('vehicle_number_id', $vehicle);
                }
                if ($trip) {
                    $subQuery->where('trip', $trip);
                }

                // Loại bỏ những khu vực trồng đã thuộc về chính ingredient này
                if (!empty($currentPlantingAreaIds)) {
                    $subQuery->whereNotIn('planting_area_id', $currentPlantingAreaIds);
                }
            })->orWhereIn('id', $currentPlantingAreaIds) // Gộp lại những khu vực cũ để hiện ra
                ->get();
        }



        return response()->json($plantingAreas);
    }
    public function getFarmsByUnit(Request $request)
    {
        $user = Auth::user();
        if ($user->farms->isNotEmpty()) {
            $farms = $user->farms;
        } else {
            $unitId = $request->unit;
            $farms = Farm::where('unit_id', $unitId)->get();
        }
        return response()->json([
            'farms' => $farms
        ]);
    }
    public function getPlantingAreasByFarm(Request $request)
    {
        $farmId = $request->farm_id;
        $ingredientId = $request->ingredientId;

        if (empty($farmId)) {
            return response()->json(['error' => 'Farm ID không hợp lệ'], 400);
        }

        $farm = Farm::find($farmId);
        if (!$farm) {
            return response()->json(['error' => 'Nông trường không tồn tại'], 404);
        }

        $dataRequest = $request->data_request;
        $receivedDate = $dataRequest[0]['receivedDate'] ?? null;
        $vehicle = $dataRequest[0]['vehicle'] ?? null;
        $trip = $dataRequest[0]['trip'] ?? null;

        $query = PlantingArea::where('farm_id', $farmId);

        // Nếu không truyền ingredientId (tức là đang thêm mới)
        if (!$ingredientId) {
            if ($receivedDate && $vehicle && $trip) {
                // Lọc ra những khu vực chưa gắn vào nguyên liệu nào với thông tin tương ứng
                $plantingAreas = $query->whereNotIn('id', function ($subQuery) use ($receivedDate, $vehicle, $trip) {
                    $subQuery->select('planting_area_id')
                        ->from('ingredient_plantingarea')
                        ->join('ingredients', 'ingredient_plantingarea.ingredient_id', '=', 'ingredients.id')
                        ->whereDate('received_date', Carbon::createFromFormat('d/m/Y', $receivedDate)->format('Y-m-d'))
                        ->where('vehicle_number_id', $vehicle)
                        ->where('trip', $trip);
                })->get();
            } else {
                // Nếu thiếu dữ liệu, không lọc gì thêm
                $plantingAreas = $query->get();
            }
        } else {
            // Nếu đang chỉnh sửa, cần giữ lại các vùng trồng đã được gắn vào nguyên liệu hiện tại
            $ingredient = Ingredient::with('plantingAreas')->find($ingredientId);

            $currentPlantingAreaIds = $ingredient?->plantingAreas->pluck('id')->toArray() ?? [];

            if ($receivedDate && $vehicle && $trip) {
                $plantingAreas = $query->where(function ($q) use ($receivedDate, $vehicle, $trip, $currentPlantingAreaIds) {
                    $q->whereNotIn('id', function ($subQuery) use ($receivedDate, $vehicle, $trip, $currentPlantingAreaIds) {
                        $subQuery->select('planting_area_id')
                            ->from('ingredient_plantingarea')
                            ->join('ingredients', 'ingredient_plantingarea.ingredient_id', '=', 'ingredients.id')
                            ->whereDate('received_date', Carbon::createFromFormat('d/m/Y', $receivedDate)->format('Y-m-d'))
                            ->where('vehicle_number_id', $vehicle)
                            ->where('trip', $trip);

                        // Loại trừ chính nguyên liệu đang sửa khỏi điều kiện lọc
                        if (!empty($currentPlantingAreaIds)) {
                            $subQuery->whereNotIn('planting_area_id', $currentPlantingAreaIds);
                        }
                    });

                    // Ngoài ra giữ lại các vùng trồng đã gắn với nguyên liệu hiện tại
                    if (!empty($currentPlantingAreaIds)) {
                        $q->orWhereIn('id', $currentPlantingAreaIds);
                    }
                })->get();
            } else {
                // Nếu thiếu dữ liệu, chỉ hiển thị tất cả + các vùng đang gắn
                $plantingAreas = $query->orWhereIn('id', $currentPlantingAreaIds)->get();
            }
        }

        return response()->json($plantingAreas);
    }


    public function getVehicles(Request $request)
    {
        $unit_id = $request->input('unit_id');

        $vehicles = Vehicle::where('unit_id', $unit_id)
            ->where('status', 'Hoạt động')
            ->get();
        return response()->json([
            'vehicles' => $vehicles
        ]);
    }
    public function getChiTieu(Request $request)
    {
        $user = Auth::user();
        $selectedIds = $request->input('ids', []);
        $unitId = $request->input('unit_id', null);
        $excludedDates = collect($request->input('excluded_dates', []))
            ->filter()
            ->map(function ($date) {
                return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            })
            ->toArray();

        $query = PlantingArea::query();

        $userFarms = $user->farms()->pluck('farm_id');

        if ($unitId) {
            $query->whereHas('farm', function ($query) use ($unitId, $userFarms) {
                $query->where('unit_id', $unitId)
                    ->whereIn('id', $userFarms);
            });
        } elseif ($userFarms->isNotEmpty()) {
            $query->whereIn('farm_id', $userFarms);
        }

        if (!empty($selectedIds)) {
            $query->whereIn('id', $selectedIds);
        }

        if (count($excludedDates) === 3) {
            $query->whereNotIn('id', function ($subQuery) use ($excludedDates) {
                $subQuery->select('planting_area_id')
                    ->from('ingredient_plantingarea')
                    ->join('ingredients', 'ingredient_plantingarea.ingredient_id', '=', 'ingredients.id')
                    ->where('received_date', $excludedDates[0])
                    ->where('harvesting_date', $excludedDates[1])
                    ->where('end_harvest_date', $excludedDates[2]);
            });
        }
        $plantingAreas = $query->get(['id', 'ma_lo', 'chi_tieu']);

        return response()->json($plantingAreas);
    }
    public function selectChiTieu(Request $request)
    {

        $selectedIds = $request->id;


        $plantingAreas = PlantingArea::whereIn('id', $selectedIds)->get();


        return response()->json($plantingAreas);
    }


    public function save(Request $request)
    {
        $validatedData = $request->validate([
            'farm_id' => 'nullable|integer',
            'planting_area_id' => 'nullable|array',
            'planting_area_id.*' => 'exists:plantingarea,id',
            'type_of_pus_id' => 'nullable|integer',
            'vehicle_number_id' => 'nullable|integer',
            'trip' => 'nullable|string|max:255',
            'received_date' => 'nullable|date_format:d/m/Y',
            'receiving_factory_id' => 'nullable',
            'harvesting_date' => 'nullable|date_format:d/m/Y',
            'end_harvest_date' => 'nullable|date_format:d/m/Y',
        ]);

        try {
            $receivedDate = !empty($request->received_date)
                ? Carbon::createFromFormat('d/m/Y', $request->received_date)->format('Y-m-d')
                : null;
            $harvestingDate = !empty($request->harvesting_date)
                ? Carbon::createFromFormat('d/m/Y', $request->harvesting_date)->format('Y-m-d')
                : null;
            $endHarvestDate = !empty($request->end_harvest_date)
                ? Carbon::createFromFormat('d/m/Y', $request->end_harvest_date)->format('Y-m-d')
                : null;
            $harvestingDateObj = $harvestingDate ? Carbon::parse($harvestingDate) : null;
            $endHarvestDateObj = $endHarvestDate ? Carbon::parse($endHarvestDate) : null;
            $receivedDateObj = $receivedDate ? Carbon::parse($receivedDate) : null;
            $today = Carbon::today()->startOfDay();
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => 'Lỗi định dạng ngày. Vui lòng kiểm tra lại!'])->withInput();
        }

        // dd($today);
        if ($harvestingDateObj && $harvestingDateObj->toDateString() > $today->toDateString()) {
            return redirect()->back()->with(['error' => 'Ngày bắt đầu cạo không được lớn hơn ngày hiện tại.'])->withInput();
        }
        if ($endHarvestDateObj && $endHarvestDateObj->toDateString() > $today->toDateString()) {
            return redirect()->back()->with(['error' => 'Ngày kết thúc cạo không được lớn hơn ngày hiện tại.'])->withInput();
        }
        if ($harvestingDateObj && $endHarvestDateObj && $harvestingDateObj->toDateString() > $endHarvestDateObj->toDateString()) {
            return redirect()->back()->with(['error' => 'Ngày bắt đầu cạo không được lớn hơn ngày kết thúc cạo.'])->withInput();
        }
        if ($receivedDateObj && $receivedDateObj->toDateString() > $today->toDateString()) {
            return redirect()->back()->with(['error' => 'Ngày tiếp nhận không được lớn hơn ngày hiện tại.'])->withInput();
        }
        if ($receivedDateObj && $harvestingDateObj && $receivedDateObj->toDateString() < $harvestingDateObj->toDateString()) {
            return redirect()->back()->with(['error' => 'Ngày tiếp nhận không được nhỏ hơn ngày bắt đầu cạo.'])->withInput();
        }
        if ($receivedDateObj && $endHarvestDateObj && $receivedDateObj->toDateString() < $endHarvestDateObj->toDateString()) {
            return redirect()->back()->with(['error' => 'Ngày tiếp nhận không được nhỏ hơn ngày kết thúc cạo.'])->withInput();
        }
        $existingIngredient = Ingredient::where('farm_id', $request->farm_id)
            ->where('type_of_pus_id', $request->type_of_pus_id)
            ->where('received_date', $receivedDate)
            ->where('trip', $request->trip)
            ->where('vehicle_number_id', $request->vehicle_number_id)
            ->first();

        if ($existingIngredient) {
            return redirect()->back()->with(['error' => 'Ngày Tiếp Nhận và Số Chuyến đã tồn tại cho cùng loại mủ và nông trường, nhưng khác số xe.'])->withInput();
        }
        try {
            $ingredient = Ingredient::create([
                'farm_id' => $request->farm_id,
                'planting_area_id' => $request->planting_area_id,
                'type_of_pus_id' => $request->type_of_pus_id,
                'vehicle_number_id' => $request->vehicle_number_id,
                'trip' => $request->trip,
                'received_date' => $receivedDate,
                'received_factory_id' => $request->received_factory_id,
                'harvesting_date' => $harvestingDate,
                'end_harvest_date' => $endHarvestDate,
            ]);

            if (!empty($request->planting_area_id)) {
                $ingredient->plantingAreas()->sync($validatedData['planting_area_id']);
            }
            $farmName = optional(Farm::find($request->farm_id))->farm_name ?? 'Trống';
            $name_pus = optional(TypeOfPus::find($request->type_of_pus_id))->name_pus ?? 'Trống';
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'Tạo',
                'model_type' => 'Thông Tin Nguyên Liệu',
                'details' => "Tạo nguyên liệu: $farmName | Loại mủ: $name_pus | Chuyến: {$request->trip} | Ngày nhận: $receivedDate"
            ]);
            return redirect()->route('ingredients.index')->with('message', 'Tạo thông tin nguyên liệu thành công');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Có lỗi xảy ra khi tạo khu trồng: ' . $e->getMessage()])
                ->withInput();
            // return redirect()->back()->with('error', 'Lỗi khi lưu thông tin nguyên liệu. Vui lòng thử lại.')->withInput();
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $ingredientId = $id;
        $ingredient = Ingredient::with('plantingAreas', 'vehicle', 'farm.unitRelation')->find($ingredientId);

        if (!$ingredient) {
            return redirect()->back()->with('error', 'Không tìm thấy nguyên liệu!');
        }
        // $ingredientHasCheckedBatch = $ingredient->batches()->where('status', 1)->exists();
        // if ($ingredientHasCheckedBatch) {
        //     // return redirect()->back()
        //     //     ->with('error', 'Nguyên liệu này đã có kết quả kiểm nghiệm không được chỉnh sửa.');
        //     return redirect()->back()
        //         ->with('error', 'Nguyên liệu đã được kết nối với Lô hàng, bạn không được cập nhật lại.');
        // }
        $units = Units::all();

        if ($user->farms->isEmpty()) {
            $farm = Farm::where('status', 'Hoạt động')->get();
            $vehicles = Vehicle::where('status', 'Hoạt động')->get();
            $plantingAreas = PlantingArea::where(function ($query) use ($ingredient) {
                $query->whereDoesntHave('ingredients')
                    ->orWhereHas('ingredients', function ($q) use ($ingredient) {
                        $q->where('ingredients.id', $ingredient->id);
                    });
            })->get();
            $singleUnit = $ingredient->farm->unitRelation;
            $singleFarm = $ingredient->farm;
        } else {
            $userFarms = $user->farms;
            $farm = Farm::whereIn('id', $userFarms->pluck('id'))->where('status', 'Hoạt động')->get();

            $vehicles = Vehicle::whereIn('unit_id', $userFarms->pluck('unit_id'))
                ->where('status', 'Hoạt động')
                ->get();

            $plantingAreas = PlantingArea::whereIn('farm_id', $userFarms->pluck('id'))
                ->where(function ($query) use ($ingredient) {
                    $query->whereDoesntHave('ingredients')
                        ->orWhereHas('ingredients', function ($q) use ($ingredient) {
                            $q->where('ingredients.id', $ingredient->id);
                        });
                })->get();

            $unitIds = $userFarms->pluck('unit_id')->unique();

            $units = Units::whereIn('id', $unitIds)->where('status', 'Hoạt động')->get();

            $singleUnit = $units->count() == 1 ? $units->first() : null;

            $singleFarm = $userFarms->count() == 1 ? $userFarms->first() : null;
        }
        $typeofpus = TypeOfPus::where('status', '=', 'Hoạt động')->get();
        $selectedPlantingAreas = $ingredient->plantingAreas()->get(['plantingarea.id', 'ma_lo', 'chi_tieu']);
        $factory = Factory::where('status', '=', 'Hoạt động')->get();
        $unit = $ingredient->unit;

        return view('infingredients.edit', compact(
            'factory',
            'vehicles',
            'selectedPlantingAreas',
            'ingredient',
            'farm',
            'typeofpus',
            'plantingAreas',
            'units',
            'unit',
            'singleUnit',
            'singleFarm'
        ));
    }

    public function update(Request $request, $id)
    {
        $ingredient = Ingredient::find($id);
        if (!$ingredient) {
            return redirect()->back()->with('error', 'Thông tin không tồn tại')->withInput();
        }
        $ingredientHasCheckedBatch = $ingredient->batches()->where('status', 1)->exists();
        if ($ingredientHasCheckedBatch) {
            // return redirect()->back()
            //     ->with('error', 'Nguyên liệu này đã có kết quả kiểm nghiệm không được chỉnh sửa.');
            return redirect()->back()
                ->with('error', 'Nguyên liệu đã được kết nối với Lô hàng, bạn không được cập nhật lại.');
        }
        $validatedData = $request->validate([
            'farm_id' => 'nullable|integer',
            'type_of_pus_id' => 'nullable|integer',
            'vehicle_number_id' => 'nullable|integer',
            'trip' => 'nullable|string|max:255',
            'received_date' => 'nullable|date_format:d/m/Y',
            'received_factory_id' => 'nullable',
            'harvesting_date' => 'nullable|date_format:d/m/Y',
            'end_harvest_date' => 'nullable|date_format:d/m/Y',
            'planting_area_id' => 'nullable|array',
            'planting_area_id.*' => 'exists:plantingarea,id',
        ]);
        try {
            $receivedDate = !empty($request->received_date)
                ? Carbon::createFromFormat('d/m/Y', $request->received_date)->format('Y-m-d')
                : null;

            $harvestingDate = !empty($request->harvesting_date)
                ? Carbon::createFromFormat('d/m/Y', $request->harvesting_date)->format('Y-m-d')
                : null;

            $endHarvestDate = !empty($request->end_harvest_date)
                ? Carbon::createFromFormat('d/m/Y', $request->end_harvest_date)->format('Y-m-d')
                : null;
            $harvestingDateObj = $harvestingDate ? Carbon::parse($harvestingDate) : null;
            $endHarvestDateObj = $endHarvestDate ? Carbon::parse($endHarvestDate) : null;
            $receivedDateObj = $receivedDate ? Carbon::parse($receivedDate) : null;
            $today = Carbon::today()->startOfDay();
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => 'Lỗi định dạng ngày. Vui lòng kiểm tra lại!'])->withInput();
        }
        $existingIngredient = Ingredient::where('farm_id', $request->farm_id)
            ->where('type_of_pus_id', $request->type_of_pus_id)
            ->where('received_date', $receivedDate)
            ->where('trip', $request->trip)
            ->where('vehicle_number_id', $request->vehicle_number_id)
            ->where('id', '!=', $id)
            ->first();

        if ($existingIngredient) {
            return redirect()->back()->with(['error' => 'Ngày Tiếp Nhận và Số Chuyến đã tồn tại cho cùng loại mủ và nông trường, nhưng khác số xe.'])->withInput();
        }
        if ($harvestingDateObj && $harvestingDateObj->toDateString() > $today->toDateString()) {
            return redirect()->back()->with(['error' => 'Ngày bắt đầu cạo không được lớn hơn ngày hiện tại.'])->withInput();
        }
        if ($endHarvestDateObj && $endHarvestDateObj->toDateString() > $today->toDateString()) {
            return redirect()->back()->with(['error' => 'Ngày kết thúc cạo không được lớn hơn ngày hiện tại.'])->withInput();
        }
        if ($harvestingDateObj && $endHarvestDateObj && $harvestingDateObj->toDateString() > $endHarvestDateObj->toDateString()) {
            return redirect()->back()->with(['error' => 'Ngày bắt đầu cạo không được lớn hơn ngày kết thúc cạo.'])->withInput();
        }
        if ($receivedDateObj && $receivedDateObj->toDateString() > $today->toDateString()) {
            return redirect()->back()->with(['error' => 'Ngày tiếp nhận không được lớn hơn ngày hiện tại.'])->withInput();
        }
        if ($receivedDateObj && $harvestingDateObj && $receivedDateObj->toDateString() < $harvestingDateObj->toDateString()) {
            return redirect()->back()->with(['error' => 'Ngày tiếp nhận không được nhỏ hơn ngày bắt đầu cạo.'])->withInput();
        }
        if ($receivedDateObj && $endHarvestDateObj && $receivedDateObj->toDateString() < $endHarvestDateObj->toDateString()) {
            return redirect()->back()->with(['error' => 'Ngày tiếp nhận không được nhỏ hơn ngày kết thúc cạo.'])->withInput();
        }
        $oldIngredient = $ingredient->getOriginal();
        $ingredient->update([
            'farm_id' => $request->farm_id,
            'type_of_pus_id' => $request->type_of_pus_id,
            'vehicle_number_id' => $request->vehicle_number_id,
            'trip' => $request->trip,
            'received_date' => $receivedDate,
            'received_factory_id' => $request->received_factory_id,
            'harvesting_date' => $harvestingDate,
            'end_harvest_date' => $endHarvestDate,
        ]);

        $ingredient->plantingAreas()->sync($validatedData['planting_area_id'] ?? []);

        $oldFarmName = optional(Farm::find($oldIngredient['farm_id']))->farm_name ?? 'Trống';
        $oldNamePus = optional(TypeOfPus::find($oldIngredient['type_of_pus_id']))->name_pus ?? 'Trống';

        $farmName = optional(Farm::find($request->farm_id))->farm_name ?? 'Trống';
        $name_pus = optional(TypeOfPus::find($request->type_of_pus_id))->name_pus ?? 'Trống';
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'Cập Nhật',
            'model_type' => 'Thông Tin Nguyên Liệu',
            'details' => "Cập nhật nguyên liệu: Cũ - Nông trường: $oldFarmName | Loại mủ: $oldNamePus | Chuyến: {$oldIngredient['trip']} | Ngày nhận: {$oldIngredient['received_date']} Mới - Nông trường: $farmName | Loại mủ: $name_pus | Chuyến: {$request->trip} | Ngày nhận: $receivedDate"
        ]);
        return redirect()->route('ingredients.index')->with('message', 'Cập nhật thông tin nguyên liệu thành công');
    }
    public function destroy($id)
    {
        $ingredients = Ingredient::find($id);
        $ingredients->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function index_ip()
    {
        return view('infingredients.import_ing');
    }
    // public function import(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:xlsx',
    //     ]);

    //     try {
    //         Excel::import(new IngredientsImport, $request->file('file'));
    //         return redirect()->back()->with('success', 'Dữ liệu đã được nhập thành công!');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Đã xảy ra lỗi khi nhập dữ liệu !');
    //     }
    // }
    public function import(Request $request)
    {
        try {
            $import = new IngredientsImport();
            Excel::import($import, $request->file('file'));
            if (!empty($import::$errors)) {
                $errors = $import::$errors;
                // dd($errors);
                $errorMessages = [];
                $unknownErrors = array_column($errors, 'Error');
                if (!empty($unknownErrors)) {
                    $errorMessages[] = implode(", ", $unknownErrors);
                } else {
                    $emptydvError = $errors['empty_donvi'] ?? [];
                    if (!empty($emptydvError)) {
                        $errorMessages[] = "Công Ty không được để trống tại các dòng: " . implode(', ', $emptydvError);
                    }
                    $invailddvError = $errors['unit_invalid'] ?? [];
                    if (!empty($invailddvError)) {
                        $errorMessages[] = "Công Ty không hợp lệ tại các dòng: " . implode(', ', $invailddvError);
                    }
                    $emptyntError = $errors['empty_nongtruong'] ?? [];
                    if (!empty($emptyntError)) {
                        $errorMessages[] = "Nông Trường không được để trống tại các dòng: " . implode(', ', $emptyntError);
                    }
                    $invaildttError = $errors['exists_invaild'] ?? [];
                    if (!empty($invaildttError)) {
                        $errorMessages[] = "Nông Trường không hợp lệ tại các dòng: " . implode(', ', $invaildttError);
                    }
                    $emptyntnError = $errors['empty_ngaytiepnhan'] ?? [];
                    if (!empty($emptyntnError)) {
                        $errorMessages[] = "Ngày Tiếp Nhận không được để trống tại các dòng: " . implode(', ', $emptyntnError);
                    }
                    $invaildntnError = $errors['invaild_ngaytiepnhan'] ?? [];
                    if (!empty($invaildntnError)) {
                        $errorMessages[] = "Ngày Tiếp Nhận không hợp lệ tại các dòng: " . implode(', ', $invaildntnError);
                    }
                    $emptylmError = $errors['empty_loaimu'] ?? [];
                    if (!empty($emptylmError)) {
                        $errorMessages[] = "Loại Mủ không được để trống tại các dòng: " . implode(', ', $emptylmError);
                    }
                    $invaildlmError = $errors['loaimu_invalid'] ?? [];
                    if (!empty($invaildlmError)) {
                        $errorMessages[] = "Loại Mủ không hợp lệ tại các dòng: " . implode(', ', $invaildlmError);
                    }
                    $emptysxError = $errors['empty_soxe'] ?? [];
                    if (!empty($emptysxError)) {
                        $errorMessages[] = "Số Xe Vận Chuyển không được để trống tại các dòng: " . implode(', ', $emptysxError);
                    }
                    $invaildexistssxError = $errors['existsSX_invaild'] ?? [];
                    if (!empty($invaildexistssxError)) {
                        $errorMessages[] = "Số Xe Vận Chuyển không hợp lệ tại các dòng: " . implode(', ', $invaildexistssxError);
                    }
                    $emptyscError = $errors['empty_sochuyen'] ?? [];
                    if (!empty($emptyscError)) {
                        $errorMessages[] = "Số Chuyến không được để trống tại các dòng: " . implode(', ', $emptyscError);
                    }
                    $invaildnumberscError = $errors['invaild_numbersc'] ?? [];
                    if (!empty($invaildnumberscError)) {
                        $errorMessages[] = "Số Chuyến không hợp lệ tại các dòng: " . implode(', ', $invaildnumberscError);
                    }
                    $emptynmError = $errors['empty_nhamay'] ?? [];
                    if (!empty($emptysxError)) {
                        $errorMessages[] = "Nhà Máy Tiếp Nhận không được để trống tại các dòng: " . implode(', ', $emptynmError);
                    }
                    $invaildnmError = $errors['nhamay_invalid'] ?? [];
                    if (!empty($invaildnmError)) {
                        $errorMessages[] = "Nhà Máy Tiếp Nhận không hợp lệ tại các dòng: " . implode(', ', $invaildnmError);
                    }
                    $emptynbdcError = $errors['empty_ngaybdcao'] ?? [];
                    if (!empty($emptynbdcError)) {
                        $errorMessages[] = "Ngày Bắt Đầu Cạo không được để trống tại các dòng: " . implode(', ', $emptynbdcError);
                    }
                    $emptynktcError = $errors['empty_ngayktcao'] ?? [];
                    if (!empty($emptynktcError)) {
                        $errorMessages[] = "Ngày Kết Thúc Cạo không được để trống tại các dòng: " . implode(', ', $emptynktcError);
                    }
                    $invaildnbdcError = $errors['invaild_ngaybdcao'] ?? [];
                    if (!empty($invaildnbdcError)) {
                        $errorMessages[] = "Ngày Bắt Đầu Cạo không hợp lệ tại các dòng: " . implode(', ', $invaildnbdcError);
                    }
                    $invaildnktcError = $errors['invaild_ngayktcao'] ?? [];
                    if (!empty($invaildnktcError)) {
                        $errorMessages[] = "Ngày Kết Thúc Cạo không hợp lệ tại các dòng: " . implode(', ', $invaildnktcError);
                    }
                    $notvaildntnError = $errors['not_valid_future_date_ntn'] ?? [];
                    if (!empty($notvaildntnError)) {
                        $errorMessages[] = "Ngày Tiếp Nhận không được lớn hơn ngày hiện tại tại các dòng: " . implode(', ', $notvaildntnError);
                    }
                    $notvaildnbdcError = $errors['not_valid_future_date_nbdc'] ?? [];
                    if (!empty($notvaildnbdcError)) {
                        $errorMessages[] = "Ngày Bắt Đầu Cạo không được lớn hơn ngày hiện tại tại các dòng: " . implode(', ', $notvaildnbdcError);
                    }
                    $notvaildnktcError = $errors['not_valid_future_date_nktc'] ?? [];
                    if (!empty($notvaildnktcError)) {
                        $errorMessages[] = "Ngày Kết Thúc Cạo không được lớn hơn ngày hiện tại tại các dòng: " . implode(', ', $notvaildnktcError);
                    }
                    $nbdcgreaternktcError = $errors['ngaybdcao_greater_ngayktcao'] ?? [];
                    if (!empty($nbdcgreaternktcError)) {
                        $errorMessages[] = "Ngày Bắt Đầu Cạo không được lớn hơn Ngày Kết Thúc Cạo tại các dòng: " . implode(', ', $nbdcgreaternktcError);
                    }
                    $nbdcgreaterntnError = $errors['ngaybdcao_greater_ngaytn'] ?? [];
                    if (!empty($nbdcgreaterntnError)) {
                        $errorMessages[] = "Ngày Tiếp Nhận không được nhỏ hơn Ngày Bắt Đầu Cạo tại các dòng: " . implode(', ', $nbdcgreaterntnError);
                    }
                    $nktcgreaterntnError = $errors['ngayktcao_greater_ngaytn'] ?? [];
                    if (!empty($nktcgreaterntnError)) {
                        $errorMessages[] = "Ngày Tiếp Nhận không được nhỏ hơn Ngày Kết Thúc Cạo tại các dòng: " . implode(', ', $nktcgreaterntnError);
                    }
                    $invaildplantingareaError = $errors['invaild_plantingArea'] ?? [];
                    if (!empty($invaildplantingareaError)) {
                        $errorMessages[] = "Khu Vực Trồng không hợp lệ tại các dòng: " . implode(', ', $invaildplantingareaError);
                    }
                    // $invailuserError = $errors['farm_and_unit_not_allowed'] ?? [];
                    // if (!empty($invailuserError)) {
                    //     $errorMessages[] = "Đơn Vị và Nông Trường không hợp lệ tại các dòng: " . implode(', ', $invailuserError);
                    // }

                    // $duplicateplantingareaError = $errors['duplicate_planting_area'] ?? [];
                    // // dd($duplicateplantingareaError);
                    // if (!empty($duplicateplantingareaError)) {
                    //     foreach ($duplicateplantingareaError as $dup) {
                    //         $rows = implode(', ', $dup['rowNumber']);
                    //         $errorMessages[] = "Mã Lô Vùng Trồng " . $dup['ma_lo'] . " đã tồn tại vào ngày " . $dup['date'] . " tại các dòng " . $rows;
                    //     }
                    // }

                    $duplicateSoChuyen = $errors['duplicate_so_chuyen'] ?? [];
                    // dd($duplicateplantingareaError);
                    if (!empty($duplicateSoChuyen)) {
                        foreach ($duplicateSoChuyen as $dup) {
                            $rows = implode(', ', $dup['row']);
                            $errorMessages[] = "Ngày tiếp nhận, Số Xe Vận Chuyển và Số Chuyến không được nhập trùng tại các dòng: " . $rows;
                        }
                    }

                    $existsPlantingArea = $errors['exists_planting_area'] ?? [];
                    // dd($duplicateplantingareaError);
                    if (!empty($existsPlantingArea)) {
                        $errorMessages[] = "Ngày Tiếp Nhận và Số Chuyến đã tồn tại cho cùng loại mủ và nông trường tại các dòng: " . implode(', ', $existsPlantingArea);
                    }
                }
                // dd($import::$errors);

                return redirect()->back()->with('error', 'Tài liệu không hợp lệ:\n' . implode('\n', $errorMessages));
            }
            return back()->with('message', 'Nhập dữ liệu thành công!');
        } catch (Exception $e) {
            return back()->with('error', 'Lỗi khi nhập dữ liệu: ' . $e->getMessage());
        }
    }
    public function downloadSample()
    {
        $filePath = public_path('files/file_example.xlsx');
        return response()->download($filePath);
    }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $ingredientsWithBatches = Ingredient::whereIn('id', $request->ids)
            ->whereHas('batches')
            ->pluck('id');

        if ($ingredientsWithBatches->isNotEmpty()) {
            return response()->json([
                'message' => 'Không thể xóa các nguyên liệu đã được kết nối với lô hàng.',
                'failed_ids' => $ingredientsWithBatches
            ], 400);
        }

        $ingredientsToDelete = Ingredient::whereIn('id', $request->ids)->get();

        foreach ($ingredientsToDelete as $ingredient) {
            $farmName = optional($ingredient->farm)->farm_name ?? 'Trống';
            $typeName = optional($ingredient->typeOfPus)->name_pus ?? 'Trống';
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'Xóa',
                'model_type' => 'Thông Tin Nguyên Liệu',
                'details' => "Xóa nguyên liệu: $farmName | Loại mủ: $typeName | Chuyến: {$ingredient->trip} | Ngày nhận: {$ingredient->received_date}"
            ]);
        }

        Ingredient::whereIn('id', $request->ids)->delete();

        return response()->json([
            'message' => 'Xóa thành công thông tin đã chọn.',
            'deleted_ids' => $request->ids
        ]);
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['farm_id', 'vehicle_number_id', 'year', 'month', 'day']);

        return Excel::download(new IngredientExport($filters), 'danh_sach_nguyen_lieu.xlsx');
    }
}
