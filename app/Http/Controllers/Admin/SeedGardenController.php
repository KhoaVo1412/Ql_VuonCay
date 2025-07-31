<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Variety;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class SeedGardenController extends Controller
{
    public function index(Request $request)
    {
        $all_seedgardens = Variety::orderBy('id', 'desc')->get();
        if ($request->ajax()) {
            return DataTables::of($all_seedgardens)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('varietyName', function ($row) {
                    return $row->varietyName;
                })
                ->editColumn('origin', function ($row) {
                    return  $row->origin;
                })
                ->addColumn('desc', function ($row) {
                    return $row->desc;
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-seedgardens/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->varietyName ?? 'N/A') . '</span>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/seedgardens/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'stt', 'varietyName', 'origin', 'desc', 'status', 'action'])
                ->make(true);
        }
        return view('seedgardens.all_seedgardens');
    }
    public function save(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'varietyName' => 'required',
            'origin' => 'required',
            'desc' => 'required',
            'status' => 'nullable',
        ]);
        $existingCode = Variety::where('varietyName', $request->varietyName)->first();

        if ($existingCode) {
            return redirect()->back()->with(['error' => 'Giống này đã tồn tại!']);
        }

        Variety::create([
            'origin' => $request->origin,
            'varietyName' => $request->varietyName,
            'desc' => $request->desc,
            'status' => $request->status ?? 'Hoạt động',
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'Variety',
            'details' => "Đã tạo giống: " . $request->varietyName,
        ]);
        session()->flash('message', 'Tạo giống thành công.');
        return redirect()->back();
    }
    public function edit($id)
    {
        $variety = Variety::find($id);
        return view('seedgardens.edit_seedgardens', compact('variety'));
    }
    public function update(Request $request, $id)
    {
        $existingVariety = Variety::where('varietyName', $request->varietyName)->where('id', '!=', $id)->first();


        $existingVariety = Variety::where(function ($query) use ($request, $id) {
            $query->where('varietyName', $request->varietyName);
        })->where('id', '!=', $id)->first();

        if ($existingVariety) {
            if ($existingVariety->varietyName === $request->varietyName) {
                return redirect()->back()->with(['error' => 'Giống này đã tồn tại!']);
            }
        }
        $seedgardens = Variety::find($id);
        if (!$seedgardens) {
            return redirect()->back()->with('error', 'Giống không tồn tại');
        }
        $request->validate([
            'varietyName' => 'required',
            'origin' => 'required',
            'desc' => 'required',
            'status' => 'nullable',
        ]);
        $seedgardens->update([
            'origin' => $request->origin,
            'varietyName' => $request->varietyName,
            'desc' => $request->desc,
            'status' => $request->status,
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'update',
            'model_type' => 'Variety',
            'details' => "Đã cập nhật giống: " . $seedgardens->varietyName,
        ]);
        return redirect()->route('seedgardens.index')->with('message', 'Cập nhật giống thành công');
    }
    public function destroy($id)
    {
        $seedgardens = Variety::find($id);
        $seedgardens->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $seedgardens = Variety::whereIn('id', $request->ids)->get();

        foreach ($seedgardens as $s) {
            $s->status = ($s->status === 'Hoạt động') ? 'Không hoạt động' : 'Hoạt động';
            $s->save();
        }
        return response()->json(['message' => 'Thành Công']);
    }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        $seedgardensToDelete = Variety::whereIn('id', $request->ids)->get();

        Variety::whereIn('id', $request->ids)->delete();

        foreach ($seedgardensToDelete as $s) {
            ActionHistory::create([
                'user_id' => Auth::id(),  // ID của người thực hiện hành động
                'action_type' => 'delete',  // Loại hành động "delete"
                'model_type' => 'Variety',  // Model "Variety"
                'details' => "Đã xóa giống: " . $s->varietyName,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các giống được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $s = Variety::find($request->id);
        if ($s) {
            $s->status = $s->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $s->save();
            return response()->json(['success' => true, 'status' => $s->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
