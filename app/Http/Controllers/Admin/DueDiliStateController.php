<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Exports\ExportDueDiliState;
use App\Models\Ingredient;
use Maatwebsite\Excel\Facades\Excel;

class DueDiliStateController extends Controller
{
    public function index()
    {
        $malo = Batch::where('status', 1)
            ->whereHas('orderExport')
            ->get();
        return view("contract-due-diligence-statement.index", compact('malo'));
    }

    public function exportExcel($id)
    {
        $malo = Batch::findOrFail($id);
        $export = new ExportDueDiliState($malo);
        return $export->download();
    }
}
