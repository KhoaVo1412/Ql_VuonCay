<?php

namespace App\Imports;

use App\Models\Factory;
use App\Models\Farm;
use App\Models\Ingredient;
use App\Models\PlantingArea;
use App\Models\TypeOfPus;
use App\Models\Units;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Events\BeforeImport;
use Illuminate\Support\Facades\Auth;

class IngredientsImport implements toCollection, WithHeadingRow, SkipsOnFailure, WithEvents
{
    // public function model(array $row)
    // {
    //     $farm = Farm::where('farm_name', $row['nongtruong'])->first();
    //     $type_of_pus = TypeOfPus::where('name_pus', $row['loaimu'])->first();
    //     $vehicle_number = Vehicle::where('vehicle_number', $row['soxe'])->first();
    //     $factory = Factory::where('factory_name', $row['nhamaytiepnhan'])->first();

    //     $receivedDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['ngaytiepnhan'])->format('Y-m-d');
    //     $harvestingDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['ngaycao'])->format('Y-m-d');
    //     $endHarvestDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['ngayketthuccao'])->format('Y-m-d');

    //     $ingredient = new Ingredient([
    //         'farm_id' => $farm ? $farm->id : 0,
    //         'received_date' => $receivedDate,
    //         'type_of_pus_id' => $type_of_pus ? $type_of_pus->id : 0,
    //         'vehicle_number_id' => $vehicle_number ? $vehicle_number->id : 0,
    //         'trip' => $row['chuyen'],
    //         'received_factory_id' => $factory ? $factory->id : 0,
    //         // 'receiving_factory' => $row['nhamaytiepnhan'],
    //         'harvesting_date' => $harvestingDate,
    //         'end_harvest_date' => $endHarvestDate,
    //     ]);

    //     $ingredient->save();

    //     $maLoList = explode(',', $row['khutrong']);

    //     $plantingAreaIds = PlantingArea::whereIn('ma_lo', $maLoList)->pluck('id')->toArray();

    //     if (!empty($plantingAreaIds)) {
    //         $ingredient->plantingAreas()->sync($plantingAreaIds);
    //     } else {
    //         Log::warning("Không tìm thấy khu trồng với mã lô: " . implode(", ", $maLoList));
    //     }

    //     return $ingredient;
    // }
    // public function model(array $row)
    // {
    //     // Tìm kiếm các đối tượng trong cơ sở dữ liệu với tên có dấu
    //     $farm = Farm::where('farm_name', $row['Nông Trường'])->first();
    //     $type_of_pus = TypeOfPus::where('name_pus', $row['Loại Mủ'])->first();
    //     $vehicle_number = Vehicle::where('vehicle_number', $row['Số Xe'])->first();
    //     $factory = Factory::where('factory_name', $row['Nhà Máy Tiếp Nhận'])->first();

    //     // Chuyển đổi ngày theo định dạng phù hợp
    //     $receivedDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['Ngày Tiếp Nhận'])->format('Y-m-d');
    //     $harvestingDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['Ngày Cạo'])->format('Y-m-d');
    //     $endHarvestDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['Ngày Kết Thúc Cạo'])->format('Y-m-d');

    //     // Tạo đối tượng Ingredient và lưu vào cơ sở dữ liệu
    //     $ingredient = new Ingredient([
    //         'farm_id' => $farm ? $farm->id : 0,
    //         'received_date' => $receivedDate,
    //         'type_of_pus_id' => $type_of_pus ? $type_of_pus->id : 0,
    //         'vehicle_number_id' => $vehicle_number ? $vehicle_number->id : 0,
    //         'trip' => $row['Chuyến'],
    //         'received_factory_id' => $factory ? $factory->id : 0,
    //         'harvesting_date' => $harvestingDate,
    //         'end_harvest_date' => $endHarvestDate,
    //     ]);

    //     $ingredient->save();

    //     // Xử lý các mã lô trồng
    //     $maLoList = explode(',', $row['Khu Trồng']);

    //     // Lấy danh sách các ID khu trồng từ cơ sở dữ liệu
    //     $plantingAreaIds = PlantingArea::whereIn('ma_lo', $maLoList)->pluck('id')->toArray();

    //     // Đồng bộ các khu trồng với ingredient
    //     if (!empty($plantingAreaIds)) {
    //         $ingredient->plantingAreas()->sync($plantingAreaIds);
    //     } else {
    //         Log::warning("Không tìm thấy khu trồng với mã lô: " . implode(", ", $maLoList));
    //     }

    //     return $ingredient;
    // }

    use SkipsFailures;

    public static $errors = [];
    public function collection(Collection $rows)
    {
        try {

            $errors = $this->validateRow($rows);

            if (empty($errors)) {
                $this->save($rows);
            } else {
                self::$errors = array_merge(self::$errors, $errors);

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
        $rowNumber = 1;
        $today = Carbon::today()->format('d-m-Y');
        $allUnits = Units::where('status', "Hoạt động")->get()->keyBy('unit_name');
        $allFactory = Factory::where('status', "Hoạt động")->get()->keyBy('factory_name');
        $allFarm = Farm::where('status', "Hoạt động")->get()->keyBy('farm_name');
        $user = Auth::user();
        // $groupedPlantingAreas = []; // Gom mã lô theo tổ hợp ngày
        $groupVerhicle = []; // ['key_tong_hop' => ['so_chuyen1', 'so_chuyen2']]
        // $duplicatePlatingAreas = [];
        foreach ($rows as $row) {
            $rowNumber++;
            $donvi = $user->farm->unitRelation->unit_name ?? ($row['cong_ty'] ?? null);
            $nongtruong = $user->farm->farm_name ?? ($row['nong_truong'] ?? null);
            $loaimu = $row['loai_mu'] ?? null;
            $soxe = $row['so_xe_van_chuyen'] ?? null;
            $nhamaytiepnhan = $row['nha_may_tiep_nhan'] ?? null;
            $ngaytiepnhan = $row['ngay_tiep_nhan'] ?? null;
            $ngaybatdaucao = $row['ngay_bat_dau_cao'] ?? null;
            $ngayketthuccao = $row['ngay_ket_thuc_cao'] ?? null;
            $sochuyen = $row['so_chuyen'] ?? null;
            $khuvuctrong = $row['khu_vuc_trong'] ?? null;

            $isValidDateNgayTiepNhan = false;
            $isValidDateNgayBatDauCao = false;
            $isValidDateNgayKetThucCao = false;
            $isValidateSoXe = false;
            $isFutureDateNgayTiepNhan = false;
            $isFutureDateNgayBatDauCao = false;
            $isFutureDateNgayKetThucCao = false;

            $farm = $allFarm[$nongtruong] ?? null;

            // $key = implode('|', [
            //     $donvi,
            //     $nongtruong,
            //     $ngaytiepnhan,
            //     $loaimu,
            //     $soxe,
            //     $nhamaytiepnhan,
            //     $ngaybatdaucao,
            //     $ngayketthuccao,
            //     $khuvuctrong
            // ]);


            // $userFarmName = $user->farm->farm_name ?? null;
            // $userUnitName = $user->farm->unitRelation->unit_name ?? null;

            if (!$donvi) {
                $errors['empty_donvi'][] = $rowNumber;
                continue;
            }
            $units = $allUnits[$donvi] ?? null;
            if (!$units) {
                $errors['unit_invalid'][] = $rowNumber;
                continue;
            }

            if (str_starts_with(strtoupper($nongtruong), 'NT')) {
                $nongtruong = str_ireplace('NT', 'NONG TRUONG', $nongtruong);
            }

            if (!$nongtruong) {
                $errors['empty_nongtruong'][] = $rowNumber;
                continue;
            }

            $farm = Farm::where('farm_name', $nongtruong)
                ->where('unit_id', $units->id)
                ->first();


            if (!$farm) {
                $errors['exists_invaild'][] = $rowNumber;
                continue;
            }

            // if ($userFarmName && $userUnitName) {
            //     if ($nongtruong !== $userFarmName || $userUnitName !== $donvi) {
            //         $errors['farm_and_unit_not_allowed'][] = $rowNumber;
            //         continue;
            //     }
            // }

            if (!$loaimu) {
                $errors['empty_loaimu'][] = $rowNumber;
            } else {
                $typeofpus = TypeOfPus::where('name_pus', $loaimu)
                    ->where('status', "Hoạt động")
                    ->first();
                if (!$typeofpus) {
                    $errors['loaimu_invalid'][] = $rowNumber;
                }
            }

            if (!$soxe) {
                $errors['empty_soxe'][] = $rowNumber;
            } else {
                // $vehicle = null;
                // if ($farm) {
                // $isValidateSoXe = Vehicle::where('vehicle_number', $soxe)
                //     ->where('farm_id', $farm->id)
                //     ->first();
                $isValidateSoXe = Vehicle::where('vehicle_number', $soxe)
                    ->where('unit_id', $units->id)
                    ->first();
                // }
                if (!$isValidateSoXe) {
                    $errors['existsSX_invaild'][] = $rowNumber;
                }
            }

            if (!$nhamaytiepnhan) {
                $errors['empty_nhamay'][] = $rowNumber;
            } else {
                $factory = $allFactory[$nhamaytiepnhan] ?? null;
                if (!$factory) {
                    $errors['nhamay_invalid'][] = $rowNumber;
                }
            }

            if (!$ngaytiepnhan) {
                $errors['empty_ngaytiepnhan'][] = $rowNumber;
            } else {
                $isValidDateNgayTiepNhan = $this->isValidDate($ngaytiepnhan);
                if (!$isValidDateNgayTiepNhan) {
                    $errors['invaild_ngaytiepnhan'][] = $rowNumber;
                }
            }

            if (!$ngaybatdaucao) {
                $errors['empty_ngaybdcao'][] = $rowNumber;
            } else {
                $isValidDateNgayBatDauCao = $this->isValidDate($ngaybatdaucao);
                if (!$isValidDateNgayBatDauCao) {
                    $errors['invaild_ngaybdcao'][] = $rowNumber;
                }
            }

            if (!$ngayketthuccao) {
                $errors['empty_ngayktcao'][] = $rowNumber;
            } else {
                $isValidDateNgayKetThucCao = $this->isValidDate($ngayketthuccao);
                if (!$isValidDateNgayKetThucCao) {
                    $errors['invaild_ngayktcao'][] = $rowNumber;
                }
            }

            if (!$sochuyen) {
                $errors['empty_sochuyen'][] = $rowNumber;
            } else {
                if (!is_numeric($sochuyen)) {
                    $errors['invaild_numbersc'][] = $rowNumber;
                }
            }

            if ($isValidDateNgayTiepNhan) {
                $isFutureDateNgayTiepNhan = $this->isFutureDate($ngaytiepnhan, $today);
                if ($isFutureDateNgayTiepNhan) {
                    $errors['not_valid_future_date_ntn'][] = $rowNumber;
                }
            }
            if ($isValidDateNgayBatDauCao) {
                $isFutureDateNgayBatDauCao = $this->isFutureDate($ngaybatdaucao, $today);
                if ($isFutureDateNgayBatDauCao) {
                    $errors['not_valid_future_date_nbdc'][] = $rowNumber;
                }
            }
            if ($isValidDateNgayKetThucCao) {
                $isFutureDateNgayKetThucCao = $this->isFutureDate($ngayketthuccao, $today);
                if ($isFutureDateNgayKetThucCao) {
                    $errors['not_valid_future_date_nktc'][] = $rowNumber;
                }
            }

            if ($isValidDateNgayBatDauCao && $isValidDateNgayKetThucCao && $isValidDateNgayTiepNhan) {
                if ($this->isFutureDate($ngaybatdaucao, $ngayketthuccao)) {
                    $errors['ngaybdcao_greater_ngayktcao'][] = $rowNumber;
                }
                if ($this->isFutureDate($ngaybatdaucao, $ngaytiepnhan)) {
                    $errors['ngaybdcao_greater_ngaytn'][] = $rowNumber;
                }
                if ($this->isFutureDate($ngayketthuccao, $ngaytiepnhan)) {
                    $errors['ngayktcao_greater_ngaytn'][] = $rowNumber;
                }
            }



            if (!$isFutureDateNgayTiepNhan && $isValidateSoXe) {


                $verhicaleDuplicate = PlantingArea::whereHas('ingredients', function ($query) use ($ngaytiepnhan, $sochuyen, $isValidateSoXe) {
                    $query->where('received_date', $this->formatDate($ngaytiepnhan))
                        ->where('trip', $sochuyen)
                        ->where('vehicle_number_id', $isValidateSoXe->id);
                })->first();

                if ($verhicaleDuplicate) {
                    $errors['exists_planting_area'][] = $rowNumber;
                }

                $groupVerhicle[$ngaytiepnhan][$soxe][$sochuyen][] = $rowNumber;
            }

            if (!empty($khuvuctrong)) {
                $plantingarea = null;
                $maLoList = explode(',', $khuvuctrong);

                $keyDate = $ngaytiepnhan . ' , ' . $ngaybatdaucao . ' , ' . $ngayketthuccao;

                foreach ($maLoList as $maLo) {

                    $plantingarea = PlantingArea::where('ma_lo', $maLo)
                        ->where('farm_id', $farm->id)
                        ->first();

                    if (!$plantingarea) {
                        $errors['invaild_plantingArea'][] = $rowNumber;
                        continue;
                    }

                    // $duplicatePlantingArea = $plantingarea
                    //     ->whereDoesntHave('ingredients', function ($query) use ($ngaytiepnhan, $ngaybatdaucao, $ngayketthuccao) {
                    //         $query->where('received_date', $this->formatDate($ngaytiepnhan))
                    //             ->where('harvesting_date', $this->formatDate($ngaybatdaucao))
                    //             ->where('end_harvest_date', $this->formatDate($ngayketthuccao));
                    //     })
                    //     ->first();

                    // if ($duplicatePlantingArea) {
                    //     $groupedPlantingAreas[$keyDate][$maLo][] = $rowNumber;
                    // }

                    // $groupedPlantingAreas[$keyDate][$maLo][] = $rowNumber;
                }
            }
        }

        // foreach ($groupedPlantingAreas as $keyDate => $maLoWithRows) {
        //     foreach ($maLoWithRows as $maLo => $rows) {
        //         // Kiểm tra duplicate trong rows nếu có nhiều hơn 1 dòng
        //         if (count($rows) > 1) {
        //             $errors['duplicate_planting_area'][] = [
        //                 "date" => $keyDate,
        //                 "rowNumber" => array_unique($rows),
        //                 "ma_lo" => $maLo
        //             ];
        //         }
        //     }
        // }

        // dd($isFutureDateNgayTiepNhan);

        foreach ($groupVerhicle as $keyDate => $xeWithRouws) {
            foreach ($xeWithRouws as $soxe => $sochuyen) {
                foreach ($sochuyen as $sc => $rows) {
                    if (count($rows) > 1) {

                        $errors['duplicate_so_chuyen'][] = [
                            // "date" => $keyDate,
                            // "soXe" => $soxe,
                            // "soChuyen" => $sc,
                            "row" => array_unique($rows)
                        ];
                    }
                }
            }
        }


        return $errors;
    }

    private function save($rows)
    {
        foreach ($rows as $row) {
            $nongtruong = $row['nong_truong'] ?? null;
            $donvi = $row['cong_ty'] ?? null;
            $vehicle = Vehicle::where('vehicle_number', $row['so_xe_van_chuyen'])->first();
            $typeofpus = TypeOfPus::where('name_pus', $row['loai_mu'])->first();
            $factory = Factory::where('factory_name', $row['nha_may_tiep_nhan'])->first();
            if (str_starts_with(strtoupper($nongtruong), 'NT')) {
                $nongtruong = str_ireplace('NT', 'NONG TRUONG', $nongtruong);
            }
            $unit = Units::where('unit_name', $donvi)->first();
            $farm = Farm::where('farm_name', $nongtruong)->where('unit_id', $unit->id)->first();
            $maLoList = explode(',', $row['khu_vuc_trong']);
            $plantingAreaIds = PlantingArea::whereIn('ma_lo', $maLoList)->pluck('id')->toArray();
            $ingredient = Ingredient::updateOrCreate([
                'farm_id' => $farm->id,
                'received_date' => $this->formatDate($row['ngay_tiep_nhan']),
                'harvesting_date' => $this->formatDate($row['ngay_bat_dau_cao']),
                'end_harvest_date' => $this->formatDate($row['ngay_ket_thuc_cao']),
                'vehicle_number_id' => $vehicle->id,
                'type_of_pus_id' => $typeofpus->id,
                'received_factory_id' => $factory->id,
                'trip' => $row['so_chuyen'],
            ]);
            if (!empty($plantingAreaIds)) {
                $ingredient->plantingAreas()->sync($plantingAreaIds);
            }
            // dd($unit);
        }

    }

    private function formatDate($date)
    {
        if (!$date) {
            return null;
        }

        // Chuẩn hóa dấu phân cách (chuyển tất cả '/' và '.' thành '-')
        $date = str_replace(['/', '.'], '-', $date);

        try {
            // Kiểm tra xem chuỗi có đúng định dạng dd-mm-yyyy hoặc mm-dd-yyyy không
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
                $dateParts = explode('-', $date);
                $day = (int) $dateParts[0];
                $month = (int) $dateParts[1];

                // Nếu ngày > 12 => Chắc chắn là 'd-m-Y'
                if ($day > 12) {
                    return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
                }

                // Nếu tháng > 12 => Chắc chắn là 'm-d-Y'
                if ($month > 12) {
                    return Carbon::createFromFormat('m-d-Y', $date)->format('Y-m-d');
                }

                // Nếu không thể xác định rõ => Mặc định là 'd-m-Y'
                return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
            }

            // Nếu nhập theo chuẩn ISO (YYYY-MM-DD) hoặc định dạng khác, cứ để Carbon tự parse
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            // Nếu lỗi, trả về nguyên gốc (hoặc có thể return null)
            return $date;
        }
    }

    private function isValidDate($date)
    {
        try {
            if (!$date) {
                return false;
            }

            // Chuẩn hóa dấu phân cách (chuyển '/' thành '-')
            $date = str_replace('/', '-', $date);

            // Kiểm tra định dạng hợp lệ: dd-mm-yyyy hoặc mm-dd-yyyy
            if (!preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
                return false;
            }

            $dateParts = explode('-', $date);

            // Kiểm tra nếu ngày > 12 thì chắc chắn là dd-mm-yyyy
            if ((int) $dateParts[0] > 12) {
                return Carbon::createFromFormat('d-m-Y', $date, null) !== false;
            }

            // Kiểm tra nếu tháng > 12 thì chắc chắn là mm-dd-yyyy
            if ((int) $dateParts[1] > 12) {
                return Carbon::createFromFormat('m-d-Y', $date, null) !== false;
            }

            // Nếu không thể xác định rõ, thử kiểm tra cả hai định dạng
            return Carbon::createFromFormat('d-m-Y', $date, null) !== false ||
                Carbon::createFromFormat('m-d-Y', $date, null) !== false;
        } catch (\Exception $e) {
            // dd($e);
            // Nếu không đúng định dạng, giữ nguyên
            return false;
        }
    }

    private function isFutureDate($date1, $date2)
    {

        $d1 = Carbon::parse($this->formatDate($date1));
        $d2 = Carbon::parse($this->formatDate($date2));

        return $d1->greaterThan($d2);
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
}