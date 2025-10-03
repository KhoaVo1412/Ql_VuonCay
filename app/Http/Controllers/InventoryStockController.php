<?php

namespace App\Http\Controllers;

use App\Models\ActionHistory;
use App\Models\InventoryStock;
use App\Models\InventoryTransaction;
use App\Models\Picking;
use App\Models\Product;
use App\Models\WareHouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class InventoryStockController extends Controller
{
    public function indexhts(Request $request)
    {
        $warehouses = WareHouse::all();
        $products = Product::all();

        if ($request->ajax()) {
            $mode = $request->input('mode');
            $from = $request->input('from');
            $to   = $request->input('to');
            $all_stock_hts = InventoryTransaction::query()
                ->with([
                    'warehouse',
                    'product',
                    'reference' => fn($q) => $q->select('id', 'type', 'code')
                ])
                ->when($request->filled('warehouse_id'), fn($q) => $q->where('warehouseID', $request->warehouse_id))
                ->when($request->filled('product_id'),   fn($q) => $q->where('productID',   $request->product_id))

                ->when($request->filled('date_id'), fn($q) => $q->where('date', $request->date_id))
                ->when($from, fn($q) => $q->where('date', '>=', Carbon::parse($from)->startOfDay()))
                ->when($to,   fn($q) => $q->where('date', '<=', Carbon::parse($to)->endOfDay()))
                ->when($mode === 'xuat', fn($q) => $q->where('type', 'export'))
                ->when($mode === 'nhap', function ($q) {
                    $q->where('type', 'import')
                        ->where(function ($sub) {
                            // import nhưng KHÔNG phải phiếu Picking kiểu 'Khai thác'
                            $sub->whereDoesntHaveMorph('reference', [Picking::class])
                                ->orWhereHasMorph('reference', [Picking::class], fn($p) => $p->where('type', '!=', 'Khai thác'));
                        });
                })

                ->when($mode === 'khaithac', function ($q) {
                    $q->where('type', 'import')
                        ->whereHasMorph('reference', [Picking::class], fn($p) => $p->where('type', 'Khai thác'));
                })

                ->orderByDesc('id');
            return DataTables::of($all_stock_hts)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->addColumn('code', function ($row) {
                    return $row->code ?? 'N/A';
                })
                ->addColumn('date', function ($row) {
                    return Carbon::parse($row->date)->format('d/m/Y');
                })
                ->addColumn('productID', function ($row) {
                    return $row->product ? $row->product->name : 'N/A';
                })
                ->addColumn('category', function ($row) {
                    return $row->product->category ? $row->product->category->name : 'N/A';
                })
                ->addColumn('unit', function ($row) {
                    return $row->unit ? $row->unit->name : 'N/A';
                })
                ->addColumn('warehouseID', function ($row) {
                    return $row->warehouse ? $row->warehouse->name : 'N/A';
                })
                ->addColumn('quantity_changed', function ($row) {
                    return number_format((float)$row->quantity_changed, 2, '.', '');
                })
                ->addColumn('balance_after', function ($row) {
                    return number_format((float)$row->balance_after, 2, '.', '');
                })
                ->editColumn('type', fn($row) => $row->display_type)

                // ->editColumn('status', function ($row) {
                //     $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                //     $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                //     return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                // })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-stocks/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->farm_name ?? 'N/A') . '</span>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/stocks/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'date', 'type', 'code', 'quantity_changed', 'balance_after',  'category', 'stt', 'quantity', 'warehouseID', 'productID', 'status', 'action'])
                ->make(true);
        }
        return view('stock.all_stock_hts', compact('products', 'warehouses'));
    }
    public function index(Request $request)
    {
        $warehouses = WareHouse::all();
        $products = Product::all();
        // $all_stock = InventoryStock::with('warehouse', 'product')->orderBy('id', 'desc')->get();
        // dd($all_stock);
        if ($request->ajax()) {
            $all_stock = InventoryStock::with('warehouse', 'product')
                ->when($request->filled('warehouse_id'), function ($q) use ($request) {
                    $q->where('warehouseID', $request->warehouse_id); // hoặc warehouse_id nếu cột snake
                })
                ->orderByDesc('id');
            return DataTables::of($all_stock)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->addColumn('productID', function ($row) {
                    return $row->product ? $row->product->name : 'N/A';
                })
                ->addColumn('category', function ($row) {
                    return $row->product->category ? $row->product->category->name : 'N/A';
                })
                ->addColumn('unit', function ($row) {
                    return $row->unit ? $row->unit->name : 'N/A';
                })
                ->addColumn('warehouseID', function ($row) {
                    return $row->warehouse ? $row->warehouse->name : 'N/A';
                })
                ->addColumn('quantity', function ($row) {
                    return $row->quantity;
                })

                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-stocks/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->farm_name ?? 'N/A') . '</span>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/stocks/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'category', 'stt', 'quantity', 'warehouseID', 'productID', 'status', 'action'])
                ->make(true);
        }
        return view('stock.all_stock', compact('products', 'warehouses'));
    }
    public function add()
    {
        // $gardens = Garden::all();
        $totalPlants = InventoryStock::withCount('plants')->get()->sum('plants_count');

        return view('stock.add_stock', compact('totalPlants'));
    }
    public function save(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'plantCode' => 'required',
            'InventoryStockName' => 'required',
            'InventoryStockArea' => 'required',
            'plantCount' => 'required',
            'gardenID' => 'required|exists:gardens,id',
            'status' => 'nullable',
        ]);
        // $existingInventoryStock = InventoryStock::where('farm_name', $request->farm_name)->first();
        $existingCode = InventoryStock::where('InventoryStockName', $request->InventoryStockName)->first();

        if ($existingCode) {
            return redirect()->back()->with(['error' => 'tồn kho này đã tồn tại!']);
        }

        InventoryStock::create([
            'plantCode' => $request->plantCode,
            'InventoryStockName' => $request->InventoryStockName,
            'InventoryStockArea' => $request->InventoryStockArea,
            'plantCount' => $request->plantCount,
            'gardenID' => $request->gardenID,
            'status' => $request->status ?? 'Hoạt động',
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'InventoryStock',
            'details' => "Đã tạo tồn kho: " . $request->farm_name . " với mã: " . $request->farm_code,
        ]);
        session()->flash('message', 'Tạo tồn kho thành công.');
        return redirect()->back();
    }
    public function edit($id)
    {
        $warehouses = WareHouse::all();
        $stock = InventoryStock::find($id);

        return view('stock.edit_stock', compact('stock', 'warehouses'));
    }
    public function update(Request $request, $id)
    {
        $existingInventoryStock = InventoryStock::where('InventoryStockName', $request->InventoryStockName)->where('id', '!=', $id)->first();

        $existingInventoryStock = InventoryStock::where(function ($query) use ($request, $id) {
            $query->where('InventoryStockName', $request->InventoryStockName);
        })->where('id', '!=', $id)->first();

        if ($existingInventoryStock) {
            if ($existingInventoryStock->InventoryStockName === $request->InventoryStockName) {
                return redirect()->back()->with(['error' => 'tồn kho này đã tồn tại!']);
            }
        }
        $stock = InventoryStock::find($id);
        if (!$stock) {
            return redirect()->back()->with('error', 'tồn kho không tồn tại');
        }
        $request->validate([
            'plantCode' => 'required',
            'InventoryStockName' => 'required',
            'InventoryStockArea' => 'required',
            'plantCount' => 'required',
            'gardenID' => 'required|exists:gardens,id',
            'status' => 'nullable',
        ]);
        $stock->update([
            'plantCode' => $request->plantCode,
            'InventoryStockName' => $request->InventoryStockName,
            'InventoryStockArea' => $request->InventoryStockArea,
            'plantCount' => $request->plantCount,
            'gardenID' => $request->gardenID,
            'status' => $request->status,
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'update',
            'model_type' => 'InventoryStock',
            'details' => "Đã cập nhật tồn kho: " . $stock->InventoryStockName,
        ]);
        return redirect()->route('stock.index')->with('message', 'Cập nhật tồn kho thành công');
    }
    public function destroy($id)
    {
        $stock = InventoryStock::find($id);
        $stock->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $stock = InventoryStock::whereIn('id', $request->ids)->get();

        foreach ($stock as $farm) {
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
        $stockToDelete = InventoryStock::whereIn('id', $request->ids)->get();

        InventoryStock::whereIn('id', $request->ids)->delete();

        foreach ($stockToDelete as $farm) {
            ActionHistory::create([
                'user_id' => Auth::id(),  // ID của người thực hiện hành động
                'action_type' => 'delete',  // Loại hành động "delete"
                'model_type' => 'InventoryStock',  // Model "InventoryStock"
                'details' => "Đã xóa tồn kho: " . $farm->farm_name . " với mã: " . $farm->farm_code,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các tồn kho được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $stocks = InventoryStock::find($request->id);
        if ($stocks) {
            $stocks->status = $stocks->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $stocks->save();
            return response()->json(['success' => true, 'status' => $stocks->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
    public function toggleActives(Request $request)
    {
        $stocks = InventoryStock::find($request->id);
        if ($stocks) {
            $stocks->active = $stocks->active == 'Hoàn thành' ? 'Chưa hoàn thành' : 'Hoàn thành';
            $stocks->save();
            return response()->json(['success' => true, 'active' => $stocks->active]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
