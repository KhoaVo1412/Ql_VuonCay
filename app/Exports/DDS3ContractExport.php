<?php

namespace App\Exports;

use App\Models\OrderExport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class DDS3ContractExport
{
    protected $order;
    public function __construct(OrderExport $order)
    {
        $this->order = $order;
    }

    public function download()
    {
        $templatePath = public_path('templates/template_dds3.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('CSVN DDS');

        if ($this->order->batches->isEmpty()) {
            $sheet->setCellValue('A1', 'Không tìm thấy lô hàng');
        } else {
            $styleArray = [
                'font' => [
                    'color' => ['argb' => Color::COLOR_BLUE],
                    'underline' => Font::UNDERLINE_SINGLE,
                ],
            ];

            // Ghi thông tin chung
            $sheet->setCellValue('B2', 'Hoa Binh Rubber Joint Stock Co.');
            $sheet->setCellValue('B3', 'horuco@horuco.com.vn');
            $sheet->setCellValue('B4', 'Hoa Binh commune, Xuyen Moc District, Ba Ria - Vung Tau province');

            // Dữ liệu đầu tiên
            // $firstquocgia = optional($this->order->batches->first()?->ingredients->first()?->plantingAreas->first())->quoc_gia;
            // $sheet->setCellValue('B6', $firstquocgia ?? '');
            $sheet->setCellValue('B6', 'Viet Nam');

            $quantity = $this->order->contract->quantity ?? '';
            $sheet->setCellValueExplicit('B13', (string) $quantity, DataType::TYPE_STRING);

            $geoUrl = url("/batch-geojson?orderexport_id={$this->order->id}");
            $sheet->setCellValue('B14', $geoUrl ?? '');
            $sheet->getCell('B14')->getHyperlink()->setUrl($geoUrl);

            $row = 26;
            $startRow = $row;

            foreach ($this->order->batches as $batch) {
                // foreach ($batch->ingredients as $nl) {
                //     foreach ($nl->plantingAreas as $planarea) {
                $sheet->setCellValue("A{$row}", 'Hoa Binh Rubber Joint Stock Co.');
                $sheet->setCellValue("B{$row}", $batch->batch_code ?? '');
                $sheet->setCellValue("C{$row}", $batch->batch_weight ?? '');
                // $idplotList = $batch->ingredients->flatMap->plantingAreas->pluck('ma_lo')->unique()->implode("\n");
                $idplotList = $batch->ingredients
                    ->flatMap(fn($ingredient) => $ingredient->plantingAreas)
                    ->pluck('ma_lo')
                    ->unique()
                    ->implode("\n");
                $sheet->setCellValue("D{$row}", $idplotList ?? '');
                $harvesting_date = $batch->ingredients->first()?->harvesting_date;
                $sheet->setCellValue("G{$row}", date('d/m/Y', strtotime($harvesting_date ?? '')));
                $totaldientich = round($batch->ingredients->flatMap->plantingAreas->pluck('dien_tich')->sum(), 2);
                $sheet->setCellValue("F{$row}", $totaldientich ?? '');
                $sheet->setCellValue("H{$row}", date('d/m/Y', strtotime($batch->date_sx ?? '')));

                $geoUrl2 = url("/batch-geojson?batch_code={$batch->batch_code}");
                $displayText = "{$batch->batch_code}.geojson";
                $sheet->setCellValue("I{$row}", $displayText ?? '');
                $sheet->getCell("I{$row}")->getHyperlink()->setUrl($geoUrl2);
                $sheet->getStyle("I{$row}")->applyFromArray($styleArray);

                $sheet->setCellValue("J{$row}", "Yes");

                $plantationList = $batch->ingredients->flatMap->plantingAreas->map->farm->pluck('farm_name')->unique()->implode(', ');
                $sheet->setCellValue("K{$row}", $plantationList ?? '');
                $sheet->getRowDimension($row)->setRowHeight(152.3);

                $row++;
                // }
                //     }
            }

            // Áp dụng border và format cho toàn bộ dữ liệu
            if ($row > $startRow) {
                $columnRange = "A{$startRow}:K" . ($row - 1);
                $sheet->getStyle($columnRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'wrapText' => true,
                    ],
                    'font' => [
                        'color' => ['rgb' => '0070C0'],
                    ],
                ]);
            }
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, "{$this->order->code}_DDS3.xlsx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . "{$this->order->code}_DDS3.xlsx" . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

}