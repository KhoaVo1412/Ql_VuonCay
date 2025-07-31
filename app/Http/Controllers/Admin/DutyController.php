<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use Illuminate\Http\Request;
use App\Models\Duty;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class DutyController extends Controller
{
    public function index(Request $request)
    {
        $all_duty = Duty::orderBy('id', 'desc')->get();
        // dd($all_duty);
        if ($request->ajax()) {
            return DataTables::of($all_duty)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->addColumn('dutyName', function ($row) {
                    return $row->dutyName ?? 'N/A';
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-duty/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->farm_name ?? 'N/A') . '</span>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/duty/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'code', 'stt', 'dutyName', 'status', 'action'])
                ->make(true);
        }
        return view('duty.all_duty');
    }
    public function save(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'dutyName' => 'required',
            'status' => 'nullable',
        ]);
        $existingCode = Duty::where('dutyName', $request->dutyName)->first();

        if ($existingCode) {
            return redirect()->back()->with(['error' => 'Tên chức vụ này đã tồn tại!']);
        }

        Duty::create([
            'dutyName' => $request->dutyName,
            'status' => $request->status ?? 'Hoạt động',
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'Duty',
            'details' => "Đã tạo chức vụ: " . $request->dutyName . " với mã: " . $request->dutyName,
        ]);
        session()->flash('message', 'Tạo chức vụ thành công.');
        return redirect()->back();
    }
    public function edit($id)
    {
        $duty = Duty::find($id);

        return view('duty.edit_duty', compact('duty'));
    }
    public function update(Request $request, $id)
    {
        $existingDuty = Duty::where('dutyName', $request->teamName)->where('id', '!=', $id)->first();

        $existingDuty = Duty::where(function ($query) use ($request, $id) {
            $query->where('dutyName', $request->dutyName);
        })->where('id', '!=', $id)->first();

        if ($existingDuty) {
            if ($existingDuty->farm_code === $request->farm_code) {
                return redirect()->back()->with(['error' => 'Tên chức vụ này đã tồn tại!']);
            }
        }
        $duty = Duty::find($id);
        if (!$duty) {
            return redirect()->back()->with('error', 'Tên chức vụ không tồn tại');
        }
        $request->validate([
            'dutyName' => 'nullable',
            'status' => 'nullable',
        ]);
        $duty->update([
            'dutyName' => $request->dutyName,
            'status' => $request->status,
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'update',
            'model_type' => 'Duty',
            'details' => "Đã cập nhật vườn: " . $duty->dutyName,
        ]);
        return redirect()->route('duty.index')->with('message', 'Cập chức vụ thành công');
    }
    public function destroy($id)
    {
        $duty = Duty::find($id);
        $duty->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $duty = Duty::whereIn('id', $request->ids)->get();

        foreach ($duty as $g) {
            $g->status = ($g->status === 'Hoạt động') ? 'Không hoạt động' : 'Hoạt động';
            $g->save();
        }
        return response()->json(['message' => 'Thành Công']);
    }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        $dutyToDelete = Duty::whereIn('id', $request->ids)->get();
        Duty::whereIn('id', $request->ids)->delete();
        foreach ($dutyToDelete as $g) {
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'delete',
                'model_type' => 'Duty',
                'details' => "Đã xóa chức vụ: " . $g->dutyName,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công chức vụ được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $duty = Duty::find($request->id);
        if ($duty) {
            $duty->status = $duty->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $duty->save();
            return response()->json(['success' => true, 'status' => $duty->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
