<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Batch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpWord\PhpWord;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\DataTables;
use ZipArchive;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\TablePosition;

class BatchBController extends Controller
{
    public function index_b(Request $request)
    {
        $query = Batch::query()->orderBy('id', 'desc');

        if ($request->filled('date_filter')) {
            $date = Carbon::createFromFormat('d/m/Y', $request->date_filter)->format('Y-m-d');
            $query->whereDate('created_at', $date);
        }

        if ($request->ajax()) {
            $all_batchesB = $query->get();

            return DataTables::of($all_batchesB)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->addColumn('batch_code', function ($row) {
                    return '<a href="/edit-batchesB/' . $row->id . '">' . $row->batch_code . '</a>';
                })
                ->addColumn('qr_code', function ($row) {
                    if ($row->qr_code) {
                        $qrCodeUrl = asset($row->qr_code);
                        return '<img src="' . $qrCodeUrl . '" alt="QR Code" width="100" height="100">';
                    } else {
                        return 'Không có mã QR Code';
                    }
                })
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d/m/Y');
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-batchesB/' . $row->id . '" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal' . $row->id . '">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                        <div class="modal fade" id="deleteModal' . $row->id . '" tabindex="-1" aria-labelledby="deleteModalLabel' . $row->id . '" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel' . $row->id . '">Xác Nhận Xóa</h5>
                                        <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Bạn có chắc chắn có muốn xóa thông tin khu vực trồng này không ?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/delete-batchesB/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'stt', 'batch_code', 'qr_code', 'created_at', 'action'])
                ->make(true);
        }

        return view('batch.batchesB.all_batchesB');
    }

    public function add_b(Request $request)
    {
        return view('batch.batchesB.add');
    }

    // public function save_b(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'batch_codes' => 'required|string',
    //         ]);
    //         $batch_codes = preg_split('/[\s,]+/', $request->batch_codes);
    //         $batch_codes = array_unique($batch_codes);
    //         $existingBatchCodes = Batch::pluck('batch_code')->toArray();
    //         $newBatchCodes = [];
    //         $existingCodes = [];
    //         $invalidCodes = [];
    //         $duplicateCodes = [];

    //         foreach ($batch_codes as $batch_code) {
    //             $batch_code = trim($batch_code);

    //             if (strlen($batch_code) != 6) {
    //                 $invalidCodes[] = $batch_code;
    //                 continue;
    //             }
    //             $pattern = '/^[0-9]{2}[1-9]{1}[0-9]{3}$/';
    //             if (!preg_match($pattern, $batch_code)) {
    //                 $invalidCodes[] = $batch_code;
    //                 continue;
    //             }
    //             if (in_array($batch_code, $existingBatchCodes)) {
    //                 $existingCodes[] = $batch_code;
    //             } else {
    //                 if (in_array($batch_code, $newBatchCodes)) {
    //                     $duplicateCodes[] = $batch_code;
    //                 } else {
    //                     $newBatchCodes[] = $batch_code;
    //                 }
    //             }
    //         }
    //         if (!empty($duplicateCodes)) {
    //             return redirect()->back()->with('error', 'Mã lô trùng: ' . implode(', ', $duplicateCodes))
    //                 ->withInput();
    //         }
    //         if (!empty($invalidCodes)) {
    //             return redirect()->back()->with('error', 'Mã lô không hợp lệ: ' . implode(', ', $invalidCodes))
    //                 ->withInput();
    //         }
    //         if (!empty($existingCodes)) {
    //             return redirect()->back()->with('error', 'Mã lô đã tồn tại: ' . implode(', ', $existingCodes))
    //                 ->withInput();
    //         }
    //         if (empty($newBatchCodes)) {
    //             return redirect()->back()->with('error', 'Tất cả mã lô đã tồn tại.')
    //                 ->withInput();
    //         }
    //         foreach ($newBatchCodes as $batch_code) {
    //             if (Batch::where('batch_code', $batch_code)->exists()) {
    //                 $existingCodes[] = $batch_code;
    //                 continue;
    //             }
    //             $batch = Batch::create([
    //                 'batch_code' => $batch_code,
    //             ]);
    //             $qrCodePath = 'qr_codes/' . $batch->id . '.png';
    //             $path = public_path($qrCodePath);
    //             if (!file_exists(public_path('qr_codes'))) {
    //                 mkdir(public_path('qr_codes'), 0777, true);
    //             }
    //             QrCode::format('png')->size(200)->generate($batch_code, $path);
    //             $batch->update([
    //                 'qr_code' => $qrCodePath,
    //             ]);
    //         }

    //         return redirect()->route('batchesB.index')->with('message', 'Tạo mã lô thành công.');
    //     } catch (\Throwable $th) {
    //         return redirect()->back()
    //             ->withErrors(['error' => 'Có lỗi xảy ra: ' . $th->getMessage()])
    //             ->withInput();
    //     }
    // }
    public function save_b(Request $request)
    {
        try {
            $request->validate([
                'batch_codes' => 'required|string',
            ]);
            $batch_codes = preg_split('/[\s,]+/', $request->batch_codes);
            $batch_codes = array_map('trim', array_filter($batch_codes));
            $unique_batch_codes = array_unique($batch_codes);
            if (count($batch_codes) !== count($unique_batch_codes)) {
                $duplicateCodes = array_unique(array_diff_assoc($batch_codes, $unique_batch_codes));
                return redirect()->back()
                    ->with('error', 'Mã lô trùng trong danh sách nhập: ' . implode(', ', $duplicateCodes))
                    ->withInput();
            }
            $existingBatchCodes = Batch::pluck('batch_code')->toArray();
            $newBatchCodes = [];
            $invalidCodes = [];
            $existingCodes = [];
            foreach ($batch_codes as $batch_code) {
                if (strlen($batch_code) != 6) {
                    $invalidCodes[] = $batch_code;
                    continue;
                }
                $pattern = '/^[0-9]{2}[0-9]{1}[0-9]{3}$/';
                if (!preg_match($pattern, $batch_code)) {
                    $invalidCodes[] = $batch_code;
                    continue;
                }
                if (in_array($batch_code, $existingBatchCodes)) {
                    $existingCodes[] = $batch_code;
                    continue;
                }
                $newBatchCodes[] = $batch_code;
            }
            if (!empty($invalidCodes)) {
                return redirect()->back()
                    ->with('error', 'Mã lô không hợp lệ: ' . implode(', ', $invalidCodes))
                    ->withInput();
            }
            if (!empty($existingCodes)) {
                return redirect()->back()
                    ->with('error', 'Mã lô đã tồn tại: ' . implode(', ', $existingCodes))
                    ->withInput();
            }
            if (empty($newBatchCodes)) {
                return redirect()->back()
                    ->with('error', 'Không có mã lô hợp lệ để tạo.')
                    ->withInput();
            }
            foreach ($newBatchCodes as $batch_code) {
                $batch = Batch::create([
                    'batch_code' => $batch_code,
                ]);
                $qrCodePath = 'qr_codes/' . $batch->id . '.png';
                $path = public_path($qrCodePath);
                if (!file_exists(public_path('qr_codes'))) {
                    mkdir(public_path('qr_codes'), 0777, true);
                }
                QrCode::format('png')->size(200)->generate($batch_code, $path);
                $batch->update([
                    'qr_code' => $qrCodePath,
                ]);
            }
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'Tạo',
                'model_type' => 'Tạo Mã Lô',
                'details' => 'Đã tạo mã lô: ' . implode(', ', $newBatchCodes),
            ]);
            return redirect()->route('batchesB.index')
                ->with('message', 'Tạo mã lô thành công.');
        } catch (\Throwable $th) {
            return redirect()->back()
                ->withErrors(['error' => 'Có lỗi xảy ra: ' . $th->getMessage()])
                ->withInput();
        }
    }
    public function edit_b($id)
    {
        $batchesB = Batch::find($id);
        if ($batchesB->ingredients()->exists()) {
            return redirect()->back()->with('error', 'Mã lô hàng đã được kết nối thông tin nguyên liệu, bạn không được phép chỉnh sửa.');
        }

        if ($batchesB->order_export_id) {
            return redirect()->back()->with('error', 'Không thể cập nhật vì lô hàng đã thuộc một lệnh xuất hàng.');
        }

        if ($batchesB->status == 1) {
            return redirect()->back()->with('error', 'Không thể cập nhật vì lô hàng đã kiểm nghiệm.');
        }
        return view('batch.batchesB.edit', compact('batchesB'));
    }
    public function update_b(Request $request, $id)
    {
        try {
            // Validate the input
            $request->validate([
                'batch_code' => 'required|string',
            ]);

            $batch_codes = preg_split('/[\s,]+/', $request->batch_code);
            $batchCodePattern = '/^\d{2}\d{1}[0-9]{3}$/';

            $invalidCodes = [];
            $existingCodes = [];

            foreach ($batch_codes as $batch_code) {
                $batch_code = trim($batch_code);

                if (strlen($batch_code) != 6 || !preg_match($batchCodePattern, $batch_code)) {
                    $invalidCodes[] = $batch_code;
                    continue;
                }
                $existingBatch = Batch::where('batch_code', $batch_code)
                    ->where('id', '!=', $id)
                    ->first();
                if ($existingBatch) {
                    $existingCodes[] = $batch_code;
                }
            }
            if (!empty($invalidCodes)) {
                return redirect()->back()->with('error', 'Mã lô không hợp lệ: ' . implode(', ', $invalidCodes))
                    ->withInput();
            }
            if (!empty($existingCodes)) {
                return redirect()->back()->with('error', 'Mã lô đã tồn tại: ' . implode(', ', $existingCodes))
                    ->withInput();
            }
            $batch = Batch::findOrFail($id);

            // if ($batch->ingredients()->exists()) {
            //     return redirect()->back()->with('error', 'Mã lô hàng đã được kết nối thông tin nguyên liệu, bạn không được phép chỉnh sửa.');
            // }

            // if ($batch->order_export_id) {
            //     return redirect()->back()->with('error', 'Không thể cập nhật vì lô hàng đã thuộc một lệnh xuất hàng.');
            // }

            // if ($batch->status == 1) {
            //     return redirect()->back()->with('error', 'Không thể cập nhật vì lô hàng đã kiểm nghiệm.');
            // }

            $oldBatchCode = $batch->batch_code;

            $oldQrCodePath = $batch->qr_code;

            $batch->update([
                'batch_code' => $batch_codes[0],
            ]);

            if ($oldQrCodePath && file_exists(public_path($oldQrCodePath))) {
                unlink(public_path($oldQrCodePath));
            }

            $qrCodePath = 'qr_codes/' . $batch->id . '.png';
            $path = public_path($qrCodePath);

            if (!file_exists(public_path('qr_codes'))) {
                mkdir(public_path('qr_codes'), 0777, true);
            }

            QrCode::format('png')->size(200)->generate($batch_codes[0], $path);

            $batch->update([
                'qr_code' => $qrCodePath,
            ]);
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'Cập nhật',
                'model_type' => 'Cập nhật Mã Lô',
                'details' => "Đã cập nhật mã lô từ '{$oldBatchCode}' thành '{$batch_codes[0]}'",
            ]);

            return redirect()->route('batchesB.index')->with('message', 'Cập nhật mã lô thành công.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Cập nhật mã lô thất bại!')->withInput();
        }
    }

    // public function update_b(Request $request, $id)
    // {
    //     try {
    //         // Validate the input
    //         $request->validate([
    //             'batch_code' => 'required|string',
    //         ]);

    //         $batchCodePattern = '/^\d{2}\d{1}[0-9]{3}$/';

    //         if (!preg_match($batchCodePattern, $request->batch_code)) {
    //             return redirect()->back()->with('error', 'Mã lô không đúng định dạng. Vui lòng kiểm tra lại mã lô.')
    //                 ->withInput();
    //         }
    //         $batch = Batch::findOrFail($id);

    //         if ($batch->status == 1) {
    //             return redirect()->back()->with('error', 'Không thể cập nhật vì lô hàng đã kiểm nghiệm.');
    //         }
    //         if ($batch->order_export_id) {
    //             return redirect()->back()->with('error', 'Không thể cập nhật vì lô hàng đã thuộc một lệnh xuất hàng.');
    //         }
    //         $existingBatch = Batch::where('batch_code', $request->batch_code)
    //             ->where('id', '!=', $id)
    //             ->first();

    //         if ($existingBatch) {
    //             return redirect()->back()->with('error', 'Mã lô đã tồn tại, vui lòng kiểm tra lại mã lô.')
    //                 ->withInput();
    //         }

    //         $oldQrCodePath = $batch->qr_code;

    //         $batch->update([
    //             'batch_code' => $request->batch_code,
    //         ]);

    //         if ($oldQrCodePath && file_exists(public_path($oldQrCodePath))) {
    //             unlink(public_path($oldQrCodePath));
    //         }

    //         $qrCodePath = 'qr_codes/' . $batch->id . '.png';
    //         $path = public_path($qrCodePath);
    //         if (!file_exists(public_path('qr_codes'))) {
    //             mkdir(public_path('qr_codes'), 0777, true);
    //         }
    //         QrCode::format('png')->size(200)->generate($request->batch_code, $path);
    //         $batch->update([
    //             'qr_code' => $qrCodePath,
    //         ]);

    //         return redirect()->route('batchesB.index')->with('message', 'Cập nhật mã lô thành công.');
    //     } catch (\Throwable $th) {
    //         return redirect()->back()->with('error', 'Cập nhật mã lô thất bại!')->withInput();
    //     }
    // }


    public function destroy_b($id)
    {
        $batch = Batch::findOrFail($id);
        if ($batch->testingResult) {
            return redirect()->back()->with('error', 'Không thể xóa lô hàng này vì đã có kết quả kiểm nghiệm.');
        }
        if ($batch->qr_code && file_exists(public_path($batch->qr_code))) {
            unlink(public_path($batch->qr_code));
        }

        $batch->delete();

        session()->flash('message', 'Xóa mã lô thành công.');
        return redirect()->back();
    }
    public function index_qr()
    {
        $batches = Batch::all();
        return view('batch.export_batchCode', compact('batches'));
    }
    public function generateQrCodes(Request $request)
    {
        $batchIds = $request->input('batches_id');

        if (in_array('all', $batchIds)) {
            $batchIds = Batch::pluck('id')->toArray();
        }

        // Nếu <= 50 thì dùng stream để tải trực tiếp
        if (count($batchIds) <= 50) {
            $phpWord = new PhpWord();
            $section = $phpWord->addSection([
                'marginLeft' => 100,  // khoảng 0.5 cm
                'marginRight' => 100, // khoảng 0.5 cm
            ]);

            $qrFolder = public_path('qr_codes');
            if (!file_exists($qrFolder)) {
                mkdir($qrFolder, 0777, true);
            }

            foreach ($batchIds as $batchId) {
                $batch = Batch::find($batchId);
                if (!$batch)
                    continue;

                $qrPath = $qrFolder . '/' . $batch->id . '.png';
                QrCode::format('png')->size(200)->generate($batch->batch_code, $qrPath);

                $tableStyle = [
                    'alignment' => Jc::CENTER,
                    'cellMargin' => 80,
                    'borderSize' => 12, // Không viền
                    'borderColor' => '000000',
                    'cellSpacing' => 0,
                ];

                $table = $section->addTable($tableStyle);

                // Tạo hàng với 2 ô
                $table->addRow();

                // Ô bên trái: text thông tin
                $cellLeft = $table->addCell(7200, [ // khoảng 12.5cm
                    'valign' => 'top',
                    'borderSize' => 0,

                ]);
                $paragraphStyle = [
                    'align' => 'left',
                    'spaceAfter' => 200,
                    'left' => 200,
                ];

                $run = $cellLeft->addTextRun($paragraphStyle);
                $run->addText('Mã Lô: ', ['bold' => true]);
                $run->addText($batch->batch_code, ['bold' => true, 'size' => 14]);

                $run = $cellLeft->addTextRun($paragraphStyle);
                $run->addText('Ngày sản xuất lô: ', ['bold' => true]);
                $run->addText(Carbon::parse($batch->date_sx)->format('d/m/Y'));

                $run = $cellLeft->addTextRun($paragraphStyle);
                $run->addText('Chủng loại: ', ['bold' => true]);
                $run->addText('..................................................................................');

                $run = $cellLeft->addTextRun($paragraphStyle);
                $run->addText('Người thực hiện làm hàng: ', ['bold' => true]);
                $run->addText('...........................................................................');

                $run = $cellLeft->addTextRun($paragraphStyle);
                $run->addText('Người chịu trách nhiệm: ', ['bold' => true]);
                $run->addText('.................................................................................');


                // Ô bên phải: QR code
                $cellRight = $table->addCell(2065, [ // khoảng 3.5cm
                    'valign' => 'center',
                    'borderSize' => 0,
                ]);
                $cellRight->addImage($qrPath, [
                    'width' => 100,
                    'height' => 100,
                    'alignment' => Jc::CENTER,
                ]);

                $section->addTextBreak(1);
            }

            return response()->stream(function () use ($phpWord) {
                $phpWord->save('php://output', 'Word2007');
            }, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => 'attachment; filename="batch_qr_codes.docx"',
            ]);
        }

        // Nếu > 50 thì chia nhóm, tạo nhiều file Word rồi zip lại
        $batchChunks = array_chunk($batchIds, 50); // Mỗi file chứa 50 lô
        $tempPath = storage_path('app/temp_qr');
        File::ensureDirectoryExists($tempPath);
        $qrFolder = $tempPath . '/images';
        File::ensureDirectoryExists($qrFolder);

        $wordFiles = [];

        foreach ($batchChunks as $index => $chunk) {
            $phpWord = new PhpWord();
            $section = $phpWord->addSection([
                'marginLeft' => 100,  // khoảng 0.5 cm
                'marginRight' => 100, // khoảng 0.5 cm
            ]);

            foreach ($chunk as $batchId) {
                $batch = Batch::find($batchId);
                if (!$batch)
                    continue;

                $qrPath = $qrFolder . '/' . $batch->id . '.png';
                QrCode::format('png')->size(200)->generate($batch->batch_code, $qrPath);

                // $section->addText('Mã Lô: ' . $batch->batch_code, ['bold' => true]);
                // $section->addImage($qrPath, ['width' => 100, 'height' => 100, 'align' => 'center']);
                $tableStyle = [
                    'alignment' => Jc::CENTER,
                    'cellMargin' => 80,
                    'borderSize' => 12, // Không viền
                    'borderColor' => '000000',
                    'cellSpacing' => 0,
                ];

                $table = $section->addTable($tableStyle);

                // Tạo hàng với 2 ô
                $table->addRow();

                // Ô bên trái: text thông tin
                $cellLeft = $table->addCell(7200, [ // khoảng 12.5cm
                    'valign' => 'top',
                    'borderSize' => 0,
                ]);
                $paragraphStyle = [
                    'align' => 'left',
                    'spaceAfter' => 200,
                    'left' => 200,
                ];

                $run = $cellLeft->addTextRun($paragraphStyle);
                $run->addText('Mã Lô: ', ['bold' => true]);
                $run->addText($batch->batch_code, ['bold' => true, 'size' => 14]);

                $run = $cellLeft->addTextRun($paragraphStyle);
                $run->addText('Ngày sản xuất lô: ', ['bold' => true]);
                $run->addText(Carbon::parse($batch->date_sx)->format('d/m/Y'));

                $run = $cellLeft->addTextRun($paragraphStyle);
                $run->addText('Chủng loại: ', ['bold' => true]);
                $run->addText('..................................................................................');

                $run = $cellLeft->addTextRun($paragraphStyle);
                $run->addText('Người thực hiện làm hàng: ', ['bold' => true]);
                $run->addText('...........................................................................');

                $run = $cellLeft->addTextRun($paragraphStyle);
                $run->addText('Người chịu trách nhiệm: ', ['bold' => true]);
                $run->addText('.................................................................................');


                // Ô bên phải: QR code
                $cellRight = $table->addCell(2065, [ // khoảng 3.5cm
                    'valign' => 'center',
                    'borderSize' => 0,

                ]);
                $cellRight->addImage($qrPath, [
                    'width' => 100,
                    'height' => 100,
                    'alignment' => Jc::CENTER,
                ]);

                $section->addTextBreak(1);
            }

            $fileName = 'batch_qr_codes_' . ($index + 1) . '.docx';
            $filePath = $tempPath . '/' . $fileName;
            $phpWord->save($filePath, 'Word2007');
            $wordFiles[] = $filePath;
        }

        // Tạo file ZIP
        $zipFileName = 'batch_qr_codes' . '.zip';
        $zipFilePath = $tempPath . '/' . $zipFileName;

        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            foreach ($wordFiles as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        // Xóa các file tạm PNG & DOCX (trừ file ZIP)
        foreach (File::allFiles($tempPath) as $file) {
            if ($file->getFilename() !== $zipFileName) {
                File::delete($file->getPathname());
            }
        }
        File::deleteDirectory($qrFolder); // xóa thư mục hình QR

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }
    // public function deleteMultiple(Request $request)
    // {
    //     $request->validate([
    //         'ids' => 'required|array',
    //         'ids.*' => 'integer',
    //     ]);
    //     Batch::whereIn('id', $request->ids)->delete();
    //     return response()->json([
    //         'message' => 'Xóa thành công mã lô đã chọn.',
    //         'deleted_ids' => $request->ids
    //     ]);
    // }
    // public function deleteMultiple(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'ids' => 'required|array',
    //             'ids.*' => 'integer',
    //         ]);

    //         $batches = Batch::whereIn('id', $request->ids)->get();

    //         foreach ($batches as $batch) {
    //             if ($batch->status == 1) {
    //                 return response()->json([
    //                     'message' => 'Không thể xóa lô hàng đã kiểm nghiệm: ' . $batch->batch_code
    //                 ], 400);
    //             }

    //             if ($batch->order_export_id) {
    //                 return response()->json([
    //                     'message' => 'Không thể xóa lô hàng vì đã thuộc lệnh xuất hàng: ' . $batch->batch_code
    //                 ], 400);
    //             }

    //             if ($batch->qr_code && file_exists(public_path($batch->qr_code))) {
    //                 unlink(public_path($batch->qr_code));  // Xóa tệp hình ảnh mã QR
    //             }
    //         }
    //         Batch::whereIn('id', $request->ids)->delete();

    //         return response()->json([
    //             'message' => 'Xóa thành công mã lô đã chọn.',
    //             'deleted_ids' => $request->ids
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'Có lỗi xảy ra khi xóa dữ liệu.'
    //         ], 500);
    //     }
    // }
    public function deleteMultiple(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer',
            ]);

            $batches = Batch::whereIn('id', $request->ids)->get();
            $messages = [];
            $allDeleted = true; // Track if all batches were deleted successfully

            foreach ($batches as $batch) {
                if ($batch->ingredients()->count() > 0) {
                    $messages[] = 'Không thể xóa mã lô hàng ' . $batch->batch_code . ' vì đã có nguyên liệu.';
                    $allDeleted = false;
                    continue;
                }

                if ($batch->status == 1) {
                    $messages[] = 'Không thể xóa mã lô hàng đã kiểm nghiệm: ' . $batch->batch_code;
                    $allDeleted = false;
                    continue;
                }

                if ($batch->order_export_id) {
                    $messages[] = 'Không thể xóa mã lô hàng vì đã thuộc lệnh xuất hàng: ' . $batch->batch_code;
                    $allDeleted = false;
                    continue;
                }

                if ($batch->qr_code && file_exists(public_path($batch->qr_code))) {
                    unlink(public_path($batch->qr_code));
                }
                $deletedBatchCode = $batch->batch_code;

                $batch->delete();
                ActionHistory::create([
                    'user_id' => Auth::id(),
                    'action_type' => 'Xóa',
                    'model_type' => 'Xóa Mã Lô',
                    'details' => "Đã xóa mã lô: {$deletedBatchCode}",
                ]);
                $messages[] = "Mã lô hàng " . $batch->batch_code . " đã được xóa.";
            }

            if (!$allDeleted) {
                // If any batch couldn't be deleted, return a 400 status with error: true
                return response()->json([
                    'message' => implode('<br>', $messages),
                    'error' => true
                ], 400);
            }

            // If all batches were deleted successfully
            return response()->json([
                'message' => 'Xóa thành công các lô hàng đã chọn.',
                'error' => false, // Explicitly set error to false on success
                'deleted_ids' => $request->ids
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi xóa dữ liệu: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }
    }
}