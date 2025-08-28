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
            $this->saveplot($rows);
        } catch (\Exception $e) {
            self::$errors[] = [
                'Error' => $e->getMessage(),
            ];
        }
    }



    public function saveplot($rows)
    {
        foreach ($rows as $row) {
            $find = $row['find'];
            // $plantation = $row['plantation'];
            $year = $row['planting_y'];
            $plot = Plot::all();
            $plotCode = "{$year}.{$find}";

            Plot::create([
                "fid" => $row["fid"],
                "idmap" => $row["idmap"],
                "plotName" => $plotCode,
                "plotCode" => $plotCode,
                "year" => $year,
                "chi_tieu" => $row["clone_spec"],
                "plotArea" => $row["area_ha"],
                "tapping_y" => $row["tapping_y"],
                "repl_time" => $row["repl_time"],
                "find" => $find,
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
                "mapJs" => $row["geojson"],
                "status" => 'Hoạt động',
            ]);
            // }
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
}
