<?php

namespace App\Imports;

use App\Models\PlantingArea;
use App\Models\Farm;
use App\Models\Units;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UpdatePlaningAreaImport implements ToCollection, WithHeadingRow, SkipsOnFailure, WithEvents
{
    use SkipsFailures;

    public static $errors = [];
    // private $rowNumber = 2;
    // private $rowNumber = 1;

    public function collection(Collection $rows)
    {

        try {
            $errors = $this->validateRow($rows);
            // dd($errors);
            if (empty($errors)) {
                $this->savePlantingArea($rows);
            } else {
                self::$errors = array_merge(self::$errors, $errors);
                // dd(self::$errors);
            }

        } catch (\Exception $e) {
            self::$errors[] = [
                'Error' => $e->getMessage(),
            ];
        }
    }


    private function validateRow($rows)
    {
        $errors = [];
        $currentYear = now()->year;
        $rowNumber = 1;
        $exitFindPlant = [];
        $allUnits = Units::where('status', "Hoạt động")->get()->keyBy('unit_name');
        $user = Auth::user();
        $userFarmName = $user->farm->farm_name ?? null;
        $userUnitName = $user->farm->unitRelation->unit_name ?? null;
        foreach ($rows as $row) {
            $rowNumber++;
            $find = $row['find'] ?? null;
            $plantation = $userFarmName ?? ($row['plantation'] ?? null);
            $geo = $row['geojson'] ?? null;
            $nam_trong = $row['planting_y'] ?? null;
            $cty = $userUnitName ?? ($row['cong_ty'] ?? null);

            if (!$nam_trong) {
                $errors['empty_planting_year'][] = $rowNumber;
            } elseif (!is_numeric($nam_trong) || strlen($nam_trong) !== 4) {
                $errors['planting_year'][] = $rowNumber;
            } elseif ($nam_trong > $currentYear) {
                $errors['invalid_planting_year'][] = $rowNumber;
            }

            if (!$cty) {
                $errors['empty_cty'][] = $rowNumber;
                continue;
            }

            $units = $allUnits[$cty] ?? null;
            if (!$units) {
                $errors['units_invalid'][] = $rowNumber;
                continue;
            }

            if (!$plantation) {
                $errors['empty_plantation'][] = $rowNumber;
                continue;
            }


            if (str_starts_with($plantation, 'NT')) {
                $plantation = str_replace('NT', 'NONG TRUONG', $plantation);
            }

            // Kiểm tra farm, tối ưu bằng cách lấy danh sách farm trước
            $farm = Farm::where('farm_name', $plantation)
                ->where('unit_id', $units->id)
                ->first();

            if (!$farm) {
                $errors['plantation_invalid'][] = $rowNumber;
                continue;
            }

            if (!$find) {
                $errors['find'][] = $rowNumber;
                continue;
            }

            if ($find && $plantation) {
                $exitFindPlant[] = [
                    "find" => $find,
                    "plantation" => $plantation,
                    "row" => $rowNumber
                ];
            }

            $plantingArea = PlantingArea::where('farm_id', $farm->id)->where('find', $find)->where('nam_trong', $nam_trong)->first();
            if ($plantingArea) {
                if (!$plantingArea->ingredients()->exists()) {
                    if (!$geo) {
                        $errors['geojson'][] = $rowNumber;
                    }
                }
            } else {
                $errors['not_found_plating_area'][] = $rowNumber;
            }


        }

        $duplicateRows = $this->findDuplicateRows($exitFindPlant);
        // dd($duplicateRows);
        if (!empty($duplicateRows)) {
            foreach ($duplicateRows as $row) {
                $errors['exit_find_plant'][] = $row;
            }
        }
        // dd($errors);
        return $errors;
    }

    private function findDuplicateRows($data)
    {
        $countFind = [];
        $duplicates = [];

        // Đếm số lần xuất hiện của từng giá trị `find`
        foreach ($data as $item) {
            $findValue = $item['find'];

            if (!isset($countFind[$findValue])) {
                $countFind[$findValue] = [];
            }

            $countFind[$findValue][] = $item; // Lưu lại toàn bộ dòng có `find` này
        }

        // Lọc ra những giá trị `find` bị trùng (xuất hiện > 1 lần)
        foreach ($countFind as $findValue => $rows) {
            // dd($rows);
            if (count($rows) > 1) { // Chỉ lấy các `find` bị trùng
                foreach ($rows as $row) {
                    $duplicates[] = $row['row'];
                }

            }
        }

        return $duplicates; // Trả về danh sách các dòng bị trùng
    }

    private function savePlantingArea($rows)
    {
        foreach ($rows as $row) {
            $find = $row['find'];
            $plantation = $row['plantation'];
            $nam_trong = $row['planting_y'];
            $cty = $row['cong_ty'] ?? null;
            if (str_starts_with($plantation, 'NT')) {
                $plantation = str_replace('NT', 'NONG TRUONG', $plantation);
            }
            $unit = Units::where('unit_name', $cty)->first();
            $farm = Farm::where('farm_name', $plantation)->where('unit_id', $unit->id)->first();
            $factoryShortName = $this->shortenFactoryName($unit->unit_name);
            $farmShortName = $this->shortenFarmName($farm->farm_name);
            $maLo = "{$nam_trong}.{$farmShortName}-{$factoryShortName}.{$find}";

            $plantingArea = PlantingArea::where('farm_id', $farm->id)->where('find', $find)->where('nam_trong', $nam_trong)->first();
            if ($plantingArea) {
                if (!$plantingArea->ingredients()->exists()) {
                    $plantingArea->update(
                        [
                            'farm_id' => $farm->id,
                            "fid" => $row["fid"],
                            "idmap" => $row["idmap"],
                            "nha_sx" => $row["producer"],
                            "quoc_gia" => $row["country"],
                            "ma_lo" => $maLo,
                            "plot" => $row["plot"],
                            "nam_trong" => $nam_trong,
                            "chi_tieu" => $row["clone_spec"],
                            "dien_tich" => $row["area_ha"],
                            "tapping_y" => $row["tapping_y"],
                            "repl_time" => $row["repl_time"],
                            'find' => $find,
                            "webmap" => $row['webmap'],
                            "gwf" => $row["gwf"],
                            "xa" => $row["xa"],
                            "huyen" => $row["huyen"],
                            "nguon_goc_lo" => $row["nguon_goc_lo"],
                            "nguon_goc_dat" => $row["nguon_goc_dat"],
                            "hang_dat" => $row["hang_dat"],
                            "hien_trang" => $row["hien_trang"],
                            "layer" => $row["layer"],
                            "chu_thich" => "chu_thich",
                            "x" => $row["x"],
                            "y" => $row["y"],
                            "geo" => $row["geojson"],
                        ]
                    );
                } else {
                    $plantingArea->update(
                        [
                            "fid" => $row["fid"],
                            "idmap" => $row["idmap"],
                            "nha_sx" => $row["producer"],
                            "quoc_gia" => $row["country"],
                            "ma_lo" => $maLo,
                            "plot" => $row["plot"],
                            "chi_tieu" => $row["clone_spec"],
                            "tapping_y" => $row["tapping_y"],
                            "repl_time" => $row["repl_time"],
                            "webmap" => $row['webmap'],
                            "gwf" => $row["gwf"],
                            "xa" => $row["xa"],
                            "huyen" => $row["huyen"],
                            "nguon_goc_lo" => $row["nguon_goc_lo"],
                            "nguon_goc_dat" => $row["nguon_goc_dat"],
                            "hang_dat" => $row["hang_dat"],
                            "hien_trang" => $row["hien_trang"],
                            "layer" => $row["layer"],
                            "chu_thich" => "chu_thich",
                            "x" => $row["x"],
                            "y" => $row["y"]
                        ]
                    );
                }
            }


            // $plantingArea = PlantingArea:: where('farm_id', $farm->id)
            //  ->where('find', $find)
            //  ->first();

            //  dd($plantingArea);

            // $plantingArea->updateOrCreate([
            //     "fid" => $row["fid"],
            //     "idmap" => $row["idmap"],
            //     "nha_sx" => $row["producer"],
            //     "quoc_gia" => $row["country"],
            //     "ma_lo" => $maLo,
            //     "farm_id" => $farm->id,
            //     // "farm_name" => $row["plantation"],
            //     "plot" => $row["plot"],
            //     "nam_trong" => $nam_trong,
            //     "chi_tieu" => $row["clone_spec"],
            //     "dien_tich" => $row["area_ha"],
            //     "tapping_y" => $row["tapping_y"],
            //     "repl_time" => $row["repl_time"],
            //     "find" => $find,
            //     "webmap" => $row['webmap'],
            //     "gwf" => $row["gwf"],
            //     "xa" => $row["xa"],
            //     "huyen" => $row["huyen"],
            //     "nguon_goc_lo" => $row["nguon_goc_lo"],
            //     "nguon_goc_dat" => $row["nguon_goc_dat"],
            //     "hang_dat" => $row["hang_dat"],
            //     "hien_trang" => $row["hien_trang"],
            //     "layer" => $row["layer"],
            //     "chu_thich" => "chu_thich",
            //     "x" => $row["x"],
            //     "y" => $row["y"],
            //     "geo" => $row["geojson"],
            // ]);
        }
    }


    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            self::$errors[] = [
                'Error' => $failure->errors()[0],
            ];
        }
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                self::$errors = []; // Xóa danh sách lỗi trước khi import
            },
            AfterImport::class => function (AfterImport $event) {
                if (!empty(self::$errors)) {
                    Log::error('Import có lỗi:', self::$errors);
                }
            },
        ];
    }

    private function shortenFarmName($farmName)
    {
        // Loại bỏ tiền tố "Nông Trường" hoặc "NT" nếu có
        $farmName = preg_replace('/^(NT|Nông Trường)\s+/i', '', trim($farmName));

        // Chia farm_name thành các từ
        $words = explode(' ', $farmName);

        // Nếu tên bắt đầu bằng "Đội X" (ví dụ: "Đội 3"), thì lấy "D3"
        if (count($words) == 2 && mb_strtolower($words[0]) == "đội" && is_numeric($words[1])) {
            return 'D' . $words[1];
        }

        // Lấy chữ cái đầu của mỗi từ còn lại
        $shortName = '';
        foreach ($words as $word) {
            $shortName .= mb_substr($word, 0, 1);
        }

        return strtoupper($shortName);
    }
    private function shortenFactoryName($factoryName)
    {
        // Loại bỏ các tiền tố phổ biến của công ty
        $factoryName = preg_replace('/\b(Công Ty Cổ Phần Cao Su|Công Ty TNHH|Công Ty)\b\s*/i', '', trim($factoryName));

        // Chia factory_name thành các từ
        $words = explode(' ', $factoryName);

        // Lấy chữ cái đầu của mỗi từ để tạo viết tắt
        $shortName = '';
        foreach ($words as $word) {
            $shortName .= mb_substr($word, 0, 1);
        }

        return strtoupper($shortName);
    }
}