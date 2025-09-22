<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActionHistory;
use App\Models\Diseases;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class DiesaseTController extends Controller
{
    public function index(Request $request)
    {
        $all_disease = Diseases::orderBy('id', 'desc')->get();
        if ($request->ajax()) {
            return DataTables::of($all_disease)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('diseaseName', function ($row) {
                    return $row->diseaseName;
                })
                ->editColumn('desc', function ($row) {
                    return  $row->desc;
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-diseaseT/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->diseaseName ?? 'N/A') . '</span>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/diseaseT/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'stt', 'diseaseName', 'desc', 'status', 'action'])
                ->make(true);
        }
        return view('diseasetype.all_diseaseT');
    }
    public function save(Request $request)
    {
        $request->validate([
            'diseaseName' => 'required',
            'desc' => 'required',
            'status' => 'nullable',
        ]);
        $existingCode = Diseases::where('diseaseName', $request->diseaseName)->first();

        if ($existingCode) {
            return redirect()->back()->with(['error' => 'loại bệnh này đã tồn tại!']);
        }

        Diseases::create([
            'diseaseName' => $request->diseaseName,
            'desc' => $request->desc,
            'status' => $request->status ?? 'Hoạt động',
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'Diseases',
            'details' => "Đã tạo loại bệnh: " . $request->diseaseName,
        ]);
        session()->flash('message', 'Tạo loại bệnh thành công.');
        return redirect()->back();
    }
    public function edit($id)
    {
        $diseases = Diseases::find($id);
        return view('diseasetype.edit_diseaseT', compact('diseases'));
    }
    public function update(Request $request, $id)
    {
        $existingDiseases = Diseases::where('diseaseName', $request->diseaseName)->where('id', '!=', $id)->first();


        $existingDiseases = Diseases::where(function ($query) use ($request, $id) {
            $query->where('diseaseName', $request->diseaseName);
        })->where('id', '!=', $id)->first();

        if ($existingDiseases) {
            if ($existingDiseases->diseaseName === $request->diseaseName) {
                return redirect()->back()->with(['error' => 'Loại bệnh này đã tồn tại!']);
            }
        }
        $diseaseT = Diseases::find($id);
        if (!$diseaseT) {
            return redirect()->back()->with('error', 'Loại bệnh không tồn tại');
        }
        $request->validate([
            'diseaseName' => 'required',
            'desc' => 'required',
            'status' => 'nullable',
        ]);
        $diseaseT->update([
            'diseaseName' => $request->diseaseName,
            'desc' => $request->desc,
            'status' => $request->status,
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'update',
            'model_type' => 'Diseases',
            'details' => "Đã cập nhật loại bệnh: " . $diseaseT->diseaseName,
        ]);
        return redirect()->route('diseaseT.index')->with('message', 'Cập nhật loại bệnh thành công');
    }
    public function destroy($id)
    {
        $diseaseT = Diseases::find($id);
        $diseaseT->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $diseaseT = Diseases::whereIn('id', $request->ids)->get();

        foreach ($diseaseT as $s) {
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
        $diseaseTToDelete = Diseases::whereIn('id', $request->ids)->get();

        Diseases::whereIn('id', $request->ids)->delete();

        foreach ($diseaseTToDelete as $s) {
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'delete',
                'model_type' => 'Diseases',
                'details' => "Đã xóa loại bệnh: " . $s->diseaseName,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các loại bệnh được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $s = Diseases::find($request->id);
        if ($s) {
            $s->status = $s->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $s->save();
            return response()->json(['success' => true, 'status' => $s->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
