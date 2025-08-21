<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\GenTask;
use App\Models\InventoryStock;
use App\Models\Product;
use App\Models\TaskProductProposal;
use App\Models\TaskProductProposalProduct;
use App\Models\UnitOfMeasure;
use App\Models\WareHouse;
use App\Models\Work;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
                    $statusClass = $row->status == 'Duyệt' ? 'success' : 'danger';
                    $statusText = $row->status == 'Duyệt' ? 'Duyệt' : 'Chưa duyệt';
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
        $warehouses = WareHouse::all();
        $units = UnitOfMeasure::all();
        $products = Product::all();
        $works = Work::all();
        $gentasks = GenTask::Where('type', 1)->get();
        return view('workPs.add_workPs', compact('warehouses', 'units', 'products', 'works', 'gentasks'));
    }
    public function addProposal($taskID)
    {
        $warehouses = WareHouse::all();
        $units = UnitOfMeasure::all();
        $products = Product::all();
        $works = Work::all();
        $gentask = GenTask::findOrFail($taskID);
        return view('workps.addProposal', compact('gentask', 'warehouses', 'units', 'products', 'works'));
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
            // 'sessionID' => 4,
            'status' => $request->status ?? 'Chờ duyệt',
            'request_status' => $request->request_status ?? 'Chờ duyệt',
            'created_by' => Auth::id(),
            'reason' => $request->reason,
        ]);
        foreach ($request->items as $item) {
            TaskProductProposalProduct::create([
                'taskproposalID' => $proposal->id,
                'warehouseID' => $item['warehouseID'],
                'productID' => $item['productID'],
                'materialQuantity' => $item['quantity'],
                'unitID' => $item['unit'],
                'note' => $item['note'],
                'status' => 'Chờ duyệt',
            ]);
        }
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'Workp',
            'details' => "Đã tạo đề xuất: " . $request->proposaName,
        ]);
        return redirect()->route('workps.index')->with('message', 'Tạo đề xuất thành công');
    }
    public function edit($id)
    {
        $warehouses = WareHouse::all();
        $units = UnitOfMeasure::all();
        $proposal = TaskProductProposal::with('creator', 'task', 'proposalProducts')->findOrFail($id);
        $products = Product::all();
        $works = Work::all();
        $gentasks = GenTask::Where('type', 1)->get();
        return view('workPs.edit_workPs', compact('warehouses', 'units', 'proposal', 'products', 'works', 'gentasks'));
    }
    // public function update(Request $request, $id)
    // {
    //     // Kiểm tra phiếu khác nhưng trùng tên
    //     $existingTaskProductProposal = TaskProductProposal::where('proposaName', $request->proposaName)
    //         ->where('id', '!=', $id)
    //         ->first();

    //     if ($existingTaskProductProposal) {
    //         return redirect()->back()->with(['error' => 'Đề xuất này đã tồn tại!']);
    //     }

    //     // Lấy phiếu đề xuất
    //     $proposal = TaskProductProposal::find($id);
    //     if (!$proposal) {
    //         return redirect()->back()->with('error', 'Đề xuất không tồn tại');
    //     }

    //     // Cập nhật thông tin chính
    //     $proposal->update([
    //         'proposaName'    => $request->proposaName,
    //         'proposalDate'   => $request->proposalDate,
    //         'approvalDate'   => $request->approvalDate,
    //         'taskID'         => $request->taskID,
    //         'treatmentID'    => $request->treatmentID,
    //         'sessionID'      => 1,
    //         'status'         => $request->status,
    //         'request_status' => $request->request_status,
    //         'created_by'     => Auth::id(),
    //         'reason'         => $request->reason,
    //     ]);

    //     $oldIDs = TaskProductProposalProduct::where('taskproposalID', $proposal->id)->pluck('id')->toArray();
    //     $newIDs = [];

    //     if ($request->has('proposalProducts')) {
    //         foreach ($request->proposalProducts as $item) {
    //             if (isset($item['id'])) {
    //                 $existingItem = TaskProductProposalProduct::find($item['id']);
    //                 if ($existingItem) {
    //                     $existingItem->update([
    //                         'taskproposalID'   => $proposal->id,
    //                         'productID'        => $item['productID'],
    //                         'warehouseID'      => $item['warehouseID'],
    //                         'unitID'           => $item['unitID'],
    //                         'materialQuantity' => $item['quantity'],
    //                         'note'             => $item['note'] ?? null,
    //                         // 'sessionID'        => 1,
    //                     ]);
    //                     $newIDs[] = $existingItem->id;
    //                 }
    //             } else {
    //                 $newItem = TaskProductProposalProduct::create([
    //                     'taskproposalID'    => $proposal->id,
    //                     'productID'         => $item['productID'],
    //                     'materialQuantity'  => $item['quantity'],
    //                     'note'              => $item['note'] ?? null,
    //                     'sessionID'         => 1,
    //                 ]);
    //                 $newIDs[] = $newItem->id;
    //             }
    //         }
    //         $toDelete = array_diff($oldIDs, $newIDs);
    //         TaskProductProposalProduct::whereIn('id', $toDelete)->delete();
    //     }

    //     // Ghi lịch sử
    //     ActionHistory::create([
    //         'user_id'     => Auth::id(),
    //         'action_type' => 'update',
    //         'model_type'  => 'Workp',
    //         'details'     => "Đã cập nhật đề xuất: " . $proposal->proposaName,
    //     ]);

    //     return redirect()->route('workps.index')->with('message', 'Cập nhật đề xuất thành công');
    // }
    public function update(Request $request, $id)
    {
        // validate
        $request->validate([
            'proposaName'  => 'required|string|max:255',
            'proposalDate' => 'nullable|date',
            'approvalDate' => 'nullable|date',
            'taskID'       => 'nullable|integer',
            'treatmentID'  => 'nullable|integer',
            'status'       => 'nullable|string',
            'request_status' => 'nullable|string',
            'reason'       => 'nullable|string',

            'proposalProducts'               => 'required|array|min:1',
            'proposalProducts.*.id'          => 'nullable|integer',
            'proposalProducts.*.productID'   => 'required|integer|exists:products,id',
            'proposalProducts.*.warehouseID' => 'required|integer|exists:ware_houses,id',
            'proposalProducts.*.unitID'      => 'required',
            'proposalProducts.*.quantity'    => 'required|numeric|min:0.000001',
            'proposalProducts.*.note'        => 'nullable|string',
        ]);

        // Kiểm tra tên phiếu trùng
        $exists = TaskProductProposal::where('proposaName', $request->proposaName)
            ->where('id', '!=', $id)
            ->exists();
        if ($exists) {
            return back()->with(['error' => 'Đề xuất này đã tồn tại!'])->withInput();
        }

        return DB::transaction(function () use ($request, $id) {
            /** @var \App\Models\TaskProductProposal $proposal */
            $proposal = TaskProductProposal::lockForUpdate()->find($id);
            if (!$proposal) {
                return back()->with('error', 'Đề xuất không tồn tại');
            }

            // Cập nhật thông tin chính (không ghi đè created_by)
            $proposal->update([
                'proposaName'    => $request->proposaName,
                'proposalDate'   => $request->proposalDate,
                'approvalDate'   => $request->approvalDate,
                'taskID'         => $request->taskID,
                'treatmentID'    => $request->treatmentID,
                // 'sessionID'      => 1,
                'status'         => $request->status ?? $proposal->status,
                'request_status' => $request->request_status ?? $proposal->request_status,
                'reason'         => $request->reason,
                // 'updated_by'   => Auth::id(), // nếu có cột
            ]);

            // Lấy danh sách id cũ
            $oldIDs = $proposal->proposalProducts()->pluck('id')->toArray();
            $newIDs = [];

            foreach ($request->proposalProducts as $item) {
                $payload = [
                    'taskproposalID'   => $proposal->id,
                    'productID'        => $item['productID'],
                    'warehouseID'      => $item['warehouseID'],
                    'unitID'           => $item['unitID'],
                    'materialQuantity' => $item['quantity'],
                    'note'             => $item['note'] ?? null,
                    // 'sessionID'        => 1,
                ];

                if (!empty($item['id'])) {
                    // Chỉ update dòng thuộc đúng phiếu
                    $existingItem = TaskProductProposalProduct::where('id', $item['id'])
                        ->where('taskproposalID', $proposal->id)
                        ->first();

                    if ($existingItem) {
                        $existingItem->update($payload);
                        $newIDs[] = $existingItem->id;
                    }
                } else {
                    $newItem = TaskProductProposalProduct::create($payload);
                    $newIDs[] = $newItem->id;
                }
            }

            // Xoá dòng không còn trong form
            $toDelete = array_diff($oldIDs, $newIDs);
            if (!empty($toDelete)) {
                TaskProductProposalProduct::whereIn('id', $toDelete)->delete();
            }

            // Log
            ActionHistory::create([
                'user_id'     => Auth::id(),
                'action_type' => 'update',
                'model_type'  => 'Workp',
                'details'     => "Đã cập nhật đề xuất: " . $proposal->proposaName,
            ]);

            return redirect()->route('workps.index')->with('message', 'Cập nhật đề xuất thành công');
        });
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

        foreach ($workps as $w) {
            $w->status = ($w->status === 'Hoạt động') ? 'Không hoạt động' : 'Hoạt động';
            $w->save();
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

        foreach ($workpsToDelete as $w) {
            ActionHistory::create([
                'user_id' => Auth::id(),  // ID của người thực hiện hành động
                'action_type' => 'delete',  // Loại hành động "delete"
                'model_type' => 'Workp',  // Model "Workp"
                'details' => "Đã xóa đề xuất: " . $w->proposaName . " với mã: " . $w->proposaName,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các đề xuất được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    // public function toggleStatus(Request $request)
    // {
    //     $w = TaskProductProposal::find($request->id);
    //     if ($w) {
    //         $w->status = $w->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
    //         $w->save();
    //         return response()->json(['success' => true, 'status' => $w->status]);
    //     } else {
    //         return response()->json(['success' => false]);
    //     }
    // }
    public function toggleStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:task_product_proposals,id',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                /** @var \App\Models\TaskProductProposal $proposal */
                $proposal = TaskProductProposal::lockForUpdate()->find($request->id);

                if (!$proposal) {
                    return response()->json(['success' => false, 'message' => 'Không tìm thấy đề xuất.'], 404);
                }

                $current = $proposal->status ?? 'Chờ duyệt';
                $next = ($current === 'Duyệt') ? 'Chờ duyệt' : 'Duyệt';

                $items = TaskProductProposalProduct::where('taskproposalID', $proposal->id)->get();

                if ($next === 'Duyệt') {
                    $errors = [];

                    foreach ($items as $it) {
                        $stock = InventoryStock::where('warehouseID', $it->warehouseID)
                            ->where('productID', $it->productID)
                            ->where('unitID', $it->unitID)
                            ->lockForUpdate()
                            ->first();

                        if (!$stock) {
                            $errors[] = "Chưa có tồn kho (WH {$it->warehouseID}, SP {$it->productID}, ĐV {$it->unitID}).";
                            continue;
                        }
                        if ($stock->quantity < $it->materialQuantity) {
                            $errors[] = "Tồn không đủ cho SP {$it->productID} tại kho {$it->warehouseID} (cần {$it->materialQuantity}, còn {$stock->quantity}).";
                            continue;
                        }
                    }

                    if (!empty($errors)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Không thể duyệt do tồn kho không đủ/không hợp lệ.',
                            'errors' => $errors
                        ]);
                    }

                    foreach ($items as $it) {
                        $stock = InventoryStock::where('warehouseID', $it->warehouseID)
                            ->where('productID', $it->productID)
                            ->where('unitID', $it->unitID)
                            ->lockForUpdate()
                            ->first();

                        $stock->quantity = $stock->quantity - $it->materialQuantity;
                        $stock->save();

                        $it->status = 'Duyệt';
                        $it->save();
                    }
                } else {
                    foreach ($items as $it) {
                        $it->status = 'Chờ duyệt';
                        $it->save();
                    }
                }

                $proposal->status = $next;
                $proposal->save();

                ActionHistory::create([
                    'user_id' => Auth::id(),
                    'action_type' => 'update_status',
                    'model_type' => 'TaskProductProposal',
                    'details' => "Đổi trạng thái đề xuất #{$proposal->id} sang {$next}",
                ]);

                return response()->json([
                    'success' => true,
                    'status' => $proposal->status,
                    'message' => $next === 'Duyệt'
                        ? 'Đã duyệt và trừ tồn kho thành công.'
                        : 'Đã chuyển về trạng thái Chờ duyệt.'
                ]);
            });
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại.',
            ], 500);
        }
    }
}
