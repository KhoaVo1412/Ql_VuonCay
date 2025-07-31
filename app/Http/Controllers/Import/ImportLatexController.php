<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Imports\ImportLatex;
use Complex\Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportLatexController extends Controller
{
    public function importLatex(Request $request)
    {
        $request->validate([
            'excel_latex' => 'required|mimes:xlsx,csv,xls'
        ]);


        try {
            Excel::import(new ImportLatex, $request->file('excel_latex'));
            return back()->with('thanhcong', 'Nhập dữ liệu thành công!');
        } catch (Exception $e) {
            return back()->with('loi', 'Lỗi khi nhập dữ liệu: ' . $e->getMessage());
        }
    }
}