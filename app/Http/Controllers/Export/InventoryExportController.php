<?php

namespace App\Http\Controllers\Export;

use App\Exports\InventoryHistoryExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class InventoryExportController extends Controller
{
    public function exportExcel(Request $req)
    {
        $data = $req->validate([
            'from'        => ['nullable', 'date'],
            'to'          => ['nullable', 'date', 'after_or_equal:from'],
            'mode'        => ['nullable', 'in:nhap,xuat,khaithac'],
            'warehouseID' => ['nullable', 'integer', 'exists:ware_houses,id'],
            'productID'   => ['nullable', 'integer', 'exists:products,id'],
        ]);

        $file = sprintf(
            'lich_su_%s_%s_%s.xlsx',
            $data['mode'] ?? 'tatca',
            $data['from'] ? date('Ymd', strtotime($data['from'])) : 'min',
            $data['to']   ? date('Ymd', strtotime($data['to']))   : 'max'
        );

        if (ob_get_length()) ob_end_clean(); // tránh lỗi headers already sent

        return Excel::download(
            new InventoryHistoryExport(
                from: $data['from'] ?? null,
                to: $data['to'] ?? null,
                warehouseID: $data['warehouseID'] ?? null,
                productID: $data['productID'] ?? null,
                mode: $data['mode'] ?? null,
                scale: 2
            ),
            $file
        );
    }
}
