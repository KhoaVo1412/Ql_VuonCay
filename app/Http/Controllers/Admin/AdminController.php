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
    private function parseRange(Request $request): array
    {
        $from = $request->query('from');
        $to   = $request->query('to');

        try {
            $fromDate = $from ? Carbon::parse($from)->startOfDay() : Carbon::now()->startOfYear();
            $toDate   = $to   ? Carbon::parse($to)->endOfDay()   : Carbon::now()->endOfDay();
        } catch (\Exception $e) {
            $fromDate = Carbon::now()->startOfYear();
            $toDate   = Carbon::now()->endOfDay();
        }
        return [$fromDate, $toDate];
    }

    public function purchaseSummary(Request $request)
    {
        [$fromDate, $toDate] = $this->parseRange($request);

        $byType = InvoiceProduct::join('invoices as i', 'i.id', '=', 'invoice_products.invoiceID')
            ->join('products as p', 'p.id', '=', 'invoice_products.productID')
            ->where('i.status', 'Duyệt')
            ->whereBetween('i.date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->select('p.name as product_name', DB::raw('SUM(invoice_products.quantity) as total_qty'))
            ->groupBy('p.name')
            ->orderByDesc(DB::raw('SUM(invoice_products.quantity)'))
            ->get();

        $labels = $byType->pluck('product_name')->values();
        $data   = $byType->pluck('total_qty')->map(fn($v) => (float)$v)->values();
        $total  = $data->sum();

        return response()->json([
            'ok'     => true,
            'labels' => $labels,
            'data'   => $data,
            'total'  => $total,
            'from'   => $fromDate->toDateString(),
            'to'     => $toDate->toDateString(),
        ]);
    }

    public function harvestSummary(Request $request)
    {
        [$fromDate, $toDate] = $this->parseRange($request);

        $byProduct = ProductPicking::join('pickings as pk', 'pk.id', '=', 'product_pickings.pickingID')
            ->join('products as p', 'p.id', '=', 'product_pickings.productID')
            ->where('pk.type', 'Khai thác')
            ->where('pk.status', 'Hoạt động')
            ->where('pk.active', 'Hoàn thành')
            ->whereBetween('pk.createDate', [$fromDate->toDateString(), $toDate->toDateString()])
            ->select('p.name as product_name', DB::raw('SUM(product_pickings.quantity) as total_qty'))
            ->groupBy('p.name')
            ->orderByDesc(DB::raw('SUM(product_pickings.quantity)'))
            ->get();

        $labels = $byProduct->pluck('product_name')->values();
        $data   = $byProduct->pluck('total_qty')->map(fn($v) => (float)$v)->values();
        $total  = $data->sum();

        return response()->json([
            'ok'     => true,
            'labels' => $labels,
            'data'   => $data,
            'total'  => $total,
            'from'   => $fromDate->toDateString(),
            'to'     => $toDate->toDateString(),
        ]);
    }
    public function index(Request $request)
    {
        $posts = Posts::first();

        // ---- 1. Lấy khoảng thời gian filter (mặc định: từ đầu năm đến hôm nay) ----
        $from = $request->query('from');
        $to   = $request->query('to');

        try {
            $fromDate = $from ? Carbon::parse($from)->startOfDay() : Carbon::now()->startOfYear();
            $toDate   = $to   ? Carbon::parse($to)->endOfDay()   : Carbon::now()->endOfDay();
        } catch (\Exception $e) {
            // fallback nếu người dùng nhập sai định dạng
            $fromDate = Carbon::now()->startOfYear();
            $toDate   = Carbon::now()->endOfDay();
        }

        // ---- 2. Thống kê cây (không phụ thuộc ngày) ----
        $total   = Plant::count();
        $healthy = Plant::where('statusTree', 'tốt')->count();
        $sick    = Plant::where('statusTree', 'sâu bệnh')->count();
        $dead    = Plant::whereIn('statusTree', ['gãy đổ', 'chết'])->count();
        $healthyPercent = $total > 0 ? round(($healthy / $total) * 100, 1) : 0;

        // ---- 3. Thu mua (lọc theo khoảng i.date) ----
        $totalRubber = InvoiceProduct::join('invoices as i', 'i.id', '=', 'invoice_products.invoiceID')
            ->where('i.status', 'Duyệt')
            ->whereBetween('i.date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->sum('quantity');

        $byType = InvoiceProduct::join('invoices as i', 'i.id', '=', 'invoice_products.invoiceID')
            ->join('products as p', 'p.id', '=', 'invoice_products.productID')
            ->where('i.status', 'Duyệt')
            ->whereBetween('i.date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->select('p.name as product_name', DB::raw('SUM(invoice_products.quantity) as total_qty'))
            ->groupBy('p.name')
            ->get();

        // ---- 4. Khai thác (lọc theo khoảng pk.createDate) ----
        $harvestTotal = ProductPicking::join('pickings as pk', 'pk.id', '=', 'product_pickings.pickingID')
            ->where('pk.type', 'Khai thác')
            ->where('pk.status', 'Hoạt động')
            ->where('pk.active', 'Hoàn thành')
            ->whereBetween('pk.createDate', [$fromDate->toDateString(), $toDate->toDateString()])
            ->sum('product_pickings.quantity');

        $harvestByProduct = ProductPicking::join('pickings as pk', 'pk.id', '=', 'product_pickings.pickingID')
            ->join('products as p', 'p.id', '=', 'product_pickings.productID')
            ->where('pk.type', 'Khai thác')
            ->where('pk.status', 'Hoạt động')
            ->where('pk.active', 'Hoàn thành')
            ->whereBetween('pk.createDate', [$fromDate->toDateString(), $toDate->toDateString()])
            ->select('p.name as product_name', DB::raw('SUM(product_pickings.quantity) as total_qty'))
            ->groupBy('p.name')
            ->orderByDesc(DB::raw('SUM(product_pickings.quantity)'))
            ->get();

        // ---- 5. Chuỗi theo tháng trong khoảng (tối đa 24 tháng để tránh quá dài) ----
        $startMonth = Carbon::parse($fromDate)->startOfMonth();
        $endMonth   = Carbon::parse($toDate)->endOfMonth();

        // Khai thác theo tháng
        $rawMonthly = ProductPicking::join('pickings as pk', 'pk.id', '=', 'product_pickings.pickingID')
            ->where('pk.type', 'Khai thác')
            ->whereBetween('pk.createDate', [$startMonth->toDateString(), $endMonth->toDateString()])
            ->select(
                DB::raw("DATE_FORMAT(pk.createDate, '%Y-%m') as ym"),
                DB::raw("SUM(product_pickings.quantity) as qty")
            )
            ->groupBy('ym')
            ->pluck('qty', 'ym');

        $labels = [];
        $dataMonthlyHarvest = [];
        $cursor = $startMonth->copy();
        $maxMonths = 24; // bạn có thể chỉnh tùy ý
        for ($i = 0; $cursor->lte($endMonth) && $i < $maxMonths; $i++) {
            $ym = $cursor->format('Y-m');
            $labels[] = $cursor->format('m/Y');
            $dataMonthlyHarvest[] = (int) ($rawMonthly[$ym] ?? 0);
            $cursor->addMonth();
        }
        $harvestThisMonth = !empty($dataMonthlyHarvest) ? end($dataMonthlyHarvest) : 0;
        $harvestLastMonth = count($dataMonthlyHarvest) >= 2 ? $dataMonthlyHarvest[count($dataMonthlyHarvest) - 2] : 0;

        // Thu mua theo tháng
        $rawMonthlyPurchase = InvoiceProduct::join('invoices as i', 'i.id', '=', 'invoice_products.invoiceID')
            ->whereBetween('i.date', [$startMonth->toDateString(), $endMonth->toDateString()])
            ->select(
                DB::raw("DATE_FORMAT(i.date, '%Y-%m') as ym"),
                DB::raw("SUM(invoice_products.quantity) as qty")
            )
            ->groupBy('ym')
            ->pluck('qty', 'ym');

        $labelsPurchase = [];
        $dataMonthlyPurchase = [];
        $cursor = $startMonth->copy();
        for ($i = 0; $cursor->lte($endMonth) && $i < $maxMonths; $i++) {
            $ym = $cursor->format('Y-m');
            $labelsPurchase[] = $cursor->format('m/Y');
            $dataMonthlyPurchase[] = (int)($rawMonthlyPurchase[$ym] ?? 0);
            $cursor->addMonth();
        }

        // ---- 6. (tuỳ chọn) 8 tuần gần nhất trong phạm vi filter ----
        // Nếu muốn vẫn hiển thị xu hướng 8 tuần, bạn có thể giữ nguyên hoặc cũng lọc theo from/to.

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
            'dataMonthlyPurchase',
            'fromDate',
            'toDate' // để fill ngược lên form
        ));
    }
    public function index1()
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
