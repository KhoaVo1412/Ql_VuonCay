<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $all_teams = Team::orderBy('id', 'desc')->get();
        // dd($all_teams);
        if ($request->ajax()) {
            return DataTables::of($all_teams)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->addColumn('name', function ($row) {
                    return $row->name ?? 'N/A';
                })
                ->addColumn('teamName', function ($row) {
                    return $row->teamName ?? 'N/A';
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-teams/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        <a href="/teams/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'code', 'stt', 'name', 'teamName', 'status', 'action'])
                ->make(true);
        }
        return view('teams.all_teams');
    }
    public function save(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'teamName' => 'nullable',
            // 'teamArea' => 'required',
            // 'teamArea' => 'required',
            // 'plotCount' => 'required',
            'status' => 'nullable',
        ]);
        // $existingTeam = Team::where('farm_name', $request->farm_name)->first();
        $existingCode = Team::where('name', $request->name)->first();

        if ($existingCode) {
            return redirect()->back()->with(['error' => 'Tên tổ này đã tồn tại!']);
        }

        Team::create([
            'name' => $request->name,
            'teamName' => $request->teamName,
            'status' => $request->status ?? 'Hoạt động',
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'Team',
            'details' => "Đã tạo tổ: " . $request->name . " với mã: " . $request->teamName,
        ]);
        session()->flash('message', 'Tạo tổ thành công.');
        return redirect()->back();
    }
    public function edit($id)
    {
        $teams = Team::find($id);

        return view('teams.edit_teams', compact('teams'));
    }
    public function update(Request $request, $id)
    {
        $existingTeam = Team::where('name', $request->teamName)->where('id', '!=', $id)->first();

        // if ($existingTeam) {
        //     return redirect()->back()->with(['error' => 'Tên tổ này đã tồn tại!']);
        // }

        $existingTeam = Team::where(function ($query) use ($request, $id) {
            $query->where('name', $request->name);
            // $query->where('farm_name', $request->farm_name)
            //     ->orWhere('farm_code', $request->farm_code);
        })->where('id', '!=', $id)->first();

        if ($existingTeam) {
            // if ($existingTeam->farm_name === $request->farm_name) {
            //     return redirect()->back()->with(['error' => 'Tên tổ này đã tồn tại!']);
            // }
            if ($existingTeam->farm_code === $request->farm_code) {
                return redirect()->back()->with(['error' => 'Tên tổ này đã tồn tại!']);
            }
        }
        $teams = Team::find($id);
        if (!$teams) {
            return redirect()->back()->with('error', 'Tên tổ không tồn tại');
        }
        $request->validate([
            'teamName' => 'nullable',
            'name' => 'nullable',
            'status' => 'nullable',
        ]);
        $teams->update([
            'teamName' => $request->teamName,
            'name' => $request->team,
            'status' => $request->status,
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'update',
            'model_type' => 'Team',
            'details' => "Đã cập nhật tổ: " . $teams->name,
        ]);
        return redirect()->route('farms.index')->with('message', 'Cập nhật tổ thành công');
    }
    public function destroy($id)
    {
        $teams = Team::find($id);
        $teams->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $teams = Team::whereIn('id', $request->ids)->get();

        foreach ($teams as $g) {
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
        $teamsToDelete = Team::whereIn('id', $request->ids)->get();
        Team::whereIn('id', $request->ids)->delete();
        foreach ($teamsToDelete as $g) {
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'delete',
                'model_type' => 'Team',
                'details' => "Đã xóa tổ: " . $g->name . " với mã: " . $g->teamName,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công tổ được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $teams = Team::find($request->id);
        if ($teams) {
            $teams->status = $teams->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $teams->save();
            return response()->json(['success' => true, 'status' => $teams->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
