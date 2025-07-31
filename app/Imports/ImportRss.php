<?php

namespace App\Imports;

use App\Models\Batch;
use App\Models\TestingResult;
use Carbon\Carbon;
use Complex\Exception;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportRss implements ToModel, WithHeadingRow, ShouldAutoSize
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function model(array $row)
    {
        $batch = Batch::where('batch_code', $row['ma_lo_hang'])->first();
        if (!$batch) {
            throw new Exception('Không tìm thấy Mã lô hàng (có thể bạn nhập mã đó không tồn tại hoặc mã đó để trống). Xin vui lòng nhập lại!');
        }

        $testingResult = TestingResult::updateOrCreate(
            ['batch_id' => $batch->id],
            [
                "ngay_gui_mau" => $this->formatDate($row["ngay_gui_mau"]) ?? null,
                "ngay_kiem_nghiem" => $this->formatDate($row["ngay_kiem_nghiem"]) ?? null,
                "rank" => $row["xep_hang"] ?? null,
                "rss_impurity" => $row["chat_ban"] ?? null,
                "rss_ash" => $row["tro"] ?? null,
                "rss_volatile" => $row["bay_hoi"] ?? null,
                "rss_nitrogen" => $row["nito"] ?? null,
                "rss_po" => $row["po"] ?? null,
                "rss_pri" => $row["pri"] ?? null,
                "rss_vr" => $row["vr"] ?? null,
                "rss_tensile_strength" => $row["luc_keo_dut"] ?? null,
                "rss_elongation" => $row["do_gian_dai"] ?? null,
                "rss_aceton" => $row["aceton"] ?? null,
                "rss_vulcanization" => $row["luu_hoa"] ?? null,
            ]
        );
        $batch->update([
            'type' => 'rss',
            'status' => 1
        ]);

        return $testingResult;
    }
    private function formatDate($date)
    {
        try {
            return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \Exception("Lỗi định dạng ngày: $date");
        }
    }
}