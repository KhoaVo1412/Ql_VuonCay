<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Category;
use App\Models\InventoryStock;
use App\Models\Picking;
use App\Models\Product;
use App\Models\ProductPicking;
use App\Models\UnitOfMeasure;
use App\Models\User;
use App\Models\WareHouse;
use App\Models\Worker;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class PWareHouseController extends Controller
{
    public function index(Request $request)
    {

        $warehouses = WareHouse::all();
        $all_pwarehouses = Picking::with('productPickings.product', 'warehouse')->orderBy('id', 'desc')->get();
        if ($request->ajax()) {

            $query = Picking::with(['productPickings.product', 'warehouse'])
                ->when($request->filled('warehouse_id'), function ($q) use ($request) {
                    $q->where('warehouseID', $request->warehouse_id);
                })
                ->when($request->filled('start_date'), function ($q) use ($request) {
                    $q->whereDate('createDate', $request->start_date);
                })
                ->orderByDesc('id');

            return DataTables::of($query)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('code', function ($row) {
                    return $row->code ?? 'Chờ kiểm tra';
                })
                ->editColumn('warehouseID', function ($row) {
                    return $row->warehouse->name;
                })
                ->addColumn('name', function ($row) {
                    return $row->name ?? 'Không rõ';
                })
                ->addColumn('type', function ($row) {
                    return $row->type ?? 'Không rõ';
                })
                ->editColumn('createDate', function ($row) {
                    return $row->createDate ? Carbon::parse($row->createDate)->format('d/m/Y')
                        : null;
                })
                ->editColumn('active', function ($row) {
                    $activeClass = $row->active == 'Hoàn thành' ? 'success' : 'danger';
                    $activeText = $row->active == 'Hoàn thành' ? 'Hoàn thành' : 'Chưa hoàn thành';
                    return '<button class="badge bg-' . $activeClass . ' toggle-active" data-id="' . $row->id . '">' . $activeText . '</button>';
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-pwarehouses/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->name ?? 'N/A') . '</span>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/pwarehouses/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'code', 'stt', 'createDate', 'name', 'type', 'warehouseID', 'status', 'active', 'action'])
                ->make(true);
        }
        return view('pwarehouses.all_pwarehouses', compact('warehouses'));
    }
    public function toggleActive(Request $request, $id, InventoryService $inventory)
    {
        $picking = Picking::with(['productPickings', 'warehouse'])->findOrFail($id);

        if ($picking->status !== 'Hoạt động') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ phiếu đang Hoạt động mới được phép thay đổi trạng thái.',
            ]);
        }

        if ($picking->active === 'Hoàn thành') {
            return response()->json([
                'success' => false,
                'message' => 'Phiếu đã được duyệt trước đó. Không thể cập nhật tồn kho.',
            ]);
        }

        $isActivating = $picking->active == 'Chưa hoàn thành';
        if (!$isActivating) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể huỷ duyệt phiếu đã hoàn thành.',
            ]);
        }

        $typeMap = [
            'Nhập'      => 'import',
            'Xuất'      => 'export',
            'Khai thác' => 'import',
        ];
        $stdType = $typeMap[$picking->type] ?? null;
        if (!$stdType) {
            return response()->json([
                'success' => false,
                'message' => 'Loại phiếu không hợp lệ.',
            ]);
        }

        $productionWarehouseId = 2;

        DB::beginTransaction();
        try {
            // 1) Nếu là Xuất
            if ($stdType === 'export') {
                $warehouseID = (int) $picking->warehouseID;
                $need = [];
                foreach ($picking->productPickings as $item) {
                    $need[$item->productID] = ($need[$item->productID] ?? 0) + (float)$item->quantity;
                }
                // lock trước khi check
                $stocks = InventoryStock::where('warehouseID', $warehouseID)
                    ->whereIn('productID', array_keys($need))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('productID');
                foreach ($need as $pid => $qtyNeed) {
                    $available = (float)($stocks[$pid]->quantity ?? 0);
                    if ($available < $qtyNeed) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Không đủ tồn kho để xuất. Sản phẩm ID $pid còn $available, cần $qtyNeed.",
                        ]);
                    }
                }
            }
            // 2) Ghi giao dịch vào kho theo từng dòng chi tiết
            foreach ($picking->productPickings as $line) {
                $productID   = $line->productID;
                $quantity    = (float)$line->quantity;
                $warehouseID = (int)$picking->warehouseID;
                $unitID      = optional(Product::find($productID))->unitID;

                if ($picking->type === 'Khai thác') {
                    // Nhập vào kho sản xuất, KHÔNG gọi record($stdType) nữa
                    $inventory->record('import', [
                        'productID'   => $productID,
                        'warehouseID' => (int)$productionWarehouseId,
                        'unitID'      => $unitID,
                        'quantity'    => $quantity,
                        'reference'   => $picking,
                        'code'        => $picking->code,
                        'note'        => "Khai thác #{$picking->code}",
                        'date'  => $picking->createDate,
                    ]);
                    continue;
                }

                // Nhập / Xuất
                $inventory->record($stdType, [
                    'productID'   => $productID,
                    'warehouseID' => $warehouseID,
                    'unitID'      => $unitID,
                    'quantity'    => $quantity,
                    'reference'   => $picking,
                    'code'        => $picking->code,
                    'note'        => "Phiếu {$picking->type} #{$picking->code}",
                    'date'  => $picking->createDate,
                ]);
            }
            // 4) Đổi trạng thái phiếu
            $picking->active = 'Hoàn thành';
            $picking->save();
            DB::commit();
            return response()->json([
                'success' => true,
                'status'  => 'Hoàn thành',
                'message' => 'Đã duyệt phiếu và cập nhật tồn kho thành công.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi duyệt phiếu: ' . $e->getMessage(),
            ], 422);
        }
    }
    public function toggleActiveOld(Request $request, $id)
    {
        $picking = Picking::with('productPickings')->findOrFail($id);

        if ($picking->status !== 'Hoạt động') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ phiếu đang Hoạt động mới được phép thay đổi trạng thái.',
            ]);
        }

        if ($picking->active === 'Hoàn thành') {
            return response()->json([
                'success' => false,
                'message' => 'Phiếu đã được duyệt trước đó. Không thể cập nhật tồn kho.'
            ]);
        }

        $isActivating = $picking->active == 'Chưa hoàn thành';
        $picking->active = $isActivating ? 'Hoàn thành' : 'Chưa hoàn thành';

        if ($isActivating) {
            $productionWarehouseId = 2;

            foreach ($picking->productPickings as $item) {
                $productID = $item->productID;
                $warehouseID = $picking->warehouseID;
                $quantity = $item->quantity;
                $product = Product::find($productID);

                $stock = InventoryStock::firstOrCreate(
                    ['productID' => $productID, 'warehouseID' => $warehouseID, 'unitID' => $product->unitID],
                    ['status' => 'Hoạt động', 'quantity' => 0]
                );
                $stock->status = 'Hoạt động';

                if ($picking->type === 'Nhập') {
                    $stock->quantity += $quantity;
                } elseif ($picking->type === 'Xuất') {
                    if ($stock->quantity < $quantity) {
                        $picking->active = 'Chưa hoàn thành';
                        return response()->json([
                            'success' => false,
                            'message' => "Không đủ tồn kho để xuất sản phẩm: $productID.",
                        ]);
                    }
                    $stock->quantity -= $quantity;
                } elseif ($picking->type === 'Khai thác') {
                    $stock->quantity += $quantity;

                    $productionStock = InventoryStock::firstOrCreate(
                        ['productID' => $productID, 'warehouseID' => $productionWarehouseId, 'unitID' => $product->unitID],
                        ['status' => 'Hoạt động', 'quantity' => 0]
                    );
                    $productionStock->status = 'Hoạt động';
                    $productionStock->quantity += $quantity;
                    $productionStock->save();
                }

                $stock->save();
            }
        }

        $picking->save();

        return response()->json([
            'success' => true,
            'status' => $picking->active == 'Hoàn thành' ? 'Hoàn thành' : 'Chưa hoàn thành',
            'message' => 'Cập nhật phiếu thành công.',
        ]);
    }


    public function getUnit($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Sản phẩm không tồn tại'], 404);
        }

        return response()->json([
            'unitID' => $product->unitID
        ]);
    }
    // <button data-id="{{ $picking->id }}"
    //         class="btn toggle-active {{ $picking->active ? 'bg-success' : 'bg-danger' }}">
    //     {{ $picking->active ? 'Hoàn thành' : 'Chưa hoàn thành' }}
    // </button>

    public function add(Request $request)
    {
        $categories = Category::all();
        $products = Product::all();
        $units = UnitOfMeasure::all();
        $users = User::all();
        $warehouses = WareHouse::all();
        $workers = Worker::all();
        return view('pwarehouses.add_pwarehouses', compact('products', 'units', 'categories', 'users', 'warehouses', 'workers'));
    }
    public function save(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|unique:pickings,code',
            'name' => 'required',
            'type' => 'required',
            'warehouseID' => 'required|exists:ware_houses,id',
            'createName' => 'required',
            'createDate' => 'required|date',
            'desc' => 'nullable|string',
            'materials' => 'required|array|min:1',
            'materials.*.productID' => 'required|exists:products,id',
            'materials.*.quantity' => 'required|numeric|min:1',
        ], [
            'code.required' => 'Mã phiếu là bắt buộc.',
            'code.unique' => 'Mã phiếu đã tồn tại.',
            'materials.required' => 'Cần nhập ít nhất một vật tư.',
            'materials.*.productID.required' => 'Chọn tên vật tư.',
            'materials.*.quantity.required' => 'Số lượng không được để trống.',
        ]);

        DB::beginTransaction();

        try {
            $picking = Picking::create([
                'code' => $data['code'],
                'name' => $data['name'],
                'type' => $data['type'],
                'warehouseID' => $data['warehouseID'],
                'createName' => $data['createName'],
                'createDate' => $data['createDate'],
                'desc' => $data['desc'] ?? null,
                'active' => $request->active ?? 'Chưa hoàn thành',
                'status' => $request->status ?? 'Hoạt động',
            ]);

            foreach ($data['materials'] as $material) {
                ProductPicking::create([
                    'pickingID' => $picking->id,
                    'productID' => $material['productID'],
                    'quantity' => $material['quantity'],
                ]);
            }

            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'create',
                'model_type' => 'Picking',
                'details' => "Đã tạo phiếu: " . $data['name'] . " với mã: " . $data['code'],
            ]);

            DB::commit();
            return redirect()->route('pwarehouses.index')->with('message', 'Tạo phiếu thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi tạo phiếu: ' . $e->getMessage())->withInput();
        }
    }
    public function edit($id)
    {
        $categories = Category::all();
        $products = Product::all();
        $units = UnitOfMeasure::all();
        $users = User::all();
        $warehouses = WareHouse::all();
        $workers = Worker::all();
        $picking = Picking::with('productPickings.product')->findOrFail($id);
        return view('pwarehouses.edit_pwarehouses', compact('picking', 'units', 'categories', 'users', 'warehouses', 'workers', 'products'));
    }
    public function update(Request $request, $id)
    {
        $existingPicking = Picking::where('code', $request->code)->where('id', '!=', $id)->first();

        $existingPicking = Picking::where(function ($query) use ($request, $id) {
            $query->where('name', $request->name)
                ->orWhere('code', $request->code);
        })->where('id', '!=', $id)->first();

        if ($existingPicking) {
            if ($existingPicking->name === $request->name) {
                return redirect()->back()->with(['error' => 'Tên phiếu kho này đã tồn tại!']);
            }
            if ($existingPicking->code === $request->code) {
                return redirect()->back()->with(['error' => 'Mã phiếu kho này đã tồn tại!']);
            }
        }
        $pic = Picking::find($id);
        if (!$pic) {
            return redirect()->back()->with('error', 'Phiếu kho không tồn tại');
        }
        $data = $request->validate([
            'code' => 'required|unique:pickings,code,' . $id,
            'name' => 'required',
            'type' => 'required',
            'warehouseID' => 'required|exists:ware_houses,id',
            'createName' => 'required',
            'createDate' => 'required|date',
            'desc' => 'nullable|string',
            // 'materials' => 'required|array',
            'productPickings.*.productID' => 'required',
            'productPickings.*.quantity' => 'required|numeric',
        ], [
            'code.required' => 'Mã phiếu là bắt buộc.',
            'code.unique' => 'Mã phiếu đã tồn tại.',
            // 'productPickings.required' => 'Cần nhập ít nhất một vật tư.',
            'productPickings.*.productID.required' => 'Chọn tên vật tư.',
            'productPickings.*.quantity.required' => 'Số lượng không được để trống.',
        ]);
        DB::beginTransaction();

        try {
            $picking = Picking::findOrFail($id);
            $picking->update([
                'code' => $data['code'],
                'name' => $data['name'],
                'type' => $data['type'],
                'warehouseID' => $data['warehouseID'],
                'createName' => $data['createName'],
                'createDate' => $data['createDate'],
                'desc' => $data['desc'] ?? null,
                'status' => $request->status ?? 'Hoạt động',
            ]);

            if (isset($data['productPickings']) && count($data['productPickings']) > 0) {
                $currentProductIDs = [];

                foreach ($data['productPickings'] as $material) {
                    $currentProductIDs[] = $material['productID'];

                    $existingProductPicking = ProductPicking::where('pickingID', $picking->id)
                        ->where('productID', $material['productID'])
                        ->first();

                    if ($existingProductPicking) {
                        $existingProductPicking->update([
                            'quantity' => $material['quantity'],
                        ]);
                    } else {
                        ProductPicking::create([
                            'pickingID' => $picking->id,
                            'productID' => $material['productID'],
                            'quantity' => $material['quantity'],
                        ]);
                    }
                }

                // XÓA các vật tư không còn trong danh sách productPickings gửi lên
                ProductPicking::where('pickingID', $picking->id)
                    ->whereNotIn('productID', $currentProductIDs)
                    ->delete();
            }


            // Log the action in the ActionHistory table
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'update',
                'model_type' => 'Picking',
                'details' => "Đã cập nhật phiếu: " . $data['name'] . " với mã: " . $data['code'],
            ]);

            DB::commit();
            return redirect()->route('pwarehouses.index')->with('message', 'Cập nhật phiếu thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi cập nhật phiếu: ' . $e->getMessage())->withInput();
        }
    }
    public function destroy($id)
    {
        $pic = Picking::find($id);
        $pic->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $pic = Picking::whereIn('id', $request->ids)->get();

        foreach ($pic as $pic) {
            $pic->status = ($pic->status === 'Hoạt động') ? 'Không hoạt động' : 'Hoạt động';
            $pic->save();
        }
        return response()->json(['message' => 'Thành Công']);
    }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        $picToDelete = Picking::whereIn('id', $request->ids)->get();

        Picking::whereIn('id', $request->ids)->delete();

        foreach ($picToDelete as $pic) {
            ActionHistory::create([
                'user_id' => Auth::id(),  // ID của người thực hiện hành động
                'action_type' => 'delete',  // Loại hành động "delete"
                'model_type' => 'Picking',  // Model "Picking"
                'details' => "Đã xóa phiếu kho: " . $pic->name . " với mã: " . $pic->code,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các phiếu kho được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $pic = Picking::find($request->id);
        if ($pic) {
            $pic->status = $pic->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $pic->save();
            return response()->json(['success' => true, 'status' => $pic->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
