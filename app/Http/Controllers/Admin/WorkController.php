<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Garden;
use App\Models\GenTask;
use App\Models\Plant;
use App\Models\Plot;
use App\Models\Work;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class WorkController extends Controller
{
    public function index(Request $request)
    {
        $works = Work::all();
        $gardens = Garden::with('plots')->get();
        $workers = Worker::all();
        $plots = Plot::with('garden')->get();
        $all_gentask = GenTask::with('work', 'worker')->orderBy('id', 'desc')->get();
        // dd($all_gentask);
        if ($request->ajax()) {
            return DataTables::of($all_gentask)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('code', function ($row) {
                    return $row->code;
                })
                ->addColumn('workName', function ($row) {
                    return $row->work->workName ?? 'Không rõ';
                })
                ->addColumn('workType', function ($row) {
                    return $row->work->workType ?? 'Không rõ';
                })
                ->editColumn('workDate', function ($row) {
                    return $row->workDate ?? 'Không rõ';
                })
                ->editColumn('dateEnd', function ($row) {
                    return $row->dateEnd ?? 'Không rõ';
                })
                ->editColumn('workerID', function ($row) {
                    return $row->worker->name ?? 'Không rõ';
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoàn thành' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoàn thành' ? 'Hoàn thành' : 'Chưa hoàn thành';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-works/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        <a href="/works/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'dateEnd', 'code', 'workerID', 'stt', 'workDate', 'workName', 'workType', 'status', 'action'])
                ->make(true);
        }
        return view('works.all_works', compact('gardens', 'workers', 'works', 'plots'));
        // return view('works.sanluong', compact('gardens', 'workers', 'works', 'plots'));
    }
    public function getPlantsByPlot($plotID)
    {
        $plants = Plant::where('plotID', $plotID)->get();
        return response()->json($plants);
    }

    public function add(Request $request)
    {
        $works = Work::all();
        $gardens = Garden::with('plots')->get();
        $workers = Worker::all();
        $plots = Plot::with('garden')->get();
        return view('works.add_works', compact('gardens', 'workers', 'works', 'plots'));
    }
    public function save(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'workID' => 'required',
            'workerID' => 'required',
            'workDate' => 'required',
            'dateEnd' => 'required',
            'plotID' => 'required',
            'type' => 'required',
            'priority' => 'required',
            'description' => 'nullable',
            'plantIDs' => 'required|array',
        ]);
        $invalidPlants = Plant::whereIn('id', $request->plantIDs)
            ->where('plotID', '!=', $request->plotID)
            ->count();

        if ($invalidPlants > 0) {
            return redirect()->back()->withErrors(['plantIDs' => 'Một hoặc nhiều cây không thuộc lô đã chọn']);
        }
        $taskSlug = Str::slug($request->workID, '_');
        $taskSlug1 = Str::slug($request->workerID);
        $prefix = '#' . $taskSlug . '_' . $taskSlug1;
        do {
            $randomCode = $prefix . rand(100, 999);
        } while (GenTask::where('code', $randomCode)->exists());

        $task = GenTask::create([
            'code' => $randomCode,
            'workID' => $request->workID,
            'workerID' => $request->workerID,
            'workDate' => $request->workDate,
            'dateEnd' => $request->dateEnd,
            'plotID' => $request->plotID,
            'type' => $request->type,
            'priority' => $request->priority,
            'description' => $request->description,
            'workStatus' => 'Đang chờ',
        ]);
        // dd($task);
        $task->plants()->sync($request->plantIDs);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'GenTask',
            'details' => "Đã tạo công việc: " . $request->randomCode,
        ]);
        session()->flash('message', 'Tạo công việc thành công.');
        return redirect()->route('works.index');
    }
    public function edit($id)
    {
        $works = Work::all();
        $gardens = Garden::all();
        $workers = Worker::all();
        $plots = Plot::all();

        $gentasks = GenTask::find($id);
        $plants = $gentasks->plants->pluck('id')->toArray(); // Get the associated plant IDs

        return view('works.edit_work', compact('gardens', 'workers', 'works', 'plots', 'gentasks', 'plants'));
    }
    public function update(Request $request, $id)
    {
        $existingGenTask = GenTask::where('code', $request->code)->where('id', '!=', $id)->first();

        $existingGenTask = GenTask::where(function ($query) use ($request, $id) {
            $query->where('code', $request->code);
        })->where('id', '!=', $id)->first();

        if ($existingGenTask) {

            if ($existingGenTask->code === $request->code) {
                return redirect()->back()->with(['error' => 'Mã công việc này đã tồn tại!']);
            }
        }
        $gentasks = GenTask::find($id);
        if (!$gentasks) {
            return redirect()->back()->with('error', 'Công việc không tồn tại');
        }
        $request->validate([
            'workID' => 'required',
            'workerID' => 'required',
            'workDate' => 'required',
            'plotID' => 'required',
            'type' => 'required',
            'priority' => 'required',
            'description' => 'nullable',
        ]);
        $gentasks->update([
            'workID' => $request->workID,
            'workerID' => $request->workerID,
            'workDate' => $request->workDate,
            'plotID' => $request->plotID,
            'type' => $request->type,
            'priority' => $request->priority,
            'description' => $request->description,
            'workStatus' => $request->workStatus,
        ]);
        $gentasks->plants()->sync($request->plantIDs);

        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'update',
            'model_type' => 'GenTask',
            'details' => "Đã cập nhật công việc: " . $gentasks->code,
        ]);
        return redirect()->route('works.index')->with('message', 'Cập nhật công việc thành công');
    }
    public function destroy($id)
    {
        $farms = GenTask::find($id);
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

        $farms = GenTask::whereIn('id', $request->ids)->get();

        foreach ($farms as $farm) {
            $farm->status = ($farm->status === 'Hoàn thành') ? 'Chưa hoàn thành' : 'Hoàn thành';
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
        $farmsToDelete = GenTask::whereIn('id', $request->ids)->get();

        GenTask::whereIn('id', $request->ids)->delete();

        foreach ($farmsToDelete as $farm) {
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'delete',
                'model_type' => 'GenTask',
                'details' => "Đã xóa công việc: " . $farm->farm_name . " với mã: " . $farm->code,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các công việc được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $farm = GenTask::find($request->id);
        if ($farm) {
            $farm->status = $farm->status == 'Hoàn thành' ? 'Chưa hoàn thành' : 'Hoàn thành';
            $farm->save();
            return response()->json(['success' => true, 'status' => $farm->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function index1(Request $request)
    {
        $all_AddWork = Work::orderBy('id', 'desc')->get();
        if ($request->ajax()) {
            return DataTables::of($all_AddWork)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->addColumn('workCode', function ($row) {
                    return $row->workCode ?? 'N/A';
                })
                ->addColumn('workName', function ($row) {
                    return $row->workName ?? 'N/A';
                })
                ->addColumn('workType', function ($row) {
                    return $row->workType ?? 'N/A';
                })
                ->addColumn('workDate', function ($row) {
                    return $row->workDate ?? 'N/A';
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-aworks/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        <a href="/aworks/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'workCode', 'stt', 'workDate', 'workType', 'workName', 'action'])
                ->make(true);
        }
        return view('Aworks.all_AddWorks');
    }
    public function save1(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'workCode' => 'required',
            'workName' => 'required',
            'workType' => 'required',
            'workDate' => 'required',

            'status' => 'nullable',
        ]);
        $existingCode = Work::where('workName', $request->workName)->first();

        if ($existingCode) {
            return redirect()->back()->with(['error' => 'Công việc này đã tồn tại!']);
        }

        Work::create([
            'workName' => $request->workName,
            'workType' => $request->workType,
            'workCode' => $request->workCode,
            'workDate' => $request->workDate,
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'Work',
            'details' => "Đã tạo công việc: " . $request->workName . " với mã: " . $request->workCode,
        ]);
        session()->flash('message', 'Tạo công việc thành công.');
        return redirect()->back();
    }
    public function edit1($id)
    {
        $aworks = Work::find($id);

        return view('Aworks.edit_Aworks', compact('aworks'));
    }
    public function update1(Request $request, $id)
    {
        $existingWork = Work::where('workName', $request->workName)->where('id', '!=', $id)->first();

        $existingWork = Work::where(function ($query) use ($request, $id) {
            $query->where('workName', $request->workName);
        })->where('id', '!=', $id)->first();

        if ($existingWork) {
            if ($existingWork->workName === $request->workName) {
                return redirect()->back()->with(['error' => 'Công việc này đã tồn tại!']);
            }
        }
        $Aworks = Work::find($id);
        if (!$Aworks) {
            return redirect()->back()->with('error', 'Công việc không tồn tại');
        }
        $request->validate([
            'workCode' => 'required',
            'workName' => 'required',
            'workType' => 'required',
            'workDate' => 'required',
        ]);
        $Aworks->update([
            'workName' => $request->workName,
            'workType' => $request->workType,
            'workCode' => $request->workCode,
            'workDate' => $request->workDate,
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'update',
            'model_type' => 'Work',
            'details' => "Đã cập nhật công việc: " . $Aworks->workName,
        ]);
        return redirect()->route('aworks.index')->with('message', 'Cập nhật công việc thành công');
    }
    public function destroy1($id)
    {
        $Aworks = Work::find($id);
        $Aworks->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple1(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $Aworks = Work::whereIn('id', $request->ids)->get();

        foreach ($Aworks as $g) {
            $g->status = ($g->status === 'Hoạt động') ? 'Không hoạt động' : 'Hoạt động';
            $g->save();
        }
        return response()->json(['message' => 'Thành Công']);
    }
    public function deleteMultiple1(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        $AworksToDelete = Work::whereIn('id', $request->ids)->get();
        Work::whereIn('id', $request->ids)->delete();
        foreach ($AworksToDelete as $g) {
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'delete',
                'model_type' => 'Work',
                'details' => "Đã xóa công việc: " . $g->name . " với mã: " . $g->workName,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công công việc được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
}
