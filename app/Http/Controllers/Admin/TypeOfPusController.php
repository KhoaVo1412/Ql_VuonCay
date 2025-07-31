<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypeOfPus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class TypeOfPusController extends Controller
{
    public function index(Request $request)
    {
        $all_typeofpus = TypeOfPus::orderBy('id', 'desc')->get();
        if ($request->ajax()) {
            return DataTables::of($all_typeofpus)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('code_pus', function ($row) {
                    return $row->code_pus;
                })
                ->editColumn('name_pus', function ($row) {
                    return '<a href="/typeofpus/edit/' . $row->id . '">' . $row->name_pus . '</a>';
                    // return $row->name_pus;
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/typeofpus/edit/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        Bạn có chắc chắn có muốn xóa thông tin ' . ($row->name_pus ?? 'N/A') . '?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/typeofpus/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'stt', 'name_pus', 'code_pus', 'status', 'action'])
                ->make(true);
        }
        return view('typeofpus.all_typeofpus');
    }
    public function save(Request $request)
    {
        $request->validate([
            'code_pus' => 'required',
            'name_pus' => 'required',
            'status' => 'nullable',
        ]);
        $existingPus = TypeOfPus::where('name_pus', $request->name_pus)->first();

        if ($existingPus) {
            return redirect()->back()->with(['error' => 'Tên mủ này đã tồn tại!']);
        }
        $existingPusCode = TypeOfPus::where('code_pus', $request->code_pus)->first();

        if ($existingPusCode) {
            return redirect()->back()->with(['error' => 'Mã mủ này đã tồn tại!']);
        }
        TypeOfPus::create([
            'code_pus' => $request->code_pus,
            'name_pus' => $request->name_pus,
            'status' => $request->status ?? 'Hoạt động',
        ]);
        session()->flash('message', 'Tạo Mủ thành công.');
        return redirect()->back();
    }
    public function edit($id)
    {
        $typeofpus = TypeOfPus::find($id);
        return view('typeofpus.edit_typeofpus', compact('typeofpus'));
    }
    public function update(Request $request, $id)
    {
        $existingPus = TypeOfPus::where(function ($query) use ($request, $id) {
            $query->where('name_pus', $request->name_pus)
                ->orWhere('code_pus', $request->code_pus);
        })->where('id', '!=', $id)->first();

        if ($existingPus) {
            if ($existingPus->name_pus === $request->name_pus) {
                return redirect()->back()->with(['error' => 'Tên mủ này đã tồn tại!']);
            }
            if ($existingPus->code_pus === $request->code_pus) {
                return redirect()->back()->with(['error' => 'Mã mủ này đã tồn tại!']);
            }
        }
        $TypeOfPuss = TypeOfPus::find($id);
        if (!$TypeOfPuss) {
            return redirect()->back()->with('error', 'Mủ không tồn tại');
        }
        $request->validate([
            'code_pus' => 'nullable',
            'name_pus' => 'nullable',
            'status' => 'nullable',
        ]);
        $TypeOfPuss->update([
            'code_pus' => $request->code_pus,
            'name_pus' => $request->name_pus,
            // 'status' => $request->status,
        ]);
        return redirect()->route('typeofpus.index')->with('message', 'Cập nhật mủ thành công.');
    }
    public function destroy($id)
    {
        $typeofpus = TypeOfPus::find($id);
        $typeofpus->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $typeofpus = TypeOfPus::whereIn('id', $request->ids)->get();

        foreach ($typeofpus as $val) {
            $val->status = ($val->status === 'Hoạt động') ? 'Không hoạt động' : 'Hoạt động';
            $val->save();
        }
        return response()->json(['message' => 'Thành Công']);
    }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        TypeOfPus::whereIn('id', $request->ids)->delete();

        return response()->json([
            'message' => 'Xóa thành công các loại mủ được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }

    public function toggleStatus(Request $request)
    {
        $typeofpus = TypeOfPus::find($request->id);
        if ($typeofpus) {
            $typeofpus->status = $typeofpus->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $typeofpus->save();
            return response()->json(['success' => true, 'status' => $typeofpus->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
