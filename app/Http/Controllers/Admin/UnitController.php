<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Units;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $all_units = Units::orderBy('id', 'desc')->get();
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
                ->editColumn('unit_name', function ($row) {
                    return '<a href="/units/edit/' . $row->id . '">' . $row->unit_name . '</a>';
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/units/edit/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->unit_name ?? 'N/A') . '</span>?
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
                ->rawColumns(['check', 'stt', 'unit_name', 'status', 'action'])
                ->make(true);
        }
        return view('units.all_units');
    }
    public function save(Request $request)
    {
        $request->validate([
            'unit_name' => 'required',
            'status' => 'nullable',
        ]);
        $existingCode = Units::where('unit_name', $request->unit_name)->first();

        if ($existingCode) {
            return redirect()->back()->with(['error' => 'Tên đơn vị này đã tồn tại!']);
        }

        Units::create([
            'unit_name' => $request->unit_name,
            'status' => $request->status ?? 'Hoạt động',
        ]);
        session()->flash('message', 'Tạo đơn vị thành công.');
        return redirect()->back();
    }
    public function edit($id)
    {
        $units = Units::find($id);
        return view('units.edit_units', compact('units'));
    }
    public function update(Request $request, $id)
    {
        $existingUnits = Units::where('unit_name', $request->unit_name)->where('id', '!=', $id)->first();


        $existingUnits = Units::where(function ($query) use ($request, $id) {
            $query->where('unit_name', $request->unit_name);
        })->where('id', '!=', $id)->first();
        if ($existingUnits) {
            if ($existingUnits->unit_name === $request->unit_name) {
                return redirect()->back()->with(['error' => 'Đơn vị này đã tồn tại!']);
            }
        }
        $units = Units::find($id);
        if (!$units) {
            return redirect()->back()->with('error', 'Đơn vị không tồn tại');
        }
        $request->validate([
            'unit_name' => 'nullable',
        ]);
        $units->update([
            'unit_name' => $request->unit_name,
            'status' => $request->status,
        ]);
        return redirect()->route('units.index')->with('message', 'Cập nhật đơn vị thành công');
    }
    public function destroy($id)
    {
        $units = Units::find($id);
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

        $units = Units::whereIn('id', $request->ids)->get();

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
        Units::whereIn('id', $request->ids)->delete();

        return response()->json([
            'message' => 'Xóa thành công các đơn vị được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $unit = Units::find($request->id);
        if ($unit) {
            $unit->status = $unit->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $unit->save();
            return response()->json(['success' => true, 'status' => $unit->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
