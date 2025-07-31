<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Carbon\Carbon;
use Illuminate\Http\Request;


class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', Carbon::now()->format('m'));
        $year = $request->get('year', Carbon::now()->format('Y'));

        $dataBatch = $this->groupBatch($month, $year);

        $sumBatch = $dataBatch['totalBatch'] ?? 0;

        $sumBatchIngredient = $dataBatch['totalBatchIngredient'] ?? 0;

        $totalBatch = empty($dataBatch) ? [
            0
        ] : [$sumBatch, $dataBatch['totalCKN'], $dataBatch['totalDKN'], $dataBatch['totalIngredient']];
        $totalIngredient = empty($dataBatch) ? [
            0
        ] : [$sumBatchIngredient, $dataBatch['totalIngredient']];


        return view('reports.index', [
            'totalBatch' => $totalBatch,
            'totalIngredient' => $totalIngredient,
            'month' => $month,
            'year' => $year
        ]);
    }

    private function getBatchQueryNSX($month, $year)
    {
        $query = Batch::when($year, fn($q) => $q->whereYear('date_sx', $year))
            ->when($month, fn($q) => $q->whereMonth('date_sx', $month));
        return $query;
    }

    private function getBatchQueryCreateAt($month, $year)
    {
        $query = Batch::when($year, fn($q) => $q->whereYear('created_at', $year))
            ->when($month, fn($q) => $q->whereMonth('created_at', $month));
        return $query;
    }



    private function getBatchStatistics($status, $month, $year)
    {
        $batches = $this->getBatchQueryNSX($month, $year)->where('status', $status)->get();
        return $batches;
        // return [
        //     'groupedData' => $this->groupBatchData($batches),
        //     'totalBatches' => $batches->pluck('batch_code')->unique()->count()
        // ];
    }

    private function sumBatch($month, $year)
    {
        $sum = $this->getBatchQueryNSX($month, $year)->count();
        return $sum;
    }


    private function sumBatchIngredient($month, $year)
    {
        $sum = $this->getBatchQueryCreateAt($month, $year)->count();
        return $sum;
    }

    private function groupBatch($month, $year)
    {
        $batchDKN = $this->getBatchStatistics(1, $month, $year)->count();
        $batchCKN = $this->getBatchStatistics(0, $month, $year)->count();
        $batchIngredient = $this->countBatchIngredient($month, $year);
        $sum = $this->sumBatch($month, $year);
        // dd($batchCKN);
        $sumIngredient = $this->sumBatchIngredient($month, $year);
        if ($sum == 0)
            return [];
        // $percenBatchDKN = round(($batchDKN / $sum) * 100, 1);
        // $percenBatchCKN = round(($batchCKN / $sum) * 100, 1);

        return [
            'totalBatch' => $sum,
            'totalBatchIngredient' => $sumIngredient,
            'totalDKN' => $batchDKN,
            'totalCKN' => $batchCKN,
            'totalIngredient' => $batchIngredient
        ];
    }

    private function countBatchIngredient($month, $year)
    {
        $sum = $this->getBatchQueryCreateAt($month, $year)
            ->withCount('ingredients') // Đếm số ingredient trong mỗi Batch
            ->get()
            ->sum('ingredients_count'); // Tính tổng số lượng ingredient từ các batch
        // $sum = $this->getBatchQueryNSX($month, $year)
        //     ->whereHas('ingredients')
        //     ->count();

        // dd($sum);
        return $sum;
    }


    // private function getBatchQueryNKN($month, $year)
    // {
    //     $query = Batch::with('testingResult')
    //     ->when($year, fn($q) => $q->whereYear('date_sx', $year))
    //     ->when($month, fn($q) => $q->whereMonth('date_sx', $month));
    //     return $query;

    // }

    //     private function groupBatchData($batches)
//     {
//         return $batches->groupBy(function ($batch) {
//             $minIngredient = $batch->ingredients->sortBy('pivot.ingredient_id')->first();
//             return $minIngredient ? $minIngredient->receiving_factory : 'N/A';
//         })->map->count();
//     }



    //   //////////////////////// Lô hàng ////////////////////////

    //    // Sum Số mã lô đã tạo

    //     private function countTotalBatches($month, $year)
//     {
//         return $this->getBatchQueryNSX($month, $year)->count();
//     }

    //     // Sum Tổng mã lô đã tạo

    //     private function countTotalBatchIngredient($month, $year)
//     {
//         return $this->getBatchQueryNSX($month, $year)
//         ->with('ingredients')
//         ->get()
//         ->map(fn($item) => $item->getRelations())
//         ->flatten()
//         ->count();
//     }

    //     private function groupBatchIngredient($month, $year)
//     {
//         $data = BatchIngredients::whereIn('batch_id', $this->getBatchQueryNSX($month, $year)->pluck('id'))
//         ->with('ingredient') // Load thông tin nguyên liệu từ BatchIngredient
//         ->select('ingredient_id')
//         ->selectRaw('COUNT(batch_id) as total')
//         ->groupBy('ingredient_id')
//         ->get();

    //         return $data;
//     }

    //     private function percentagesBatch($month, $year)
//     {
//         $totalRows = $this->countTotalBatches($month, $year);
//         // Nếu tổng số lô hàng = 0, trả về danh sách rỗng
//         if ($totalRows === 0) {
//             return [
//                 'total' => 0,
//                 'percentages' => collect() // Trả về một collection rỗng
//             ];
//         }

    //         $groupedData = $this->groupBatchIngredient($month, $year);
//         // Tính phần trăm và lấy danh sách batch_id
//         $percentages = $groupedData->map(function ($item) use ($totalRows) {
//             return [
//                 'total' => $item->total,
//                 'ingredient_name' => $item->ingredient->receiving_factory ?? '', // Lấy tên từ relationship
//                 'percentage' => number_format(($item->total / $totalRows) * 100, 1) // Làm tròn 1 chữ số thập phân // Tính %
//             ];
//         });

    //         // Tính tổng phần trăm đã tính
//         $totalPercentage = collect($percentages)->sum('percentage');

    //         // Nếu tổng phần trăm chưa đủ 100%, thêm mục "N/A"
//         if ($totalPercentage < 100) {
//             // Tính tổng số hàng đã sử dụng
//             $usedTotal = collect($percentages)->sum('total');

    //             // Nếu còn hàng chưa phân loại, thêm vào "N/A"
//             $remaining = $totalRows - $usedTotal;

    //             $percentages->push([
//                 'total' => $remaining,
//                 'ingredient_name' => 'N/A',
//                 'percentage' => number_format(100 - $totalPercentage, 1) // Phần trăm còn thiếu
//             ]);
//         }

    //         // dd($percentages);

    //         return [
//             'total' => $totalRows,
//             'percentages' => $percentages
//         ];
//     }

    //     private function percenBatchIngredient($month, $year)
//     {
//         $totalRows = $this->countTotalBatchIngredient($month, $year);
//         if ($totalRows === 0) {
//             return [
//                 'total' => 0,
//                 'percentages' => collect() // Trả về một collection rỗng
//             ];
//         }

    //         $groupedData = $this->groupBatchIngredient($month, $year);
//         // Tính phần trăm và lấy danh sách batch_id
//         $percentages = $groupedData->map(function ($item) use ($totalRows) {
//             dd($item->total);
//             return [
//                 'total' => $item->total,
//                 'ingredient_name' => $item->ingredient->receiving_factory ?? '', // Lấy tên từ relationship
//                 'percentage' => number_format(($item->total / $totalRows) * 100, 1) // Làm tròn 1 chữ số thập phân // Tính %
//             ];
//         });


    //         // Tính tổng phần trăm đã tính
//         $totalPercentage = collect($percentages)->sum('percentage');

    //         // Nếu tổng phần trăm chưa đủ 100%, thêm mục "N/A"

    //         if ($totalPercentage < 100) {

    //             // Tính tổng số hàng đã sử dụng
//             $usedTotal = collect($percentages)->sum('total');

    //             $remaining = $totalRows - $usedTotal;
//             dd($totalPercentage);
//             $percentages->push([
//                 'total' => $remaining,
//                 'ingredient_name' => 'N/A',
//                 'percentage' => number_format(100 - $totalPercentage, 1) // Phần trăm còn thiếu
//             ]);
//         }

    //         // dd($percentages);

    //         return [
//             'total' => $totalRows,
//             'percentages' => $percentages
//         ];
//     }


}