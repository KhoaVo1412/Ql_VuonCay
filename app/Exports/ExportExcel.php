<?php

namespace App\Exports;

use App\Models\TestingResult;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class ExportExcel implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $filters;
    public function __construct($filters)
    {
        $this->filters = $filters;
    }
    public function collection()
    {
        $query = TestingResult::query()
            ->join('batches', 'testing_results.batch_id', '=', 'batches.id')
            ->select('batches.batch_code', 'testing_results.ngay_gui_mau', 'testing_results.ngay_kiem_nghiem', 'testing_results.rank')
            ->where('batches.status', 1);

        if (!empty($this->filters['day']) && !empty($this->filters['month']) && !empty($this->filters['year'])) {
            $query
                ->whereDay('testing_results.ngay_kiem_nghiem', $this->filters['day'])
                ->whereMonth('testing_results.ngay_kiem_nghiem', $this->filters['month'])
                ->whereYear('testing_results.ngay_kiem_nghiem', $this->filters['year']);
        } elseif (!empty($this->filters['day']) && !empty($this->filters['month'])) {
            $query->whereDay('testing_results.ngay_kiem_nghiem', $this->filters['day'])
                ->whereMonth('testing_results.ngay_kiem_nghiem', $this->filters['month']);
        } elseif (!empty($this->filters['day']) && !empty($this->filters['year'])) {
            $query->whereDay('testing_results.ngay_kiem_nghiem', $this->filters['day'])
                ->whereYear('testing_results.ngay_kiem_nghiem', $this->filters['year']);
        } elseif (!empty($this->filters['month']) && !empty($this->filters['year'])) {
            $query->whereMonth('testing_results.ngay_kiem_nghiem', $this->filters['month'])
                ->whereYear('testing_results.ngay_kiem_nghiem', $this->filters['year']);
        } elseif (!empty($this->filters['day'])) {
            $query->whereDay('testing_results.ngay_kiem_nghiem', $this->filters['day']);
        } elseif (!empty($this->filters['month'])) {
            $query->whereMonth('testing_results.ngay_kiem_nghiem', $this->filters['month']);
        } elseif (!empty($this->filters['year'])) {
            $query->whereYear('testing_results.ngay_kiem_nghiem', $this->filters['year']);
        }

        if (!empty($this->filters['rank'])) {
            $query->where('testing_results.rank', $this->filters['rank']);
        }

        // if (!empty($this->filters['type'])) {
        //     $type = $this->filters['type'];
        //     $query->where('batches.type', $type);
        // }

        // $columns = [];

        $columns = [
            'testing_results.svr_impurity',
            'testing_results.svr_ash',
            'testing_results.svr_volatile',
            'testing_results.svr_nitrogen',
            'testing_results.svr_po',
            'testing_results.svr_pri',
            'testing_results.svr_viscous',
            'testing_results.svr_vul',
            'testing_results.svr_color',
            'testing_results.svr_vr',
        ];


        $query->addSelect($columns);

        $data = $query->get();

        if ($data->isEmpty()) {
            return collect([
                ['Không có dữ liệu phù hợp với bộ lọc.']
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        $headings = [
            'Mã Lô Hàng',
            'Ngày Gửi Mẫu',
            'Ngày Kiểm Nghiệm',
            'Xếp Hạng',
        ];



        $headings = array_merge($headings, [
            'Tạp Chất',
            'Tro',
            'Bay Hơi',
            'Nitơ',
            'Po',
            'PRI',
            'Độ Nhớt',
            'Lưu Hóa',
            'Màu',
            'Lovibond',
        ]);

        return $headings;
    }

    // public function map($row): array
    // {
    //     // Convert object to array
    //     $data = $row->toArray();

    //     // Format riêng 2 trường ngày
    //     $data['ngay_gui_mau'] = $this->convertDate($row->ngay_gui_mau);
    //     $data['ngay_kiem_nghiem'] = $this->convertDate($row->ngay_kiem_nghiem);

    //     return array_values($data); // Đảm bảo trả về danh sách giá trị, không phải mảng key-value
    // }

    public function map($row): array
    {
        // Format lại ngày
        $row->ngay_gui_mau = $this->convertDate($row->ngay_gui_mau);
        $row->ngay_kiem_nghiem = $this->convertDate($row->ngay_kiem_nghiem);

        // Rank: in hoa
        $row->rank = strtoupper($row->rank);

        // Màu: mapping lại theo danh sách value → tên hiển thị
        $colorMapping = [
            'dacam' => 'Da cam',
            'xanhlacaynhat' => 'Xanh lá cây nhạt',
            'nau' => 'Nâu',
            'do' => 'Đỏ',
        ];
        $row->svr_color = $colorMapping[$row->svr_color] ?? $row->svr_color;

        // Trả về dữ liệu dạng array
        return [
            $row->batch_code,
            $row->ngay_gui_mau,
            $row->ngay_kiem_nghiem,
            $row->rank,
            $row->svr_impurity,
            $row->svr_ash,
            $row->svr_volatile,
            $row->svr_nitrogen,
            $row->svr_po,
            $row->svr_pri,
            $row->svr_viscous,
            $row->svr_vul,
            $row->svr_color,
            $row->svr_vr,
        ];
    }


    private function convertDate($date)
    {
        return Carbon::parse($date)->format('d/m/Y');
    }
}