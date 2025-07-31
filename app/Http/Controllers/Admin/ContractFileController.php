<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderExport;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


class ContractFileController extends Controller
{
    public function index()
    {
        $orderExports = OrderExport::all();
        return view('contract-files.index', compact('orderExports'));
    }
    public function edit($id)
    {

        $xuathang = OrderExport::with('exportFile')->find($id);
        return view('contract-files.edit', data: compact('xuathang'));
    }
    public function create()
    {
        $orderExports = OrderExport::all();
        return view('contract-files.create_file', compact('orderExports'));
    }

    public function getData()
    {
        $data = OrderExport::with('exportFile')->get();
        $formattedData = [];

        foreach ($data as $order) {
            $exportFiles = $order->exportFile; // Lấy danh sách file xuất hàng

            $files = [];
            foreach ($exportFiles as $file) {
                $files[] = [
                    'name' => $file->name ?? '',
                    'file_name' => $file->file_name ?? '',
                ];
            }

            $names = $order->exportFile->pluck('name')->filter()->implode('<br>');

            // Lấy danh sách file_name và nối bằng <br>
            $fileLinks = $order->exportFile->map(function ($file) {
                return !empty($file->file_name)
                    ? $file->file_name
                    : '';
            })->implode('<br>');

            $actions = (!empty($names) && !empty($fileLinks))
                ? '<a href="' . route('edit.index', $order->id) . '" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>'
                : '';

            $formattedData[] = [
                'id' => $order->id,
                'code' => $order->code,
                'files' => $files, // Chứa danh sách các file kèm tên
                'actions' => $actions,
            ];
        }
        // dd($formattedData);

        return DataTables::of(collect($formattedData))
            ->addIndexColumn()
            ->rawColumns(['name', 'file_name', 'actions']) // Giữ nguyên HTML
            ->make(true);
    }



    public function editFile(Request $request, $id)
    {

        // // Lấy dữ liệu từ DB
        // $xuathang = OrderExport::with('exportFile')->findOrFail($id);

        // // Lấy danh sách tên file mới và file mới (nếu có)
        // $names = $request->ten_file ?? [];
        // $files = $request->file('file_input') ?? [];
        // $newNames = $request->new_file ?? [];
        // $newFiles = $request->file('new_files_uploaded') ?? [];
        // $validFiles = [];

        // // Lấy danh sách file bị xóa từ request
        // $deletedFiles = $request->deleted_files ?? [];

        // // Đường dẫn thư mục chứa file
        // $folderPath = public_path('contracts/' . $xuathang->id);

        // foreach ($newNames as $index => $name) {
        //     $newFile = $newFiles[$index];
        //     $newFileName = $newFile->getClientOriginalName();
        //     $newFile->move($folderPath, $newFileName);

        //     $validFiles[] = [
        //         'order_export_id' => $xuathang->id, // Bắt buộc thêm vào
        //         'name' => $name,
        //         'file_name' => $newFileName
        //     ];
        // }

        // // Chèn tất cả các file hợp lệ vào DB một lần duy nhất
        // if (!empty($validFiles)) {
        //     $xuathang->exportFile()->insert($validFiles);
        // }

        // // Xóa các file được đánh dấu xóa
        // $filesToDelete = $xuathang->exportFile()->whereIn('id', $deletedFiles)->get();

        // foreach ($filesToDelete as $file) {
        //     $filePath = $folderPath . '/' . $file->file_name;
        //     // dd($filePath);
        //     // Đếm số lượng file có cùng tên trong database một lần duy nhất
        //     $fileCount = $xuathang->exportFile()->where('file_name', $file->file_name)->count();

        //     // Nếu chỉ có 1 file trùng tên, thì xóa khỏi thư mục
        //     if ($fileCount == 1 && file_exists($filePath)) {
        //         unlink($filePath);
        //     }

        //     // Xóa file khỏi database
        //     $file->delete();

        // }

        // // Duyệt qua từng file cũ để cập nhật
        // foreach ($xuathang->exportFile as $index => $file) {

        //     // Bỏ qua nếu file đã bị xóa
        //     if (in_array($file->id, $deletedFiles)) {
        //         continue;
        //     }

        //     $fileName = $file->file_name;
        //     $name = $file->name;

        //     if (!empty($files[$index])) {
        //         $fileName = $files[$index]->getClientOriginalName();
        //         $oldFilePath = $folderPath . '/' . $file->file_name;
        //         if (file_exists($oldFilePath)) {
        //             unlink($oldFilePath);
        //         }

        //         $files[$index]->move($folderPath, $fileName);
        //     }

        //     // Nếu có tên file mới, cập nhật
        //     if (!empty($names[$index])) {
        //         $name = $names[$index];
        //     }

        //     // Chuẩn bị dữ liệu update
        //     $data = [
        //         'name' => $name,
        //         'file_name' => $fileName
        //     ];

        //     // Cập nhật dữ liệu vào DB
        //     $xuathang->exportFile()->updateOrCreate(['id' => $file->id], $data);
        // }

        // // Lưu Thay Đổi
        // $xuathang->save();

        // // Điều hướng về danh sách với thông báo thành công
        // // return redirect()->route('contract-files.index')->with('message', 'File đã được cập nhật thành công!');
        // return response()->json([
        //     'redirect' => route('contract-files.index'), // Trả về URL cần chuyển hướng
        //     'message' => 'File đã được cập nhật thành công!'
        // ], 200);

        try {
            // Lấy dữ liệu từ DB
            $xuathang = OrderExport::with('exportFile')->findOrFail($id);

            // Đường dẫn thư mục chứa file
            $folderPath = public_path('contracts/' . $xuathang->id);
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            // Lấy danh sách file mới
            $newNames = $request->new_file ?? [];
            $newFiles = $request->file('new_files_uploaded') ?? [];
            $validFiles = [];

            foreach ($newNames as $index => $name) {
                $newFile = $newFiles[$index];
                $newFileName = $newFile->getClientOriginalName(); // Tránh trùng tên file
                $newFile->move($folderPath, $newFileName);

                $validFiles[] = [
                    'order_export_id' => $xuathang->id,
                    'name' => $name,
                    'file_name' => $newFileName
                ];
            }

            // Thêm mới vào DB nếu có file hợp lệ
            if (!empty($validFiles)) {
                $xuathang->exportFile()->insert($validFiles);
            }

            // Xóa file bị đánh dấu xóa
            $deletedFiles = $request->deleted_files ?? [];
            if (!empty($deletedFiles)) {
                $filesToDelete = $xuathang->exportFile()->whereIn('id', $deletedFiles)->get();

                foreach ($filesToDelete as $file) {
                    $filePath = $folderPath . '/' . $file->file_name;

                    // Kiểm tra nếu file này chỉ tồn tại một lần duy nhất thì mới xóa
                    if ($xuathang->exportFile()->where('file_name', $file->file_name)->count() == 1 && File::exists($filePath)) {
                        File::delete($filePath);
                    }

                    $file->delete();
                }
            }

            // Cập nhật file cũ
            $names = $request->ten_file ?? [];
            $files = $request->file('file_input') ?? [];

            foreach ($xuathang->exportFile as $index => $file) {
                if (in_array($file->id, $deletedFiles)) {
                    continue; // Bỏ qua file đã bị xóa
                }

                $updateData = [];
                if (!empty($files[$index])) {
                    $fileName = $files[$index]->getClientOriginalName();
                    $files[$index]->move($folderPath, $fileName);
                    $updateData['file_name'] = $fileName;
                }


                //Cập nhật tên file nếu có
                if (!empty($names[$index])) {
                    $updateData['name'] = $names[$index];
                }

                if (!empty($updateData)) {
                    $file->update($updateData);
                }
            }

            $existingFiles = File::files($folderPath);
            $validFileNames = $xuathang->exportFile()->pluck('file_name')->toArray();
            foreach ($existingFiles as $file) {
                $fileName = $file->getFilename();

                if (!in_array($fileName, $validFileNames)) {
                    File::delete($file->getPathname()); // Xóa file không còn trong database
                }
            }


            return response()->json([
                'redirect' => route('contract-files.index'),
                'message' => 'File đã được cập nhật thành công!'
            ], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function createFile(Request $request)
    {
        try {
            $xuatHang = OrderExport::find($request->code);

            if (!$xuatHang) {
                return response()->json(['error' => 'Không tìm thấy lệnh xuất hàng!'], 404);
            }

            $names = $request->ten_file ?? [];
            $files = $request->file('files_uploaded') ?? [];

            // Nếu không có file nào được tải lên
            if (empty($files)) {
                return response()->json(['error' => 'Không có file nào được tải lên!'], 400);
            }

            // Tạo thư mục lưu trữ
            $folderPath = public_path('contracts/' . $xuatHang->id);
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0777, true, true);
            }

            foreach ($files as $index => $file) {
                if ($file) { // Kiểm tra file hợp lệ
                    $fileName = $file->getClientOriginalName(); // Tránh trùng file
                    $file->move($folderPath, $fileName);

                    $name = $names[$index] ?? end($names);

                    $xuatHang->exportFile()->create([
                        'name' => $name,
                        'file_name' => $fileName
                    ]);
                }
            }
            return response()->json([
                'redirect' => route('contract-files.index'), // Trả về URL cần chuyển hướng
                'message' => 'Tải lên thành công!'
            ], 200);
            // return response()->json([
            //     'message' => 'Tải lên thành công!',
            //     'files' => $savedFiles
            // ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Đã xảy ra lỗi!',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
