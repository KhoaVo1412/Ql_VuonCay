<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Duty;
use App\Models\Team;
use App\Models\User;
use App\Models\Worker;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class WorkerController extends Controller
{
    public function index(Request $request)
    {
        $duties = Duty::all();
        $teams = Team::all();
        // $all_workers = Worker::with('team', 'duty')->orderBy('id', 'desc')->get();
        // dd($all_workers);
        if ($request->ajax()) {
            $all_workers = Worker::with(['team:id,name', 'duty:id,dutyName'])
                ->when(
                    $request->filled('team_id'),
                    fn($q) =>
                    $q->where('team_id', $request->team_id)
                )
                ->when(
                    $request->filled('duty_id'),
                    fn($q) =>
                    $q->where('duty_id', $request->duty_id)
                )
                ->orderByDesc('id');
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
                        return 'Không có ảnh';
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
        $roles = Role::pluck('name', 'name');
        $workers = Worker::with('team', 'duty')->orderBy('id', 'desc')->get();
        return view('workers.add_workers', compact('duties', 'teams', 'workers', 'roles'));
    }
    public function save(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'email' => 'nullable|email',
            'image' => 'nullable|image|max:2048',
            'code_name' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'bdate' => 'required|date',
            'cccd' => 'nullable|numeric|digits_between:9,12',
            'address' => 'required|string',
            'team_id' => 'required',
            'duty_id' => 'nullable',
            'gender' => 'required',
            'phone' => 'required|regex:/^\d{10,11}$/',
            'status' => 'nullable|string',
        ]);
        $existingCode = Worker::where('code_name', $request->code_name)->first();

        if ($existingCode) {
            return redirect()->back()->with(['error' => 'Mã công nhân này đã tồn tại!']);
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('/image_workers'), $imageName);
            $imagePath = 'image_workers/' . $imageName;
        }
        $result = DB::transaction(function () use ($request, $imagePath) {
            $rawPassword = $request->password;
            $user = User::create([
                'name'     => $request->name,
                'email' => $request->email,
                'phone'    => $request->phone ?? null,
                'password' => Hash::make($rawPassword ?? '123'),
            ]);

            $user->assignRole($request->role);

            $worker = Worker::create([
                'name'      => $request->name,
                'email'      => $request->email,
                'code_name' => $request->code_name,
                'bdate'     => $request->bdate,
                'cccd'      => $request->cccd,
                'address'   => $request->address,
                'team_id'   => $request->team_id,
                'duty_id'   => $request->duty_id,
                'gender'    => $request->gender,
                'phone'     => $request->phone,
                'image'     => $imagePath,
                'status'    => $request->status ?? 'Đang làm việc',
                'user_id' => $user->id,
            ]);

            return [$user, $worker, $rawPassword];
        });


        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'Worker',
            'details' => "Đã tạo công nhân: " . $request->workers_name . " với mã: " . $request->workers_code,
        ]);
        return redirect()->route('workers.index')->with('message', 'Tạo công nhân thành công');
    }
    public function edit($id)
    {
        $duties = Duty::all();
        $teams = Team::all();
        $roles = Role::pluck('name', 'name');
        $workers = Worker::find($id);
        $user = $workers->user;
        return view('workers.edit_workers', compact('user', 'roles', 'workers', 'duties', 'teams'));
    }
    public function update(Request $request, $id)
    {
        $worker = Worker::findOrFail($id);
        $user   = $worker->user;

        $request->validate([
            'email' => 'nullable|email',
            'image' => 'nullable|image|max:2048',
            'code_name' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'bdate' => 'required|date',
            'cccd' => 'nullable|numeric|digits_between:9,12',
            'address' => 'required|string',
            'team_id' => 'required',
            'duty_id' => 'nullable',
            'gender' => 'required',
            'phone' => 'required',
            'status' => 'nullable|string',
            'role'      => 'nullable',
        ]);
        $imagePath = $worker->image;
        if ($request->hasFile('image')) {
            if ($imagePath && File::exists(public_path($imagePath))) {
                File::delete(public_path($imagePath));
            }
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('/image_workers'), $imageName);
            $imagePath = 'image_workers/' . $imageName;
        }

        DB::transaction(function () use ($request, $worker, $user, $imagePath) {
            if ($user) {
                $user->name  = $request->name;
                $user->email = $request->email;
                $user->save();

                if ($request->filled('role')) {
                    $user->syncRoles([$request->role]);
                }
            }

            $worker->update([
                'name'      => $request->name,
                'email'     => $request->email,
                'code_name' => $request->code_name,
                'bdate'     => $request->bdate,
                'cccd'      => $request->cccd,
                'address'   => $request->address,
                'team_id'   => $request->team_id,
                'duty_id'   => $request->duty_id,
                'gender'    => $request->gender,
                'phone'     => $request->phone,
                'image'     => $imagePath,
                'status'    => $request->status,
            ]);
        });
        ActionHistory::create([
            'user_id'     => Auth::id(),
            'action_type' => 'update',
            'model_type'  => 'Worker',
            'details'     => "Đã cập nhật công nhân: {$worker->name} (mã: {$worker->code_name})",
        ]);

        return redirect()->route('workers.index')->with('message', 'Cập nhật công nhân thành công');
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
            $worker->status = ($worker->status === 'Đang làm việc') ? 'Không hoạt động' : 'Đang làm việc';
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
            $worker->status = $worker->status == 'Đang làm việc' ? 'Nghỉ việc' : 'Đang làm việc';
            $worker->save();
            return response()->json(['success' => true, 'status' => $worker->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
