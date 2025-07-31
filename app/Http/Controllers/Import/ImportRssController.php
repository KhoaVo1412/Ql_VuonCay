<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Imports\ImportRss;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportRssController extends Controller
{
    public function importRss(Request $request)
    {
        $request->validate([
            'excel_rss' => 'required|mimes:xlsx,csv,xls'
        ]);

        try {
            Excel::import(new ImportRss, $request->file('excel_rss'));
            return back()->with('thanhcong', 'Nhập dữ liệu thành công!');
        } catch (Exception $e) {
            return back()->with('loi', 'Lỗi khi nhập dữ liệu: ' . $e->getMessage());
        }
    }
}