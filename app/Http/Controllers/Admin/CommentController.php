<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Evaluate;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $workers = Worker::all();
        $all_comments = Evaluate::with('worker')->orderBy('id', 'desc')->get();
        // dd($all_comments);
        if ($request->ajax()) {
            return DataTables::of($all_comments)
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
                ->editColumn('rating', function ($row) {
                    return $row->rating;
                })
                ->addColumn('workerID', function ($row) {
                    return $row->worker ? $row->worker->name : 'N/A';
                })
                ->addColumn('note', function ($row) {
                    return $row->unitRelation ? $row->unitRelation->note : 'N/A';
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-comments/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        <a href="/comments/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'stt', 'name', 'workerID', 'rating', 'note', 'status', 'action'])
                ->make(true);
        }
        return view('comments.all_comments', compact('workers'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'workerID' => 'required',
            'deductionPoints' => 'nullable',
            'rating' => 'nullable',
            'note' => 'nullable',
            'status' => 'nullable',
        ]);

        $existing = Evaluate::where('name', $request->name)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Mã đánh giá này đã tồn tại!');
        }

        // Tạo đánh giá mới
        $evaluate = Evaluate::create([
            'name' => $request->name,
            'workerID' => $request->workerID,
            'deductionPoints' => $request->deductionPoints ?? 0,
            'rating' => $request->rating,
            'note' => $request->note,
            'status' => $request->status ?? 'Hoạt động',
        ]);

        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'Evaluate',
            'details' => "Đã tạo đánh giá cho nhân viên ID: {$evaluate->workerID} với mã: {$evaluate->name}",
        ]);

        return redirect()->back()->with('message', 'Tạo đánh giá thành công.');
    }

    public function edit($id)
    {
        $workers = Worker::all();

        $comments = Evaluate::find($id);
        return view('comments.edit_comments', compact('comments', 'workers'));
    }
    public function update(Request $request, $id)
    {
        $existingEvaluate = Evaluate::where('name', $request->name)->where('id', '!=', $id)->first();

        $existingEvaluate = Evaluate::where(function ($query) use ($request, $id) {
            $query->where('name', $request->name);
        })->where('id', '!=', $id)->first();

        if ($existingEvaluate) {

            if ($existingEvaluate->name === $request->name) {
                return redirect()->back()->with(['error' => 'Đánh giá này đã tồn tại!']);
            }
        }
        $comments = Evaluate::find($id);
        if (!$comments) {
            return redirect()->back()->with('error', 'Đánh giá không tồn tại');
        }
        $request->validate([
            'name' => 'required',
            'workerID' => 'required',
            'deductionPoints' => 'nullable',
            'rating' => 'nullable',
            'note' => 'nullable',
            'status' => 'nullable',
        ]);
        $originalData = $comments->only([
            'name',
            'workerID',
            'deductionPoints',
            'rating',
            'note',
            'status'
        ]);
        $comments->update([
            'name' => $request->name,
            'workerID' => $request->workerID,
            'deductionPoints' => $request->deductionPoints ?? 0,
            'rating' => $request->rating,
            'note' => $request->note,
            'status' => $request->status,
        ]);
        $changedFields = [];
        foreach ($originalData as $key => $oldValue) {
            $newValue = $comments->$key;
            if ($oldValue != $newValue) {
                $changedFields[] = "$key: \"$oldValue\" => \"$newValue\"";
            }
        }

        $details = count($changedFields)
            ? "Đã cập nhật đánh giá {$comments->name}. Thay đổi: " . implode(', ', $changedFields)
            : "Cập nhật đánh giá {$comments->name} nhưng không có thay đổi dữ liệu.";

        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'update',
            'model_type' => 'Evaluate',
            'details' => $details,
        ]);
        return redirect()->route('comments.index')->with('message', 'Cập nhật đánh giá thành công');
    }
    public function destroy($id)
    {
        $comments = Evaluate::find($id);
        $comments->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $comments = Evaluate::whereIn('id', $request->ids)->get();

        foreach ($comments as $farm) {
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
        $commentsToDelete = Evaluate::whereIn('id', $request->ids)->get();

        Evaluate::whereIn('id', $request->ids)->delete();

        foreach ($commentsToDelete as $farm) {
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'delete',
                'model_type' => 'Evaluate',
                'details' => "Đã xóa đánh giá: " . $farm->name,
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
