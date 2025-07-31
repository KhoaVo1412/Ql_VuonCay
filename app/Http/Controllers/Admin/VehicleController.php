<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Farm;
use App\Models\Units;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userFarms = $user->farms;

        if ($userFarms->isEmpty()) {
            $units = Units::all();
            $query = Vehicle::with('unit');
            $singleUnit = null;
        } else {
            $userFarms->load('unitRelation');
            $units = $userFarms->flatMap(function ($farm) {
                return $farm->unitRelation ? [$farm->unitRelation] : [];
            })->unique('id');
            $singleUnit = $units->count() == 1 ? $units->first() : null;
            $query = Vehicle::with('unit')->whereIn('unit_id', $units->pluck('id'));
        }

        if ($request->has('unit_id') && !empty($request->unit_id)) {
            $query->where('unit_id', $request->unit_id);
        }
        if ($singleUnit) {
            $query->where('unit_id', $singleUnit->id);
        }
        // Nếu có nhiều đơn vị, kiểm tra request để lọc
        elseif ($request->has('unit_id') && !empty($request->unit_id)) {
            $query->where('unit_id', $request->unit_id);
        }
        $all_vehicles = $query->orderBy('id', 'desc')->get();

        if ($request->ajax()) {
            return DataTables::of($all_vehicles)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('unit', function ($row) {
                    return $row->unit ? $row->unit->unit_name : 'Chưa có đơn vị';
                })
                ->editColumn('driver_name', function ($row) {
                    return $row->driver_name;
                })
                ->editColumn('vehicle_type', function ($row) {
                    return $row->vehicle_type;
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->editColumn('vehicle_number', function ($row) {
                    return '<a href="/vehicles/edit/' . $row->id . '">' . $row->vehicle_number . '</a>';
                })
                ->addColumn('action', function ($row) {
                    return '
                    <a href="/vehicles/edit/' . $row->id . '" class="btn btn-sm btn-primary">Sửa</a>
                    <a href="/vehicles/delete/' . $row->id . '" class="btn btn-sm btn-danger">Xóa</a>
                ';
                })
                ->rawColumns(['check', 'stt', 'vehicle_number', 'vehicle_type', 'driver_name', 'unit', 'action', 'status'])
                ->make(true);
        }

        return view('vehicles.all_vehicles', compact('units', 'userFarms', 'singleUnit'));
    }

    public function getFarmsByUnit($unitId)
    {
        $farms = Farm::where('unit_id', $unitId)->get();

        return response()->json($farms);
    }

    public function save(Request $request)
    {
        $request->validate([
            'vehicle_number' => 'required|unique:vehicles,vehicle_number|regex:/^[A-Za-z0-9]+$/',
            'vehicle_name' => 'required|unique:vehicles,vehicle_name',
            'driver_name' => 'required|unique:vehicles,driver_name',
            'unit_id' => 'required',
            'status' => 'nullable',
        ], [
            'vehicle_number.unique' => 'Số Xe này đã tồn tại!',
            'vehicle_name.unique' => 'Tên Xe này đã tồn tại!',
            'driver_name.unique' => 'Tên Tài Xế này đã tồn tại!',
            'vehicle_number.regex' => 'Số xe không được chứa khoảng trắng và ký tự đặc biệt!',
        ]);
        try {
            $vehicle = Vehicle::create([
                'unit_id' => $request->unit_id,
                'driver_name' => $request->driver_name,
                'vehicle_type' => $request->vehicle_type,
                'vehicle_name' => $request->vehicle_name,
                'vehicle_number' => $request->vehicle_number,
                'status' => $request->status ?? 'Hoạt động',
            ]);
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'Tạo',
                'model_type' => 'Tạo Xe',
                'details' => "Đã tạo xe: Số xe '{$vehicle->vehicle_number}', Tên xe '{$vehicle->vehicle_name}', Tài xế '{$vehicle->driver_name}'",
            ]);
            session()->flash('message', 'Tạo Xe thành công.');
            return redirect()->route('vehicles.index');
        } catch (\Throwable $th) {
            return redirect()->back()
                ->withErrors(['error' => 'Có lỗi xảy ra: ' . $th->getMessage()])
                ->withInput();
        }
    }
    public function edit($id)
    {
        $user = Auth::user();
        $userFarms = $user->farms;

        if ($userFarms->isEmpty()) {
            $vehicle = Vehicle::with('unit')->findOrFail($id);
            $units = Units::all();
            $farms = Farm::all();
            $userFarmId = null;
            $userUnitId = null;
        } else {
            $units = $userFarms->flatMap(function ($farm) {
                return $farm->unitRelation ? [$farm->unitRelation] : [];
            })->unique('id');

            if ($units->isEmpty()) {
                Log::warning("Không tìm thấy đơn vị cho các nông trường của người dùng.");
            }

            $vehicle = Vehicle::with('unit')->whereIn('unit_id', $userFarms->pluck('unit_id'))->findOrFail($id);
            $farms = $userFarms;

            $userFarmId = $userFarms->first()->id;
            $userUnitId = $userFarms->first()->unit_id;
        }

        return view('vehicles.edit_vehicles', compact('vehicle', 'farms', 'units', 'userFarmId', 'userUnitId'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'farm_id' => 'nullable|exists:farm,id',
            'vehicle_number' => 'nullable|string|max:255|regex:/^[A-Za-z0-9]+$/',
            'vehicle_name' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:Hoạt động,Không hoạt động',
        ], [
            'vehicle_number.unique' => 'Số Xe này đã tồn tại!',
            'vehicle_name.unique' => 'Tên Xe này đã tồn tại!',
            'driver_name.unique' => 'Tên Tài Xế này đã tồn tại!',
            'vehicle_number.regex' => 'Số xe không được chứa khoảng trắng và ký tự đặc biệt!',
        ]);
        try {
            $existingVeh = Vehicle::where(function ($query) use ($request, $id) {
                $query->where('vehicle_number', $request->vehicle_number)
                    ->orWhere('vehicle_name', $request->vehicle_name);
            })->where('id', '!=', $id)->first();

            // if ($existingVeh) {
            //     if ($existingVeh->vehicle_number === $request->vehicle_number) {
            //         return redirect()->back()->with(['error' => 'Số xe này đã tồn tại!']);
            //     }
            //     if ($existingVeh->vehicle_name === $request->vehicle_name) {
            //         return redirect()->back()->with(['error' => 'Tên xe này đã tồn tại!']);
            //     }
            // }
            $vehicle = Vehicle::find($id);

            if (!$vehicle) {
                return redirect()->route('vehicles.index')->with('error', 'Không tìm thấy xe.');
            }
            $oldValues = $vehicle->getOriginal();

            $vehicle->update([
                'unit_id' => $request->input('unit_id'),
                'driver_name' => $request->input('driver_name'),
                'vehicle_type' => $request->input('vehicle_type'),
                'vehicle_number' => $request->input('vehicle_number'),
                'vehicle_name' => $request->input('vehicle_name'),
                'status' => $request->input('status'),
            ]);
            $newValues = $vehicle->getChanges();
            $details = "Cập nhật thông tin xe: ";
            foreach ($newValues as $key => $newValue) {
                $oldValue = $oldValues[$key] ?? 'N/A';
                $details .= "'{$oldValue}' -> '{$newValue}'; ";
            }

            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'Cập nhật',
                'model_type' => 'Xe',
                'details' => $details,
            ]);
            return redirect()->route('vehicles.index')->with('message', 'Cập nhật thành công.');
        } catch (\Throwable $th) {
            return redirect()->back()
                ->withErrors(['error' => 'Có lỗi xảy ra: ' . $th->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $vehicles = Vehicle::find($id);
        $vehicles->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $vehicles = Vehicle::whereIn('id', $request->ids)->get();

        foreach ($vehicles as $farm) {
            $farm->status = ($farm->status === 'Hoạt động') ? 'Không hoạt động' : 'Hoạt động';
            $farm->save();
        }
        return response()->json(['message' => 'Thành Công']);
    }
    // public function deleteMultiple(Request $request)
    // {
    //     $request->validate([
    //         'ids' => 'required|array',
    //         'ids.*' => 'integer',
    //     ]);

    //     $vehicles = Vehicle::whereIn('id', $request->ids)->get();
    //     $messages = [];

    //     foreach ($vehicles as $vehicle) {
    //         if ($vehicle->ingredients()->count() > 0) {
    //             $vehicle->status = 'Không hoạt động';
    //             $vehicle->save();
    //             $messages[] = "Xe {$vehicle->vehicle_name} đã được thay đổi trạng thái.";

    //             $messages[] = "Xe {$vehicle->vehicle_name} có thông tin nguyên liệu, bạn có muốn thay đổi trạng thái xe không?";
    //         } else {
    //             $vehicle->delete();
    //             $messages[] = "Xe {$vehicle->vehicle_name} đã được xóa.";
    //         }
    //     }
    //     foreach ($vehicles as $vehicle) {
    //         $existingVehicle = Vehicle::where('vehicle_number', $vehicle->vehicle_number)
    //             ->where('status', 'Không hoạt động')
    //             ->first();

    //         if ($existingVehicle) {
    //             return response()->json([
    //                 'message' => "Xe đã tồn tại, bạn có muốn cập nhật lại trạng thái xe {$vehicle->vehicle_name} hay không?",
    //                 'vehicle' => $vehicle
    //             ]);
    //         }
    //     }
    //     return response()->json([
    //         'message' => 'Cập nhật trạng thái xe thành công.',
    //         'vehicles' => $messages
    //     ]);
    // }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $vehicles = Vehicle::whereIn('id', $request->ids)->get();
        $messages = [];

        foreach ($vehicles as $vehicle) {
            if ($vehicle->ingredients()->count() > 0) {
                // Xe có nguyên liệu, không xóa mà chuyển trạng thái
                $messages[] = [
                    'id' => $vehicle->id,
                    'vehicle_name' => $vehicle->vehicle_name,
                    'message' => "Xe {$vehicle->vehicle_number} có thông tin nguyên liệu. Bạn có muốn thay đổi trạng thái xe không?",
                    'status' => 'update_status' // Thêm trạng thái
                ];
                ActionHistory::create([
                    'user_id' => Auth::id(),
                    'action_type' => 'Chuyển trạng thái',
                    'model_type' => 'Xe',
                    'details' => "Xe '{$vehicle->vehicle_number}' có nguyên liệu nên không thể xóa. Cần thay đổi trạng thái.",
                ]);
            } else {
                // Xe không có nguyên liệu, xóa luôn
                $vehicle->delete();
                $messages[] = [
                    'id' => $vehicle->id,
                    'vehicle_name' => $vehicle->vehicle_number,
                    'message' => "Xe {$vehicle->vehicle_number} đã được xóa.",
                    'status' => 'deleted' // Thêm trạng thái
                ];
                ActionHistory::create([
                    'user_id' => Auth::id(),
                    'action_type' => 'Xóa',
                    'model_type' => 'Xe',
                    'details' => "Xe '{$vehicle->vehicle_number}' đã xóa.",
                ]);
            }
        }

        return response()->json([
            'message' => 'Thao tác thành công.',
            'vehicles' => $messages
        ]);
    }
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'status' => 'required|string', // Adjust based on allowed values
        ]);

        $vehicle = Vehicle::find($request->id);

        if ($vehicle) {
            $vehicle->status = $request->status;
            $vehicle->save();
            return response()->json(['message' => 'Trạng thái xe đã được cập nhật.']);
        }

        return response()->json(['message' => 'Xe không tồn tại.'], 404);
    }
    public function toggleStatus(Request $request)
    {
        $vehicle = Vehicle::find($request->id);

        if ($vehicle) {
            $vehicle->status = $vehicle->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $vehicle->save();

            return response()->json(['success' => true, 'status' => $vehicle->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
