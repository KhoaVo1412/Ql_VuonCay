<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\WareHouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class WarehousesController extends Controller
{
    public function index(Request $request)
    {

        $all_warehouses = WareHouse::all();
        // dd($all_warehouses);
        if ($request->ajax()) {
            return DataTables::of($all_warehouses)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('name', function ($row) {
                    return $row->name;
                })
                ->editColumn('code', function ($row) {
                    return $row->code;
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-warehouses/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->name ?? 'N/A') . '</span>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/warehouses/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'stt', 'name', 'code', 'status', 'action'])
                ->make(true);
        }
        return view('warehouses.all_warehouses');
    }
    public function save(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'code' => 'required',
            'status' => 'nullable',
        ]);
        // $existingWareHouse = WareHouse::where('farm_name', $request->farm_name)->first();
        $existingCode = WareHouse::where('code', $request->code)->first();

        if ($existingCode) {
            return redirect()->back()->with(['error' => 'Mã kho này đã tồn tại!']);
        }
        // if ($existingWareHouse) {
        //     return redirect()->back()->with(['error' => 'Tên kho này đã tồn tại!']);
        // }

        // $farmNameSlug = Str::slug($request->farm_name, '_');
        // $prefix = '#' . $farmNameSlug . '_';
        // do {
        //     $randomCode = $prefix . rand(100, 999);
        // } while (WareHouse::where('farm_code', $randomCode)->exists());
        WareHouse::create([
            'name' => $request->name,
            'code' => $request->code,
            'status' => $request->status ?? 'Hoạt động',
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'WareHouse',
            'details' => "Đã tạo kho: " . $request->name . " với mã: " . $request->code,
        ]);
        session()->flash('message', 'Tạo kho thành công.');
        return redirect()->back();
    }
    public function edit($id)
    {
        $warehouses = WareHouse::find($id);
        return view('warehouses.edit_warehouses', compact('warehouses'));
    }
    public function update(Request $request, $id)
    {
        $existingWareHouse = WareHouse::where('name', $request->name)->where('id', '!=', $id)->first();

        if ($existingWareHouse) {
            return redirect()->back()->with(['error' => 'Tên kho này đã tồn tại!']);
        }

        $existingWareHouse = WareHouse::where(function ($query) use ($request, $id) {
            $query->where('name', $request->name)
                ->orWhere('code', $request->code);
        })->where('id', '!=', $id)->first();

        if ($existingWareHouse) {
            if ($existingWareHouse->name === $request->name) {
                return redirect()->back()->with(['error' => 'Tên kho này đã tồn tại!']);
            }
            if ($existingWareHouse->farm_code === $request->farm_code) {
                return redirect()->back()->with(['error' => 'Mã kho này đã tồn tại!']);
            }
        }
        $warehouses = WareHouse::find($id);
        if (!$warehouses) {
            return redirect()->back()->with('error', 'Kho không tồn tại');
        }
        $request->validate([
            'name' => 'required',
            'code' => 'required',
            'status' => 'nullable',
        ]);
        $warehouses->update([
            'name' => $request->name,
            'code' => $request->code,
            'status' => $request->status,
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'update',
            'model_type' => 'WareHouse',
            'details' => "Đã cập nhật kho: " . $warehouses->name,
        ]);
        return redirect()->route('warehouses.index')->with('message', 'Cập nhật kho thành công');
    }
    public function destroy($id)
    {
        $warehouses = WareHouse::find($id);
        $warehouses->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $warehouses = WareHouse::whereIn('id', $request->ids)->get();

        foreach ($warehouses as $farm) {
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
        $warehousesToDelete = WareHouse::whereIn('id', $request->ids)->get();

        WareHouse::whereIn('id', $request->ids)->delete();

        foreach ($warehousesToDelete as $farm) {
            ActionHistory::create([
                'user_id' => Auth::id(),  // ID của người thực hiện hành động
                'action_type' => 'delete',  // Loại hành động "delete"
                'model_type' => 'WareHouse',  // Model "WareHouse"
                'details' => "Đã xóa kho: " . $farm->farm_name . " với mã: " . $farm->farm_code,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các kho được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $farm = WareHouse::find($request->id);
        if ($farm) {
            $farm->status = $farm->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $farm->save();
            return response()->json(['success' => true, 'status' => $farm->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
