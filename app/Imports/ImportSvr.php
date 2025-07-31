<?php

namespace App\Imports;

use App\Models\Batch;
use App\Models\BatchIngredients;
use App\Models\TestingResult;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;


class ImportSvr implements ToCollection, WithHeadingRow, SkipsOnFailure, WithEvents
{
    use SkipsFailures;
    public static $errors = [];

    public function collection(Collection $rows)
    {
        // try {
        $errors = $this->validateRow($rows);
        // dd($errors);
        if (empty($errors)) {
            $this->saveBatch($rows);
        } else {
            self::$errors = array_merge(self::$errors, $errors);
            // dd(self::$errors);
        }

        // } catch (\Exception $e) {
        //     self::$errors[] = [
        //         'Error' => "Xảy ra lỗi không xác định!",
        //     ];
        // }
    }

    private function validateRow($rows)
    {
        $errors = [];
        $rowNumber = 1;
        $validColors = [
            'dacam' => 'Da cam',
            'xanhlacaynhat' => 'Xanh lá cây nhạt',
            'nau' => 'Nâu',
            'do' => 'Đỏ'
        ];

        // $vaildRank = [
        //     'CV60' => 'CV60',
        //     'CV50' => 'CV50',
        //     '3L' => '3L',
        //     '5' => '5',
        //     '10' => '10',
        //     '20' => '20'
        // ];

        $vaildRank = [
            'CV60' => [
                'label' => 'CV60',
                'required_fields' => ['tap_chat', 'tro', 'bay_hoi', 'nito', 'pri', 'do_nhot', 'luu_hoa', 'mau'],
            ],
            'CV50' => [
                'label' => 'CV50',
                'required_fields' => ['tap_chat', 'tro', 'bay_hoi', 'nito', 'pri', 'do_nhot', 'luu_hoa', 'mau'],
            ],
            // '3L' => [
            //     'label' => '3L',
            //     'required_fields' => ['chat_ban', 'tro', 'nito', 'bay_hoi', 'po', 'pri', 'lovibond', 'do_rong', 'luu_hoa', 'mau'], // ví dụ
            // ],
            '3L' => [
                'label' => '3L',
                'required_fields' => ['tap_chat', 'tro', 'bay_hoi', 'nito', 'po', 'pri', 'luu_hoa', 'mau', 'lovibond'], // ví dụ
            ],
            '5' => [
                'label' => '5',
                'required_fields' => ['tap_chat', 'tro', 'bay_hoi', 'nito', 'po', 'pri', 'mau'],
            ],
            '10' => [
                'label' => '10',
                'required_fields' => ['tap_chat', 'tro', 'bay_hoi', 'nito', 'po', 'pri', 'mau'],
            ],
            '20' => [
                'label' => '20',
                'required_fields' => ['tap_chat', 'tro', 'bay_hoi', 'nito', 'po', 'pri', 'mau'],
            ],
        ];

        foreach ($rows as $row) {
            // dd($row);
            $rowNumber++;
            $malohang = $row['ma_lo_hang'] ?? null;
            $ngayguimau = $row['ngay_gui_mau'] ?? null;
            $ngaykiemnghiem = $row['ngay_kiem_nghiem'] ?? null;
            $svr_impurity = $row["tap_chat"] ?? null;
            $svr_ash = $row["tro"] ?? null;
            $svr_volatile = $row["bay_hoi"] ?? null;
            $svr_nitrogen = $row["nito"] ?? null;
            $svr_po = $row["po"] ?? null;
            $svr_pri = $row["pri"] ?? null;
            // $svr_dorong = $row["do_rong"] ?? null;
            $svr_donhot = $row["do_nhot"] ?? null;
            // $svr_luuhoa = $row["luu_hoa"] ?? null;
            $svr_color = $row["mau"] ?? null;
            $svr_vr = $row["lovibond"] ?? null;
            $rank = $row["xep_hang"] ?? null;
            $isValidDateNgayGuiMau = false;
            $isValidDateNgaykiemnghiem = false;
            $isFutureDateNgayGuiMau = false;
            $isFutureDateNgayKiemNghiem = false;
            $today = Carbon::today()->format('d-m-Y');
            $mauNhap = mb_strtolower(str_replace(' ', '', trim($svr_color)));
            $xephangNhap = mb_strtolower(str_replace(' ', '', trim($rank)));
            $matchedRank = null;
            $missingFields = [];
            $maLoMap = [];

            if (!$malohang) {
                $errors['empty_malohang'][] = $rowNumber;
                continue;
            }

            if (isset($maLoMap[$malohang])) {
                $errors['duplicate_ma_lo'][] = $rowNumber;
                continue;
            } else {
                $maLoMap[$malohang] = $rowNumber;
            }

            $batch = Batch::where('batch_code', $malohang)->where('status', 0)->first();

            if (!$batch) {
                $errors['batchcode_invalid'][] = $rowNumber;
                continue;
            }

            $batchIngredients = BatchIngredients::where('batch_id', $batch->id)->first();

            if (!$batchIngredients) {
                $errors['batch_ingredients_invalid'][] = $rowNumber;
                continue;
            }

            if (!$ngayguimau) {
                $errors['empty_ngayguimau'][] = $rowNumber;
            }

            if (!$ngaykiemnghiem) {
                $errors['empty_ngaykiemnghiem'][] = $rowNumber;
            }


            if ($ngayguimau) {
                $isValidDateNgayGuiMau = $this->isValidDate($ngayguimau);
                if (!$isValidDateNgayGuiMau) {
                    $errors['not_valid_date_ngayguimau'][] = $rowNumber;
                }
            }

            if ($ngaykiemnghiem) {
                $isValidDateNgaykiemnghiem = $this->isValidDate($ngaykiemnghiem);
                if (!$isValidDateNgaykiemnghiem) {
                    $errors['not_valid_date_ngaykiemnghiem'][] = $rowNumber;
                }
            }


            if ($isValidDateNgayGuiMau) {
                $isFutureDateNgayGuiMau = $this->isFutureDate($ngayguimau, $today);
                if ($isFutureDateNgayGuiMau) {
                    $errors['not_valid_future_date_ngayguimau'][] = $rowNumber;
                }
                // dd($isValidDateNgayGuiMau);
            }

            if ($isValidDateNgaykiemnghiem) {
                $isFutureDateNgayKiemNghiem = $this->isFutureDate($ngaykiemnghiem, $today);
                if ($isFutureDateNgayKiemNghiem) {
                    $errors['not_valid_future_date_ngaykiemnghiem'][] = $rowNumber;
                }
            }


            if ($isValidDateNgaykiemnghiem && $isValidDateNgayGuiMau) {
                if ($this->isFutureDate($ngayguimau, $ngaykiemnghiem)) {
                    $errors['ngayguimau_greater_ngaykiemngiem'][] = $rowNumber;
                }
            }

            foreach ($vaildRank as $key => $value) {
                if (
                    mb_strtolower(str_replace(' ', '', $value['label'])) ===
                    mb_strtolower(str_replace(' ', '', $xephangNhap))
                ) {
                    $matchedRank = $key;
                    break;
                }
            }

            if (!$rank) {
                $errors['empty_rank'][] = $rowNumber;
                continue;
            }

            if (!$matchedRank) {
                $errors['invalid_list_rank'][] = $rowNumber;
                continue;
            }

            $requiredFields = $vaildRank[$matchedRank]['required_fields'];

            foreach ($requiredFields as $fieldName) {
                if (!isset($row[$fieldName]) || trim($row[$fieldName]) === '') {
                    $missingFields[] = $fieldName;
                }
            }

            if (!empty($missingFields)) {
                $errors['missing_required_field'][] = [
                    'rowNumber' => $rowNumber,
                    'rank' => $matchedRank,
                    'fields' => $missingFields, // danh sách các field bị thiếu
                ];
                continue;
            }

            $rank = $matchedRank;
            // So sánh với danh sách hợp lệ (không phân biệt chữ hoa/thường)
            $matchedColor = null;
            foreach ($validColors as $key => $value) {
                if (mb_strtolower(str_replace(' ', '', $value)) === $mauNhap) {
                    $matchedColor = $key;
                    break;
                }
            }

            if (!empty($svr_color)) {
                if (!$matchedColor) {
                    $errors['invalid_list_color'][] = $rowNumber;
                } else {
                    // Nếu hợp lệ, cập nhật lại giá trị để lưu vào DB
                    $svr_color = $matchedColor;
                }
            }

            // dd($ngayguimau);

            // if ($ngaykiemnghiem) {
            //     if (!$this->isValidDateFormat($ngaykiemnghiem)) {
            //         $errors['date_invalid'][] = $rowNumber;
            //     }
            //     dd($ngaykiemnghiem);
            // }

            // foreach (['invalid_number' => $svr_impurity, 'invalid_number_donhot' => $svr_donhot, 'invalid_number_dorong' => $svr_dorong, 'invalid_number_ash' => $svr_ash, 'invalid_number_volatile' => $svr_volatile, 'invalid_number_nitrogen' => $svr_nitrogen, 'invalid_number_po' => $svr_po, 'invalid_number_pri' => $svr_pri, 'invalid_number_vr' => $svr_vr,] as $key => $value) {
            //     if (!empty($value)) {
            //         if (!is_numeric($value)) {
            //             $errors[$key][] = $rowNumber;
            //         }
            //     }
            // }

            foreach (['invalid_number' => $svr_impurity, 'invalid_number_donhot' => $svr_donhot, 'invalid_number_ash' => $svr_ash, 'invalid_number_volatile' => $svr_volatile, 'invalid_number_nitrogen' => $svr_nitrogen, 'invalid_number_po' => $svr_po, 'invalid_number_pri' => $svr_pri, 'invalid_number_vr' => $svr_vr,] as $key => $value) {
                if (!empty($value)) {
                    if (!is_numeric($value)) {
                        $errors[$key][] = $rowNumber;
                    }
                }
            }

            // $row['mau'] = $svr_color;
            // $row["xep_hang"] = $rank;
        }

        // dd($errors);

        return $errors;
    }
    private function saveBatch($rows)
    {
        foreach ($rows as $row) {
            $batch = Batch::where('batch_code', $row['ma_lo_hang'])->first();
            // $testingResult = TestingResult::where('batch_id', $batch->id)->first();
            // if ($testingResult) {
            //     $testingResult->updateOrCreate([
            //         "ngay_gui_mau" => $this->formatDate($row["ngay_gui_mau"]),
            //         "ngay_kiem_nghiem" => $this->formatDate($row["ngay_kiem_nghiem"]),
            //         "rank" => $row["xep_hang"],
            //         "batch_id" => $batch->id,
            //         "svr_impurity" => $row["chat_ban"],
            //         "svr_ash" => $row["tro"],
            //         "svr_volatile" => $row["bay_hoi"],
            //         "svr_nitrogen" => $row["nito"],
            //         "svr_po" => $row["po"],
            //         "svr_pri" => $row["pri"],
            //         "svr_color" => $row["mau"],
            //         "svr_vr" => $row["vr"],
            //     ]);
            // } else {
            //     TestingResult::create([
            //         "ngay_gui_mau" => $this->formatDate($row["ngay_gui_mau"]),
            //         "ngay_kiem_nghiem" => $this->formatDate($row["ngay_kiem_nghiem"]),
            //         "rank" => $row["xep_hang"],
            //         "batch_id" => $batch->id,
            //         "svr_impurity" => $row["chat_ban"],
            //         "svr_ash" => $row["tro"],
            //         "svr_volatile" => $row["bay_hoi"],
            //         "svr_nitrogen" => $row["nito"],
            //         "svr_po" => $row["po"],
            //         "svr_pri" => $row["pri"],
            //         "svr_color" => $row["mau"],
            //         "svr_vr" => $row["vr"],
            //     ]);
            // }

            TestingResult::updateOrCreate(
                ['batch_id' => $batch->id],
                [
                    "ngay_gui_mau" => $this->formatDate($row["ngay_gui_mau"]),
                    "ngay_kiem_nghiem" => $this->formatDate($row["ngay_kiem_nghiem"]),
                    "rank" => $this->normalizeString($row["xep_hang"]),
                    "svr_impurity" => $row["tap_chat"],
                    "svr_ash" => $row["tro"],
                    "svr_volatile" => $row["bay_hoi"],
                    "svr_nitrogen" => $row["nito"],
                    "svr_po" => $row["po"],
                    "svr_pri" => $row["pri"],
                    // "svr_width" => $row["do_rong"],
                    "svr_viscous" => $row["do_nhot"],
                    "svr_vul" => $row["luu_hoa"],
                    "svr_color" => $this->normalizeString($row["mau"]),
                    "svr_vr" => $row["lovibond"],
                ]
            );

            $batch->update([
                'type' => 'svr',
                'status' => 1
            ]);
        }
    }
    // public function model(array $row)
    // {
    //     // $this->hasHeaders = true;
    //     $batch = Batch::where('batch_code', $row['ma_lo_hang'])->first();

    //     if (!$batch) {
    //         throw new Exception('Không tìm thấy Mã lô hàng (có thể bạn nhập mã đó không tồn tại hoặc mã đó để trống). Xin vui lòng nhập lại!');
    //     }

    //     $testingResult = TestingResult::updateOrCreate(
    //         ['batch_id' => $batch->id],
    //         [
    //             "ngay_gui_mau" => $this->formatDate($row["ngay_gui_mau"]) ?? null,
    //             "ngay_kiem_nghiem" => $this->formatDate($row["ngay_kiem_nghiem"]) ?? null,
    //             "rank" => $row["xep_hang"] ?? null,
    //             "svr_impurity" => $row["chat_ban"] ?? null,
    //             "svr_ash" => $row["tro"] ?? null,
    //             "svr_volatile" => $row["bay_hoi"] ?? null,
    //             "svr_nitrogen" => $row["nito"] ?? null,
    //             "svr_po" => $row["po"] ?? null,
    //             "svr_pri" => $row["pri"] ?? null,
    //             "svr_color" => $row["mau"] ?? null,
    //             "svr_vr" => $row["vr"] ?? null,
    //         ]
    //     );
    //     $batch->update([
    //         'type' => 'svr',
    //         'status' => 1
    //     ]);

    //     return $testingResult;
    // }


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

    private function normalizeString($str)
    {
        // Xoá dấu tiếng Việt
        $str = preg_replace([
            '/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/u',
            '/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/u',
            '/(ì|í|ị|ỉ|ĩ)/u',
            '/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/u',
            '/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/u',
            '/(ỳ|ý|ỵ|ỷ|ỹ)/u',
            '/(đ)/u',
            '/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/u',
            '/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/u',
            '/(Ì|Í|Ị|Ỉ|Ĩ)/u',
            '/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/u',
            '/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/u',
            '/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/u',
            '/(Đ)/u',
        ], [
            'a',
            'e',
            'i',
            'o',
            'u',
            'y',
            'd',
            'a',
            'e',
            'i',
            'o',
            'u',
            'y',
            'd'
        ], $str);

        // Xoá khoảng trắng và chuyển về chữ thường
        $str = strtolower($str);
        $str = preg_replace('/\s+/', '', $str);

        return $str;                      // Chuyển về chữ thường
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


    // private function isValidDateFormat($date)
    // {
    //     try {
    //         // Kiểm tra xem có đúng định dạng d-m-Y không
    //         $parsedDate = Carbon::createFromFormat('d-m-Y', $date);
    //         return $parsedDate && $parsedDate->format('d-m-Y') === $date;
    //     } catch (\Exception $e) {
    //         return false;
    //     }
    // }
}