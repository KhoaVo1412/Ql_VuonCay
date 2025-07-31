<?php

namespace App\Imports;

use App\Models\Batch;
use App\Models\TestingResult;
use Complex\Exception;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportLatex implements ToModel, WithHeadingRow, ShouldAutoSize
    // , SkipsEmptyRows
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function model(array $row)
    {
        // dd($row);

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
                "latex_tsc" => $row["tsc"] ?? null,
                "latex_drc" => $row["drc"] ?? null,
                "latex_nrs" => $row["non_rubber_solids"] ?? null,
                "latex_nh3" => $row["nh3"] ?? null,
                "latex_mst" => $row["mst"] ?? null,
                "latex_vfa" => $row["vfa"] ?? null,
                "latex_koh" => $row["koh"] ?? null,
                "latex_ph" => $row["ph"] ?? null,
                "latex_coagulant" => $row["dong_ket"] ?? null,
                "latex_residue" => $row["can"] ?? null,
                "latex_mg" => $row["mg"] ?? null,
                "latex_mn" => $row["mn"] ?? null,
                "latex_cu" => $row["cu"] ?? null,
                "latex_acid_boric" => $row["acid_boric"] ?? null,
                "latex_surface_tension" => $row["suc_cang_be_mat"] ?? null,
                "latex_viscosity" => $row["do_nhot_brookfield"] ?? null,
            ]
        );
        $batch->update([
            'type' => 'latex',
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