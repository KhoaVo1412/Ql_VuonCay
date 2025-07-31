<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Imports\ImportSvr;
use Complex\Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportSvrController extends Controller
{
    public function importSvr(Request $request)
    {
        // $request->validate([
        //     'excel_svr' => 'required|mimes:xlsx,csv,xls'
        // ]);

        try {
            $import = new ImportSvr();
            Excel::import(new ImportSvr, $request->file('excel_svr'));
            if (!empty($import::$errors)) {
                $errors = $import::$errors;
                // dd($errors);
                // $errorMessages = [];
                $unknownErrors = array_column($errors, 'Error');
                if (!empty($unknownErrors)) {
                    $errorMessages[] = implode(", ", $unknownErrors);
                } else {
                    $malohangError = $errors['empty_malohang'] ?? [];

                    if (!empty($malohangError)) {
                        $errorMessages[] = "Cột Mã lô hàng không được để trống tại các dòng: " . implode(', ', $malohangError);
                    }

                    $duplicateMaLoError = $errors['duplicate_ma_lo'] ?? [];
                    if (!empty($duplicateMaLoError)) {
                        $errorMessages[] = "Cột Mã lô hàng không được trùng tại các dòng: " . implode(', ', $duplicateMaLoError);
                    }

                    $batchcodeError = $errors['batchcode_invalid'] ?? [];
                    if (!empty($batchcodeError)) {
                        $errorMessages[] = "Cột Mã lô hàng không hợp lệ tại các dòng: " . implode(', ', $batchcodeError);
                    }

                    $batchIngredientsError = $errors['batch_ingredients_invalid'] ?? [];
                    if (!empty($batchIngredientsError)) {
                        $errorMessages[] = "Mã lô hàng chưa kết nối thông tin nguyên liệu tại các dòng: " . implode(', ', $batchIngredientsError);
                    }

                    $xephangError = $errors['empty_rank'] ?? [];
                    if (!empty($xephangError)) {
                        $errorMessages[] = "Cột Xếp hạng không được để trống tại các dòng: " . implode(', ', $xephangError);
                    }

                    $invaildrankError = $errors['invalid_list_rank'] ?? [];
                    if (!empty($invaildrankError)) {
                        $errorMessages[] = "Cột Xếp hạng không hợp lệ tại các dòng: " . implode(', ', $invaildrankError);
                    }

                    $missingRequiredFieldError = $errors['missing_required_field'] ?? [];
                    if (!empty($missingRequiredFieldError)) {
                        foreach ($missingRequiredFieldError as $error) {
                            $row = $error['rowNumber'];
                            $rank = $error['rank'];
                            $fields = implode(', ', $error['fields']);

                            $errorMessages[] = "Xếp Hạng $rank bắt buộc nhập $fields tại các dòng: $row";
                        }
                    }

                    $emptyNGM = $errors['empty_ngayguimau'] ?? [];
                    if (!empty($emptyNGM)) {
                        $errorMessages[] = "Cột Ngày gửi mẫu không được để trống tại các dòng: " . implode(', ', $emptyNGM);
                    }

                    $emptyNKN = $errors['empty_ngaykiemnghiem'] ?? [];
                    if (!empty($emptyNKN)) {
                        $errorMessages[] = "Cột Ngày kiểm nghiệm không được để trống tại các dòng: " . implode(', ', $emptyNKN);
                    }

                    $numberError = $errors['invalid_number'] ?? [];
                    if (!empty($numberError)) {
                        $errorMessages[] = "Cột Chất bẩn không hợp lệ tại các dòng: " . implode(', ', $numberError);
                    }

                    $numberashError = $errors['invalid_number_ash'] ?? [];
                    if (!empty($numberashError)) {
                        $errorMessages[] = "Cột Tro không hợp lệ tại các dòng: " . implode(', ', $numberashError);
                    }

                    $numbervolatileError = $errors['invalid_number_volatile'] ?? [];
                    if (!empty($numbervolatileError)) {
                        $errorMessages[] = "Cột Bay hơi không hợp lệ tại các dòng: " . implode(', ', $numbervolatileError);
                    }

                    $numbernitoError = $errors['invalid_number_nitrogen'] ?? [];
                    if (!empty($numbernitoError)) {
                        $errorMessages[] = "Cột Nitơ không hợp lệ tại các dòng: " . implode(', ', $numbernitoError);
                    }

                    $numberpoError = $errors['invalid_number_po'] ?? [];
                    if (!empty($numberpoError)) {
                        $errorMessages[] = "Cột Po không hợp lệ tại các dòng: " . implode(', ', $numberpoError);
                    }

                    $numberpriError = $errors['invalid_number_pri'] ?? [];
                    if (!empty($numberpriError)) {
                        $errorMessages[] = "Cột Pri không hợp lệ tại các dòng: " . implode(', ', $numberpriError);
                    }

                    // $numbercolorError = $errors['invalid_number_color'] ?? [];
                    // if (!empty($numbercolorError)) {
                    //     $errorMessages[] = "Cột Color không hợp lệ tại các dòng: " . implode(', ', $numbercolorError);
                    // }
                    $listcolorError = $errors['invalid_list_color'] ?? [];
                    if (!empty($listcolorError)) {
                        $errorMessages[] = "Cột Màu không hợp lệ tại các dòng: " . implode(', ', $listcolorError);
                    }

                    $numbervrError = $errors['invalid_number_vr'] ?? [];
                    if (!empty($numbervrError)) {
                        $errorMessages[] = "Cột Lovibond không hợp lệ tại các dòng: " . implode(', ', $numbervrError);
                    }

                    // $numberluuhoaError = $errors['invalid_number_luuhoa'] ?? [];
                    // if (!empty($numberluuhoaError)) {
                    //     $errorMessages[] = "Cột Lưu hóa không hợp lệ tại các dòng: " . implode(', ', $numberluuhoaError);
                    // }

                    $numberdonhotError = $errors['invalid_number_donhot'] ?? [];
                    if (!empty($numberdonhotError)) {
                        $errorMessages[] = "Cột Độ nhớt không hợp lệ tại các dòng: " . implode(', ', $numberdonhotError);
                    }

                    // $numberdorongError = $errors['invalid_number_dorong'] ?? [];
                    // if (!empty($numberdorongError)) {
                    //     $errorMessages[] = "Cột Độ rộng không hợp lệ tại các dòng: " . implode(', ', $numberdonhotError);
                    // }

                    $invaildngayguimau = $errors['not_valid_date_ngayguimau'] ?? [];
                    if (!empty($invaildngayguimau)) {
                        $errorMessages[] = "Cột Ngày gửi mẫu không hợp lệ tại các dòng: " . implode(', ', $invaildngayguimau);
                    }

                    $invaildngaykimenghiem = $errors['not_valid_date_ngaykiemnghiem'] ?? [];
                    if (!empty($invaildngaykimenghiem)) {
                        $errorMessages[] = "Cột Ngày kiểm ngiệm không hợp lệ tại các dòng: " . implode(', ', $invaildngaykimenghiem);
                    }

                    // $formatngayguimau = $errors['not_valid_format_date_ngayguimau'] ?? [];
                    // if (!empty($formatngayguimau)) {
                    //     $errorMessages[] = "Cột Ngày gửi mẫu không đúng dạng d-m-Y tại các dòng: " . implode(', ', $formatngayguimau);
                    // }

                    // $formatngaykiemnghiem = $errors['not_valid_format_date_ngaykiemnghiem'] ?? [];
                    // if (!empty($formatngaykiemnghiem)) {
                    //     $errorMessages[] = "Cột Ngày kiểm nghiệm không đúng dạng d-m-Y tại các dòng: " . implode(', ', $formatngaykiemnghiem);
                    // }

                    $futurengayguimau = $errors['not_valid_future_date_ngayguimau'] ?? [];
                    if (!empty($futurengayguimau)) {
                        $errorMessages[] = "Cột Ngày gửi mẫu không được lớn hơn ngày hiện tại tại các dòng: " . implode(', ', $futurengayguimau);
                    }

                    $futurengayguimau = $errors['not_valid_future_date_ngaykiemnghiem'] ?? [];
                    if (!empty($futurengayguimau)) {
                        $errorMessages[] = "Cột Ngày kiểm nghiệm không được lớn hơn ngày hiện tại tại các dòng: " . implode(', ', $futurengayguimau);
                    }

                    $greater = $errors['ngayguimau_greater_ngaykiemngiem'] ?? [];
                    if (!empty($greater)) {
                        $errorMessages[] = "Ngày gửi mẫu không được lớn hơn ngày kiểm nghiệm tại các dòng: " . implode(', ', $greater);
                    }
                    // dd($greater);
                }

                return redirect()->back()->with('error', 'File excel không hợp lệ:\n' . implode('\n', $errorMessages));
            }
            return back()->with('message', 'Nhập dữ liệu thành công!');
        } catch (Exception $e) {
            return back()->with('error', 'Lỗi khi nhập dữ liệu: ' . $e->getMessage());
        }
    }
}