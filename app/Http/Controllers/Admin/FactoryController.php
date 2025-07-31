<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class FactoryController extends Controller
{
    public function index(Request $request)
    {
        $all_factorys = Factory::all();
        if ($request->ajax()) {
            return DataTables::of($all_factorys)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('factory_code', function ($row) {
                    return $row->factory_code;
                })
                ->editColumn('factory_name', function ($row) {
                    return '<a href="/factorys/edit/' . $row->id . '">' . $row->factory_name . '</a>';
                    // return $row->factory_name;
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/factorys/edit/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        Bạn có chắc chắn có muốn xóa thông tin ' . ($row->factory_name ?? 'N/A') . '?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/factorys/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'stt', 'factory_name', 'factory_code', 'status', 'action'])
                ->make(true);
        }
        return view('factorys.all_factorys');
    }
    public function save(Request $request)
    {
        $request->validate([
            'factory_code' => 'required',
            'factory_name' => 'required',
            'status' => 'nullable',
        ]);
        $existingFac = Factory::where('factory_name', $request->factory_name)->first();

        $existingFacCode = Factory::where('factory_code', $request->factory_code)->first();
        if ($existingFacCode) {
            return redirect()->back()->with(['error' => 'Mã nhà máy này đã tồn tại!']);
        }
        if ($existingFac) {
            return redirect()->back()->with(['error' => 'Tên nhà máy này đã tồn tại!']);
        }

        Factory::create([
            'factory_code' => $request->factory_code,
            'factory_name' => $request->factory_name,
            'status' => $request->status ?? 'Hoạt động',
        ]);
        session()->flash('message', 'Tạo nhà máy thành công.');
        return redirect()->back();
    }
    public function edit($id)
    {
        $factorys = Factory::find($id);
        return view('factorys.edit_factorys', compact('factorys'));
    }
    public function update(Request $request, $id)
    {

        $existingFac = Factory::where(function ($query) use ($request, $id) {
            $query->where('factory_name', $request->factory_name)
                ->orWhere('factory_code', $request->factory_code);
        })->where('id', '!=', $id)->first();

        if ($existingFac) {
            if ($existingFac->factory_name === $request->factory_name) {
                return redirect()->back()->with(['error' => 'Tên nhà máy này đã tồn tại!']);
            }
            if ($existingFac->factory_code === $request->factory_code) {
                return redirect()->back()->with(['error' => 'Mã nhà máy này đã tồn tại!']);
            }
        }
        $factoryss = Factory::find($id);

        if (!$factoryss) {
            return redirect()->back()->with('error', 'Nhà máy không tồn tại');
        }
        $request->validate([
            'factory_code' => 'nullable',
            'factory_name' => 'nullable',
            'status' => 'nullable',
        ]);
        $factoryss->update([
            'factory_code' => $request->factory_code,
            'factory_name' => $request->factory_name,
            'status' => $request->status,
        ]);
        return redirect()->route('factorys.index')->with('message', 'Cập nhật Xe thành công');
    }
    public function destroy($id)
    {
        $factorys = Factory::find($id);
        $factorys->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $factorys = Factory::whereIn('id', $request->ids)->get();

        foreach ($factorys as $val) {
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
        Factory::whereIn('id', $request->ids)->delete();

        return response()->json([
            'message' => 'Xóa thành công các nhà máy được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }

    public function toggleStatus(Request $request)
    {
        $factorys = Factory::find($request->id);
        if ($factorys) {
            $factorys->status = $factorys->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $factorys->save();
            return response()->json(['success' => true, 'status' => $factorys->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
