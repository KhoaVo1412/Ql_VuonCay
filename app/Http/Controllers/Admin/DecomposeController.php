<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DecomposeController extends Controller
{
    public function index(Request $request)
    {
        // $decomposes = Work::all();
        // $gardens = Garden::with('plots')->get();
        // $workers = Worker::all();
        // $plots = Plot::with('garden')->get();
        // $all_gentask = GenTask::with('work', 'worker')->orderBy('id', 'desc')->get();
        // // dd($all_gentask);
        // if ($request->ajax()) {
        //     return DataTables::of($all_gentask)
        //         ->addColumn('check', function ($row) {
        //             return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
        //         })
        //         ->addColumn('stt', function ($row) {
        //             static $stt = 0;
        //             $stt++;
        //             return $stt;
        //         })
        //         ->editColumn('decomposes', function ($row) {
        //             return $row->id;
        //         })
        //         ->addColumn('workName', function ($row) {
        //             return $row->work->workName ?? 'Không rõ';
        //         })
        //         ->addColumn('workType', function ($row) {
        //             return $row->work->workType ?? 'Không rõ';
        //         })
        //         ->editColumn('workDate', function ($row) {
        //             return $row->workDate ?? 'Không rõ';
        //         })
        //         ->editColumn('status', function ($row) {
        //             $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
        //             $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
        //             return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
        //         })
        //         ->addColumn('action', function ($row) {
        //             $action = '
        //                 <div class="d-flex gap-1">
        //                     <a href="/decomposes/edit/' . $row->id . '" class="btn btn-sm btn-primary">
        //                         <i class="fas fa-edit"></i>
        //                     </a>
        //                     <a class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal' . $row->id . '">
        //                         <i class="fas fa-trash-alt"></i>
        //                     </a>
        //                 </div>
        //                 <div class="modal fade" id="deleteModal' . $row->id . '" tabindex="-1" aria-labelledby="deleteModalLabel' . $row->id . '" aria-hidden="true">
        //                     <div class="modal-dialog">
        //                         <div class="modal-content">
        //                             <div class="modal-header">
        //                                 <h5 class="modal-title" id="deleteModalLabel' . $row->id . '">Xác Nhận Xóa</h5>
        //                                 <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        //                             </div>
        //                             <div class="modal-body">
        //                                 Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->farm_name ?? 'N/A') . '</span>?
        //                             </div>
        //                             <div class="modal-footer">
        //                                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        //                                 <a href="/decomposes/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
        //                             </div>
        //                         </div>
        //                     </div>
        //                 </div>
        //             ';
        //             return $action;
        //         })
        //         ->rawColumns(['check', 'decomposes', 'stt', 'workDate', 'workName', 'workType', 'status', 'action'])
        //         ->make(true);
        // }
        // return view('decomposes.all_decomposes', compact('gardens', 'workers', 'decomposes', 'plots'));
        return view('decomposes.all_decomposes');
    }
    public function add(Request $request)
    {
        return view('decomposes.add_decomposes');
    }
    public function save(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'farm_code' => 'required',
            'farm_name' => 'required',
            'unit_id' => 'required|exists:units,id',
            'status' => 'nullable',
        ]);
        // $existingFarm = Farm::where('farm_name', $request->farm_name)->first();
        $existingCode = Farm::where('farm_code', $request->farm_code)->first();

        if ($existingCode) {
            return redirect()->back()->with(['error' => 'Mã nông trường này đã tồn tại!']);
        }
        // if ($existingFarm) {
        //     return redirect()->back()->with(['error' => 'Tên nông trường này đã tồn tại!']);
        // }

        // $farmNameSlug = Str::slug($request->farm_name, '_');
        // $prefix = '#' . $farmNameSlug . '_';
        // do {
        //     $randomCode = $prefix . rand(100, 999);
        // } while (Farm::where('farm_code', $randomCode)->exists());
        Farm::create([
            'farm_code' => $request->farm_code,
            'farm_name' => $request->farm_name,
            'unit_id' => $request->unit_id,
            'status' => $request->status ?? 'Hoạt động',
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'Farm',
            'details' => "Đã tạo nông trường: " . $request->farm_name . " với mã: " . $request->farm_code,
        ]);
        session()->flash('message', 'Tạo nông trường thành công.');
        return redirect()->back();
    }
    public function edit($id)
    {
        $farms = Farm::find($id);
        $units = Units::all();
        return view('decomposes.all_decomposes', compact('farms', 'units'));
    }
    public function update(Request $request, $id)
    {
        $existingFarm = Farm::where('farm_name', $request->farm_name)->where('id', '!=', $id)->first();

        // if ($existingFarm) {
        //     return redirect()->back()->with(['error' => 'Tên nông trường này đã tồn tại!']);
        // }

        $existingFarm = Farm::where(function ($query) use ($request, $id) {
            $query->where('farm_code', $request->farm_code);
            // $query->where('farm_name', $request->farm_name)
            //     ->orWhere('farm_code', $request->farm_code);
        })->where('id', '!=', $id)->first();

        if ($existingFarm) {
            // if ($existingFarm->farm_name === $request->farm_name) {
            //     return redirect()->back()->with(['error' => 'Tên nông trường này đã tồn tại!']);
            // }
            if ($existingFarm->farm_code === $request->farm_code) {
                return redirect()->back()->with(['error' => 'Mã nông trường này đã tồn tại!']);
            }
        }
        $farms = Farm::find($id);
        if (!$farms) {
            return redirect()->back()->with('error', 'Nông trường không tồn tại');
        }
        $request->validate([
            'farm_name' => 'nullable',
            'farm_code' => 'nullable',
            'unit_id' => 'nullable',
        ]);
        $farms->update([
            'farm_code' => $request->farm_code,
            'farm_name' => $request->farm_name,
            'unit_id' => $request->unit_id,
            'status' => $request->status,
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'update',
            'model_type' => 'Farm',
            'details' => "Đã cập nhật nông trường: " . $farms->farm_name,
        ]);
        return redirect()->route('farms.index')->with('message', 'Cập nhật nông trường thành công');
    }
    public function destroy($id)
    {
        $farms = Farm::find($id);
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

        $farms = Farm::whereIn('id', $request->ids)->get();

        foreach ($farms as $farm) {
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
        $farmsToDelete = Farm::whereIn('id', $request->ids)->get();

        Farm::whereIn('id', $request->ids)->delete();

        foreach ($farmsToDelete as $farm) {
            ActionHistory::create([
                'user_id' => Auth::id(),  // ID của người thực hiện hành động
                'action_type' => 'delete',  // Loại hành động "delete"
                'model_type' => 'Farm',  // Model "Farm"
                'details' => "Đã xóa nông trường: " . $farm->farm_name . " với mã: " . $farm->farm_code,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các nông trường được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $farm = Farm::find($request->id);
        if ($farm) {
            $farm->status = $farm->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $farm->save();
            return response()->json(['success' => true, 'status' => $farm->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
