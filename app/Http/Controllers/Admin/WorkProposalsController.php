<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\GenTask;
use App\Models\Product;
use App\Models\TaskProductProposal;
use App\Models\TaskProductProposalProduct;
use App\Models\Work;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class WorkProposalsController extends Controller
{
    public function index(Request $request)
    {
        $all_workps = TaskProductProposal::with('task', 'creator')->orderBy('id', 'desc')->get();
        if ($request->ajax()) {
            return DataTables::of($all_workps)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('proposaName', function ($row) {
                    return $row->proposaName;
                })
                ->editColumn('proposalDate', function ($row) {
                    return $row->proposalDate;
                })
                ->addColumn('approvalDate', function ($row) {
                    return $row->approvalDate;
                })
                ->addColumn('created_by', function ($row) {
                    return $row->creator ? $row->creator->name : 'N/A';
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Đã duyệt' ? 'success' : 'danger';
                    $statusText = $row->status == 'Đã duyệt' ? 'Đã duyệt' : 'Chưa duyệt';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-workps/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->proposalName ?? 'N/A') . '</span>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/workps/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'stt', 'proposaName', 'proposalDate', 'approvalDate', 'created_by', 'status', 'action'])
                ->make(true);
        }
        return view('workPs.all_workPs');
    }
    public function add(Request $request)
    {
        $products = Product::all();
        $works = Work::all();
        $gentasks = GenTask::all();
        // Trả dữ liệu vào view
        return view('workps.add_workps', compact('products', 'works', 'gentasks'));
    }
    public function save(Request $request)
    {
        // dd($request->all());
        // $request->validate([
        //     'proposaName' => 'required',
        //     'proposaName' => 'required',
        //     'unit_id' => 'required|exists:units,id',
        //     'status' => 'nullable',
        // ]);
        // $existingCode = TaskProductProposal::where('proposaName', $request->proposaName)->first();

        // if ($existingCode) {
        //     return redirect()->back()->with(['error' => 'Mã đề xuất này đã tồn tại!']);
        // }

        $proposal = TaskProductProposal::create([
            'proposaName' => $request->proposaName,
            'proposalDate' => $request->proposalDate,
            'approvalDate' => $request->approvalDate,
            'taskID' => $request->taskID,
            'treatmentID' => $request->treatmentID,
            'sessionID' => 1,
            'status' => $request->status ?? 'Chờ duyệt',
            'request_status' => $request->request_status ?? 'Chờ duyệt',
            'created_by' => Auth::id(),
            'reason' => $request->reason,
        ]);
        foreach ($request->items as $item) {
            TaskProductProposalProduct::create([
                'taskproposalID' => $proposal->id,
                'productID' => $item['productID'],
                'materialQuantity' => $item['quantity'],
                'sessionID' => 1,
                'note' => $item['note'],
            ]);
        }
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'Workp',
            'details' => "Đã tạo đề xuất: " . $request->proposaName,
        ]);
        session()->flash('message', 'Tạo đề xuất thành công.');
        return redirect()->back();
    }
    public function edit($id)
    {
        $proposal = TaskProductProposal::with('creator', 'task', 'proposalProducts')->findOrFail($id);
        $products = Product::all();
        $works = Work::all();
        $gentasks = GenTask::all();
        return view('workps.edit_workPs', compact('proposal', 'products', 'works', 'gentasks'));
    }
    public function update(Request $request, $id)
    {
        // Kiểm tra phiếu khác nhưng trùng tên
        $existingTaskProductProposal = TaskProductProposal::where('proposaName', $request->proposaName)
            ->where('id', '!=', $id)
            ->first();

        if ($existingTaskProductProposal) {
            return redirect()->back()->with(['error' => 'Đề xuất này đã tồn tại!']);
        }

        // Lấy phiếu đề xuất
        $proposal = TaskProductProposal::find($id);
        if (!$proposal) {
            return redirect()->back()->with('error', 'Đề xuất không tồn tại');
        }

        // Cập nhật thông tin chính
        $proposal->update([
            'proposaName'    => $request->proposaName,
            'proposalDate'   => $request->proposalDate,
            'approvalDate'   => $request->approvalDate,
            'taskID'         => $request->taskID,
            'treatmentID'    => $request->treatmentID,
            'sessionID'      => 1,
            'status'         => $request->status,
            'request_status' => $request->request_status,
            'created_by'     => Auth::id(),
            'reason'         => $request->reason,
        ]);

        $oldIDs = TaskProductProposalProduct::where('taskproposalID', $proposal->id)->pluck('id')->toArray();
        $newIDs = [];

        if ($request->has('proposalProducts')) {
            foreach ($request->proposalProducts as $item) {
                if (isset($item['id'])) {
                    $existingItem = TaskProductProposalProduct::find($item['id']);
                    if ($existingItem) {
                        $existingItem->update([
                            'productID'        => $item['productID'],
                            'materialQuantity' => $item['quantity'],
                            'note'             => $item['note'] ?? null,
                            'sessionID'        => 1,
                        ]);
                        $newIDs[] = $existingItem->id;
                    }
                } else {
                    $newItem = TaskProductProposalProduct::create([
                        'taskproposalID'    => $proposal->id,
                        'productID'         => $item['productID'],
                        'materialQuantity'  => $item['quantity'],
                        'note'              => $item['note'] ?? null,
                        'sessionID'         => 1,
                    ]);
                    $newIDs[] = $newItem->id;
                }
            }
            $toDelete = array_diff($oldIDs, $newIDs);
            TaskProductProposalProduct::whereIn('id', $toDelete)->delete();
        }

        // Ghi lịch sử
        ActionHistory::create([
            'user_id'     => Auth::id(),
            'action_type' => 'update',
            'model_type'  => 'Workp',
            'details'     => "Đã cập nhật đề xuất: " . $proposal->proposaName,
        ]);

        return redirect()->route('workps.index')->with('message', 'Cập nhật đề xuất thành công');
    }

    public function destroy($id)
    {
        $workps = TaskProductProposal::find($id);
        $workps->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $workps = TaskProductProposal::whereIn('id', $request->ids)->get();

        foreach ($workps as $farm) {
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
        $workpsToDelete = TaskProductProposal::whereIn('id', $request->ids)->get();

        TaskProductProposal::whereIn('id', $request->ids)->delete();

        foreach ($workpsToDelete as $farm) {
            ActionHistory::create([
                'user_id' => Auth::id(),  // ID của người thực hiện hành động
                'action_type' => 'delete',  // Loại hành động "delete"
                'model_type' => 'Workp',  // Model "Workp"
                'details' => "Đã xóa đề xuất: " . $farm->proposaName . " với mã: " . $farm->proposaName,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các đề xuất được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $farm = TaskProductProposal::find($request->id);
        if ($farm) {
            $farm->status = $farm->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $farm->save();
            return response()->json(['success' => true, 'status' => $farm->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
