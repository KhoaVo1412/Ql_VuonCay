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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class PWareHouseController extends Controller
{
    public function index(Request $request)
    {

        $all_pwarehouses = Picking::with('productPickings.product', 'warehouse')->orderBy('id', 'desc')->get();
        if ($request->ajax()) {
            return DataTables::of($all_pwarehouses)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('code', function ($row) {
                    return $row->code;
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
                    return $row->createDate ?? 'Không rõ';
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
        // return view('decomposes.all_decomposes', compact('gardens', 'workers', 'decomposes', 'plots'));
        return view('pwarehouses.all_pwarehouses');
    }
    public function toggleActive(Request $request, $id)
    {
        $picking = Picking::with('productPickings')->findOrFail($id);
        if ($picking->status !== 'Hoạt động') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ phiếu đang Hoạt động mới được phép thay đổi trạng thái.',
            ]);
        }

        $isActivating = $picking->active == 'Chưa hoàn thành';
        $picking->active = $isActivating ? 'Hoàn thành' : 'Chưa hoàn thành';

        if ($isActivating) {
            foreach ($picking->productPickings as $item) {
                $productID = $item->productID;
                $warehouseID = $picking->warehouseID;
                $quantity = $item->quantity;

                $stock = InventoryStock::firstOrCreate(
                    ['productID' => $productID, 'warehouseID' => $warehouseID],
                    ['quantity' => 0]
                );

                if ($picking->type === 'Nhập') {
                    $stock->quantity += $quantity;
                } elseif ($picking->type === 'Xuất') {
                    if ($stock->quantity < $quantity) {
                        $picking->active = 0;
                        return response()->json([
                            'success' => false,
                            'message' => "Không đủ tồn kho để xuất sản phẩm: $productID.",
                        ]);
                    }
                    $stock->quantity -= $quantity;
                }

                $stock->save();
            }
        }

        $picking->save();

        return response()->json([
            'success' => true,
            'status' => $picking->active == 1 ? 'Hoàn thành' : 'Chưa hoàn thành',
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
        $farms = Picking::find($id);
        if (!$farms) {
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
        $farms = Picking::find($id);
        $farms->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $farms = Picking::whereIn('id', $request->ids)->get();

        foreach ($farms as $farm) {
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
        $farmsToDelete = Picking::whereIn('id', $request->ids)->get();

        Picking::whereIn('id', $request->ids)->delete();

        foreach ($farmsToDelete as $farm) {
            ActionHistory::create([
                'user_id' => Auth::id(),  // ID của người thực hiện hành động
                'action_type' => 'delete',  // Loại hành động "delete"
                'model_type' => 'Picking',  // Model "Picking"
                'details' => "Đã xóa phiếu kho: " . $farm->farm_name . " với mã: " . $farm->farm_code,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các phiếu kho được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $farm = Picking::find($request->id);
        if ($farm) {
            $farm->status = $farm->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $farm->save();
            return response()->json(['success' => true, 'status' => $farm->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
