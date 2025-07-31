<?php

namespace App\Imports;

use App\Models\BatchIngredients;
use App\Models\Farm;
use App\Models\Vehicle;
use App\Models\Batch;
use App\Models\Ingredient;
use App\Models\Units;
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
// class BatchImport implements ToModel, WithHeadingRow, SkipsOnFailure, WithEvents
// {
//     use SkipsFailures;

//     public static $errors = [];

//     private $row = 0;

//     public function model(array $row)
//     {
//         try {
//             $farmName = trim($row['nguyen_lieu_nong_truong']);
//             $vehicleNumber = trim($row['so_xe_van_chuyen']);
//             $receivedDate = trim($row['ngay_tiep_nhan']);
//             $trip = (int) trim($row['so_chuyen']);



//             if (empty($farmName)) {
//                 self::$errors[] = [
//                     'message' => "Nông trại không được để trống!",
//                 ];
//                 return null;
//             }
//             $farm = Farm::where('farm_name', $farmName)->first();
//             if (empty($farm)) {
//                 self::$errors[] = [
//                     'message' => "Nông trại '$farmName' không hợp lệ!",
//                 ];
//                 return null;
//             }

//             // dd($farm, gettype($farm), empty($farm), is_null($farm));

//             if (empty($vehicleNumber)) {
//                 self::$errors[] = [
//                     'message' => "Số xe không được để trống!",
//                 ];
//                 return null;
//             }

//             $vehicle = Vehicle::where('vehicle_number', $vehicleNumber)->first();
//             if (empty($vehicle)) {
//                 self::$errors[] = [
//                     'message' => "Số xe '$vehicleNumber' không hợp lệ!",
//                 ];
//                 return null;
//             }


//             $ingredient = Ingredient::whereHas('farm', function ($query) use ($farmName) {
//                 $query->where('farm_name', $farmName);
//             })
//                 ->whereHas('vehicle', function ($query) use ($vehicleNumber) {
//                     $query->where('vehicle_number', $vehicleNumber);
//                 })
//                 ->where('received_date', $receivedDate)
//                 ->where('trip', $trip)
//                 ->first();


//             $batch = Batch::where('batch_code', $row["so_lo_hang"])->first();

//             if ($batch) {
//                 $batch->update([
//                     "date_sx" => $row["ngay_san_xuat"],
//                     "batch_weight" => $row["khoi_luong_lo_hang"],
//                     "banh_weight" => $row["khoi_luong_banh"],
//                 ]);
//                 if ($ingredient) {
//                     $batch->ingredients()->syncWithoutDetaching([$ingredient->id]);
//                 }
//                 return null;
//             }

//             $batch = Batch::create([
//                 "batch_code" => $row["so_lo_hang"],
//                 "date_sx" => $row["ngay_san_xuat"],
//                 "batch_weight" => $row["khoi_luong_lo_hang"],
//                 "banh_weight" => $row["khoi_luong_banh"],
//             ]);

//             if ($ingredient) {
//                 $batch->ingredients()->syncWithoutDetaching([$ingredient->id]);
//             }

//             return $batch;
//         } catch (\Exception $e) {
//             self::$errors[] = [
//                 'row' => $row,
//                 'message' => "Lỗi tại hàng: " . $e->getMessage(),
//             ];
//             return null;
//         }
//     }


//     public function onFailure(Failure ...$failures)
//     {
//         foreach ($failures as $failure) {
//             self::$errors[] = [
//                 'row' => $failure->values(),
//                 'message' => $failure->errors()[0],
//             ];
//         }
//     }

//     public function registerEvents(): array
//     {
//         return [
//             AfterImport::class => function (AfterImport $event) {
//                 if (!empty(self::$errors)) {
//                     Log::error('Import có lỗi:', self::$errors);
//                 }
//             },
//         ];
//     }
// }

class BatchImport implements toCollection, WithHeadingRow, SkipsOnFailure, WithEvents
{
    use SkipsFailures;

    public static $errors = [];

    // private $row = 1;

    public function collection(Collection $rows)
    {

        try {

            $errors = $this->validateRow($rows);
            // dd($errors);
            if (empty($errors)) {
                $this->saveBatch($rows);
            } else {
                self::$errors = array_merge(self::$errors, $errors);
                // dd(self::$errors);
            }

            // $farmName = $row['nguyen_lieu_nong_truong'] ?? null;
            // $vehicleNumber = $row['so_xe_van_chuyen'] ?? null;
            // $receivedDate = $row['ngay_tiep_nhan'] ?? null;
            // $trip = $row['so_chuyen'] ?? null;

            // if (empty($farmName)) {
            //     self::$errors['empty_farm_name'][] = $this->row;
            // }

            // $farm = Farm::where('farm_name', $farmName)->first();

            // if (empty($farm)) {
            //     self::$errors['invaild_farm'][] = $this->row;
            // }


            // if (empty($vehicleNumber)) {
            //     self::$errors['empty_vehicle_number'][] = $this->row;

            // }

            // $vehicle = Vehicle::where('vehicle_number', $vehicleNumber)->first();

            // if (empty($vehicle)) {
            //     self::$errors['invaild_vehicle'][] = $this->row;

            // }




            // return $batch;

        } catch (\Exception $e) {
            self::$errors[] = [
                'Error' => $e->getMessage(),
            ];
        }
    }


    private function validateRow($rows)
    {
        // dd($rows);
        $errors = [];
        $rowNumber = 1;
        $today = Carbon::today()->format('d-m-Y');
        $existingBatches = []; // Mảng lưu trữ các mã lô đã kiểm tra
        $existingTusIds = []; // Lưu trữ tus_id để kiểm tra trong DB
        $allUnits = Units::where('status', "Hoạt động")->get()->keyBy('unit_name');
        $allFarm = Farm::where('status', "Hoạt động")->get()->keyBy('farm_name');
        $user = Auth::user();
        $validLohangWeights = ['2.52', '2.4'];
        $validBanhWeights = ['35', '33.33'];
        foreach ($rows as $row) {
            $rowNumber++;
            $malo = $row['ma_lo'] ?? null;
            $nsx = $row['ngay_san_xuat'] ?? null;
            $klBanh = $row['kl_banh_kg'] ?? null;
            $klLohang = $row['kl_lo_hang_tan'] ?? null;
            // $farmName = $row['nong_truong'] ?? null;
            $farmName = $user->farm->farm_name ?? ($row['nong_truong'] ?? null);
            $vehicleNumber = $row['so_xe_van_chuyen'] ?? null;
            $soChuyen = $row['so_chuyen'] ?? null;
            $receivedDate = $row['ngay_tiep_nhan'] ?? null;
            // $cty = $row['cong_ty'] ?? null;
            $cty = $user->farm->unitRelation->unit_name ?? ($row['cong_ty'] ?? null);

            $isValidDateNSX = false;
            $isFutureDateNSX = false;
            $isValidFarm = false;
            $isValidVehicle = false;
            $isValidDateNgayTiepNhan = false;
            $isValidSoChuyen = false;

            // $userFarmName = $user->farm->farm_name ?? null;
            // $userUnitName = $user->farm->unitRelation->unit_name ?? null;

            $farm = $allFarm[$farmName] ?? null;
            // $trip = $row['so_chuyen'] ?? null;
            if (!$malo) {
                $errors['empty_ma_lo'][] = $rowNumber;
            } else {
                $batch = Batch::where('batch_code', $row["ma_lo"])->first();
                if ($batch) {
                    if (isset($existingBatches[$malo])) {
                        $prev = $existingBatches[$malo];

                        // Nếu có khác biệt về ngày sản xuất, KL Bánh hoặc KL Lô Hàng
                        if (
                            $prev['ngay_san_xuat'] !== $nsx ||
                            $prev['kl_banh_kg'] != $klBanh ||
                            $prev['kl_lo_hang_tan'] != $klLohang
                        ) {
                            $errors['duplicate_malo_conflict'][] = $rowNumber;
                        }
                    } else {
                        // Nếu chưa có mã lô này, lưu lại thông tin để so sánh các dòng sau
                        $existingBatches[$malo] = [
                            'ngay_san_xuat' => $nsx,
                            'kl_banh_kg' => $klBanh,
                            'kl_lo_hang_tan' => $klLohang
                        ];
                    }
                } else {
                    $errors['not_exits_malo'][] = $rowNumber;
                }

            }

            // if (!$klBanh) {
            //     $errors['empty_kl_banh'][] = $rowNumber;
            // } else {
            //     if (!is_numeric($klBanh) || $klBanh < 0 || is_string($klBanh)) {
            //         $errors['invalid_kl_banh'][] = $rowNumber;
            //     }
            // }

            // if (!$klLohang) {
            //     $errors['empty_kl_lohang'][] = $rowNumber;
            // } else {
            //     if (!is_numeric($klLohang) || $klLohang < 0 || is_string($klLohang)) {
            //         $errors['invalid_kl_lohang'][] = $rowNumber;
            //     }
            // }

            if (!$klBanh) {
                $errors['empty_kl_banh'][] = $rowNumber;
            } else {
                if (!is_numeric($klBanh) || $klBanh < 0 || !in_array((string) $klBanh, $validBanhWeights, true)) {
                    $errors['invalid_kl_banh'][] = $rowNumber;
                }
            }

            if (!$klLohang) {
                $errors['empty_kl_lohang'][] = $rowNumber;
            } else {
                if (!is_numeric($klLohang) || $klLohang < 0 || !in_array((string) $klLohang, $validLohangWeights, true)) {
                    $errors['invalid_kl_lohang'][] = $rowNumber;
                }
            }


            if (!$soChuyen) {
                $errors['empty_so_chuyen'][] = $rowNumber;
            } else {
                $isValidSoChuyen = is_numeric($soChuyen) && $soChuyen >= 0 && floor($soChuyen) == $soChuyen;
                if (!$isValidSoChuyen) {
                    $errors['invalid_so_chuyen'][] = $rowNumber;
                }
            }

            if (!$vehicleNumber) {
                $errors['empty_vehicle_number'][] = $rowNumber;
            } else {
                // $vehicle = Vehicle::where('vehicle_number', $vehicleNumber)->first();
                $vehicle = Vehicle::where('vehicle_number', $vehicleNumber)->first();
                if (!$vehicle) {
                    $errors['invaild_vehicle'][] = $rowNumber;
                } else {
                    $isValidVehicle = true;
                }
            }

            if ($receivedDate) {
                $isValidDateNgayTiepNhan = $this->isValidDate($receivedDate);
                if (!$isValidDateNgayTiepNhan) {
                    $errors['not_valid_date_ngaytiepnhan'][] = $rowNumber;
                }
            } else {
                $errors['empty_ngaytiepnhan'][] = $rowNumber;
            }

            if ($nsx) {
                $isValidDateNSX = $this->isValidDate($nsx);
                if (!$isValidDateNSX) {
                    $errors['not_valid_date_nsx'][] = $rowNumber;
                }
            } else {
                $errors['empty_nsx'][] = $rowNumber;
            }

            if ($isValidDateNSX) {
                $isFutureDateNSX = $this->isFutureDate($nsx, $today);
                if ($isFutureDateNSX) {
                    $errors['not_valid_future_date_nsx'][] = $rowNumber;
                }
            }

            if (!$cty) {
                $errors['empty_cty'][] = $rowNumber;
            } else {
                $units = $allUnits[$cty] ?? null;
                if (!$units) {
                    $errors['units_invalid'][] = $rowNumber;
                    continue;
                }
            }

            if (str_starts_with($farmName, 'NT')) {
                $farmName = str_replace('NT', 'NONG TRUONG', $farmName);
            }

            if (!$farmName) {
                $errors['empty_farm_name'][] = $rowNumber;
            } else {
                // $isValidFarm = Farm::where('farm_name', $farmName)->first();

                // if (!$isValidFarm) {
                //     $errors['invaild_farm'][] = $rowNumber;
                // }


                $farm = Farm::where('farm_name', $farmName)
                    ->where('unit_id', $units->id)
                    ->first();

                if (!$farm) {
                    $errors['invaild_farm'][] = $rowNumber;
                } else {
                    $isValidFarm = true;
                }
            }

            // if ($userFarmName && $userUnitName) {
            //     if ($farmName !== $userFarmName || $userUnitName !== $cty) {
            //         $errors['farm_and_unit_not_allowed'][] = $rowNumber;
            //         continue;
            //     }
            // }

            if ($isValidFarm && $isValidVehicle && $isValidDateNgayTiepNhan && $isValidSoChuyen) {

                $ingredient = Ingredient::where('farm_id', $farm->id)
                    ->where('vehicle_number_id', $vehicle->id)
                    ->where('received_date', $this->formatDate($receivedDate))
                    ->where('trip', $soChuyen)
                    ->first();
                // dd($ingredient);
                if (!$ingredient) {
                    $errors['not_valid_ingredient'][] = $rowNumber;
                } else {
                    $batchIngredientExists = BatchIngredients::where('ingredient_id', $ingredient->id)->exists();
                    if (!$batchIngredientExists) {
                        if (!isset($existingTusIds[$malo])) {
                            $existingTusIds[$malo] = [
                                'unit_name' => $ingredient->farm->unitRelation->unit_name,
                                'farm_name' => $ingredient->farm->farm_name,
                                'vehicle_number' => $ingredient->vehicle->vehicle_number,
                                'received_date' => $ingredient->received_date,
                                'trip' => $ingredient->trip,
                                'row_first' => $rowNumber
                            ];
                        } else {
                            $prevTus = $existingTusIds[$malo];
                            if (
                                $prevTus['unit_name'] == $ingredient->farm->unitRelation->unit_name &&
                                $prevTus['farm_name'] == $ingredient->farm->farm_name &&
                                $prevTus['vehicle_number'] == $ingredient->vehicle->vehicle_number &&
                                $prevTus['received_date'] == $ingredient->received_date &&
                                $prevTus['trip'] == $ingredient->trip

                            ) {
                                if (!isset($errors['different_tus_id'])) {
                                    $errors['different_tus_id'] = [];
                                }
                                if (!in_array($prevTus['row_first'], $errors['different_tus_id'])) {
                                    $errors['different_tus_id'][] = $prevTus['row_first'];
                                }
                                $errors['different_tus_id'][] = $rowNumber;
                            }
                        }
                    } else {
                        $errors['batch_ingredient_exists'][] = $rowNumber;
                    }

                }
            }
            // dd($receivedDate);

        }

        // dd($errors);
        return $errors;
    }

    private function saveBatch($rows)
    {
        // foreach ($rows as $row) {
        //     $farmName = $row['nong_truong'] ?? null;
        //     $vehicleNumber = $row['xe_van_chuyen'] ?? null;
        //     $receivedDate = $row['ngay_tiep_nhan'] ?? null;
        //     $trip = $row['so_chuyen'] ?? null;
        //     $ingredient = Ingredient::whereHas('farm', function ($query) use ($farmName) {
        //         $query->where('farm_name', $farmName);
        //     })
        //         ->whereHas('vehicle', function ($query) use ($vehicleNumber) {
        //             $query->where('vehicle_number', $vehicleNumber);
        //         })
        //         ->where('received_date', $receivedDate)
        //         ->where('trip', $trip)
        //         ->first();


        //     $batch = Batch::where('batch_code', $row["ma_lo"])->first();

        //     if ($batch) {
        //         $batch->update([
        //             "date_sx" => $row["ngay_san_xuat"],
        //             "batch_weight" => $row["kl_lo_hang"],
        //             "banh_weight" => $row["kl_banh"],
        //         ]);
        //         if ($ingredient) {
        //             $batch->ingredients()->syncWithoutDetaching([$ingredient->id]);
        //         }
        //     }

        //     $batch = Batch::create([
        //         "batch_code" => $row["ma_lo"],
        //         "date_sx" => $row["ngay_san_xuat"],
        //         "batch_weight" => $row["kl_lo_hang"],
        //         "banh_weight" => $row["kl_banh"],
        //     ]);

        //     if ($ingredient) {
        //         $batch->ingredients()->syncWithoutDetaching([$ingredient->id]);
        //     }
        // }

        foreach ($rows as $row) {
            $farmName = $row['nong_truong'] ?? null;
            $vehicleNumber = $row['so_xe_van_chuyen'] ?? null;
            $receivedDate = $this->formatDate($row['ngay_tiep_nhan']);
            $trip = $row['so_chuyen'] ?? null;
            $unitName = $row['cong_ty'] ?? null;
            if (str_starts_with($farmName, 'NT')) {
                $farmName = str_replace('NT', 'NONG TRUONG', $farmName);
            }
            // Tìm nguyên liệu liên quan
            $ingredient = Ingredient::whereHas('farm.unitRelation', function ($query) use ($unitName) {
                $query->where('unit_name', $unitName);
            })->whereHas('farm', function ($query) use ($farmName) {
                $query->where('farm_name', $farmName);
            })
                ->whereHas('vehicle', function ($query) use ($vehicleNumber) {
                    $query->where('vehicle_number', $vehicleNumber);
                })
                ->where('received_date', $receivedDate)
                ->where('trip', $trip)
                ->first();
            // dd($ingredient);

            // Tìm hoặc tạo mới batch
            $batch = Batch::where('batch_code', $row["ma_lo"])->first();
            // ->where("date_sx", $this->formatDate($row["ngay_san_xuat"]))
            // ->where("batch_weight", $row["kl_lo_hang"]) // ✅ Sửa lỗi: bỏ formatDate
            // ->where("banh_weight", $row["kl_banh"]) // ✅ Sửa lỗi: bỏ formatDate
            // ->first();
            $batch->update([
                // "batch_code" => $row["ma_lo"],
                "date_sx" => $this->formatDate($row["ngay_san_xuat"]),
                "batch_weight" => $row["kl_lo_hang_tan"],
                "banh_weight" => $row["kl_banh_kg"],
            ]);

            if ($ingredient) {
                $batch->ingredients()->syncWithoutDetaching([$ingredient->id]);
            }
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
            dd($e);
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