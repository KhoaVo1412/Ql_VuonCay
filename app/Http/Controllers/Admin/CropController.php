<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Plant;
use App\Models\Plot;
use App\Models\Variety;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class CropController extends Controller
{
    public function index(Request $request)
    {
        $plots = Plot::all();
        $varieties = Variety::all();

        $all_plants = Plant::with('plot', 'variety')->orderBy('id', 'desc')->get();
        // dd($all_plants);
        if ($request->ajax()) {
            return DataTables::of($all_plants)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('plantCode', function ($row) {
                    return $row->plantCode;
                })
                ->addColumn('plotID', function ($row) {
                    return $row->plot ? $row->plot->plotName : 'N/A';
                })
                ->addColumn('varietyID', function ($row) {
                    return $row->variety ? $row->variety->varietyName : 'N/A';
                })
                ->addColumn('RF_id', function ($row) {
                    return $row->RF_id;
                })
                ->addColumn('statusTree', function ($row) {
                    return $row->statusTree;
                })
                ->addColumn('year', function ($row) {
                    return $row->year;
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-crops/' . $row->id . '" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal' . $row->id . '">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                        <div class="modal fade" id="deleteModal' . $row->id . '" tabindex="-1" aria-labelledby="deleteModalLabel' . $row->id . '" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel' . $row->id . '">Xác Nhận Xóa</h5>
                                        <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->plantCode ?? 'N/A') . '</span>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/farms/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'stt', 'statusTree', 'plantCode', 'rfID', 'year', 'plotID', 'varietyID', 'status', 'action'])
                ->make(true);
        }
        return view('crops.all_crops', compact('plots', 'varieties'));
    }
    public function save(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'plantCode' => 'required',
            'plotID' => 'required',
            'varietyID' => 'required',
            'RF_id' => 'required',
            'year' => 'required',
            'status' => 'nullable',
        ]);
        $existingCode = Variety::where('plantCode', $request->plantCode)->first();

        if ($existingCode) {
            return redirect()->back()->with(['error' => 'Mã cây này đã tồn tại!']);
        }

        $variety = Variety::find($request->varietyID);
        $varietyNameSlug = Str::slug($variety->varietyName, '_');
        $prefix = '#' . $varietyNameSlug . '_';
        do {
            $randomCode = $prefix . rand(100, 999);
        } while (Variety::where('plantCode', $randomCode)->exists());
        Variety::create([
            'plantCode' => $request->randomCode,
            'plotID' => $request->plotID,
            'varietyID' => $request->varietyID,
            'RF_id' => $request->RF_id,
            'year' => $request->year,
            'status' => $request->status ?? 'Hoạt động',
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'Variety',
            'details' => "Đã tạo cây: " . $request->farm_name . " với mã: " . $request->plantCode,
        ]);
        session()->flash('message', 'Tạo cây thành công.');
        return redirect()->back();
    }
    public function edit($id)
    {
        $varietys = Variety::all();
        $plots = Plot::all();
        $crops = Plant::find($id);
        return view('crops.edit_crops', compact('crops', 'varietys', 'plots'));
    }
    public function update(Request $request, $id)
    {
        $existingVariety = Variety::where('farm_name', $request->farm_name)->where('id', '!=', $id)->first();

        // if ($existingVariety) {
        //     return redirect()->back()->with(['error' => 'Tên cây này đã tồn tại!']);
        // }

        $existingVariety = Variety::where(function ($query) use ($request, $id) {
            $query->where('plantCode', $request->plantCode);
            // $query->where('farm_name', $request->farm_name)
            //     ->orWhere('plantCode', $request->plantCode);
        })->where('id', '!=', $id)->first();

        if ($existingVariety) {
            // if ($existingVariety->farm_name === $request->farm_name) {
            //     return redirect()->back()->with(['error' => 'Tên cây này đã tồn tại!']);
            // }
            if ($existingVariety->plantCode === $request->plantCode) {
                return redirect()->back()->with(['error' => 'Mã cây này đã tồn tại!']);
            }
        }
        $farms = Variety::find($id);
        if (!$farms) {
            return redirect()->back()->with('error', 'Nông trường không tồn tại');
        }
        $request->validate([
            'plantCode' => 'required',
            'plotID' => 'required',
            'varietyID' => 'required',
            'RF_id' => 'required',
            'year' => 'required',
            'status' => 'nullable',
        ]);
        $farms->update([
            'plantCode' => $request->plantCode,
            'farm_name' => $request->farm_name,
            'unit_id' => $request->unit_id,
            'status' => $request->status,
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'update',
            'model_type' => 'Variety',
            'details' => "Đã cập nhật cây: " . $farms->farm_name,
        ]);
        return redirect()->route('farms.index')->with('message', 'Cập nhật cây thành công');
    }
    public function destroy($id)
    {
        $farms = Variety::find($id);
        $farms->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $farms = Variety::whereIn('id', $request->ids)->get();

        foreach ($farms as $farm) {
            $farm->status = ($farm->status === 'Hoạt động') ? 'Không hoạt động' : 'Hoạt động';
            $farm->save();
        }
        return response()->json(['message' => 'Thành Công']);
    }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        $farmsToDelete = Variety::whereIn('id', $request->ids)->get();

        Variety::whereIn('id', $request->ids)->delete();

        foreach ($farmsToDelete as $farm) {
            ActionHistory::create([
                'user_id' => Auth::id(),  // ID của người thực hiện hành động
                'action_type' => 'delete',  // Loại hành động "delete"
                'model_type' => 'Variety',  // Model "Variety"
                'details' => "Đã xóa cây: " . $farm->farm_name . " với mã: " . $farm->plantCode,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các cây được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $farm = Variety::find($request->id);
        if ($farm) {
            $farm->status = $farm->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $farm->save();
            return response()->json(['success' => true, 'status' => $farm->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
