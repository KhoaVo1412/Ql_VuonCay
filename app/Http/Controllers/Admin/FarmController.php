<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Garden;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class FarmController extends Controller
{
    public function index(Request $request)
    {
        $all_garden = Garden::orderBy('id', 'desc')->get();
        // dd($all_garden);
        if ($request->ajax()) {
            return DataTables::of($all_garden)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('gardenName', function ($row) {
                    return $row->gardenName;
                })
                ->editColumn('code', function ($row) {
                    return $row->code;
                })
                // ->editColumn('farm_name', function ($row) {
                //     return '<a href="/farms/edit/' . $row->id . '">' . $row->farm_name . '</a>';
                // })
                ->addColumn('gardenArea', function ($row) {
                    return $row->gardenArea;
                })
                ->addColumn('plotCount', function ($row) {
                    return $row->plotCount;
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-farms/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        <a href="/farms/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'code', 'stt', 'gardenArea', 'gardenName', 'plotCount', 'status', 'action'])
                ->make(true);
        }
        return view('farms.all_farms');
    }
    public function save(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'gardenName' => 'required',
            'code' => 'required',
            // 'gardenArea' => 'required',
            // 'gardenArea' => 'required',
            // 'plotCount' => 'required',
            'status' => 'nullable',
        ]);
        // $existingGarden = Garden::where('farm_name', $request->farm_name)->first();
        $existingCode = Garden::where('gardenName', $request->gardenName)->first();

        if ($existingCode) {
            return redirect()->back()->with(['error' => 'Mã vườn. này đã tồn tại!']);
        }
        // if ($existingGarden) {
        //     return redirect()->back()->with(['error' => 'Tên vườn. này đã tồn tại!']);
        // }

        // $farmNameSlug = Str::slug($request->farm_name, '_');
        // $prefix = '#' . $farmNameSlug . '_';
        // do {
        //     $randomCode = $prefix . rand(100, 999);
        // } while (Garden::where('farm_code', $randomCode)->exists());
        Garden::create([
            'gardenName' => $request->gardenName,
            'code' => $request->code,
            // 'gardenArea' => $request->gardenArea,
            // 'plotCount' => $request->plotCount,
            'status' => $request->status ?? 'Hoạt động',
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'Garden',
            'details' => "Đã tạo vườn.: " . $request->gardenName . " với mã: " . $request->code,
        ]);
        session()->flash('message', 'Tạo vườn. thành công.');
        return redirect()->back();
    }
    public function edit($id)
    {
        $gardens = Garden::find($id);
        return view('farms.edit_farms', compact('gardens'));
    }
    public function update(Request $request, $id)
    {
        $existingGarden = Garden::where('gardenName', $request->gardenName)->where('id', '!=', $id)->first();

        // if ($existingGarden) {
        //     return redirect()->back()->with(['error' => 'Tên vườn. này đã tồn tại!']);
        // }

        $existingGarden = Garden::where(function ($query) use ($request, $id) {
            $query->where('gardenName', $request->gardenName);
            // $query->where('farm_name', $request->farm_name)
            //     ->orWhere('farm_code', $request->farm_code);
        })->where('id', '!=', $id)->first();

        if ($existingGarden) {
            // if ($existingGarden->farm_name === $request->farm_name) {
            //     return redirect()->back()->with(['error' => 'Tên vườn. này đã tồn tại!']);
            // }
            if ($existingGarden->farm_code === $request->farm_code) {
                return redirect()->back()->with(['error' => 'Mã vườn. này đã tồn tại!']);
            }
        }
        $gardens = Garden::find($id);
        if (!$gardens) {
            return redirect()->back()->with('error', 'Nông trường không tồn tại');
        }
        $request->validate([
            'gardenName' => 'nullable',
            'gardenArea' => 'nullable',
            'plotCount' => 'nullable',
            'status' => 'nullable',
        ]);
        $gardens->update([
            'gardenName' => $request->gardenName,
            'gardenArea' => $request->gardenArea,
            'plotCount' => $request->plotCount,
            'status' => $request->status,
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'update',
            'model_type' => 'Garden',
            'details' => "Đã cập nhật vườn: " . $gardens->gardenName,
        ]);
        return redirect()->route('farms.index')->with('message', 'Cập nhật vườn thành công');
    }
    public function destroy($id)
    {
        $gardens = Garden::find($id);
        $gardens->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $gardens = Garden::whereIn('id', $request->ids)->get();

        foreach ($gardens as $g) {
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
        $gardensToDelete = Garden::whereIn('id', $request->ids)->get();

        Garden::whereIn('id', $request->ids)->delete();

        foreach ($gardensToDelete as $g) {
            ActionHistory::create([
                'user_id' => Auth::id(),  // ID của người thực hiện hành động
                'action_type' => 'delete',  // Loại hành động "delete"
                'model_type' => 'Garden',  // Model "Garden"
                'details' => "Đã xóa vườn.: " . $g->gardenName . " với mã: " . $g->farm_code,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các vườn. được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $gardens = Garden::find($request->id);
        if ($gardens) {
            $gardens->status = $gardens->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $gardens->save();
            return response()->json(['success' => true, 'status' => $gardens->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
