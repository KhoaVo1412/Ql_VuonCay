<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use Illuminate\Http\Request;
use App\Imports\UpdatePlaningAreaImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class UpdatePlantingAreaImportController extends Controller
{

    public function importExcel(Request $request)
    {
        // $request->validate(
        //     [
        //         "excel_file" => "required|mimes:xlsx",
        //     ],
        //     [
        //         "excel_file.required" => "Vui lòng chọn một file để nhập.",
        //         "excel_file.mimes" => "File không đúng định dạng. Vui lòng chọn file có đuôi .xlsx",
        //     ]
        // );

        try {
            $import = new UpdatePlaningAreaImport();

            Excel::import($import, $request->file("excel_file"));

            if (!empty($import::$errors)) {
                $errors = $import::$errors;
                // dd($errors);
                $errorMessages = [];
                // foreach ($errors as $error) {
                $unknownErrors = array_column($errors, 'Error');
                // dd($errors);
                if (!empty($unknownErrors)) {
                    $errorMessages[] = implode(", ", $unknownErrors);
                } else {
                    $emptyCty = $errors['empty_cty'] ?? [];
                    if (!empty($emptyCty)) {
                        $errorMessages[] = "Cột Công ty không được để trống tại các dòng: " . implode(', ', $emptyCty);
                    }
                    $unitsInvalid = $errors['units_invalid'] ?? [];
                    if (!empty($unitsInvalid)) {
                        $errorMessages[] = "Cột Công ty không hợp lệ tại các dòng: " . implode(', ', $unitsInvalid);
                    }
                    $plantationError = $errors['plantation_invalid'] ?? [];
                    if (!empty($plantationError)) {
                        $errorMessages[] = "Cột Plantation không hợp lệ tại các dòng: " . implode(', ', $plantationError);
                    }
                    // dd($$plantationError);
                    $emptyplareaError = $errors['empty_plantation'] ?? [];

                    if (!empty($emptyplareaError)) {
                        $errorMessages[] = "Cột Plantation không được để trống tại các dòng: " . implode(', ', $emptyplareaError);
                    }
                    $findError = $errors['find'] ?? [];

                    if (!empty($findError)) {
                        $errorMessages[] = "Cột Find không được để trống tại các dòng: " . implode(', ', $findError);
                    }
                    $geojsonError = $errors['geojson'] ?? [];
                    // dd($geojsonError);
                    if (!empty($geojsonError)) {
                        $errorMessages[] = "Cột GeoJSON không được để trống tại các dòng: " . implode(', ', $geojsonError);
                    }

                    // $findplanError = $errors['duplicate_find_farm'] ?? [];

                    // if (!empty($findplanError)) {
                    //     $errorMessages[] = "Cột Find và Plantation đã tồn tại trước đó tại các dòng: " . implode(', ', $findplanError);
                    // }

                    $invaildpyError = $errors['invalid_planting_year'] ?? [];
                    if (!empty($invaildpyError)) {
                        $errorMessages[] = "Cột Planting_y không được lớn hơn năm hiện tại tại các dòng: " . implode(', ', $invaildpyError);
                    }

                    $emptypyError = $errors['empty_planting_year'] ?? [];
                    // dd($emptypyError);
                    if (!empty($emptypyError)) {
                        $errorMessages[] = "Cột Planting_y không được để trống tại các dòng: " . implode(', ', $emptypyError);
                    }

                    $invaildpyError = $errors['planting_year'] ?? [];
                    if (!empty($invaildpyError)) {
                        $errorMessages[] = "Cột Planting_y không hợp lệ tại các dòng: " . implode(', ', $invaildpyError);
                    }

                    $exitFindPlant = $errors['exit_find_plant'] ?? [];
                    if (!empty($exitFindPlant)) {
                        $errorMessages[] = "Cột Find và Plantation bị trùng tại các dòng " . implode(', ', $exitFindPlant);
                    }
                    // $invailuserError = $errors['farm_and_unit_not_allowed'] ?? [];
                    // if (!empty($invailuserError)) {
                    //     $errorMessages[] = "Đơn Vị và Nông Trường không hợp lệ tại các dòng: " . implode(', ', $invailuserError);
                    // }

                    $notFoundPlantingAreaError = $errors['not_found_plating_area'] ?? [];
                    if (!empty($notFoundPlantingAreaError)) {
                        $errorMessages[] = "Không tìm thấy Khu Vực Trồng tại các dòng: " . implode(', ', $notFoundPlantingAreaError);
                    }

                }

                // dd(implode("<br>", $errorMessages));

                return redirect()->route('edit-excel')->with('error', 'File excel không hợp lệ:\n' . implode('\n', $errorMessages));
            }

            return redirect()->route('plantingareas.index')->with("message", "Tạo khu vực thành công!");

        } catch (\Exception $e) {
            return redirect()->route('plantingareas.index')->with('error', 'Lỗi khi nhập dữ liệu: ' . $e->getMessage());
        }
    }
    public function edit_excel()
    {
        $farms = implode(', ', Farm::pluck('farm_name')->toArray());
        // dd($farms);
        return view('planting_areas.edit_excel', compact('farms'));
    }
}