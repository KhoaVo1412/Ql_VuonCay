<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\DiseasePlant;
use App\Models\GenTask;
use App\Models\InventoryStock;
use App\Models\MaterialProposal;
use App\Models\Product;
use App\Models\TaskProductProposal;
use App\Models\MaterialProposalItem;
use App\Models\UnitOfMeasure;
use App\Models\WareHouse;
use App\Models\Work;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class MaterialProposalController extends Controller
{
    public function index(Request $request)
    {
        $all_materialproposals = MaterialProposal::with('creator')->orderBy('id', 'desc')->get();
        if ($request->ajax()) {
            return DataTables::of($all_materialproposals)
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
                    return $row->proposalDate->format('d/m/Y');
                })
                ->addColumn('approvalDate', function ($row) {
                    return $row->approvalDate->format('d/m/Y');
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
                            <a href="/edit-materialproposals/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        <a href="/materialproposals/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
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
        return view('Dxcb.all_Cb');
    }
    public function add(Request $request)
    {
        $warehouses = WareHouse::where('id', 1)->get(['id', 'name']);
        $products   = Product::with(['unit:id,name'])
            ->get(['id', 'name', 'unitID']);
        $units      = UnitOfMeasure::all(['id', 'name']);
        $works      = Work::all();
        $diseaseplant = DiseasePlant::all();
        return view('Dxcb.add_Cb', compact('warehouses', 'units', 'products', 'works', 'diseaseplant'));
    }
    public function save(Request $request)
    {
        $validated = $request->validate([
            'proposaName'    => 'required|string|max:255',
            'proposalDate'   => 'nullable|date',
            'approvalDate'   => 'nullable|date',
            'diseaseplantID' => 'nullable|integer',
            'status'         => 'nullable|string',
            'reason'         => 'nullable|string',

            'items'               => 'required|array|min:1',
            'items.*.id'          => 'nullable|integer',
            'items.*.productID'   => 'required|integer|exists:products,id',
            'items.*.warehouseID' => 'required|integer|exists:ware_houses,id',
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
                    "items.$i.unit" => "Dòng " . ($i + 1) . ": Đơn vị không tồn tại trong tồn kho của vật tư/kho đã chọn.",
                ]);
            }
        }
        DB::transaction(function () use ($validated) {
            $proposal = MaterialProposal::create([
                'proposaName'   => $validated['proposaName'],
                'proposalDate'  => $validated['proposalDate'] ?? null,
                'approvalDate'  => $validated['approvalDate'] ?? null,
                'diseaseplantID' => $validated['diseaseplantID'] ?? null,
                'status'        => $validated['status'] ?? 'Chờ duyệt',
                'created_by'    => Auth::id(),
                'reason'        => $validated['reason'] ?? null,
            ]);
            $logDetails = "Đã tạo đề xuất: {$validated['proposaName']}<br>";
            foreach ($validated['items'] as $item) {
                MaterialProposalItem::create([
                    'material_proposal_id' => $proposal->id,
                    'warehouseID'          => $item['warehouseID'],
                    'productID'            => $item['productID'],
                    'materialQuantity'     => $item['quantity'],
                    'unitID'               => $item['unit'],
                    'note'                 => $item['note'] ?? null,
                    'status'               => 'Chờ duyệt',
                ]);
                $product  = Product::find($item['productID']);
                $warehouse = WareHouse::find($item['warehouseID']);
                $unit      = UnitOfMeasure::find($item['unit']);

                $logDetails .= "- {$product->name} ({$item['quantity']} {$unit->name}) tại kho {$warehouse->name}";
                if (!empty($item['note'])) {
                    $logDetails .= " | Ghi chú: {$item['note']}";
                }
                $logDetails .= "<br>";
            }

            ActionHistory::create([
                'user_id'     => Auth::id(),
                'action_type' => 'create',
                'model_type'  => 'MaterialProposal',
                'details'     => $logDetails,
            ]);
        });
        return redirect()
            ->route('materialproposals.index')
            ->with('message', 'Tạo đề xuất thành công');
    }
    public function edit($id)
    {
        $warehouses = WareHouse::where('id', 1)->get();
        $units = UnitOfMeasure::all();
        $diseaseplants = DiseasePlant::all();
        $proposal = MaterialProposal::with('creator', 'items')->findOrFail($id);
        $products = Product::all();
        $works = Work::all();
        // dd($proposal);
        return view('Dxcb.edit_Cb', compact('warehouses', 'units', 'proposal', 'products', 'works', 'diseaseplants'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'proposaName'  => 'required|string|max:255',
            'proposalDate' => 'nullable',
            'approvalDate' => 'nullable',
            'diseaseplantID'  => 'nullable|integer',
            'status'       => 'nullable|string',
            'reason'       => 'nullable|string',

            'items'               => 'required|array|min:1',
            'items.*.id'          => 'nullable|integer',
            'items.*.productID'   => 'required|integer|exists:products,id',
            'items.*.warehouseID' => 'required|integer|exists:ware_houses,id',
            'items.*.unitID'      => 'required',
            'items.*.quantity'    => 'required|numeric|min:0.000001',
            'items.*.note'        => 'nullable|string',
        ]);

        $exists = MaterialProposal::where('proposaName', $request->proposaName)
            ->where('id', '!=', $id)
            ->exists();
        if ($exists) {
            return back()->with(['error' => 'Đề xuất này đã tồn tại!'])->withInput();
        }

        return DB::transaction(function () use ($request, $id) {
            $proposal = MaterialProposal::lockForUpdate()->find($id);
            if (!$proposal) {
                return back()->with('error', 'Đề xuất không tồn tại');
            }

            $proposal->update([
                'proposaName'    => $request->proposaName,
                'proposalDate'   => $request->proposalDate,
                'approvalDate'   => $request->approvalDate,
                'diseaseplantID'    => $request->diseaseplantID,
                'status'         => $request->status ?? $proposal->status,
                'reason'         => $request->reason,
            ]);

            $oldIDs = $proposal->items()->pluck('id')->toArray();
            $newIDs = [];

            foreach ($request->items as $item) {
                $payload = [
                    'material_proposal_id'   => $proposal->id,
                    'productID'        => $item['productID'],
                    'warehouseID'      => $item['warehouseID'],
                    'unitID'           => $item['unitID'],
                    'materialQuantity' => $item['quantity'],
                    'note'             => $item['note'] ?? null,
                ];
                if (!empty($item['id'])) {
                    $existingItem = MaterialProposalItem::where('id', $item['id'])
                        ->where('material_proposal_id', $proposal->id)
                        ->first();
                    if ($existingItem) {
                        $existingItem->update($payload);
                        $newIDs[] = $existingItem->id;
                    }
                } else {
                    $newItem = MaterialProposalItem::create($payload);
                    $newIDs[] = $newItem->id;
                }
            }

            // Xoá dòng không còn trong form
            $toDelete = array_diff($oldIDs, $newIDs);
            if (!empty($toDelete)) {
                MaterialProposalItem::whereIn('id', $toDelete)->delete();
            }

            // Log
            ActionHistory::create([
                'user_id'     => Auth::id(),
                'action_type' => 'update',
                'model_type'  => 'Workp',
                'details'     => "Đã cập nhật đề xuất: " . $proposal->proposaName,
            ]);

            return redirect()->route('materialproposals.index')->with('message', 'Cập nhật đề xuất thành công');
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
                'model_type' => 'MaterialProposal',  // Model "Workp"
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

                $items = MaterialProposalItem::where('material_proposal_id', $proposal->id)->get();

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
                    'model_type' => 'MaterialProposal',
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
