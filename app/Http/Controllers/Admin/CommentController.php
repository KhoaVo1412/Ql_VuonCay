<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Evaluate;
use App\Models\GenTask;
use App\Models\Plot;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $plots = Plot::all();
        $user = Auth::user();
        /** @var \App\Models\User $user */
        $workers = Worker::query()
            ->when(!$user->hasRole('Admin'), fn($q) => $q->where('user_id', $user->id))
            ->get();
        $all_comments = Evaluate::with(['worker'])
            ->when($request->filled('warehouse_id'), fn($q) => $q->where('warehouseID', $request->warehouse_id))
            ->when($request->filled('start_date'), fn($q) => $q->whereDate('date_comment', $request->start_date))
            ->when(!$user->hasRole('Admin'), fn($q) => $q->whereHas('worker', fn($w) => $w->where('user_id', $user->id)))
            ->when($request->filled('deadline'), function ($q) use ($request) {
                $deadline = $request->deadline;
                $q->whereHas('task', function ($t) use ($deadline) {
                    return match ($deadline) {
                        'on_time'          => $t->completedOnTime(),
                        'late'             => $t->completedLate(),
                        'pending_ontrack'  => $t->pendingOnTrack(),
                        'pending_overdue'  => $t->pendingOverdue(),
                        default            => $t,
                    };
                });
            })
            ->orderByDesc('id');
        foreach ($workers as $worker) {
            $base = GenTask::where('workerID', $worker->id);

            $worker->countWork       = (clone $base)->count();
            $worker->doneOnTime      = (clone $base)->completedOnTime()->count();
            $worker->doneLate        = (clone $base)->completedLate()->count();
            $worker->pendingOnTrack  = (clone $base)->pendingOnTrack()->count();
            $worker->pendingOverdue  = (clone $base)->pendingOverdue()->count();
            $worker->countCofirm = $worker->doneOnTime + $worker->doneLate + $worker->pendingOnTrack;
            $worker->countUn     = $worker->pendingOverdue;
        }

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
                ->editColumn('date_comment', function ($row) {
                    return $row->date_comment ? Carbon::parse($row->date_comment)->format('d/m/Y')
                        : null;
                })
                ->editColumn('opinion', function ($row) {
                    if ($row->opinion) {
                        return '<span style="color:red;">' . e($row->opinion) . '</span>';
                    } else {
                        return '<span style="color:black;">Chưa có ý kiến</span>';
                    }
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
                ->rawColumns(['check', 'opinion', 'stt', 'name', 'date_comment', 'workerID', 'rating', 'note', 'status', 'action'])
                ->make(true);
        }
        return view('comments.all_comments', compact('workers', 'plots'));
    }
    public function save(Request $request)
    {
        $v = $request->validate([
            'name'            => 'required|string|max:255',
            'workerID'        => 'required|integer|exists:workers,id',
            'deductionPoints' => 'required|numeric|min:0',
            'rating'          => 'required|string|max:255',
            'date_comment'    => 'required|date',
            'note'            => 'nullable|string',
        ]);

        $asOf = Carbon::parse($v['date_comment'])->endOfDay();

        $base = GenTask::where('workerID', $v['workerID'])
            ->where('created_at', '<=', $asOf);

        $countWork  = (clone $base)->count();
        $doneOnTime = (clone $base)->completedOnTime()->count(); // 'Hoàn thành'
        $doneLate   = (clone $base)->completedLate()->count();   // 'Hoàn thành trễ'
        $notDone    = (clone $base)->pending()->count();         // 'Đang chờ'

        $evaluate = Evaluate::create([
            'name'            => $v['name'],
            'workerID'        => $v['workerID'],
            'deductionPoints' => $v['deductionPoints'],
            'rating'          => $v['rating'],
            'date_comment'    => $v['date_comment'],
            'countWork'       => $countWork,
            'countCofirm'     => $doneOnTime, // đúng hạn
            'countLate'       => $doneLate,   // trễ (cần cột này trong bảng evaluates)
            'countUn'         => $notDone,    // chưa làm
            'note'            => $v['note'] ?? null,
            'status'            => "Hoạt động",
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'Evaluate',
            'details' => "Đã tạo đánh giá cho nhân viên ID: {$evaluate->workerID} với mã: {$evaluate->name}",
        ]);
        return back()->with('message', 'Đã lưu đánh giá.');
    }
    public function edit($id)
    {
        $comments = Evaluate::findOrFail($id);
        $asOf = $comments->date_comment
            ? Carbon::parse($comments->date_comment)->endOfDay()
            : null;
        $workers = Worker::all();
        foreach ($workers as $worker) {
            $base = GenTask::where('workerID', $worker->id);
            if ($asOf) {
                $base->where('created_at', '<=', $asOf);
            }
            $worker->countWork   = (clone $base)->count();
            $worker->countCofirm = (clone $base)->where('workStatus', 'Hoàn thành')->count();
            $worker->countLate   = (clone $base)->where('workStatus', 'Hoàn thành trễ')->count();
            $worker->countUn     = (clone $base)->where('workStatus', 'Đang chờ')->count();
        }
        return view('comments.edit_comments', compact('comments', 'workers'));
    }
    public function update(Request $request, $id)
    {
        $comments = Evaluate::find($id);
        if (!$comments) {
            return redirect()->back()->with('error', 'Đánh giá không tồn tại');
        }
        $request->validate([
            'name'            => 'required|string|max:255',
            'workerID'        => 'required',
            'deductionPoints' => 'nullable|numeric|min:0',
            'rating'          => 'nullable|string|max:255',
            'note'            => 'nullable|string',
            'status'          => 'nullable|string',
            'date_comment'    => 'nullable|date',
            'opinion'         => 'nullable|string',
        ]);
        $exists = Evaluate::where('name', $request->name)
            ->where('id', '!=', $id)
            ->exists();
        if ($exists) {
            return back()->with(['error' => 'Đánh giá này đã tồn tại!'])->withInput();
        }
        $originalData = $comments->only([
            'name',
            'workerID',
            'deductionPoints',
            'rating',
            'note',
            'status',
            'date_comment',
            'opinion',
            'countWork',
            'countCofirm',
            'countLate',
            'countUn'
        ]);
        $comments->fill([
            'name'            => $request->name,
            'workerID'        => $request->workerID,
            'deductionPoints' => $request->deductionPoints ?? 0,
            'rating'          => $request->rating,
            'note'            => $request->note,
            'status'          => $request->status,
            'date_comment'    => $request->date_comment,
            'opinion'         => $request->opinion,
        ]);
        $asOf = $comments->date_comment
            ? Carbon::parse($comments->date_comment)->endOfDay()
            : null;
        $base = GenTask::where('workerID', $comments->workerID);
        if ($asOf) $base->where('created_at', '<=', $asOf);
        $countWork  = (clone $base)->count();
        $doneOnTime = (clone $base)->where('workStatus', 'Hoàn thành')->count();
        $doneLate   = (clone $base)->where('workStatus', 'Hoàn thành trễ')->count();
        $notDone    = (clone $base)->where('workStatus', 'Đang chờ')->count();
        $comments->countWork   = $countWork;
        $comments->countCofirm = $doneOnTime;
        $comments->countLate   = $doneLate;    // cần cột này
        $comments->countUn     = $notDone;
        $comments->save();
        $changedFields = [];
        foreach ($originalData as $key => $oldValue) {
            $newValue = $comments->$key;
            if ((string)$oldValue !== (string)$newValue) {
                $changedFields[] = "$key: \"$oldValue\" => \"$newValue\"";
            }
        }
        $details = count($changedFields)
            ? "Đã cập nhật đánh giá {$comments->name}. Thay đổi: " . implode(', ', $changedFields)
            : "Cập nhật đánh giá {$comments->name} nhưng không có thay đổi dữ liệu.";

        ActionHistory::create([
            'user_id'    => Auth::id(),
            'action_type' => 'update',
            'model_type' => 'Evaluate',
            'details'    => $details,
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

        foreach ($comments as $c) {
            $c->status = ($c->status === 'Hoạt động') ? 'Không hoạt động' : 'Hoạt động';
            $c->save();
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

        foreach ($commentsToDelete as $c) {
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'delete',
                'model_type' => 'Evaluate',
                'details' => "Đã xóa đánh giá: " . $c->name,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các đánh giá được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $c = Evaluate::find($request->id);
        if ($c) {
            $c->status = $c->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $c->save();
            return response()->json(['success' => true, 'status' => $c->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
