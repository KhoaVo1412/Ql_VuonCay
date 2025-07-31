<?php

namespace App\Exports;

use App\Models\Batch;
use App\Models\Webmap;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ExportDueDiliState
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $malo;
    public function __construct(Batch $malo)
    {
        $this->malo = $malo;
        // $this->quocgia = $quocgia;
    }
    public function download()
    {

        // Đường dẫn file mẫu
        $templatePath = public_path('templates/template_duedilistate.xlsx');
        $spreadsheet = IOFactory::load($templatePath); // Load file mẫu
        $sheet = $spreadsheet->getActiveSheet();
        $linkwebmap = Webmap::value('webmap');
        // Tạo qr batch code
        $batchcode = $this->malo->batch_code;
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
                $drawing->setWorksheet($sheet);

                // Gộp ô A2:B7 để chứa mã QR
                $sheet->mergeCells('A2:B7');
            }
        } else {
            // Nếu không có dữ liệu, đặt ô A2 thành trống
            $sheet->setCellValue('A2', '');
        }

        // // Tạo qr webmap
        // $webmap = optional($this->malo->ingredients->first()?->plantingAreas->first())->webmap;
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
                $drawing->setWorksheet($sheet);
            }
        } else {
            // Nếu không có dữ liệu, đặt ô A2 thành trống
            $sheet->setCellValue('K3', '');
        }

        $styleArray = [
            'font' => [
                'color' => ['argb' => Color::COLOR_BLUE], // Màu xanh dương
                'underline' => Font::UNDERLINE_SINGLE, // Gạch chân
            ],
        ];

        // Các cột
        $sheet->setCellValue('D2', $this->malo->batch_code);
        $sheet->setCellValue('I3', $this->malo->batch_weight);
        $formatdate_sx = date('d/m/Y', strtotime($this->malo->date_sx));
        $sheet->setCellValue('I4', $formatdate_sx);
        $sheet->setCellValue('D3', 'Hoa Binh Rubber Joint stock Company');
        $sheet->setCellValue('D4', 'Nhà máy Chế Biến Cao Su Hòa Bình');
        $firstquocgia = optional($this->malo->ingredients->first()?->plantingAreas->first())->quoc_gia;
        $sheet->setCellValue('D5', $firstquocgia);
        $plantationList = $this->malo->ingredients->flatMap->plantingAreas->map->farm->pluck('farm_name')->unique()->implode(', ');
        $sheet->setCellValue('D6', $plantationList);
        $geoUrl = url("/batch-geojson?batch_code={$this->malo->batch_code}");
        $sheet->setCellValue('D7', $geoUrl);
        $sheet->getCell('D7')->getHyperlink()->setUrl($geoUrl);
        $sheet->getStyle('D7')->applyFromArray($styleArray);
        $sheet->setCellValue('I5', strtoupper($this->malo->type));
        // $firstrank = $this->malo->testingResult ? optional($this->malo->testingResult->first())->rank : '';
        $firstrank = optional($this->malo->testingResult)->rank;
        $sheet->setCellValue('I2', strtoupper($firstrank));
        $sheet->setCellValue("K2", $linkwebmap);
        $sheet->getCell("K2")->getHyperlink()->setUrl($linkwebmap);

        // $geo = optional($this->malo->ingredients->first()?->plantingAreas->first())->geo;
        // $sheet->setCellValue('D7', $geo);
        // $sheet->getColumnDimension('D')->setAutoSize(true);
        $row = 10;
        foreach ($this->malo->ingredients as $nl) {
            foreach ($nl->plantingAreas as $planarea) {
                $formattedDate = date('d/m/Y', strtotime($nl->received_date));
                $sheet->setCellValue("C{$row}", $formattedDate);
                $sheet->setCellValue("A{$row}", $nl->vehicle->vehicle_number);
                $formatharvestDate = date('d/m/Y', strtotime($nl->harvesting_date));
                $sheet->setCellValue("B{$row}", $formatharvestDate);
                $sheet->setCellValue("D{$row}", $planarea->ma_lo);
                $sheet->setCellValue("E{$row}", $planarea->find);
                $sheet->setCellValue("F{$row}", $planarea->chi_tieu);
                $sheet->setCellValue("G{$row}", $planarea->dien_tich);
                $sheet->setCellValue("H{$row}", "Yes");
                $sheet->setCellValue("I{$row}", "Yes");
                // $values = array_filter([$planarea->x, $planarea->y], function ($value) {
                //     return $value !== null && $value !== ''; // Loại bỏ null hoặc chuỗi rỗng
                // });
                // $listValues = implode(', ', $values);
                // $sheet->setCellValue("J{$row}", $listValues);
                // $sheet->setCellValue("K{$row}", $planarea->geo);
                $longlat = url("/batch-geojson?ma_lo={$planarea->ma_lo}");
                $sheet->setCellValue("J{$row}", $longlat);
                $sheet->getCell("J{$row}")->getHyperlink()->setUrl($longlat);
                $sheet->getStyle("J{$row}")->applyFromArray($styleArray);
                $geojson = url("/batch-geojson?ma_lo={$planarea->ma_lo}");
                $sheet->setCellValue("K{$row}", $geojson);
                $sheet->getCell("K{$row}")->getHyperlink()->setUrl($geojson);
                $sheet->getStyle("K{$row}")->applyFromArray($styleArray);
                $sheet->setCellValue("L{$row}", $planarea->gwf);
                $row++;
            }
            $columnRange = "A10:L" . ($row - 1);

            // **Thêm border**
            $sheet->getStyle($columnRange)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

            // **Căn trên & Wrap text**
            $sheet->getStyle($columnRange)->applyFromArray([
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP, // Căn trên
                    'wrapText' => true, // Xuống dòng tự động
                ],
            ]);
        }


        // Xuất file với tên dựa theo mã đơn hàng
        $fileName = "{$this->malo->batch_code}.xlsx";
        return Response::streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName);
    }
}