<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use App\Models\Plot;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PlotImport implements ToCollection, WithHeadingRow, SkipsOnFailure, WithEvents
{
    use SkipsFailures;

    public static $errors = [];
    // private $rowNumber = 2;
    // private $rowNumber = 1;
    protected $importedRows = [];
    protected $datas = [];
    public function getImportedData()
    {
        return $this->importedRows;
    }
    public function getData()
    {
        return $this->datas;
    }
    public function collection(Collection $rows)
    {

        try {
            $errors = $this->validateRow($rows);
            // dd($errors);
            if (empty($errors)) {
                $this->importedRows = $rows->toArray();
                $this->datas = $rows->toArray();
                $this->saveplot($rows);
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

        foreach ($rows as $row) {
            $rowNumber++;
            $find = $row['find'] ?? null;
            $mapJs = $row['geojson'] ?? null;
            $year = $row['planting_y'] ?? null;

            if (!$find) {
                $errors['find'][] = $rowNumber;
            }

            if (!$mapJs) {
                $errors['geojson'][] = $rowNumber;
            }

            if (!$year) {
                $errors['empty_planting_year'][] = $rowNumber;
            } elseif (!filter_var($year, FILTER_VALIDATE_INT) || strlen($year) !== 4) {
                $errors['planting_year'][] = $rowNumber;
            } elseif ($year > $currentYear) {
                $errors['invalid_planting_year'][] = $rowNumber;
            }
            // $plantationNormalized = Str::ascii(mb_strtolower(trim($plantation)));
            // $ctyNormalized = Str::ascii(mb_strtolower(trim($cty)));
            // if ($user->farms->isNotEmpty()) {
            //     if (!in_array($plantationNormalized, $userFarmNames) || !in_array($ctyNormalized, $userUnitNames)) {
            //         $errors['farm_and_unit_not_allowed'][] = $rowNumber;
            //         continue;
            //     }
            // }

            // Kiểm tra trùng lặp
            // $existingArea = Plot::where('find', (string) $find)
            //     // ->where('farm_id', $farm->id)
            //     ->exists();

            // if ($existingArea) {
            //     $errors['duplicate_find_farm'][] = $rowNumber;
            // }

            // if ($find && $plantation) {
            //     $exitFindPlant[] = [
            //         "find" => $find,
            //         "plantation" => $plantation,
            //         "row" => $rowNumber
            //     ];
            // }
        }

        // Kiểm tra trùng lặp trong danh sách
        $duplicateRows = $this->findDuplicateRows($exitFindPlant);
        if (!empty($duplicateRows)) {
            foreach ($duplicateRows as $row) {
                $errors['exit_find_plant'][] = $row;
            }
        }

        return $errors;
    }

    private function findDuplicateRows($data)
    {
        $countFind = [];
        $duplicates = [];
        foreach ($data as $item) {
            $findValue = $item['find'];
            if (!isset($countFind[$findValue])) {
                $countFind[$findValue] = [];
            }
            $countFind[$findValue][] = $item;
        }
        foreach ($countFind as $findValue => $rows) {
            if (count($rows) > 1) {
                foreach ($rows as $row) {
                    $duplicates[] = $row['row'];
                }
            }
        }
        return $duplicates;
    }

    public function saveplot($rows)
    {
        foreach ($rows as $row) {
            $find = $row['find'];
            $year = $row['planting_y'];
            $plotCode = "{$year}.{$find}";

            Plot::updateOrCreate(
                [
                    // 'fid' => $row['fid'],
                    // 'idmap' => $row['idmap'],
                    'plotCode' => $plotCode,
                ],
                [
                    "plotCode"      => $plotCode,
                    "plotName"      => $plotCode,
                    "year"          => $year,
                    "status"          => 'Hoạt động',
                    "chi_tieu"      => $row["clone_spec"],
                    "plotArea"      => $row["area_ha"],
                    "tapping_y"     => $row["tapping_y"],
                    "repl_time"     => $row["repl_time"],
                    "find"          => $find,
                    "webmap"        => $row['webmap'],
                    "gwf"           => $row["gwf"],
                    "xa"            => $row["xa"],
                    "huyen"         => $row["huyen"],
                    "nguon_goc_lo"  => $row["nguon_goc_lo"],
                    "nguon_goc_dat" => $row["nguon_goc_dat"],
                    "hang_dat"      => $row["hang_dat"],
                    "hien_trang"    => $row["hien_trang"],
                    "layer"         => $row["layer"],
                    "chu_thich"     => "chu_thich",
                    "x"             => $row["x"],
                    "y"             => $row["y"],
                    "mapJs"         => $row["geojson"],
                ]
            );
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
                self::$errors = [];
            },
            AfterImport::class => function (AfterImport $event) {
                if (!empty(self::$errors)) {
                    Log::error('Import có lỗi:', self::$errors);
                }
            },
        ];
    }

    // public function shortenFarmName($farmName)
    // {
    //     // Loại bỏ tiền tố "Nông Trường" hoặc "NT" nếu có
    //     $farmName = preg_replace('/^(NT|Nông Trường)\s+/i', '', trim($farmName));

    //     // Chia farm_name thành các từ
    //     $words = explode(' ', $farmName);

    //     // Nếu tên bắt đầu bằng "Đội X" (ví dụ: "Đội 3"), thì lấy "D3"
    //     if (count($words) == 2 && mb_strtolower($words[0]) == "đội" && is_numeric($words[1])) {
    //         return 'D' . $words[1];
    //     }

    //     // Lấy chữ cái đầu của mỗi từ còn lại
    //     $shortName = '';
    //     foreach ($words as $word) {
    //         $shortName .= mb_substr($word, 0, 1);
    //     }

    //     return strtoupper($shortName);
    // }
    // public function shortenFactoryName($factoryName)
    // {
    //     // Loại bỏ các tiền tố phổ biến của công ty
    //     $factoryName = preg_replace('/\b(Công Ty Cổ Phần Cao Su|Công Ty TNHH|Công Ty)\b\s*/i', '', trim($factoryName));

    //     // Chia factory_name thành các từ
    //     $words = explode(' ', $factoryName);

    //     // Lấy chữ cái đầu của mỗi từ để tạo viết tắt
    //     $shortName = '';
    //     foreach ($words as $word) {
    //         $shortName .= mb_substr($word, 0, 1);
    //     }

    //     return strtoupper($shortName);
    // }
}
