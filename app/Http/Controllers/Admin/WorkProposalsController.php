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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class WorkProposalsController extends Controller
{
    public function index(Request $request)
    {
        // $all_workps = TaskProductProposal::with('task', 'creator')->orderBy('id', 'desc')->get();
        if ($request->ajax()) {
            $all_workps = TaskProductProposal::with('task', 'creator')
                ->orderBy('id', 'desc');

            $start = $request->input('start_date');
            $end   = $request->input('end_date');

            if ($start && $end) {
                $startDT = Carbon::parse($start)->startOfDay();
                $endDT   = Carbon::parse($end)->endOfDay();
                $all_workps->whereBetween('proposalDate', [$startDT, $endDT]);
            } elseif ($start) {
                $all_workps->where('proposalDate', '>=', Carbon::parse($start)->startOfDay());
            } elseif ($end) {
                $all_workps->where('proposalDate', '<=', Carbon::parse($end)->endOfDay());
            }
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
                    return $row->proposalDate ? Carbon::parse($row->proposalDate)->format('d/m/Y')
                        : null;
                })
                ->addColumn('approvalDate', function ($row) {
                    return $row->approvalDate ? Carbon::parse($row->approvalDate)->format('d/m/Y')
                        : null;
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
        $warehouses = WareHouse::all(['id', 'name']);
        // $units = UnitOfMeasure::all();   // không cần trong view này
        // $products = Product::all();      // bỏ, vì sẽ lấy từ stock theo kho
        $works = Work::all();
        $gentasks = GenTask::where('type', 1)->get();

        return view('workPs.add_workPs', compact('warehouses', 'works', 'gentasks'));
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
        // 1) Validate input
        $validated = $request->validate([
            'proposaName'   => 'required|string|max:255',
            'proposalDate'  => 'nullable|date',
            'approvalDate'  => 'nullable|date',
            'taskID'        => 'required|integer|exists:gen_tasks,id',
            'treatmentID'   => 'nullable|integer',
            'status'        => 'nullable|string',
            'reason'        => 'nullable|string',

            'items'               => 'required|array|min:1',
            'items.*.warehouseID' => 'required|integer|exists:ware_houses,id',
            'items.*.productID'   => 'required|integer|exists:products,id',
            'items.*.unit'        => 'required|integer|exists:unit_of_measures,id',
            'items.*.quantity'    => 'required|numeric|min:0.000001',
            'items.*.note'        => 'nullable|string',
        ]);
        foreach ($validated['items'] as $i => $it) {
            $exists = InventoryStock::where('warehouseID', $it['warehouseID'])
                ->where('productID',   $it['productID'])
                ->where('unitID',      $it['unit'])
                ->exists();

            if (!$exists) {
                throw ValidationException::withMessages([
                    "items.$i.unit" => "Dòng " . ($i + 1) . ": Đơn vị không tồn tại trong tồn kho của (Kho/Vật tư) đã chọn.",
                ]);
            }

            // (Tuỳ chọn) Chặn số lượng > tổng tồn theo unit
            // $available = InventoryStock::where('warehouseID', $it['warehouseID'])
            //     ->where('productID',   $it['productID'])
            //     ->where('unitID',      $it['unit'])
            //     ->sum('quantity');
            // if ($it['quantity'] > $available) {
            //     throw ValidationException::withMessages([
            //         "items.$i.quantity" => "Dòng ".($i+1).": Số lượng vượt quá tồn khả dụng (".$available.").",
            //     ]);
            // }
        }
        DB::transaction(function () use ($validated) {
            $proposal = TaskProductProposal::create([
                'proposaName'  => $validated['proposaName'],
                'proposalDate' => $validated['proposalDate'] ?? null,
                'approvalDate' => $validated['approvalDate'] ?? null,
                'taskID'       => $validated['taskID'],
                'treatmentID'  => $validated['treatmentID'] ?? null,
                'status'       => $validated['status'] ?? 'Chờ duyệt',
                'created_by'   => Auth::id(),
                'reason'       => $validated['reason'] ?? null,
            ]);

            foreach ($validated['items'] as $it) {
                TaskProductProposalProduct::create([
                    'taskproposalID'    => $proposal->id,
                    'warehouseID'       => $it['warehouseID'],
                    'productID'         => $it['productID'],
                    'materialQuantity'  => $it['quantity'],
                    'unitID'            => $it['unit'],
                    'note'              => $it['note'] ?? null,
                    'status'            => 'Chờ duyệt',
                ]);
            }

            ActionHistory::create([
                'user_id'     => Auth::id(),
                'action_type' => 'create',
                'model_type'  => 'TaskProductProposal', // đặt đúng model để sau này lọc/trace
                'details'     => "Đã tạo đề xuất: " . $proposal->proposaName,
            ]);
        });
        return redirect()->route('workps.index')->with('message', 'Tạo đề xuất thành công');
    }
    public function save1(Request $request)
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
        $warehouses = WareHouse::all(['id', 'name']);
        $proposal   = TaskProductProposal::with('creator', 'task', 'proposalProducts')->findOrFail($id);
        $works      = Work::all();
        $gentasks   = GenTask::where('type', 1)->get();

        return view('workPs.edit_workPs', compact('warehouses', 'proposal', 'works', 'gentasks'));
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'proposaName'  => 'required|string|max:255',
            'proposalDate' => 'nullable|date',
            'approvalDate' => 'nullable|date',
            'taskID'       => 'nullable|integer',
            'treatmentID'  => 'nullable|integer',
            'status'       => 'nullable|string',
            'reason'       => 'nullable|string',
            'proposalProducts'               => 'required|array|min:1',
            'proposalProducts.*.id'          => 'nullable|integer',
            'proposalProducts.*.productID'   => 'required|integer|exists:products,id',
            'proposalProducts.*.warehouseID' => 'required|integer|exists:ware_houses,id',
            'proposalProducts.*.unitID'      => 'required|integer|exists:unit_of_measures,id',
            'proposalProducts.*.quantity'    => 'required|numeric|min:0.000001',
            'proposalProducts.*.note'        => 'nullable|string',
        ]);
        $exists = TaskProductProposal::where('proposaName', $validated['proposaName'])
            ->where('id', '!=', $id)
            ->exists();
        if ($exists) {
            return back()->with(['error' => 'Đề xuất này đã tồn tại!'])->withInput();
        }
        foreach ($validated['proposalProducts'] as $i => $it) {
            $inStock = InventoryStock::where('warehouseID', $it['warehouseID'])
                ->where('productID',   $it['productID'])
                ->where('unitID',      $it['unitID'])
                ->exists();
            if (!$inStock) {
                return back()->withErrors([
                    "proposalProducts.$i.unitID" => "Dòng " . ($i + 1) . ": Đơn vị không tồn tại trong tồn kho của (Kho/Vật tư) đã chọn.",
                ])->withInput();
            }
            // (Tuỳ chọn) chặn vượt tồn
            // $available = InventoryStock::where('warehouseID', $it['warehouseID'])
            //     ->where('productID',   $it['productID'])
            //     ->where('unitID',      $it['unitID'])
            //     ->sum('quantity');
            // if ($it['quantity'] > $available) {
            //     return back()->withErrors([
            //         "proposalProducts.$i.quantity" => "Dòng ".($i+1).": Số lượng vượt tồn khả dụng (".$available.").",
            //     ])->withInput();
            // }
        }
        return DB::transaction(function () use ($validated, $id) {
            /** @var TaskProductProposal|null $proposal */
            $proposal = TaskProductProposal::lockForUpdate()->find($id);
            if (!$proposal) {
                return back()->with('error', 'Đề xuất không tồn tại');
            }
            $proposal->update([
                'proposaName'  => $validated['proposaName'],
                'proposalDate' => $validated['proposalDate'] ?? null,
                'approvalDate' => $validated['approvalDate'] ?? null,
                'taskID'       => $validated['taskID'] ?? null,
                'treatmentID'  => $validated['treatmentID'] ?? null,
                'status'       => $validated['status'] ?? $proposal->status,
                'reason'       => $validated['reason'] ?? null,
            ]);
            $oldIDs = $proposal->proposalProducts()->pluck('id')->toArray();
            $newIDs = [];
            foreach ($validated['proposalProducts'] as $it) {
                $payload = [
                    'taskproposalID'   => $proposal->id,
                    'productID'        => $it['productID'],
                    'warehouseID'      => $it['warehouseID'],
                    'unitID'           => $it['unitID'],
                    'materialQuantity' => $it['quantity'],
                    'note'             => $it['note'] ?? null,
                ];
                if (!empty($it['id'])) {
                    $existingItem = TaskProductProposalProduct::where('id', $it['id'])
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
            $toDelete = array_diff($oldIDs, $newIDs);
            if (!empty($toDelete)) {
                TaskProductProposalProduct::whereIn('id', $toDelete)->delete();
            }
            ActionHistory::create([
                'user_id'     => Auth::id(),
                'action_type' => 'update',
                'model_type'  => 'TaskProductProposal',
                'details'     => "Đã cập nhật đề xuất: " . $proposal->proposaName,
            ]);
            return redirect()->route('workps.index')->with('message', 'Cập nhật đề xuất thành công');
        });
    }

    public function updateOld(Request $request, $id)
    {
        // validate
        $request->validate([
            'proposaName'  => 'required|string|max:255',
            'proposalDate' => 'nullable|date',
            'approvalDate' => 'nullable|date',
            'taskID'       => 'nullable|integer',
            'treatmentID'  => 'nullable|integer',
            'status'       => 'nullable|string',
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
                if ($proposal->status === 'Duyệt') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Phiếu đã được duyệt trước đó.'
                    ]);
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
