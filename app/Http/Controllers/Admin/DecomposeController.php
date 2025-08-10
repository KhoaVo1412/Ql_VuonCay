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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class DecomposeController extends Controller
{
    public function index(Request $request)
    {
        $decomposes = Decompose::with(['warehouse', 'user'])->get();
        $workers = Worker::all();

        if ($request->ajax()) {
            return DataTables::of($decomposes)
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
                    return $row->date ?? 'Không rõ';
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
        return view('decomposes.all_decomposes', compact('decomposes', 'workers'));
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
        $products = Product::all(['id', 'name']);
        $units = UnitOfMeasure::all(['id', 'name']);

        return view('decomposes.add_decomposes', compact('warehouses', 'products', 'units'));
    }
    public function save(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'name' => 'required|string',
            'warehouseID' => 'required|integer',
            'userID' => 'required|integer',
            'date' => 'required|date',
            'desc' => 'nullable|string',
            'materials' => 'required|array', // Array of material data (including original and new product details)
            'materials.*.productID' => 'required|integer', // Original product ID
            'materials.*.productDecomposeID' => 'required|integer', // New product ID after decomposition
            'materials.*.productUnitID' => 'required|integer', // Unit ID for the original product
            'materials.*.quantityDecompose' => 'required', // Quantity to decompose
            'materials.*.quantityProduct' => 'required', // Quantity of new product created
            'materials.*.unitDecomposeID' => 'required', // Unit ID for the product
        ]);

        // Create a new Decompose entry
        $decompose = Decompose::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'warehouseID' => $validated['warehouseID'],
            'userID' => $validated['userID'],
            'status' => 'Hoạt động', // You can adjust status dynamically based on your logic
            'date' => $validated['date'],
            'desc' => $validated['desc'],
            'active' => 'Chưa Duyệt',
        ]);

        foreach ($validated['materials'] as $material) {
            ProductDecompose::create([
                'decomposeID' => $decompose->id, // Link to the current decomposition record
                'productID' => $material['productID'], // Original product ID
                'productDecomposeID' => $material['productDecomposeID'], // New product ID
                'unitDecomposeID' => $material['unitDecomposeID'], // Unit of decomposed product
                'productUnitID' => $material['productUnitID'], // Unit of the original product
                'quantityDecompose' => $material['quantityDecompose'], // Quantity to decompose
                'quantityProduct' => $material['quantityProduct'], // Quantity of new product created
            ]);

            // $oldProduct = Product::find($material['productID']);
            // $newProduct = Product::find($material['productDecomposeID']);

            // if ($oldProduct) {
            //     $oldProduct->quantity -= $material['quantityDecompose']; // Reduce original product's stock
            //     $oldProduct->save();
            // }

            // if ($newProduct) {
            //     $newProduct->quantity += $material['quantityProduct']; // Increase new product's stock
            //     $newProduct->save();
            // }
        }

        return redirect()->route('decomposes.index')->with('message', 'Tạo phiếu phân rã thành công.');
    }
    public function edit($id)
    {
        $warehouses = WareHouse::all(['id', 'name']);
        $products = Product::all(['id', 'name']);
        $units = UnitOfMeasure::all(['id', 'name']);
        $decomposes = Decompose::with('items')->findOrFail($id);
        return view('decomposes.edit_decomposes', compact('decomposes', 'warehouses', 'products', 'units'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'code' => 'required|string',
                'name' => 'required|string',
                'warehouseID' => 'required|integer',
                'userID' => 'required|integer',
                'date' => 'required|date',
                'desc' => 'nullable|string',
                'materials' => 'required|array',
                'materials.*.productID' => 'required|integer',
                'materials.*.productUnitID' => 'required|integer',
                'materials.*.quantityProduct' => 'required|numeric',
                'materials.*.productDecomposeID' => 'required|integer',
                'materials.*.unitDecomposeID' => 'required|integer',
                'materials.*.quantityDecompose' => 'required|numeric',
            ]);
            $decompose = Decompose::findOrFail($id);
            $decompose->code = $request->code;
            $decompose->name = $request->name;
            $decompose->warehouseID = $request->warehouseID;
            $decompose->userID = $request->userID;
            $decompose->date = $request->date;
            $decompose->desc = $request->desc;
            $decompose->save();

            ProductDecompose::where('decomposeID', $decompose->id)->delete();

            if ($request->has('materials')) {
                foreach ($request->materials as $material) {
                    ProductDecompose::create([
                        'decomposeID' => $decompose->id,
                        'productID' => $material['productID'] ?? null,
                        'quantityProduct' => $material['quantityProduct'] ?? 0,
                        'productUnitID' => $material['productUnitID'] ?? null,
                        'productDecomposeID' => $material['productDecomposeID'] ?? null,
                        'quantityDecompose' => $material['quantityDecompose'] ?? 0,
                        'unitDecomposeID' => $material['unitDecomposeID'] ?? null,
                    ]);
                }
            }
            // Product::where('id', $material['productID'])->decrement('quantity', $material['quantityDecompose']);
            // Product::where('id', $material['productDecomposeID'])->increment('quantity', $material['quantityProduct']);

            DB::commit();
            return redirect()->route('decomposes.index')->with('message', 'Cập nhật phiếu phân rã thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Lỗi: ' . $e->getMessage()]);
        }
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
                    $stockNew = InventoryStock::updateOrCreate(
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
