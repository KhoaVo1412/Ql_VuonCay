<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Imports\BatchImport;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;



// class ImportBatchIngControllor extends Controller
// {
//     public function index()
//     {
//         return view('batch.import_batchIng');
//     }

//     public function importExcel(Request $request)
//     {
//         $request->validate([
//             "excel_file" => "required|mimes:xlsx",
//         ]);

//         try {
//             $import = new BatchImport();

//             Excel::import($import, $request->file("excel_file"));

//             if (!empty($import::$errors)) {

//                 // dd($import::$errors);
//                 return redirect()->back()->with([
//                     'warning' => 'Import hoàn tất nhưng có lỗi!',
//                     'errors' => $import::$errors
//                 ]);
//             }
//             return redirect()->back()->with("message", "Dữ liệu đã được nhập thành công!");
//         } catch (\Exception $e) {
//             return redirect()->back()->with('error', 'Lỗi khi nhập dữ liệu: ' . $e->getMessage());
//         }
//     }
// }

class ImportBatchIngControllor extends Controller
{
    public function index()
    {
        return view('batch.import_batchIng');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            "excel_file" => "required|mimes:xlsx",
        ]);

        try {
            $import = new BatchImport();

            Excel::import($import, $request->file("excel_file"));

            if (!empty($import::$errors)) {
                $errors = $import::$errors;
                // dd($errors);
                $errorMessages = [];
                $unknownErrors = array_column($errors, 'Error');
                if (!empty($unknownErrors)) {
                    $errorMessages[] = implode(", ", $unknownErrors);
                } else {
                    $emptyMaLo = $errors['empty_ma_lo'] ?? [];
                    if (!empty($emptyMaLo)) {
                        $errorMessages[] = "Mã lô không được để trống tại các dòng: " . implode(', ', $emptyMaLo);
                    }
                    $notExitsMaLo = $errors['not_exits_malo'] ?? [];
                    if (!empty($notExitsMaLo)) {
                        $errorMessages[] = "Mã lô không tồn tại tại các dòng: " . implode(', ', $notExitsMaLo);
                    }
                    $emptyNSX = $errors['empty_nsx'] ?? [];
                    if (!empty($emptyNSX)) {
                        $errorMessages[] = "Ngày sản xuất không được trống tại các dòng: " . implode(', ', $emptyNSX);
                    }

                    $notValidNSX = $errors['not_valid_date_nsx'] ?? [];
                    if (!empty($notValidNSX)) {
                        $errorMessages[] = "Ngày sản xuất không hợp lệ tại các dòng: " . implode(', ', $notValidNSX);
                    }

                    $notValidFutureNsx = $errors['not_valid_future_date_nsx'] ?? [];
                    if (!empty($notValidFutureNsx)) {
                        $errorMessages[] = "Ngày sản xuất không được lớn hơn ngày hiện tại tại các dòng: " . implode(', ', $notValidFutureNsx);
                    }

                    $emptyKlLoHang = $errors['empty_kl_lohang'] ?? [];
                    if (!empty($emptyKlLoHang)) {
                        $errorMessages[] = "KL Lô hàng không được để trống tại các dòng: " . implode(', ', $emptyKlLoHang);
                    }
                    $invalidKlLoHang = $errors['invalid_kl_lohang'] ?? [];
                    if (!empty($invalidKlLoHang)) {
                        $errorMessages[] = "KL Lô hàng không hợp lệ tại các dòng: " . implode(', ', $invalidKlLoHang);
                    }
                    $emptyKlBanh = $errors['empty_kl_banh'] ?? [];
                    if (!empty($emptyKlBanh)) {
                        $errorMessages[] = "KL Bành không được để trống tại các dòng: " . implode(', ', $emptyKlBanh);
                    }
                    $invalidKlBanh = $errors['invalid_kl_banh'] ?? [];
                    if (!empty($invalidKlBanh)) {
                        $errorMessages[] = "KL Bành không hợp lệ tại các dòng: " . implode(', ', $invalidKlBanh);
                    }
                    $emptyNgayTiepNhan = $errors['empty_ngaytiepnhan'] ?? [];
                    if (!empty($emptyNgayTiepNhan)) {
                        $errorMessages[] = "Ngày tiếp nhận không được trống tại các dòng: " . implode(', ', $emptyNgayTiepNhan);
                    }

                    $notValidDateNgayTiepNhan = $errors['not_valid_date_ngaytiepnhan'] ?? [];
                    if (!empty($notValidDateNgayTiepNhan)) {
                        $errorMessages[] = "Ngày tiếp nhận không hợp lệ tại các dòng: " . implode(', ', $notValidDateNgayTiepNhan);
                    }
                    $emptyCty = $errors['empty_cty'] ?? [];
                    if (!empty($emptyCty)) {
                        $errorMessages[] = "Công ty không được để trống tại các dòng: " . implode(', ', $emptyCty);
                    }
                    $unitsInvalid = $errors['units_invalid'] ?? [];
                    if (!empty($unitsInvalid)) {
                        $errorMessages[] = "Công ty không hợp lệ tại các dòng: " . implode(', ', $unitsInvalid);
                    }
                    $emptyfarmname = $errors['empty_farm_name'] ?? [];
                    if (!empty($emptyfarmname)) {
                        $errorMessages[] = "Nông Trường không được để trống tại các dòng: " . implode(', ', $emptyfarmname);
                    }
                    $farm = $errors['invaild_farm'] ?? [];
                    if (!empty($farm)) {
                        $errorMessages[] = "Nông Trường không hợp lệ tại các dòng: " . implode(', ', $farm);

                    }
                    $emptyvehicleNumber = $errors['empty_vehicle_number'] ?? [];
                    if (!empty($emptyvehicleNumber)) {
                        $errorMessages[] = "Số xe vận chuyển không được để trống tại các dòng: " . implode(', ', $emptyvehicleNumber);
                    }
                    $vehicle = $errors['invaild_vehicle'] ?? [];
                    if (!empty($vehicle)) {
                        $errorMessages[] = "Số xe vận chuyển không hợp lệ tại các dòng: " . implode(', ', $vehicle);
                    }
                    $emptySoChuyen = $errors['empty_so_chuyen'] ?? [];
                    if (!empty($emptySoChuyen)) {
                        $errorMessages[] = "Số chuyến không được để trống tại các dòng: " . implode(', ', $emptySoChuyen);
                    }
                    $invalidSoChuyen = $errors['invalid_so_chuyen'] ?? [];
                    if (!empty($invalidSoChuyen)) {
                        $errorMessages[] = "Số chuyến không hợp lệ tại các dòng: " . implode(', ', $invalidSoChuyen);
                    }

                    $invalidIngredient = $errors['not_valid_ingredient'] ?? [];
                    if (!empty($invalidIngredient)) {
                        $errorMessages[] = "Thông tin nguyên liệu không tồn tại tại các dòng: " . implode(', ', $invalidIngredient);
                    }
                    $batchIngredientExists = $errors['batch_ingredient_exists'] ?? [];
                    if (!empty($batchIngredientExists)) {
                        $errorMessages[] = "Thông tin nguyên liệu đã được liên kết tại các dòng: " . implode(', ', $batchIngredientExists);
                    }
                    $duplicateMaloConflict = $errors['duplicate_malo_conflict'] ?? [];
                    if (!empty($duplicateMaloConflict)) {
                        $errorMessages[] = "Lô hàng không hợp lệ tại các dòng: " . implode(', ', $duplicateMaloConflict);
                    }
                    $differentTusId = $errors['different_tus_id'] ?? [];
                    if (!empty($differentTusId)) {
                        $errorMessages[] = "Thông tin nguyên liệu không được trùng số chuyến tại các dòng: " . implode(', ', $differentTusId);
                    }
                    // $invailuserError = $errors['farm_and_unit_not_allowed'] ?? [];
                    // if (!empty($invailuserError)) {
                    //     $errorMessages[] = "Đơn Vị và Nông Trường không hợp lệ tại các dòng: " . implode(', ', $invailuserError);
                    // }
                    // dd($errorMessages);
                }
                // dd($import::$errors);
                // return redirect()->back()->with([
                //     'warning' => 'Import hoàn tất nhưng có lỗi!',
                //     'error' => $import::$errors
                // ]);
                return redirect()->back()->with('error', 'File excel không hợp lệ:\n' . implode('\n', $errorMessages));

            }

            return redirect()->route('batches.index')->with("message", "Dữ liệu đã được nhập thành công!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi nhập dữ liệu: ' . $e->getMessage());
        }
    }
}