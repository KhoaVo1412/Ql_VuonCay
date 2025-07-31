<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\FallentPlant;
use App\Models\Plant;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class TreesFelledController extends Controller
{
    public function index(Request $request)
    {
        $plants = Plant::all();
        $workers = Worker::all();
        $fallentplants = FallentPlant::with(['plant.plot', 'worker'])->orderBy('id', 'desc')->get();

        if ($request->ajax()) {
            return DataTables::of($fallentplants)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function () {
                    static $stt = 0;
                    return ++$stt;
                })
                ->addColumn('plant_code', function ($row) {
                    return $row->plant->plantCode ?? '---';
                })
                ->addColumn('plot_name', function ($row) {
                    return $row->plant->plot->plotName ?? '---';
                })
                ->addColumn('specific_location', function ($row) {
                    return $row->specificLocation ?? '---';
                })
                ->addColumn('detection_date', function ($row) {
                    return \Carbon\Carbon::parse($row->detectionDate)->format('d/m/Y');
                })
                ->addColumn('worker_name', function ($row) {
                    return $row->worker->name ?? '---';
                })
                ->addColumn('tree_condition', function ($row) {
                    return $row->treeCondition ?? '---';
                })
                ->addColumn('report_status', function ($row) {
                    $class = $row->reportStatus === 'Đã xử lý' ? 'success' : 'danger';
                    return '<span class="badge bg-' . $class . '">' . $row->reportStatus . '</span>';
                })
                ->editColumn('status', function ($row) {
                    $class = $row->status === 'Hoạt động' ? 'success' : 'danger';
                    return '<span class="badge bg-' . $class . '">' . $row->status . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                    <div class="d-flex gap-1">
                        <a href="' . route('treesfelleds.edit', $row->id) . '" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal' . $row->id . '">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>

                    <div class="modal fade" id="deleteModal' . $row->id . '" tabindex="-1" aria-labelledby="deleteModalLabel' . $row->id . '" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel' . $row->id . '">Xác nhận xóa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Bạn có chắc chắn muốn xóa cây <strong>#' . ($row->plant->plantCode ?? '---') . '</strong>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                    <a href="' . route('treesfelleds.delete', $row->id) . '" class="btn btn-danger">Xóa</a>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
                })
                ->rawColumns(['check', 'detection_date', 'plant_code', 'plot_name', 'specific_location', 'worker_name', 'tree_condition', 'report_status', 'status', 'action'])
                ->make(true);
        }
        return view('treesfelled.all_treesfelleds', compact('plants', 'workers'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'detectionDate' => 'required',
            'plantID' => 'required',
            'specificLocation' => 'required|string',
            'cause' => 'nullable|string',
            'treeCondition' => 'nullable|string',
            'reportStatus' => 'nullable|string',
            'workerID' => 'required',
            'status' => 'nullable',
        ]);

        $fallent = FallentPlant::create([
            'detectionDate' => $request->detectionDate,
            'cause' => $request->cause,
            'plantID' => $request->plantID,
            'specificLocation' => $request->specificLocation,
            'reportStatus' => $request->reportStatus ?? 'Chưa xử lý',
            'treeCondition' => $request->treeCondition,
            'workerID' => $request->workerID,
            'status' => $request->status ?? 'Hoạt động',
        ]);

        // Ghi lại lịch sử
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'FallentPlant',
            'details' => "Đã ghi nhận cây ngã: Mã cây #" . optional($fallent->plant)->plantCode . ", Ngày: " . $fallent->detectionDate,
        ]);

        return redirect()->back()->with('message', 'Ghi nhận cây ngã thành công.');
    }
    public function edit($id)
    {
        $workers = Worker::all();
        $plants = Plant::all();
        $treesfelleds = FallentPlant::with('plant')->find($id);
        return view('treesfelled.edit_treesfelleds', compact('treesfelleds', 'plants', 'workers'));
    }
    public function update(Request $request, $id)
    {
        $fallentplant = FallentPlant::find($id);

        if (!$fallentplant) {
            return redirect()->back()->with('error', 'Thông tin cây ngã không tồn tại');
        }

        $request->validate([
            'detectionDate' => 'required',
            'plantID' => 'required',
            'specificLocation' => 'required|string',
            'cause' => 'nullable|string',
            'treeCondition' => 'nullable|string',
            'reportStatus' => 'nullable|string',
            'workerID' => 'required',
            'status' => 'nullable',
        ]);

        $originalData = $fallentplant->only([
            'detectionDate',
            'plantID',
            'specificLocation',
            'cause',
            'treeCondition',
            'reportStatus',
            'workerID',
            'status'
        ]);

        $fallentplant->update([
            'detectionDate' => $request->detectionDate,
            'plantID' => $request->plantID,
            'specificLocation' => $request->specificLocation,
            'cause' => $request->cause,
            'treeCondition' => $request->treeCondition,
            'reportStatus' => $request->reportStatus ?? 'Chưa xử lý',
            'workerID' => $request->workerID,
            'status' => $request->status ?? 'Hoạt động',
        ]);

        $changes = [];
        foreach ($originalData as $key => $old) {
            $new = $fallentplant->$key;
            if ($old != $new) {
                $changes[] = "$key: \"$old\" → \"$new\"";
            }
        }

        $changeLog = count($changes) ? implode(', ', $changes) : 'Không có thay đổi';

        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'update',
            'model_type' => 'FallentPlant',
            'details' => "Cập nhật cây ngã ID {$fallentplant->id}. {$changeLog}",
        ]);

        return redirect()->route('treesfelleds.index')->with('message', 'Cập nhật cây ngã thành công.');
    }

    public function destroy($id)
    {
        $fallentplants = FallentPlant::find($id);
        $fallentplants->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $fallentplants = FallentPlant::whereIn('id', $request->ids)->get();

        foreach ($fallentplants as $f) {
            $f->status = ($f->status === 'Hoạt động') ? 'Không hoạt động' : 'Hoạt động';
            $f->save();
        }
        return response()->json(['message' => 'Thành Công']);
    }
    // public function deleteMultiple(Request $request)
    // {
    //     $request->validate([
    //         'ids' => 'required|array',
    //         'ids.*' => 'integer',
    //     ]);
    //     $fallentPlantsToDelete = FallentPlant::whereIn('id', $request->ids)->get();

    //     FallentPlant::whereIn('id', $request->ids)->delete();

    //     foreach ($fallentPlantsToDelete as $f) {
    //         ActionHistory::create([
    //             'user_id' => Auth::id(),
    //             'action_type' => 'delete',
    //             'model_type' => 'FallentPlant',
    //             'details' => "Đã xóa phiếu cây ngã: " . $f->workers_name,
    //         ]);
    //     }
    //     return response()->json([
    //         'message' => 'Xóa thành công các phiếu cây ngã được chọn.',
    //         'deleted_ids' => $request->ids
    //     ]);
    // }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $fallentPlantsToDelete = FallentPlant::with(['plant', 'worker'])
            ->whereIn('id', $request->ids)
            ->get();

        // Xóa bản ghi
        FallentPlant::whereIn('id', $request->ids)->delete();

        foreach ($fallentPlantsToDelete as $f) {
            $plantCode = optional($f->plant)->plantCode ?? '---';
            $workerName = optional($f->worker)->name ?? '---';
            $date = $f->detectionDate;

            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'delete',
                'model_type' => 'FallentPlant',
                'details' => "Đã xóa cây ngã: Mã cây #{$plantCode}, ngày phát hiện {$date}, người ghi nhận: {$workerName}",
            ]);
        }

        return response()->json([
            'message' => 'Xóa thành công các phiếu cây ngã được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }

    public function toggleStatus(Request $request)
    {
        $fallentplants = FallentPlant::find($request->id);
        if ($fallentplants) {
            $fallentplants->status = $fallentplants->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $fallentplants->save();
            return response()->json(['success' => true, 'status' => $fallentplants->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
