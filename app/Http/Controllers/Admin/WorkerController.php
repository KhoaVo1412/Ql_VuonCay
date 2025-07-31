<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Duty;
use App\Models\Team;
use App\Models\Worker;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class WorkerController extends Controller
{
    public function index(Request $request)
    {
        $duties = Duty::all();
        $teams = Team::all();
        $all_workers = Worker::with('team', 'duty')->orderBy('id', 'desc')->get();
        // dd($all_workers);
        if ($request->ajax()) {
            return DataTables::of($all_workers)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('image', function ($row) {
                    if ($row->image) {
                        return '<img src="' . asset($row->image) . '" alt="Worker Image" width="50" height="50">';
                    } else {
                        return 'No Image';
                    }
                })
                ->editColumn('code_name', function ($row) {
                    return $row->code_name ?? 'N/A';
                })
                ->editColumn('bdate', function ($row) {
                    return $row->bdate ? Carbon::parse($row->bdate)->format('d-m-Y') : 'N/A';
                })
                ->editColumn('name', function ($row) {
                    return  $row->name;
                })
                ->editColumn('teamName', function ($row) {
                    return $row->team->name ?? 'N/A';
                })
                ->addColumn('dutyName', function ($row) {
                    return $row->duty->dutyName ?? 'N/A';
                })
                ->addColumn('gender', function ($row) {
                    return $row->gender == 0 ? 'Nam' : 'Nữ';
                })
                ->editColumn('phone', function ($row) {
                    return $row->phone ?? 'N/A';
                })
                ->editColumn('status', function ($row) {
                    return $row->status ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                    <div class="d-flex gap-1">
                        <a href="' . route('workers.edit', $row->id) . '" class="btn btn-sm btn-primary">
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
                                    <a href="' . route('workers.delete', $row->id) . '" class="btn btn-primary">Xóa</a>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
                    return $action;
                })
                ->rawColumns(['check', 'image', 'dutyName', 'stt', 'code_name', 'name', 'team', 'teamName', 'gender', 'bdate', 'phone', 'action'])
                ->make(true);
        }
        return view('workers.all_workers', compact('duties', 'teams'));
        // return view('workers.sanluong', compact('duty', 'teams'));
    }
    public function add(Request $request)
    {
        $duties = Duty::all();
        $teams = Team::all();
        $workers = Worker::with('team', 'duty')->orderBy('id', 'desc')->get();
        return view('workers.add_workers', compact('duties', 'teams', 'workers'));
    }
    public function save(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'image' => 'nullable|image|max:2048',
            'code_name' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'bdate' => 'required|date',
            'cccd' => 'nullable|numeric|digits_between:9,12',
            'address' => 'required|string',
            'team_id' => 'required|exists:teams,id',
            'duty_id' => 'nullable|exists:duties,id',
            'gender' => 'required|in:0,1',
            'phone' => 'required|regex:/^\d{10,11}$/',
            'status' => 'nullable|string',
        ]);
        $existingCode = Worker::where('name', $request->name)->first();

        if ($existingCode) {
            return redirect()->back()->with(['error' => 'Mã công nhân này đã tồn tại!']);
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('/image_workers'), $imageName);
            $imagePath = 'image_workers/' . $imageName;
        }
        Worker::create([
            'name' => $request->name,
            'code_name' => $request->code_name,
            'bdate' => $request->bdate,
            'cccd' => $request->cccd,
            'address' => $request->address,
            'team_id' => $request->team_id,
            'duty_id' => $request->duty_id,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'image' => $imagePath,
            'status' => $request->status ?? 'Hoạt động',
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'Worker',
            'details' => "Đã tạo công nhân: " . $request->workers_name . " với mã: " . $request->workers_code,
        ]);
        session()->flash('message', 'Tạo công nhân thành công.');
        return redirect()->back();
    }
    public function edit($id)
    {
        $duties = Duty::all();
        $teams = Team::all();
        $workers = Worker::find($id);
        return view('workers.edit_workers', compact('workers', 'duties', 'teams'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'code_name' => 'required|string',
            'bdate' => 'required|date',
            'cccd' => 'nullable|digits_between:9,12',
            'address' => 'required|string',
            'team_id' => 'required',
            'duty_id' => 'required',
            'gender' => 'required|in:0,1',
            'phone' => 'required|regex:/^\d{10,11}$/',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $worker = Worker::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($worker->image) {
                $oldImagePath = public_path('storage/' . $worker->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $imagePath = 'image_workers/' . $imageName;
            $request->file('image')->move(public_path('/image_workers'), $imageName);

            $worker->image = $imagePath;
        }
        $worker->update([
            'name' => $request->name,
            'code_name' => $request->code_name,
            'bdate' => $request->bdate,
            'cccd' => $request->cccd,
            'address' => $request->address,
            'team_id' => $request->team_id,
            'duty_id' => $request->duty_id,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'status' => $request->status ?? 'Hoạt động',
        ]);

        return redirect()->route('workers.index')->with('message', 'Công Nhân đã được cập nhật!');
    }

    public function destroy($id)
    {
        $workers = Worker::find($id);
        $workers->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $workers = Worker::whereIn('id', $request->ids)->get();

        foreach ($workers as $worker) {
            $worker->status = ($worker->status === 'Hoạt động') ? 'Không hoạt động' : 'Hoạt động';
            $worker->save();
        }
        return response()->json(['message' => 'Thành Công']);
    }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        $workersToDelete = Worker::whereIn('id', $request->ids)->get();

        Worker::whereIn('id', $request->ids)->delete();

        foreach ($workersToDelete as $worker) {
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'delete',
                'model_type' => 'Worker',
                'details' => "Đã xóa công nhân: " . $worker->workers_name . " với mã: " . $worker->workers_code,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các công nhân được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $worker = Worker::find($request->id);
        if ($worker) {
            $worker->status = $worker->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $worker->save();
            return response()->json(['success' => true, 'status' => $worker->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
