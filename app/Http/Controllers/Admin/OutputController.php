<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\InventoryStock;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\Product;
use App\Models\UnitOfMeasure;
use App\Models\WareHouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class OutputController extends Controller
{
    public function index(Request $request)
    {
        $all_outputs = Invoice::orderBy('id', 'desc')->get();
        // dd($all_outputs);
        if ($request->ajax()) {
            return DataTables::of($all_outputs)
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
                ->editColumn('name', function ($row) {
                    return  $row->name;
                })
                ->addColumn('date', function ($row) {
                    return $row->date;
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Duyệt' ? 'success' : 'danger';
                    $statusText = $row->status == 'Duyệt' ? 'Duyệt' : 'Chờ duyệt';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-outputs/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        <a href="/outputs/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'stt', 'code', 'name', 'date', 'status', 'action'])
                ->make(true);
        }
        return view('outputs.all_outputs');
    }
    public function productsByWarehouse($warehouseId)
    {
        $rows = DB::table('warehouse_products as wp')
            ->join('products as p', 'p.id', '=', 'wp.productID')
            ->join('unit_of_measures as u', 'u.id', '=', 'wp.unitID')
            ->where('wp.warehouseID', $warehouseId)
            ->select('p.id as product_id', 'p.name as product_name', 'u.id as unit_id', 'u.name as unit_name')
            ->orderBy('p.name')
            ->get();

        $grouped = $rows->groupBy('product_id')->map(function ($rows) {
            return [
                'id'    => $rows->first()->product_id,
                'name'  => $rows->first()->product_name,
                'units' => $rows->map(fn($r) => ['id' => $r->unit_id, 'name' => $r->unit_name])->values(),
            ];
        })->values();

        return response()->json($grouped);
    }
    public function add()
    {
        $warehouseId = 2;
        $pairs = InvoiceProduct::query()
            ->where('warehouseID', $warehouseId)
            ->with([
                'product:id,name',
                'unit:id,name',
            ])
            ->select('productID', 'unitID')
            ->distinct()
            ->get();
        $productsGrouped = $pairs->groupBy('productID')->map(function ($rows) {
            $first = $rows->first();
            return [
                'id'    => $first->productID,
                'name'  => optional($first->product)->name ?? ('SP#' . $first->productID),
                'units' => $rows->map(function ($r) {
                    return [
                        'id'   => $r->unitID,
                        'name' => optional($r->unit)->name ?? ('ĐV#' . $r->unitID),
                    ];
                })->unique('id')->values(),
            ];
        })->values()->toArray();

        $warehouses = WareHouse::where('id', $warehouseId)->get();

        $units = UnitOfMeasure::all();

        return view('outputs.add_outputs', compact('warehouses', 'productsGrouped', 'units'));
    }
    public function save(Request $request)
    {
        $request->validate([
            'code' => 'nullable',
            'name' => 'required',
            'supplier' => 'nullable|string',
            'created_by' => 'nullable|string',
            'date' => 'required|date',
            'type' => 'nullabale|string',
            'invoice_products' => 'required|array',
            'invoice_products.*.productID' => 'nullable',
            'invoice_products.*.quantity' => 'required|numeric|min:0',
            'invoice_products.*.warehousesID' => 'nullable',
            'invoice_products.*.unitID' => 'nullable',
            'invoice_products.*.quality' => 'nullable|string',
            'invoice_products.*.slice' => 'nullable|integer|min:0',
            'invoice_products.*.price' => 'nullable|numeric|min:0',
        ]);

        $existingName = Invoice::where('name', $request->name)->first();
        $existingCode = Invoice::where('code', $request->code)->first();

        if ($existingCode) {
            return redirect()->back()->with(['error' => 'Mã phiếu này đã tồn tại!']);
        }
        if ($existingName) {
            return redirect()->back()->with(['error' => 'Tên phiếu này đã tồn tại!']);
        }

        $PnameSlug = Str::slug($request->name, '_');
        $prefix = '#' . $PnameSlug . '_';
        do {
            $randomCode = $prefix . rand(100, 999);
        } while (Invoice::where('code', $randomCode)->exists());
        $invoice = Invoice::create([
            'code' => $randomCode,
            'name' => $request->name,
            'supplier' => $request->supplier,
            'created_by' => $request->created_by,
            'date' => $request->date,
            'desc' => $request->desc,
            'type' => 'Thu mua',
            'status' => 'Chờ duyệt',
        ]);
        foreach ($request->invoice_products as $productData) {
            InvoiceProduct::create([
                'invoiceID' => $invoice->id,
                'warehouseID' => $productData['warehouseID'],
                'productID' => $productData['productID'],
                'quantity' => $productData['quantity'],
                'unitID' => $productData['unitID'],
                'quality' => $productData['quality'] ?? null,
                'slice' => $productData['slice'] ?? 0,
                'price' => $productData['price'] ?? 0,
                'status' => 'Chờ duyệt',
            ]);
        }
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'Invoice',
            'details' => "Đã tạo phiếu: " . $request->name . " với mã: " . $request->code,
        ]);
        return redirect()->route('outputs.index')->with('message', 'Tạo phiếu thành công.');
    }
    public function edit($id)
    {
        $warehouseId = 2;
        $outputs = Invoice::with(['invoice_products' => function ($q) {
            $q->select('id', 'invoiceID', 'warehouseID', 'productID', 'unitID', 'quantity', 'quality', 'slice', 'price');
        }])->findOrFail($id);
        $rows = DB::table('invoice_products as ip')
            ->join('products as p', 'p.id', '=', 'ip.productID')
            ->join('unit_of_measures as u', 'u.id', '=', 'ip.unitID')
            ->where('ip.warehouseID', $warehouseId)
            ->whereNotNull('ip.productID')
            ->whereNotNull('ip.unitID')
            ->groupBy('p.id', 'p.name', 'u.id', 'u.name')
            ->orderBy('p.name')
            ->get([
                'p.id as product_id',
                'p.name as product_name',
                'u.id as unit_id',
                'u.name as unit_name',
            ]);
        $productsGrouped = $rows->groupBy('product_id')->map(function ($g) {
            return [
                'id'    => $g->first()->product_id,
                'name'  => $g->first()->product_name,
                'units' => $g->map(fn($r) => ['id' => $r->unit_id, 'name' => $r->unit_name])->values(),
            ];
        })->values()->toArray();

        $warehouse = WareHouse::select('id', 'name')->findOrFail($warehouseId);

        return view('outputs.edit_outputs', compact('outputs', 'warehouse', 'productsGrouped'));
    }
    public function update(Request $request, $id)
    {
        // Validate dữ liệu nếu cần
        $request->validate([
            'code' => 'nullable|string',
            'name' => 'required|string',
            'desc' => 'nullable|string',
            'supplier' => 'nullable|string',
            'status' => 'nullable|string',
            'date' => 'required|date',
            'invoice_products' => 'required|array',
            'invoice_products.*.warehouseID' => 'required|integer|exists:ware_houses,id',
            'invoice_products.*.productID' => 'required|integer|exists:products,id',
            'invoice_products.*.quantity' => 'required|numeric',
            'invoice_products.*.unitID' => 'nullable|integer|exists:unit_of_measures,id',
            'invoice_products.*.quality' => 'nullable|string',
            'invoice_products.*.slice' => 'nullable|integer',
            'invoice_products.*.price' => 'nullable|numeric',
        ]);

        $invoice = Invoice::with('invoice_products')->findOrFail($id);

        $invoice->update($request->only(['code', 'name', 'desc', 'supplier', 'status', 'date']));
        $invoice->invoice_products()->delete();

        foreach ($request->invoice_products as $productData) {
            $invoice->invoice_products()->create([
                'warehouseID' => $productData['warehouseID'],
                'productID' => $productData['productID'],
                'quantity' => $productData['quantity'],
                'unitID' => $productData['unitID'] ?? null,
                'quality' => $productData['quality'] ?? null,
                'slice' => $productData['slice'] ?? null,
                'price' => $productData['price'] ?? null,
                'status' => 'Chờ duyệt',
            ]);
        }

        return redirect()->route('outputs.index')->with('message', 'Cập nhật phiếu thành công!');
    }

    public function destroy($id)
    {
        $outputs = Invoice::find($id);
        $outputs->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $outputs = Invoice::whereIn('id', $request->ids)->get();

        foreach ($outputs as $o) {
            $o->status = ($o->status === 'Duyệt') ? 'Chờ duyệt' : 'Duyệt';
            $o->save();
        }
        return response()->json(['message' => 'Thành Công']);
    }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        $outputsToDelete = Invoice::whereIn('id', $request->ids)->get();

        Invoice::whereIn('id', $request->ids)->delete();

        foreach ($outputsToDelete as $output) {
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'delete',
                'model_type' => 'Invoice',
                'details' => "Đã xóa phiếu: " . $output->name . " với mã: " . $output->code,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các phiếu được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    // public function toggleStatus(Request $request)
    // {
    //     $outputs = Invoice::find($request->id);
    //     if ($outputs) {
    //         $outputs->status = $outputs->status == 'Duyệt' ? 'Chờ duyệt' : 'Duyệt';
    //         $outputs->save();
    //         return response()->json(['success' => true, 'status' => $outputs->status]);
    //     } else {
    //         return response()->json(['success' => false]);
    //     }
    // }
    public function toggleStatus(Request $request)
    {
        $invoice = Invoice::with('invoice_products')->find($request->id);

        if (!$invoice) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy phiếu.']);
        }

        if ($invoice->type !== 'Thu mua') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ phiếu Thu mua mới được cập nhật tồn kho tại đây.'
            ]);
        }
        if ($invoice->status === 'Duyệt') {
            return response()->json([
                'success' => false,
                'message' => 'Phiếu đã được duyệt trước đó. Không thể duyệt lại hoặc cập nhật tồn kho.'
            ]);
        }
        try {
            DB::transaction(function () use ($invoice) {
                $from = $invoice->status; // 'Chờ duyệt' hoặc 'Duyệt'
                $to   = $from === 'Duyệt' ? 'Chờ duyệt' : 'Duyệt';

                foreach ($invoice->invoice_products as $item) {
                    $stock = InventoryStock::firstOrCreate(
                        [
                            'productID'   => $item->productID,
                            'warehouseID' => $item->warehouseID,
                            'unitID'      => $item->unitID,
                        ],
                        ['quantity' => 0, 'status' => 'Hoạt động']
                    );

                    if ($to === 'Duyệt') {
                        $stock->quantity += $item->quantity;
                    } else {
                        if ($stock->quantity < $item->quantity) {
                            throw new \RuntimeException(
                                "Không đủ tồn để hoàn tác cho sản phẩm {$item->productID}."
                            );
                        }
                        $stock->quantity -= $item->quantity;
                    }

                    $stock->save();
                }

                $invoice->status = $to;
                $invoice->save();
            });

            return response()->json(['success' => true, 'status' => $invoice->status]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
