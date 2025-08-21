<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Decompose;
use App\Models\DiseasePlant;
use App\Models\Duty;
use App\Models\Evaluate;
use App\Models\FallentPlant;
use App\Models\GenTask;
use App\Models\InventoryStock;
use App\Models\Invoice;
use App\Models\MaterialProposal;
use App\Models\Picking;
use App\Models\TaskProductProposal;
use App\Models\Team;
use App\Models\TreatmentSessions;
use App\Models\UnitOfMeasure;
use App\Models\WareHouse;
use App\Models\Work;
use App\Models\Worker;
use Illuminate\Support\Facades\DB;

class WarehouseControllerApi extends Controller
{
    public function warehouses()
    {
        $all_ware = WareHouse::all();
        if ($all_ware->isNotEmpty()) {

            return response()->json([
                'data' => $all_ware,
                'status' => 200,
                'message' => 'Danh sách kho.',
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }
    public function categories()
    {
        $categories = Category::with('warehouse')->orderBy('id', 'desc')->get();
        if ($categories->isNotEmpty()) {

            return response()->json([
                'data' => $categories,
                'status' => 200,
                'message' => 'Danh sách vật tư.',
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }
    public function units()
    {
        $categories = UnitOfMeasure::all();
        if ($categories->isNotEmpty()) {

            return response()->json([
                'data' => $categories,
                'status' => 200,
                'message' => 'Danh sách đơn vị vật tư.',
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }

    public function pwarehouses()
    {
        $all_pwarehouses = Picking::with('productPickings.product', 'warehouse')->orderBy('id', 'desc')->get();

        if ($all_pwarehouses->isNotEmpty()) {
            return response()->json([
                'data' => $all_pwarehouses,
                'status' => 200,
                'message' => 'Danh sách phiếu kho.',
            ]);
        }

        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }

    public function detailPw($id)
    {
        $pwarehouses = Picking::with('productPickings.product', 'warehouse')->find($id);

        if (!$pwarehouses) {
            return response()->json([
                'message' => 'Phiếu kho không tồn tại.'
            ], 404);
        }
        // $pwarehouses->items = $pwarehouses->items->map(function ($item) {
        //     return [
        //         'id' => $item->id,
        //         'materialQuantity' => $item->materialQuantity,
        //         'unitID' => $item->unitID,
        //         'note' => $item->note,
        //         'status' => $item->status,
        //         'productName' => $item->product ? $item->product->name : null,
        //         'unitName' => $item->unit ? $item->unit->name : null,
        //     ];
        // });
        return response()->json([
            'status' => 200,
            'data' => $pwarehouses,
            'message' => 'Chi tiết phiếu kho.'
        ], 200);
    }
    public function decomposes()
    {
        $decomposes = Decompose::with(['items.unitDecompose', 'items.newProduct', 'items.productUnit', 'items.oldProduct', 'warehouse', 'user'])->get();

        if ($decomposes->isNotEmpty()) {
            $decomposes->map(function ($decompose) {
                $decompose->items = $decompose->items->map(function ($item) {
                    $item->productID = $item->oldProduct ? $item->oldProduct->name : null;
                    $item->productUnitID = $item->productUnit ? $item->productUnit->name : null;

                    $item->productDecomposeID = $item->newProduct ? $item->newProduct->name : null;
                    $item->unitDecomposeID = $item->unitDecompose ? $item->unitDecompose->name : null;
                    unset($item->newProduct);
                    unset($item->unitDecompose);
                    unset($item->oldProduct);
                    unset($item->productUnit);
                    return $item;
                });

                $decompose->warehouseID = $decompose->warehouse ? $decompose->warehouse->name : null;
                $decompose->userID = $decompose->user ? $decompose->user->name : null;

                unset($decompose->warehouse);
                unset($decompose->user);

                return $decompose;
            });

            return response()->json([
                'data' => $decomposes,
                'status' => 200,
                'message' => 'Danh sách phiếu phân rã.',
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }
    public function detailD($id)
    {
        $decomposes = Decompose::with(['items.unitDecompose', 'items.newProduct', 'items.productUnit', 'items.oldProduct', 'warehouse', 'user'])->find($id);

        if (!$decomposes) {
            return response()->json([
                'message' => 'Phiếu phân rã không tồn tại.'
            ], 404);
        }

        $decomposes->warehouseID = $decomposes->warehouse ? $decomposes->warehouse->name : null;
        $decomposes->userID = $decomposes->user ? $decomposes->user->name : null;

        unset($decomposes->warehouse);
        unset($decomposes->user);

        $decomposes->items = $decomposes->items->map(function ($item) {
            $item->productID = $item->oldProduct ? $item->oldProduct->name : null;
            $item->productUnitID = $item->productUnit ? $item->productUnit->name : null;

            $item->productDecomposeID = $item->newProduct ? $item->newProduct->name : null;
            $item->unitDecomposeID = $item->unitDecompose ? $item->unitDecompose->name : null;
            unset($item->newProduct);
            unset($item->unitDecompose);
            unset($item->oldProduct);
            unset($item->productUnit);
            return $item;
        });
        return response()->json([
            'status' => 200,
            'data' => $decomposes,
            'message' => 'Chi tiết phiếu phân rã.'
        ], 200);
    }

    public function outputs()
    {
        $all_outputs = Invoice::with('invoice_products.product', 'invoice_products.warehouse', 'invoice_products.unit')
            ->get();

        if ($all_outputs->isNotEmpty()) {
            $all_outputs->map(function ($outputs) {
                $outputs->invoice_products = $outputs->invoice_products->map(function ($item) {
                    $item->productName = $item->product ? $item->product->name : null;
                    $item->warehouseName = $item->warehouse ? $item->warehouse->name : null;
                    $item->unitName = $item->unit ? $item->unit->name : null;

                    unset($item->product);
                    unset($item->warehouse);
                    unset($item->unit);

                    return $item;
                });

                return $outputs;
            });

            return response()->json([
                'data' => $all_outputs,
                'status' => 200,
                'message' => 'Danh sách phiếu thu mua.',
            ]);
        }

        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }

    public function detailO($id)
    {
        $Invoice = Invoice::with('invoice_products.product', 'invoice_products.warehouse', 'invoice_products.unit')
            ->find($id);

        if (!$Invoice) {
            return response()->json([
                'message' => 'Phiếu thu mua không tồn tại.'
            ], 404);
        }

        $Invoice->invoice_products = $Invoice->invoice_products->map(function ($item) {
            $item->productID = $item->product ? $item->product->name : null;
            $item->warehouseID = $item->warehouse ? $item->warehouse->name : null;
            $item->unitID = $item->unit ? $item->unit->name : null;

            unset($item->product);
            unset($item->warehouse);
            unset($item->unit);

            return $item;
        });

        return response()->json([
            'status' => 200,
            'data' => $Invoice,
            'message' => 'Chi tiết phiếu thu mua.'
        ], 200);
    }

    public function inventorystocks()
    {
        $all_stock = InventoryStock::with('warehouse', 'product', 'unit')
            ->orderBy('id', 'desc')
            ->get();

        if ($all_stock->isNotEmpty()) {
            $all_stock->map(function ($stock) {
                $stock->warehouseID = $stock->warehouse ? $stock->warehouse->name : null;
                $stock->productID = $stock->product ? $stock->product->name : null;
                $stock->unitID = $stock->unit ? $stock->unit->name : null;

                unset($stock->warehouse);
                unset($stock->product);
                unset($stock->unit);

                return $stock;
            });

            return response()->json([
                'data' => $all_stock,
                'status' => 200,
                'message' => 'Danh sách tồn kho.',
            ]);
        }

        // Nếu không có dữ liệu
        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy dữ liệu.',
        ]);
    }
}
