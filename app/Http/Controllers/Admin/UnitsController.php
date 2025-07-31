<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class UnitsController extends Controller
{
    public function index(Request $request)
    {
        $all_units = UnitOfMeasure::orderBy('id', 'desc')->get();
        if ($request->ajax()) {
            return DataTables::of($all_units)
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
                ->editColumn('name', function ($row) {
                    return $row->name;
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-units/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        <a href="/units/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
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
        return view('units.all_units');
    }
    public function save(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'status' => 'nullable',
        ]);
        $existingCode = UnitOfMeasure::where('name', $request->name)->first();

        if ($existingCode) {
            return redirect()->back()->with(['error' => 'Tên đơn vị vật tư này đã tồn tại!']);
        }

        UnitOfMeasure::create([
            'code' => $request->code,
            'name' => $request->name,
            'status' => $request->status ?? 'Hoạt động',
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'unitOfMeasure',
            'details' => "Đã tạo đơn vị vật tư: " . $request->name . " với mã: " . $request->code,
        ]);
        session()->flash('message', 'Tạo đơn vị vật tư thành công.');
        return redirect()->back();
    }
    public function edit($id)
    {
        $units = UnitOfMeasure::find($id);
        return view('units.edit_units', compact('units'));
    }
    public function update(Request $request, $id)
    {
        $existingUnitOfMeasure = UnitOfMeasure::where('name', $request->name)->where('id', '!=', $id)->first();


        $existingUnitOfMeasure = UnitOfMeasure::where(function ($query) use ($request, $id) {
            $query->where('name', $request->name);
        })->where('id', '!=', $id)->first();
        if ($existingUnitOfMeasure) {
            if ($existingUnitOfMeasure->name === $request->name) {
                return redirect()->back()->with(['error' => 'Đơn vị này đã tồn tại!']);
            }
        }
        $units = UnitOfMeasure::find($id);
        if (!$units) {
            return redirect()->back()->with('error', 'Đơn vị không tồn tại');
        }
        $request->validate([
            'code' => 'nullable',
            'name' => 'nullable',
            'status' => 'nullable',
        ]);
        $units->update([
            'code' => $request->code,
            'name' => $request->name,
            'status' => $request->status,
        ]);

        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'update',
            'model_type' => 'unitOfMeasure',
            'details' => "Đã cập nhật đơn vị vật tư: " . $units->name . " (ID: " . $units->id . ")",
        ]);
        return redirect()->route('units.index')->with('message', 'Cập nhật đơn vị vật tư thành công');
    }
    public function destroy($id)
    {
        $units = UnitOfMeasure::find($id);
        $units->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $units = UnitOfMeasure::whereIn('id', $request->ids)->get();

        foreach ($units as $unit) {
            $unit->status = ($unit->status === 'Hoạt động') ? 'Không hoạt động' : 'Hoạt động';
            $unit->save();
        }
        return response()->json(['message' => 'Thành Công']);
    }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        $unitDL = UnitOfMeasure::whereIn('id', $request->ids)->get();

        UnitOfMeasure::whereIn('id', $request->ids)->delete();
        foreach ($unitDL as $c) {
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'delete',
                'model_type' => 'UnitOfMeasure',
                'details' => "Đã xóa đơn vị vật tư: " . $c->name . " với mã: " . $c->c_code,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các đơn vị vật tư được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $unit = UnitOfMeasure::find($request->id);
        if ($unit) {
            $unit->status = $unit->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $unit->save();
            return response()->json(['success' => true, 'status' => $unit->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
