<?php

namespace App\Exports;

use App\Models\Contract;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class ContractsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    // protected $year;

    // public function __construct($year)
    // {
    //     $this->year = $year;
    // }
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    // public function collection()
    // {
    //     return Contract::whereYear('delivery_date', $this->year)->orderBy('created_at', 'desc')->get();
    // }
    public function query()
    {
        $query = Contract::query()->orderBy('created_at', 'desc');
        if (!empty($this->filters['day'])) {
            $query->whereDay('delivery_date', $this->filters['day']);
        }

        if (!empty($this->filters['year'])) {
            $query->whereYear('delivery_date', $this->filters['year']);
        }

        if (!empty($this->filters['month'])) {
            $query->whereMonth('delivery_date', $this->filters['month']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            "Mã hợp đồng",
            "Loại hợp đồng",
            "Hợp đồng gốc số",
            "Khách hàng",
            "Tháng giao hàng",
            "Số lượng(kg)",
            "Lệnh xuất hàng",
            "Tên chủng loại sản phẩm",
            "Đóng gói",
            "Đơn vị Sản xuất/ Thương mại",
            "Mã lô hàng",
            "Ngày giao hàng",
            "Ngày đóng cont",
            "Thị trường",
            "Bán cho Bên thứ 3"
        ];
    }

    public function map($contract): array
    {
        return [
            $contract->contract_code,
            $contract->contractType->contract_type_name,
            $contract->original_contract_number,
            $contract->customer->company_name,
            $this->convertMonth($contract->delivery_month),
            $contract->quantity,
            $contract->orderExports->pluck('code')->implode(', '),
            $contract->product_type_name,
            $contract->packaging_type,
            $contract->production_or_trade_unit,
            $contract->batches,
            $this->convertDate($contract->delivery_date),
            $this->convertDate($contract->container_closing_date),
            $contract->market,
            $contract->third_party_sale,
        ];
    }

    private function convertDate($date)
    {
        return Carbon::parse($date)->format('d/m/Y');
    }
    private function convertMonth($date)
    {
        return Carbon::parse($date)->format('m/Y');
    }
}