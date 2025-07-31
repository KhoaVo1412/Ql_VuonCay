<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use Illuminate\Http\Request;

use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class CertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("certificates.index");
    }

    public function getCertificatesData()
    {
        $certificates = Certificate::query();

        return DataTables::of($certificates)
            ->addColumn('stt', function ($row) {
                static $stt = 0;
                $stt++;
                return $stt;
            })
            ->editColumn('file_name', function ($certificate) {
                $fileUrl = asset('certificates/' . $certificate->file_name);
                return '<a href="' . $fileUrl . '" target="_blank">' . $certificate->file_name . '</a>';
            })
            ->addColumn('action', function ($certificate) {
                return '
                <a href="' . route('certi.edit', $certificate->id) . '" class="edit-btn btn btn-sm btn-primary mb-2"><i class="fa fa-edit"></i></a>
                    <form action="' . route('certi.destroy', $certificate->id) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="delete-btn btn btn-sm btn-danger mb-2" onclick="return confirm(\'Xóa chứng chỉ này?\')"><i class="fa fa-trash"></i></button>
                    </form>
                ';
            })
            ->rawColumns(['file_name', 'action', 'stt'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("certificates.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'attachment' => 'required|file|mimes:pdf,jpg,jpeg,png'
            ]);

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . $file->getClientOriginalName();
                // $filePath = 'uploads/' . $fileName;

                $file->move(public_path('certificates'), $fileName);

                $certificate = Certificate::create([
                    'name' => $request->input('name'),
                    'file_name' => $fileName
                ]);
                ActionHistory::create([
                    'user_id' => Auth::id(),
                    'action_type' => 'Tạo',
                    'model_type' => 'Chứng chỉ',
                    'details' => "Tải lên chứng chỉ: '{$certificate->name}' với file: '{$fileName}'"
                ]);
            }
            return redirect()->back()->with('message', 'Tải lên file thành công!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Tải file thất bại!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $certificate = Certificate::findOrFail($id);

        return view("certificates.edit", compact('certificate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $certificate = Certificate::findOrFail($id);
            $oldName = $certificate->name;
            $oldFile = $certificate->file_name;
            $certificate->name = $request->name;
            $newFileName = $oldFile;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('certificates'), $fileName);

                if ($certificate->file_name && file_exists(public_path('certificates/' . $certificate->file_name))) {
                    unlink(public_path('certificates/' . $certificate->file_name));
                }

                $certificate->file_name = $fileName;
            }

            $certificate->save();
            $logDetails = "Cập nhật chứng chỉ";
            if ($oldName !== $certificate->name) {
                $logDetails .= " Tên: '{$oldName}' → '{$certificate->name}'.";
            }
            if ($oldFile !== $newFileName) {
                $logDetails .= " File: '{$oldFile}' → '{$newFileName}'.";
            }

            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'Cập nhật',
                'model_type' => 'Chứng chỉ',
                'details' => $logDetails,
            ]);
            return redirect()->route('certi.index')->with('message', 'Chứng chỉ đã được cập nhật thành công!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Cập nhật thất bại!');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $certificate = Certificate::findOrFail($id);
        $logDetails = "Xóa chứng chỉ tên: '{$certificate->name}', file: '{$certificate->file_name}'.";

        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'Xóa',
            'model_type' => 'Chứng chỉ',
            'details' => $logDetails,
        ]);

        if ($certificate->file_name && file_exists(public_path('certificates/' . $certificate->file_name))) {
            unlink(public_path('certificates/' . $certificate->file_name));
        }
        $certificate->delete();

        return redirect()->route('certi.index')->with('message', 'Chứng chỉ đã được xóa!');
    }

    public function getCertificates(Request $request)
    {
        $certificates = Certificate::all()->map(function ($certificate) {
            // Mã hóa URL của tên tệp và thay thế dấu '+' bằng '%20'
            $encodedFileName = urlencode($certificate->file_name);
            $encodedFileName = str_replace('+', '%20', $encodedFileName); // Đảm bảo dấu cách được mã hóa thành '%20'
            $certificate->file_name = asset('certificates/' . $encodedFileName);
            return $certificate;
        });

        if ($certificates->isNotEmpty()) {
            return response()->json([
                'success' => true,
                'data' => $certificates
            ]);
        }

        return response()->json([
            'success' => false,
            'data' => 'Không có chứng chỉ nào'
        ]);
    }
}
