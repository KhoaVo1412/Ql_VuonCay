<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Decompose;
use App\Models\InventoryStock;
use App\Models\Product;
use App\Models\ProductDecompose;
use App\Models\UnitOfMeasure;
use App\Models\WareHouse;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class DecomposeController extends Controller
{
    public function stockItems(Request $r)
    {
        $warehouseId = (int) $r->query('warehouseID', 0);

        $rows = InventoryStock::with(['product:id,name', 'unit:id,name'])
            ->when($warehouseId, fn($q) => $q->where('warehouseID', $warehouseId))
            ->where('quantity', '>', 0)
            ->orderBy('productID')
            ->get(['id', 'productID', 'unitID', 'warehouseID', 'quantity'])
            ->map(fn($s) => [
                'stockID'     => $s->id,
                'productID'   => $s->productID,
                'productName' => $s->product?->name,
                'unitID'      => $s->unitID,
                'unitName'    => $s->unit?->name,
                'quantity'    => (float) $s->quantity,
            ]);

        return response()->json($rows);
    }
    public function index(Request $request)
    {
        $decomposes = Decompose::with(['warehouse', 'user'])->get();
        $workers = Worker::all();
        $warehouses = WareHouse::all();

        if ($request->ajax()) {
            $query = Decompose::with(['warehouse', 'user'])
                ->when($request->filled('warehouse_id'), function ($q) use ($request) {
                    $q->where('warehouseID', $request->warehouse_id);
                })
                ->when($request->filled('start_date'), function ($q) use ($request) {
                    $q->whereDate('date', $request->start_date);
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
                ->addColumn('warehouseName', function ($row) {
                    return $row->warehouse->name ?? 'Không rõ';
                })
                ->addColumn('date', function ($row) {
                    return $row->date ? Carbon::parse($row->date)->format('d/m/Y')
                        : null;
                })
                ->addColumn('userName', function ($row) {
                    return $row->user->name ?? 'Không rõ';
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->editColumn('active', function ($row) {
                    $activeClass = $row->active == 'Duyệt' ? 'success' : 'danger';
                    $activeText = $row->active == 'Duyệt' ? 'Duyệt' : 'Chưa duyệt';
                    return '<button class="badge bg-' . $activeClass . ' toggle-active" data-id="' . $row->id . '">' . $activeText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    return '
                    <div class="d-flex gap-1">
                        <a href="/edit-decomposes/' . $row->id . '" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal' . $row->id . '">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>
                    <div class="modal fade" id="deleteModal' . $row->id . '" tabindex="-1" aria-labelledby="deleteModalLabel' . $row->id . '" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel' . $row->id . '">Xác nhận xóa</h5>
                                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Bạn có chắc chắn muốn xóa quá trình phân rã vật tư: <strong>' . $row->name . '</strong>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                    <a href="/decomposes/delete/' . $row->id . '" class="btn btn-danger">Xóa</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    ';
                })
                ->rawColumns(['check', 'active', 'stt', 'warehouseName', 'userName', 'status', 'action'])  // Đảm bảo các cột như button, modal được render đúng
                ->make(true);
        }
        return view('decomposes.all_decomposes', compact('warehouses', 'decomposes', 'workers'));
    }
    public function approve($id)
    {
        DB::beginTransaction();

        try {
            $decompose = Decompose::with(['items', 'warehouse'])->findOrFail($id);

            if ($decompose->status === 'Duyệt') {
                return back()->with('warning', 'Phiếu này đã được duyệt.');
            }

            foreach ($decompose->items as $item) {
                $stockOld = InventoryStock::where('productID', $item->productID)
                    ->where('warehouseID', $decompose->warehouseID)
                    ->first();

                if (!$stockOld || $stockOld->quantity < $item->quantityDecompose) {
                    throw new \Exception("Không đủ vật tư gốc trong kho để phân rã.");
                }

                $stockOld->decrement('quantity', $item->quantityDecompose);
                InventoryStock::updateOrCreate(
                    [
                        'productID' => $item->productDecomposeID,
                        'warehouseID' => $decompose->warehouseID
                    ],
                    [
                        'quantity' => DB::raw('quantity + ' . $item->quantityProduct)
                    ]
                );
            }
            $decompose->update(['status' => 'Duyệt']);

            DB::commit();
            return back()->with('success', 'Duyệt phân rã thành công và cập nhật tồn kho.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi duyệt phân rã: ' . $e->getMessage());
        }
    }

    public function add()
    {
        $warehouses = WareHouse::all(['id', 'name']);
        $products = Product::select('id', 'name', 'unitID')
            ->with(['unit:id,name'])
            ->get();
        $units = UnitOfMeasure::all(['id', 'name']);

        return view('decomposes.add_decomposes', compact('warehouses', 'products', 'units'));
    }
    public function save(Request $request)
    {
        $validated = $request->validate([
            'code'        => 'required|string',
            'name'        => 'required|string',
            'warehouseID' => 'required|integer',
            'userID'      => 'required|integer',
            'date'        => 'required|date',
            'desc'        => 'nullable|string',

            'materials'                        => 'required|array|min:1',
            'materials.*.oldStockID'           => 'required|integer|exists:inventory_stocks,id',
            'materials.*.productID'            => 'required|integer',
            'materials.*.productUnitID'        => 'required|integer',
            'materials.*.quantityProduct'      => 'required|numeric|min:0.000001',

            'materials.*.productDecomposeID'   => 'required|integer',
            'materials.*.unitDecomposeID'      => 'required|integer',
            'materials.*.quantityDecompose'    => 'required|numeric|min:0.000001',
        ]);

        DB::transaction(function () use ($validated) {

            $decompose = Decompose::create([
                'code'        => $validated['code'],
                'name'        => $validated['name'],
                'warehouseID' => $validated['warehouseID'],
                'userID'      => $validated['userID'],
                'status'      => 'Hoạt động',
                'date'        => $validated['date'],
                'desc'        => $validated['desc'] ?? null,
                'active'      => 'Chưa Duyệt',
            ]);

            foreach ($validated['materials'] as $idx => $m) {
                // 1) Lấy thông tin tồn kho cũ
                $stock = InventoryStock::findOrFail($m['oldStockID']);
                // 2) Kiểm tra nhất quán kho / product / unit
                if ((int)$stock->warehouseID !== (int)$validated['warehouseID']) {
                    throw ValidationException::withMessages([
                        "materials.$idx.oldStockID" => "Dòng " . ($idx + 1) . ": tồn kho không thuộc kho đã chọn.",
                    ]);
                }
                if ((int)$stock->productID !== (int)$m['productID']) {
                    throw ValidationException::withMessages([
                        "materials.$idx.productID" => "Dòng " . ($idx + 1) . ": sản phẩm không khớp với tồn kho đã chọn.",
                    ]);
                }
                if ((int)$stock->unitID !== (int)$m['productUnitID']) {
                    throw ValidationException::withMessages([
                        "materials.$idx.productUnitID" => "Dòng " . ($idx + 1) . ": đơn vị gốc không khớp với tồn kho đã chọn.",
                    ]);
                }
                if ((int)$m['unitDecomposeID'] === (int)$m['productUnitID']) {
                    throw ValidationException::withMessages([
                        "materials.$idx.unitDecomposeID" => "Dòng " . ($idx + 1) . ": đơn vị phân rã phải khác đơn vị gốc.",
                    ]);
                }
                // 3) Lưu chi tiết (chưa trừ kho)
                ProductDecompose::create([
                    'decomposeID'         => $decompose->id,
                    'oldStockID'          => $stock->id,                // <-- thêm cột này vào bảng/Model nếu chưa có
                    'productID'           => $m['productID'],           // gốc
                    'productUnitID'       => $m['productUnitID'],       // đơn vị gốc
                    'quantityProduct'     => $m['quantityProduct'],     // số lượng trừ

                    'productDecomposeID'  => $m['productDecomposeID'],  // mới
                    'unitDecomposeID'     => $m['unitDecomposeID'],     // đơn vị mới
                    'quantityDecompose'   => $m['quantityDecompose'],   // số lượng cộng
                ]);
            }
        });
        return redirect()->route('decomposes.index')->with('message', 'Tạo phiếu phân rã thành công (chưa trừ tồn).');
    }
    public function edit($id)
    {
        $warehouses = WareHouse::all(['id', 'name']);
        $products   = Product::with(['unit:id,name'])
            ->get(['id', 'name', 'unitID']);
        $units      = UnitOfMeasure::all(['id', 'name']);
        $decomposes = Decompose::with(['items'])->findOrFail($id);
        return view(
            'decomposes.edit_decomposes',
            compact('decomposes', 'warehouses', 'products', 'units')
        );
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'code'        => 'required|string',
            'name'        => 'required|string',
            'warehouseID' => 'required|integer|exists:ware_houses,id',
            'userID'      => 'required|integer',
            'date'        => 'required|date',
            'desc'        => 'nullable|string',
            'materials'                         => 'required|array|min:1',
            // (gốc)
            'materials.*.oldStockID'            => 'required|integer|exists:inventory_stocks,id',
            'materials.*.productID'             => 'required|integer|exists:products,id',
            'materials.*.productUnitID'         => 'required|integer|exists:unit_of_measures,id',
            'materials.*.quantityProduct'       => 'required|numeric|min:0.000001',
            // (phân rã)
            'materials.*.productDecomposeID'    => 'required|integer|exists:products,id',
            'materials.*.unitDecomposeID'       => 'required|integer|exists:unit_of_measures,id',
            'materials.*.quantityDecompose'     => 'required|numeric|min:0.000001',
        ]);
        DB::transaction(function () use ($validated, $id) {
            $decompose = Decompose::findOrFail($id);
            $decompose->fill([
                'code'        => $validated['code'],
                'name'        => $validated['name'],
                'warehouseID' => $validated['warehouseID'],
                'userID'      => $validated['userID'],
                'date'        => $validated['date'],
                'desc'        => $validated['desc'] ?? null,
            ])->save();
            // Ghi lại chi tiết
            ProductDecompose::where('decomposeID', $decompose->id)->delete();
            foreach ($validated['materials'] as $row => $m) {
                $stock = InventoryStock::findOrFail($m['oldStockID']);
                if ((int)$stock->warehouseID !== (int)$validated['warehouseID']) {
                    throw ValidationException::withMessages([
                        "materials.$row.oldStockID" => "Dòng " . ($row + 1) . ": tồn kho không thuộc kho đã chọn.",
                    ]);
                }
                if ((int)$stock->productID !== (int)$m['productID']) {
                    throw ValidationException::withMessages([
                        "materials.$row.productID" => "Dòng " . ($row + 1) . ": sản phẩm không khớp tồn kho.",
                    ]);
                }
                if ((int)$stock->unitID !== (int)$m['productUnitID']) {
                    throw ValidationException::withMessages([
                        "materials.$row.productUnitID" => "Dòng " . ($row + 1) . ": đơn vị gốc không khớp tồn kho.",
                    ]);
                }
                if ((int)$m['unitDecomposeID'] === (int)$m['productUnitID']) {
                    throw ValidationException::withMessages([
                        "materials.$row.unitDecomposeID" => "Dòng " . ($row + 1) . ": đơn vị phân rã phải khác đơn vị gốc.",
                    ]);
                }
                ProductDecompose::create([
                    'decomposeID'         => $decompose->id,
                    // (gốc)
                    'oldStockID'          => $stock->id,
                    'productID'           => $m['productID'],
                    'productUnitID'       => $m['productUnitID'],
                    'quantityProduct'     => $m['quantityProduct'],
                    // (phân rã)
                    'productDecomposeID'  => $m['productDecomposeID'],
                    'unitDecomposeID'     => $m['unitDecomposeID'],
                    'quantityDecompose'   => $m['quantityDecompose'],
                ]);
            }
        });
        return redirect()->route('decomposes.index')
            ->with('message', 'Cập nhật phiếu phân rã thành công (chưa trừ tồn).');
    }

    public function destroy($id)
    {
        $decomposes = Decompose::find($id);
        $decomposes->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $decomposes = Decompose::whereIn('id', $request->ids)->get();

        foreach ($decomposes as $Decompose) {
            $Decompose->status = ($Decompose->status === 'Duyệt') ? 'Chưa duyệt' : 'Duyệt';
            $Decompose->save();
        }
        return response()->json(['message' => 'Thành Công']);
    }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        $decomposesToDelete = Decompose::whereIn('id', $request->ids)->get();

        Decompose::whereIn('id', $request->ids)->delete();

        foreach ($decomposesToDelete as $Decompose) {
            ActionHistory::create([
                'user_id' => Auth::id(),  // ID của người thực hiện hành động
                'action_type' => 'delete',  // Loại hành động "delete"
                'model_type' => 'Decompose',  // Model "Decompose"
                'details' => "Đã xóa phân rã: " . $Decompose->Decompose_name . " với mã: " . $Decompose->Decompose_code,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các phân rã được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $decompose = Decompose::with('items')->find($request->id);

        if (!$decompose) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy phiếu phân rã.']);
        }

        DB::beginTransaction();

        try {
            if ($decompose->active !== 'Duyệt') {

                foreach ($decompose->items as $item) {
                    if (
                        !$item->productID ||
                        !$item->productDecomposeID ||
                        !$item->unitDecomposeID ||
                        !$item->productUnitID ||
                        $item->quantityDecompose <= 0 ||
                        $item->quantityProduct <= 0
                    ) {
                        throw new \Exception("Dữ liệu phân rã không hợp lệ.");
                    }

                    $stockOld = InventoryStock::where('productID', $item->productID)
                        ->where('unitID', $item->productUnitID)
                        ->where('warehouseID', $decompose->warehouseID)
                        ->first();

                    if (!$stockOld || $stockOld->quantity < $item->quantityProduct) {
                        throw new \Exception("Không đủ tồn kho vật tư gốc.");
                    }

                    $stockOld->decrement('quantity', $item->quantityProduct);

                    // Tăng tồn kho vật tư mới
                    $stockNew = InventoryStock::firstOrCreate(
                        [
                            'productID' => $item->productDecomposeID,
                            'unitID' => $item->unitDecomposeID,
                            'warehouseID' => $decompose->warehouseID,
                            'status' => 'Hoạt động',
                        ],
                        [
                            'quantity' => 0
                        ]
                    );
                    $stockNew->increment('quantity', $item->quantityDecompose);
                }

                // Cập nhật trạng thái
                $decompose->active = 'Duyệt';
                $decompose->save();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'active' => $decompose->active,
                    'message' => 'Phiếu đã duyệt và cập nhật tồn kho.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Phiếu đã duyệt trước đó – không thể duyệt lại.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
