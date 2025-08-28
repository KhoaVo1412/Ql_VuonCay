<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Imports\PlotImport;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;


class PlotImportController extends Controller
{
    public function importExcel(Request $request)
    {
        try {
            $import = new PlotImport();
            $i = Excel::import($import, $request->file("excel_file"));



            return redirect()->route('plots.index')->with("message", "Nhập dữ liệu lô thành công!");
        } catch (\Exception $e) {
            return redirect()->route('plots.index')->with('error', 'Lỗi khi nhập dữ liệu: ' . $e->getMessage());
        }
    }


    public function add_excel()
    {
        return view('plots.add_excel');
    }
}
