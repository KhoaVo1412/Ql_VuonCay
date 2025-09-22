<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvoiceProduct;
use App\Models\Posts;
use Illuminate\Http\Request;
use App\Models\Plant;
use App\Models\ProductPicking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Posts::first();
        // Tổng số cây
        $total = Plant::count();
        // Cây khỏe mạnh (statusTree = 'tốt')
        $healthy = Plant::where('statusTree', 'tốt')->count();
        // Cây bệnh (statusTree = 'sâu bệnh')
        $sick = Plant::where('statusTree', 'sâu bệnh')->count();
        // Cây đổ/chết (statusTree = 'gãy đổ' hoặc 'chết')
        $dead = Plant::whereIn('statusTree', ['gãy đổ', 'chết'])->count();
        // Tính % khỏe mạnh
        $healthyPercent = $total > 0 ? round(($healthy / $total) * 100, 1) : 0;

        // SẢN LƯỢNG THU MUA
        $totalRubber = InvoiceProduct::join('invoices as i', 'i.id', '=', 'invoice_products.invoiceID')
            ->where('i.status', 'Duyệt')
            ->sum('quantity');

        $byType = InvoiceProduct::join('invoices as i', 'i.id', '=', 'invoice_products.invoiceID')
            ->join('products as p', 'p.id', '=', 'invoice_products.productID')
            ->where('i.status', 'Duyệt')
            ->select('p.name as product_name', DB::raw('SUM(invoice_products.quantity) as total_qty'))
            ->groupBy('p.name')
            ->get();

        // SẢN LƯỢNG KHAI THÁC
        $harvestTotal = ProductPicking::join('pickings as pk', 'pk.id', '=', 'product_pickings.pickingID')
            ->where('pk.type', 'Khai thác')                 // nếu bạn dùng type để phân biệt
            ->where('pk.status', 'Hoạt động')               // tuỳ thực tế
            ->where('pk.active', 'Hoàn thành')               // tuỳ thực tế
            ->sum('product_pickings.quantity');

        // gom theo sản phẩm (loại mủ từ product_id)
        $harvestByProduct = ProductPicking::join('pickings as pk', 'pk.id', '=', 'product_pickings.pickingID')
            ->join('products as p', 'p.id', '=', 'product_pickings.productID')
            ->where('pk.type', 'Khai thác')
            ->where('pk.status', 'Hoạt động')
            ->where('pk.active', 'Hoàn thành')               // tuỳ thực tế
            ->select('p.name as product_name', DB::raw('SUM(product_pickings.quantity) as total_qty'))
            ->groupBy('p.name')
            ->orderByDesc(DB::raw('SUM(product_pickings.quantity)'))
            ->get();

        $startMonth = Carbon::now()->startOfMonth()->subMonths(11);
        $endMonth   = Carbon::now()->endOfMonth();
        // Lấy tổng sản lượng khai thác nhóm theo tháng (YYYY-MM)
        $rawMonthly = ProductPicking::join('pickings as pk', 'pk.id', '=', 'product_pickings.pickingID')
            ->where('pk.type', 'Khai thác')
            ->whereBetween('pk.createDate', [$startMonth->toDateString(), $endMonth->toDateString()])
            ->select(
                DB::raw("DATE_FORMAT(pk.createDate, '%Y-%m') as ym"),
                DB::raw("SUM(product_pickings.quantity) as qty")
            )
            ->groupBy('ym')
            ->pluck('qty', 'ym');        // -> ['2025-01' => 123, '2025-02' => 456, ...]

        // Tạo đầy đủ 12 mốc tháng, chèn 0 cho tháng không có dữ liệu
        $labels = [];
        $dataMonthlyHarvest = [];
        $cursor = $startMonth->copy();
        for ($i = 0; $i < 12; $i++) {
            $ym = $cursor->format('Y-m');
            $labels[] = $cursor->format('m/Y'); // hiển thị: 01/2025
            $dataMonthlyHarvest[] = (int) ($rawMonthly[$ym] ?? 0);
            $cursor->addMonth();
        }
        // Nếu bạn vẫn muốn tổng tháng này và tháng trước (hiển thị ô nhỏ):
        $harvestThisMonth = end($dataMonthlyHarvest);
        $harvestLastMonth = count($dataMonthlyHarvest) >= 2 ? $dataMonthlyHarvest[count($dataMonthlyHarvest) - 2] : 0;

        // Gom Thu mua theo tháng từ
        $rawMonthlyPurchase = InvoiceProduct::join('invoices as i', 'i.id', '=', 'invoice_products.invoiceID')
            ->whereBetween('i.date', [$startMonth->toDateString(), $endMonth->toDateString()])
            ->select(
                DB::raw("DATE_FORMAT(i.date, '%Y-%m') as ym"),
                DB::raw("SUM(invoice_products.quantity) as qty")
            )
            ->groupBy('ym')
            ->pluck('qty', 'ym');
        // Tạo mảng labels & data cho 12 tháng
        $labelsPurchase = [];
        $dataMonthlyPurchase = [];
        $cursor = $startMonth->copy();
        for ($i = 0; $i < 12; $i++) {
            $ym = $cursor->format('Y-m');
            $labelsPurchase[] = $cursor->format('m/Y');
            $dataMonthlyPurchase[] = (int)($rawMonthlyPurchase[$ym] ?? 0);
            $cursor->addMonth();
        }
        // Chuỗi 8 tuần gần nhất (xu hướng khai thác)
        $harvestWeeklySeries = ProductPicking::join('pickings as pk', 'pk.id', '=', 'product_pickings.pickingID')
            ->where('pk.type', 'Khai thác')
            ->where('pk.createDate', '>=', Carbon::now()->startOfWeek()->subWeeks(7)->toDateString())
            ->select(
                DB::raw("YEARWEEK(pk.createDate, 1) as yw"),
                DB::raw("MIN(pk.createDate) as week_start"),
                DB::raw("SUM(product_pickings.quantity) as qty")
            )
            ->groupBy('yw')
            ->orderBy('week_start')
            ->get();
        return view('home', compact(
            'posts',
            'total',
            'healthy',
            'sick',
            'dead',
            'healthyPercent',
            'totalRubber',
            'byType',
            'harvestTotal',
            'harvestByProduct',
            'labels',
            'dataMonthlyHarvest',
            'harvestThisMonth',
            'harvestLastMonth',
            'labelsPurchase',
            'dataMonthlyPurchase'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
