<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Evaluate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class WorkerEvaluationController extends Controller
{
    public function index(Request $request)
    {

        $all_workevs = Evaluate::wwith('worker')->orderBy('id', 'desc')->get();
        // dd($all_workevs);
        if ($request->ajax()) {
            return DataTables::of($all_workevs)
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
                ->editColumn('workerID', function ($row) {
                    return $row->worker ? $row->worker->name : 'N/A';
                })
                ->editColumn('rating', function ($row) {
                    return  $row->rating;
                })
                ->addColumn('deductionPoints', function ($row) {
                    return $row->deductionPoints ?? 'N/A';
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-workevs/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        <a href="/workevs/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'stt', 'workerID', 'rating', 'deductionPoints', 'status', 'action'])
                ->make(true);
        }
        return view('workerEvs.all_workerEvs');
    }
    public function save(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'farm_name' => 'required',
            'unit_id' => 'required|exists:units,id',
            'status' => 'nullable',
        ]);
        $existingCode = Evaluate::where('name', $request->name)->first();

        if ($existingCode) {
            return redirect()->back()->with(['error' => 'Đánh giá này đã tồn tại!']);
        }
        Evaluate::create([
            'name' => $request->name,
            'farm_name' => $request->farm_name,
            'unit_id' => $request->unit_id,
            'status' => $request->status ?? 'Hoạt động',
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'Evaluate',
            'details' => "Đã tạo đánh giá: " . $request->farm_name . " với mã: " . $request->name,
        ]);
        session()->flash('message', 'Tạo đánh giá thành công.');
        return redirect()->back();
    }
    public function edit($id)
    {
        $workevs = Evaluate::find($id);
        return view('workerEvs.edit_workerEvs', compact('workevs'));
    }
    public function update(Request $request, $id)
    {
        $existingEvaluate = Evaluate::where('farm_name', $request->farm_name)->where('id', '!=', $id)->first();

        $existingEvaluate = Evaluate::where(function ($query) use ($request, $id) {
            $query->where('name', $request->name);
        })->where('id', '!=', $id)->first();

        if ($existingEvaluate) {
            if ($existingEvaluate->name === $request->name) {
                return redirect()->back()->with(['error' => 'Đánh giá này đã tồn tại!']);
            }
        }
        $workevs = Evaluate::find($id);
        if (!$workevs) {
            return redirect()->back()->with('error', 'Đáng giá không tồn tại');
        }
        $request->validate([
            'farm_name' => 'nullable',
            'name' => 'nullable',
            'unit_id' => 'nullable',
        ]);
        $workevs->update([
            'name' => $request->name,
            'farm_name' => $request->farm_name,
            'unit_id' => $request->unit_id,
            'status' => $request->status,
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'update',
            'model_type' => 'Evaluate',
            'details' => "Đã cập nhật đánh giá: " . $workevs->farm_name,
        ]);
        return redirect()->route('workevs.index')->with('message', 'Cập nhật đánh giá thành công');
    }
    public function destroy($id)
    {
        $workevs = Evaluate::find($id);
        $workevs->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $workevs = Evaluate::whereIn('id', $request->ids)->get();

        foreach ($workevs as $farm) {
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
        $workevsToDelete = Evaluate::whereIn('id', $request->ids)->get();

        Evaluate::whereIn('id', $request->ids)->delete();

        foreach ($workevsToDelete as $farm) {
            ActionHistory::create([
                'user_id' => Auth::id(),  // ID của người thực hiện hành động
                'action_type' => 'delete',  // Loại hành động "delete"
                'model_type' => 'Evaluate',  // Model "Evaluate"
                'details' => "Đã xóa đánh giá: " . $farm->farm_name . " với mã: " . $farm->name,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các đánh giá được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $farm = Evaluate::find($request->id);
        if ($farm) {
            $farm->status = $farm->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $farm->save();
            return response()->json(['success' => true, 'status' => $farm->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
