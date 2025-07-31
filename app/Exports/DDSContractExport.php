<?php

namespace App\Exports;


use App\Models\OrderExport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Font;
use App\Models\Webmap;

class DDSContractExport
{
    protected $order;
    public function __construct(OrderExport $order)
    {
        $this->order = $order;
    }
    public function download()
    {
        // Đường dẫn file mẫu
        $templatePath = public_path('templates/template_duedilistate.xlsx');

        // Load file mẫu
        $spreadsheet = IOFactory::load($templatePath);

        // Lấy sheet mẫu (mặc định là sheet đầu tiên)
        $templateSheet = $spreadsheet->getActiveSheet();
        $linkwebmap = Webmap::value('webmap');

        // Nếu không có batch, giữ nguyên sheet mẫu và ghi thông báo
        if ($this->order->batches->isEmpty()) {
            $templateSheet->setTitle('Không tìm thấy lô hàng');
        } else {
            $batchSheets = [];

            // Duyệt qua từng batch để tạo sheet mới dựa trên file mẫu
            foreach ($this->order->batches as $batch) {
                // Clone sheet mẫu
                $newSheet = clone $templateSheet;
                $newSheet->setTitle($batch->batch_code);
                $spreadsheet->addSheet($newSheet);

                // Tạo qr batch code
                $batchcode = $batch->batch_code;
                if (!empty($batchcode)) {
                    // Tạo ảnh QR code dưới dạng Base64
                    $qrCodeImage = QrCode::format('png')->size(200)->generate($batchcode);
                    $imageResource = imagecreatefromstring($qrCodeImage);

                    if ($imageResource) {
                        $drawing = new MemoryDrawing();
                        $drawing->setImageResource($imageResource);
                        $drawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
                        $drawing->setMimeType(MemoryDrawing::MIMETYPE_PNG);
                        $drawing->setHeight(110); // Điều chỉnh kích thước QR
                        $drawing->setCoordinates('A2'); // Chèn vào ô A2
                        $drawing->setOffsetX(50);
                        $drawing->setOffsetY(50);
                        $drawing->setWorksheet($newSheet);

                        // Gộp ô A2:B7 để chứa mã QR
                        $newSheet->mergeCells('A2:B7');
                    }
                } else {
                    // Nếu không có dữ liệu, đặt ô A2 thành trống
                    $newSheet->setCellValue('A2', '');
                }

                // $webmap = optional($batch->ingredients->first()?->plantingAreas->first())->webmap;
                if (!empty($linkwebmap)) {
                    // Tạo ảnh QR code dưới dạng Base64
                    $qrCodeImage = QrCode::format('png')->size(200)->generate($linkwebmap);
                    $imageResource = imagecreatefromstring($qrCodeImage);

                    if ($imageResource) {
                        $drawing = new MemoryDrawing();
                        $drawing->setImageResource($imageResource);
                        $drawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
                        $drawing->setMimeType(MemoryDrawing::MIMETYPE_PNG);
                        $drawing->setHeight(110); // Điều chỉnh kích thước QR
                        $drawing->setCoordinates('K3'); // Chèn vào ô A2
                        $drawing->setOffsetX(5);
                        $drawing->setOffsetY(5);
                        $drawing->setWorksheet($newSheet);
                    }
                } else {
                    // Nếu không có dữ liệu, đặt ô A2 thành trống
                    $newSheet->setCellValue('K3', '');
                }
                $styleArray = [
                    'font' => [
                        'color' => ['argb' => Color::COLOR_BLUE], // Màu xanh dương
                        'underline' => Font::UNDERLINE_SINGLE, // Gạch chân
                    ],
                ];
                // Thêm dữ liệu vào sheet mới
                $newSheet->setCellValue('D2', $batch->batch_code ?? '');
                $newSheet->setCellValue('D3', 'Hoa Binh Rubber Joint stock Company');

                // $newSheet->setCellValue('D4', 'Nhà máy Chế Biến Cao Su Hòa Bình');
                // $factory = $batch->ingredients->pluck('factory.factory_name')->unique()->implode(', ');
                $factory = $batch->ingredients
                    ->map(fn($ingredient) => $ingredient->factory->factory_name ?? null)
                    ->filter() // loại bỏ null nếu có
                    ->unique()
                    ->implode(', ');
                $newSheet->setCellValue('D4', $factory ?? '');
                $newSheet->getStyle('D4')->getAlignment()->setWrapText(true);
                // $firstquocgia = optional($batch->ingredients->first()?->plantingAreas->first())->quoc_gia;
                // $newSheet->setCellValue('D5', $firstquocgia);
                $newSheet->setCellValue('D5', 'Viet Nam');
                $plantationList = $batch->ingredients->flatMap->plantingAreas->map->farm->pluck('farm_name')->unique()->implode(', ');
                $newSheet->setCellValue('D6', $plantationList ?? '');
                $newSheet->getStyle('D6')->getAlignment()->setWrapText(true);
                $geoUrl = url("/batch-geojson?batch_code={$batch->batch_code}");
                $newSheet->setCellValue('D7', $geoUrl ?? '');
                $newSheet->getCell('D7')->getHyperlink()->setUrl($geoUrl);
                $newSheet->getStyle('D7')->applyFromArray($styleArray);
                $firstrank = optional($batch->testingResult)->rank;
                $newSheet->setCellValue('I2', strtoupper($firstrank ?? ''));
                $newSheet->setCellValue('I3', $batch->batch_weight ?? '');
                $formatdate_sx = date('d/m/Y', strtotime($batch->date_sx));
                $newSheet->setCellValue('I4', $formatdate_sx ?? '');
                $newSheet->setCellValue('I5', strtoupper($batch->type ?? ''));
                $newSheet->setCellValue("K2", $linkwebmap ?? '');
                $newSheet->getCell("K2")->getHyperlink()->setUrl($linkwebmap ?? '');
                $row = 10;
                $startRow = $row;
                foreach ($batch->ingredients as $nl) {
                    foreach ($nl->plantingAreas as $planarea) {
                        $formattedDate = date('d/m/Y', strtotime($nl->received_date));
                        $newSheet->setCellValue("C{$row}", $formattedDate ?? '');
                        $newSheet->setCellValue("A{$row}", $nl->vehicle->vehicle_number ?? '');
                        $formatharvestDate = date('d/m/Y', strtotime($nl->harvesting_date));
                        $newSheet->setCellValue("B{$row}", $formatharvestDate ?? '');
                        $newSheet->setCellValue("D{$row}", $planarea->ma_lo ?? '');
                        $newSheet->setCellValue("E{$row}", $planarea->find ?? '');
                        $newSheet->setCellValue("F{$row}", $planarea->chi_tieu ?? '');
                        $newSheet->setCellValue("G{$row}", $planarea->dien_tich ?? '');
                        $newSheet->setCellValue("H{$row}", "Yes");
                        $newSheet->setCellValue("I{$row}", "Yes");
                        // $values = array_filter([$planarea->x, $planarea->y], function ($value) {
                        //     return $value !== null && $value !== ''; // Loại bỏ null hoặc chuỗi rỗng
                        // });
                        // $listValues = implode(', ', $values);
                        // $newSheet->setCellValue("J{$row}", $listValues);
                        $longlat = url("/batch-geojson?ma_lo={$planarea->ma_lo}");
                        $newSheet->setCellValue("J{$row}", $longlat ?? '');
                        $newSheet->getCell("J{$row}")->getHyperlink()->setUrl($longlat);
                        $newSheet->getStyle("J{$row}")->applyFromArray($styleArray);
                        // $newSheet->setCellValue("K{$row}", $planarea->geo);
                        $geojson = url("/batch-geojson?ma_lo={$planarea->ma_lo}");
                        $newSheet->setCellValue("K{$row}", $geojson ?? '');
                        $newSheet->getCell("K{$row}")->getHyperlink()->setUrl($geojson);
                        $newSheet->getStyle("K{$row}")->applyFromArray($styleArray);
                        $newSheet->setCellValue("L{$row}", $planarea->gwf ?? '');
                        $newSheet->getRowDimension($row)->setRowHeight(150);
                        $row++;
                    }
                    // $columnRange = "A10:L" . ($row - 1);
                }
                // Thêm sheet vào danh sách
                $batchSheets[] = $newSheet;
                if ($row > $startRow) {
                    $columnRange = "A{$startRow}:L" . ($row - 1);
                    $newSheet->getStyle($columnRange)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                    ]);

                    // **Căn trên & Wrap text**
                    $newSheet->getStyle($columnRange)->applyFromArray([
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_TOP, // Căn trên
                            'wrapText' => true, // Xuống dòng tự động
                        ],
                    ]);
                }
            }

            // Xóa tất cả các sheet cũ (bao gồm sheet mẫu)
            while ($spreadsheet->getSheetCount() > 0) {
                $spreadsheet->removeSheetByIndex(0);
            }

            // Thêm các sheet mới vào workbook
            foreach ($batchSheets as $sheet) {
                $spreadsheet->addSheet($sheet);
            }

            // Đặt sheet đầu tiên làm active
            $spreadsheet->setActiveSheetIndex(0);
        }

        // Xuất file với tên dựa theo mã đơn hàng
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, "{$this->order->code}_DDS.xlsx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . "{$this->order->code}_DDS.xlsx" . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}