<?php

namespace App\Exports;

use App\Models\Ingredient;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class IngredientExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }
    public function query()
    {
        $query = Ingredient::with(['vehicle', 'factory', 'farm.unitRelation', 'plantingAreas', 'typeOfPus'])->orderBy('created_at', 'desc');

        // if (!empty($this->filters['farm_id'])) {
        //     $query->whereHas('farm', function ($q) {
        //         $q->where('farm_name', $this->filters['farm_id']);
        //     });
        // }

        if (!empty($this->filters['farm_id'])) {
            list($farm_name, $unit_name) = explode('|', $this->filters['farm_id']);

            $query->whereHas('farm', function ($q) use ($farm_name) {
                $q->where('farm_name', $farm_name);
            });

            if ($unit_name) {
                $query->whereHas('farm.unitRelation', function ($q) use ($unit_name) {
                    $q->where('unit_name', $unit_name);
                });
            }
        }

        if (!empty($this->filters['vehicle_number_id'])) {
            $query->whereHas('vehicle', function ($q) {
                $q->where('vehicle_number', $this->filters['vehicle_number_id']);
            });
        }

        if (!empty($this->filters['day'])) {
            $query->whereDay('received_date', $this->filters['day']);
        }
        if (!empty($this->filters['year'])) {
            $query->whereYear('received_date', $this->filters['year']);
        }

        if (!empty($this->filters['month'])) {
            $query->whereMonth('received_date', $this->filters['month']);
        }

        return $query;
    }
    public function headings(): array
    {
        return [
            'Công ty',
            'Nông trường',
            'Loại mủ',
            'Số xe vận chuyển',
            'Số chuyến',
            'Nhà máy tiếp nhận',
            'Ngày tiếp nhận',
            'Ngày Bắt Đầu Cạo',
            'Ngày Kết Thúc Cạo',
            'Giống cây'
        ];
    }
    public function map($ingredient): array
    {
        return [
            $ingredient->farm->unitRelation->unit_name ?? '',
            $ingredient->farm->farm_name ?? '',
            $ingredient->typeOfPus->name_pus ?? '',
            $ingredient->vehicle->vehicle_number ?? '',
            $ingredient->trip ?? '',
            $ingredient->factory->factory_name ?? '',
            $this->convertDate($ingredient->received_date) ?? '',
            $this->convertDate($ingredient->harvesting_date) ?? '',
            $this->convertDate($ingredient->end_harvest_date) ?? '',
            implode(', ', $ingredient->plantingAreas->pluck('chi_tieu')->toArray()) ?: '',
        ];
    }


    private function convertDate($date)
    {
        return Carbon::parse($date)->format('d/m/Y');
    }

    public function columnWidths(): array
    {
        return [
            'E' => 34,
        ];
    }
}