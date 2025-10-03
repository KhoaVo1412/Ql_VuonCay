<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use App\Models\Picking;
use App\Models\Product;
use App\Models\WareHouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class InventoryHistoryController extends Controller
{
    public function index(Request $request)
    {
        return view('repost', [
            'warehouses' => WareHouse::orderBy('name')->get(),
            'products'   => Product::orderBy('name')->get(),
        ]);
    }

    public function data(Request $request)
    {
        $mode = $request->input('mode');               // nhap|xuat|khaithac|(null = all)
        $from = $request->input('from');               // yyyy-mm-dd
        $to   = $request->input('to');                 // yyyy-mm-dd
        $wid  = $request->input('warehouse_id');
        $pid  = $request->input('product_id');

        $q = InventoryTransaction::query()
            ->with([
                'warehouse:id,name,code',
                'product:id,name',
                'unit:id,name',
                'reference' => fn($x) => $x->select('id', 'code', 'type', 'createDate'),
            ])
            ->select([
                'id',
                'productID',
                'warehouseID',
                'unitID',
                'type',
                'quantity_changed',
                'balance_after',
                'reference_type',
                'reference_id',
                'code',
                'note',
                'date'
            ]);

        if ($from) $q->where('date', '>=', Carbon::parse($from)->startOfDay());
        if ($to)   $q->where('date', '<=', Carbon::parse($to)->endOfDay());
        if ($wid)  $q->where('warehouseID', $wid);
        if ($pid)  $q->where('productID',   $pid);

        // Lọc theo mode
        if ($mode === 'xuat') {
            $q->where('type', 'export');
        } elseif ($mode === 'nhap') {
            $q->where('type', 'import')
                ->where(function ($sub) {
                    $sub->whereDoesntHaveMorph('reference', [Picking::class])
                        ->orWhereHasMorph('reference', [Picking::class], fn($p) =>
                        $p->where('type', '!=', 'Khai thác'));
                });
        } elseif ($mode === 'khaithac') {
            $q->where('type', 'import')
                ->whereHasMorph('reference', [Picking::class], fn($p) =>
                $p->where('type', 'Khai thác'));
        } else {
            $q->whereIn('type', ['import', 'export']);
        }

        return DataTables::of($q)
            ->addColumn('date', fn($row) => optional($row->date)->format('d/m/Y'))
            ->addColumn('display_type', function ($row) {
                if ($row->type === 'export') return 'Xuất';
                $isHarvest = $row->type === 'import'
                    && $row->reference_type === \App\Models\Picking::class
                    && optional($row->reference)->type === 'Khai thác';
                return $isHarvest ? 'Khai thác' : 'Nhập';
            })
            ->addColumn('doc_code', fn($row) => $row->code ?: (optional($row->reference)->code ?? ''))
            ->addColumn('product_label', fn($row) =>
            trim(($row->product->code ?? '') . ' - ' . ($row->product->name ?? ''), ' -'))
            ->addColumn('warehouse_label', fn($row) =>
            trim(($row->warehouse->code ?? '') . ' - ' . ($row->warehouse->name ?? ''), ' -'))
            ->addColumn('unit_name', fn($row) => $row->unit->name ?? '')
            ->editColumn('quantity_changed', function ($row) {
                $val = (float)$row->quantity_changed;
                $formatted = number_format(abs($val), 2, '.', ''); // 00.00
                return ($val >= 0 ? '+' : '−') . $formatted;
            })
            ->editColumn(
                'balance_after',
                fn($row) =>
                number_format((float)$row->balance_after, 2, '.', '')
            )
            ->rawColumns(['quantity_changed'])
            ->orderColumn('date', 'date $1')
            ->toJson();
    }
}
