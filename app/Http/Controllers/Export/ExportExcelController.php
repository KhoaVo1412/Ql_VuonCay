<?php

namespace App\Http\Controllers\Export;

use App\Exports\ExportExcel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportExcelController extends Controller
{
    public function exportExcel(Request $request)
    {
        $filters = [
            'day' => $request->input('day'),
            'month' => $request->input('month'),
            'year' => $request->input('year'),
            'type' => $request->input('type'),
            'rank' => $request->input('rank'),
        ];

        return Excel::download(new ExportExcel($filters), 'hoabinh_qualityChecked.xlsx');
    }
}