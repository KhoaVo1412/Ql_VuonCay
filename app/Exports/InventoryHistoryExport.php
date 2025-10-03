<?php

namespace App\Exports;

use App\Models\InventoryTransaction;
use App\Models\Picking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class InventoryHistoryExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(
        public ?string $from = null,
        public ?string $to = null,
        public ?int $warehouseID = null,
        public ?int $productID = null,
        public ?string $mode = null,
        public int $scale = 2
    ) {}

    public function query()
    {
        $q = InventoryTransaction::query()
            ->with([
                'product:id,name',
                'warehouse:id,code,name',
                'unit:id,name',
                'reference' => fn($x) => $x->select('id', 'code', 'type', 'createDate'),
            ])
            ->select([
                'id',
                'productID',
                'warehouseID',
                'unitID',
                'type',
                'quantity_changed',
                'balance_after',
                'reference_type',
                'reference_id',
                'code',
                'note',
                'date'
            ]);

        if ($this->from) $q->where('date', '>=', Carbon::parse($this->from)->startOfDay());
        if ($this->to)   $q->where('date', '<=', Carbon::parse($this->to)->endOfDay());
        if ($this->warehouseID) $q->where('warehouseID', $this->warehouseID);
        if ($this->productID)   $q->where('productID', $this->productID);

        // Lọc mode
        if ($this->mode === 'xuat') {
            $q->where('type', 'export');
        } elseif ($this->mode === 'nhap') {
            $q->where('type', 'import')
                ->where(function (Builder $sub) {
                    $sub->whereDoesntHaveMorph('reference', [Picking::class])
                        ->orWhereHasMorph('reference', [Picking::class], fn($p) =>
                        $p->where('type', '!=', 'Khai thác'));
                });
        } elseif ($this->mode === 'khaithac') {
            $q->where('type', 'import')
                ->whereHasMorph('reference', [Picking::class], fn($p) =>
                $p->where('type', 'Khai thác'));
        } else {
            $q->whereIn('type', ['import', 'export']);
        }

        return $q->orderBy('date', 'desc');
    }

    public function headings(): array
    {
        return ['Ngày', 'Loại', 'Mã phiếu', 'Sản phẩm', 'Kho', 'Đơn vị', 'Số lượng', 'Tồn sau', 'Ghi chú'];
    }

    public function map($row): array
    {
        $label = match ($row->type) {
            'export' => 'Xuất',
            'import' => (($row->reference_type === \App\Models\Picking::class)
                && optional($row->reference)->type === 'Khai thác') ? 'Khai thác' : 'Nhập',
            default  => strtoupper($row->type),
        };

        $qtySigned = (float) $row->quantity_changed;
        $balance   = (float) $row->balance_after;

        return [
            $row->date ? \Carbon\Carbon::parse($row->date)->format('d/m/Y H:i') : '',
            $label,
            $row->code ?: (optional($row->reference)->code ?? ''),
            ($row->product->name ?? ''),
            trim(($row->warehouse->code ?? '') . ' - ' . ($row->warehouse->name ?? ''), ' -'),
            $row->unit->name ?? '',
            $qtySigned,
            $balance,
            $row->note,
        ];
    }


    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_NUMBER_00,
            'H' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }
}
