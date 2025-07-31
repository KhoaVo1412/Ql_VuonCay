<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Farm;
use App\Models\PlantingArea;
use App\Models\Units;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class PlantingAreaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $userFarms = $user->farms->pluck('id')->toArray();

        $query = PlantingArea::with('farm.unitRelation');
        if (empty($userFarms)) {
            $query = PlantingArea::with('farm.unitRelation');
        } else {
            $query->whereIn('farm_id', $userFarms);
        }
        if ($request->farm_id) {
            list($farm_name, $unit_name) = explode('|', $request->farm_id);
            $query->whereHas('farm', function ($q) use ($farm_name) {
                $q->where('farm_name', $farm_name);
            });
            if ($unit_name) {
                $query->whereHas('farm.unitRelation', function ($q) use ($unit_name) {
                    $q->where('unit_name', $unit_name);
                });
            }
        }
        $khuvuctrong = Farm::join('units', 'farm.unit_id', '=', 'units.id');

        if (!empty($userFarms)) {
            $khuvuctrong->whereIn('farm.id', $userFarms);
        }

        $plantingareas = $khuvuctrong
            ->select('farm.farm_name', 'units.unit_name')
            ->distinct()
            ->get();

        $defaultFarm = null;
        if ($plantingareas->count() === 1) {
            $defaultFarm = $plantingareas->first()->farm_name . '|' . $plantingareas->first()->unit_name;
        }
        $farms = Farm::whereIn('id', $userFarms)->get();
        $all_plan = $query->orderBy('id', 'desc');


        if ($request->ajax()) {
            return DataTables::of($all_plan)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('find', function ($row) {
                    return $row->find;
                })
                ->editColumn('farm_id', function ($row) {
                    return $row->farm->farm_name;
                })
                ->addColumn('unit', function ($row) {
                    return $row->farm && $row->farm->unitRelation ? $row->farm->unitRelation->unit_name : 'Chưa có đơn vị';
                })
                ->filterColumn('farm_id', function ($query, $keyword) {
                    $query->whereHas('farm', function ($q) use ($keyword) {
                        $q->where('farm_name', 'like', "%{$keyword}%");
                    });
                })
                ->editColumn('nam_trong', function ($row) {
                    return $row->nam_trong;
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex">
                            <a href="/edit-plantingareas/' . $row->id . '" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <!--<a class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal' . $row->id . '">
                                <i class="fas fa-trash-alt"></i>
                            </a>-->
                        </div>
                        <div class="modal fade" id="deleteModal' . $row->id . '" tabindex="-1" aria-labelledby="deleteModalLabel' . $row->id . '" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel' . $row->id . '">Xác Nhận Xóa</h5>
                                        <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Bạn có chắc chắn muốn xóa thông tin mã lô <span style="color: red;">' . $row->ma_lo . '</span>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/delete-plantingareas/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'stt', 'find', 'nam_trong', 'unit', 'farm_id', 'status', 'action'])
                ->make(true);
        }
        return view('planting_areas.all_plantingAreas', compact('defaultFarm', 'farms', 'plantingareas'));
    }
    public function getFarmsByUnit($unitId)
    {
        $farms = Farm::where('unit_id', $unitId)
            ->where('status', 'Hoạt động')
            ->get();

        return response()->json($farms);
    }

    // public function add(Request $request)
    // {
    //     $user = Auth::user();

    //     $userFarms = $user->farms()->pluck('farm.id')->toArray();

    //     if (empty($userFarms)) {
    //         $farm = Farm::where('status', 'Hoạt động')->get();
    //     } else {
    //         $farm = Farm::whereIn('id', $userFarms)->where('status', 'Hoạt động')->get();
    //     }
    //     $units = Units::whereIn('id', $farm->pluck('unit_id'))->where('status', 'Hoạt động')->get();

    //     $units = Units::where('status', 'Hoạt động')->get();

    //     return view('planting_areas.add', compact('farm', 'units'));
    // }
    public function add(Request $request)
    {
        $user = Auth::user();
        $userFarms = $user->farms->pluck('id')->toArray();

        // Lấy các đơn vị mà người dùng có quyền truy cập
        if (empty($userFarms)) {
            $units = Units::where('status', 'Hoạt động')->get();
        } else {
            $units = Units::whereIn('id', Farm::whereIn('id', $userFarms)->pluck('unit_id'))
                ->where('status', 'Hoạt động')
                ->get();
        }

        // Lấy các nông trại mà người dùng có quyền truy cập
        $farm = Farm::whereIn('id', $userFarms)->where('status', 'Hoạt động')->get();

        // Kiểm tra nếu người dùng chỉ có một đơn vị và một nông trại duy nhất
        $singleUnit = null;
        $singleFarm = null;

        if ($farm->count() == 1) {
            $singleFarm = $farm->first();  // Nếu chỉ có một nông trại, chọn nông trại đó
            $singleUnit = $singleFarm->unitRelation;  // Lấy đơn vị của nông trại
        }

        return view('planting_areas.add', compact('farm', 'units', 'singleUnit', 'singleFarm'));
    }


    public function save(Request $request)
    {
        // dd($request->all());
        try {
            $validated = $request->validate([
                'farm_id' => 'nullable|integer',
                'fid' => 'nullable|integer',
                'idmap' => 'nullable|string|max:255',
                'ma_lo' => 'nullable|string|max:255',
                'nha_sx' => 'nullable|string|max:255',
                'quoc_gia' => 'nullable|string|max:255',
                'plot' => 'nullable|string|max:255',
                'nam_trong' => 'nullable|integer|min:1900|max:' . date('Y'),
                'chi_tieu' => 'nullable|string|max:255',
                'dien_tich' => 'nullable|numeric|min:0',
                'tapping_y' => 'nullable|integer|min:0',
                'repl_time' => 'nullable',
                'find' => 'nullable|string|max:255',
                'webmap' => 'nullable|string|max:255',
                'gwf' => 'nullable|string|max:255',
                'xa' => 'nullable|string|max:255',
                'huyen' => 'nullable|string|max:255',
                'nguon_goc_lo' => 'nullable|string|max:255',
                'nguon_goc_dat' => 'nullable|string|max:255',
                'hang_dat' => 'nullable|string|max:255',
                'hien_trang' => 'nullable|string|max:255',
                'layer' => 'nullable|string|max:255',
                'x' => 'nullable|string|max:255',
                'y' => 'nullable|string|max:255',
                'chu_thich' => 'nullable|string|max:1000',
                'geo' => 'required',
                'pdf' => 'nullable|mimes:pdf|max:10240',
            ], [
                'farm_id.integer' => 'Nông trường phải là một số nguyên.',
                'fid.integer' => 'Fid phải là một số nguyên.',
                'idmap.max' => 'ID Map không được vượt quá 255 ký tự.',
                'ma_lo.max' => 'Mã lô không được vượt quá 255 ký tự.',
                'nha_sx.max' => 'Nhà sản xuất không được vượt quá 255 ký tự.',
                'quoc_gia.max' => 'Quốc gia không được vượt quá 255 ký tự.',
                'plot.max' => 'Plot không được vượt quá 255 ký tự.',
                'nam_trong.integer' => 'Năm trồng phải là một số nguyên.',
                'nam_trong.min' => 'Năm trồng phải từ 1900 trở lên.',
                'nam_trong.max' => 'Năm trồng không được vượt quá năm hiện tại (' . date('Y') . ').',
                'chi_tieu.max' => 'Chỉ tiêu không được vượt quá 255 ký tự.',
                'dien_tich.numeric' => 'Diện tích phải là một số.',
                'dien_tich.min' => 'Diện tích không được nhỏ hơn 0.',
                'tapping_y.integer' => 'Tapping Y phải là một số nguyên.',
                'tapping_y.min' => 'Tapping Y không được nhỏ hơn 0.',
                'find.max' => 'Find không được vượt quá 255 ký tự.',
                'webmap.max' => 'Webmap không được vượt quá 255 ký tự.',
                'gwf.max' => 'GWF không được vượt quá 255 ký tự.',
                'xa.max' => 'Xã không được vượt quá 255 ký tự.',
                'huyen.max' => 'Huyện không được vượt quá 255 ký tự.',
                'nguon_goc_lo.max' => 'Nguồn gốc lô không được vượt quá 255 ký tự.',
                'nguon_goc_dat.max' => 'Nguồn gốc đất không được vượt quá 255 ký tự.',
                'hang_dat.max' => 'Hạng đất không được vượt quá 255 ký tự.',
                'hien_trang.max' => 'Hiện trạng không được vượt quá 255 ký tự.',
                'layer.max' => 'Layer không được vượt quá 255 ký tự.',
                'x.max' => 'X không được vượt quá 255 ký tự.',
                'y.max' => 'Y không được vượt quá 255 ký tự.',
                'chu_thich.max' => 'Chú thích không được vượt quá 1000 ký tự.',
                'geo.required' => 'GeoJson không được bỏ trống.',
                'pdf.mimes' => 'File phải là định dạng PDF.',
                'pdf.max' => 'File không được vượt quá 10MB.',
            ]);

            $exists = PlantingArea::where('farm_id', $request->farm_id)
                ->where('find', $request->find)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Find đã tồn tại ở nông trường này.')->withInput();
            }

            $currentYear = date('Y');
            if ($request->nam_trong > $currentYear) {
                return redirect()->back()->with('error', 'Năm trồng không được lớn hơn năm hiện tại.')->withInput();
            }
            $farm = Farm::find($request->farm_id);
            $farmCode = $farm ? $farm->farm_code : 'Không tìm thấy.';
            $maLo = $request->nam_trong . '.' . $farmCode . '.' . $request->find;
            // dd($maLo);
            $validated['ma_lo'] = $maLo;
            $plantingArea = PlantingArea::create($validated);
            if ($request->hasFile('pdf')) {
                $pdfFile = $request->file('pdf');
                $pdfFileName = time() . '_' . $pdfFile->getClientOriginalName();  // Generate a unique file name
                $destinationPath = public_path('uploads/pdf');  // Destination path for storing PDF files
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);  // Create the directory if it doesn't exist
                }
                $pdfFile->move($destinationPath, $pdfFileName);

                $pdfPath = 'uploads/pdf/' . $pdfFileName;
                $plantingArea->update(['pdf' => $pdfPath]);
            }
            $farmName = optional(Farm::find($request->farm_id))->farm_name ?? 'N/A';
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'Tạo',
                'model_type' => 'Khu Vực Trồng',
                'details' => 'Tạo khu vực trồng mới: Mã lô = ' . $maLo .
                    ', Nông trường = ' . $farmName .
                    ', Năm trồng = ' . $request->nam_trong .
                    ', Find = ' . $request->find,
            ]);

            return redirect()->route('plantingareas.index')->with('message', 'Tạo khu vực thành công');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Có lỗi xảy ra khi tạo khu trồng: ' . $e->getMessage()])
                ->withInput();
            // return redirect()->back()->with('error', 'Tạo khu vực thất bại. Vui lòng kiểm tra lại dữ liệu.');
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $plantingArea = PlantingArea::find($id);
        $units = Units::all();
        $farm = is_null($user->farm_id)
            ? Farm::where('status', 'Hoạt động')->get()
            : Farm::where('id', $user->farm_id)->where('status', 'Hoạt động')->get();
        $selectedUnitId = $plantingArea->farm ? $plantingArea->farm->unit_id : null;
        $farms = $selectedUnitId
            ? Farm::where('unit_id', $selectedUnitId)->where('status', 'Hoạt động')->get()
            : [];
        return view('planting_areas.edit', compact('plantingArea', 'farm', 'units', 'farms', 'selectedUnitId'));
    }

    public function update(Request $request, $id)
    {
        $plantingArea = PlantingArea::find($id);
        $hasIngredients = $plantingArea->ingredients()->exists();
        if ($hasIngredients) {
            return redirect()->back()->with('error', 'Không thể sửa khu vực trồng vì đã có thông tin nguyên liệu liên quan.');
        }

        $exists = PlantingArea::where('farm_id', $request->farm_id)
            ->where('find', $request->find)
            ->where('id', '!=', $id)
            ->exists();

        $currentYear = date('Y');
        if ($request->nam_trong > $currentYear) {
            return redirect()->back()->with('error', 'Năm trồng không được lớn hơn năm hiện tại.');
        }
        if ($exists) {
            return redirect()->back()->with('error', 'Find đã tồn tại ở nông trường này.');
        }
        if (!$plantingArea) {
            return redirect()->back()->with('error', 'Khu vực trồng không tồn tại');
        }
        $request->validate([
            'farm_id' => 'nullable|integer',
            'fid' => 'nullable|integer',
            'idmap' => 'nullable|string|max:255',
            'ma_lo' => 'nullable|string|max:255',
            'nha_sx' => 'nullable|string|max:255',
            'quoc_gia' => 'nullable|string|max:255',
            'plot' => 'nullable|string|max:255',
            'nam_trong' => 'nullable|integer|min:1900|max:' . $currentYear,
            'chi_tieu' => 'nullable|string|max:255',
            'dien_tich' => 'nullable|numeric|min:0',
            'tapping_y' => 'nullable|integer|min:0',
            'repl_time' => 'nullable',
            'find' => 'nullable|string|max:255',
            'webmap' => 'nullable|string|max:255',
            'gwf' => 'nullable|string|max:255',
            'xa' => 'nullable|string|max:255',
            'huyen' => 'nullable|string|max:255',
            'nguon_goc_lo' => 'nullable|string|max:255',
            'nguon_goc_dat' => 'nullable|string|max:255',
            'hang_dat' => 'nullable|string|max:255',
            'hien_trang' => 'nullable|string|max:255',
            'layer' => 'nullable|string|max:255',
            'x' => 'nullable|string|max:255',
            'y' => 'nullable|string|max:255',
            'chu_thich' => 'nullable|string|max:1000',
            'geo' => 'nullable',
        ]);
        $oldPlantingArea = $plantingArea->getOriginal();
        $farm = Farm::find($request->farm_id);
        if ($farm) {
            $farmCode = $farm->farm_code;
        } else {
            $farmCode = 'null';
        }

        $maLo = $request->nam_trong . '.' . $farmCode . '.' . $request->find;
        if ($request->hasFile('pdf')) {
            if ($plantingArea->pdf && file_exists(public_path($plantingArea->pdf))) {
                unlink(public_path($plantingArea->pdf));
            }

            $pdfFile = $request->file('pdf');
            $pdfFileName = time() . '_' . $pdfFile->getClientOriginalName();
            $destinationPath = public_path('uploads/pdf');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $pdfFile->move($destinationPath, $pdfFileName);

            $pdfPath = 'uploads/pdf/' . $pdfFileName;
        }

        $plantingArea->update([
            'farm_id' => $request->farm_id,
            'fid' => $request->fid,
            'idmap' => $request->idmap,
            'ma_lo' => $maLo,
            'nha_sx' => $request->nha_sx,
            'quoc_gia' => $request->quoc_gia,
            'plot' => $request->plot,
            'nam_trong' => $request->nam_trong,
            'chi_tieu' => $request->chi_tieu,
            'dien_tich' => $request->dien_tich,
            'tapping_y' => $request->tapping_y,
            'repl_time' => $request->repl_time,
            'find' => $request->find,
            'webmap' => $request->webmap,
            'gwf' => $request->gwf,
            'xa' => $request->xa,
            'huyen' => $request->huyen,
            'nguon_goc_lo' => $request->nguon_goc_lo,
            'nguon_goc_dat' => $request->nguon_goc_dat,
            'hang_dat' => $request->hang_dat,
            'hien_trang' => $request->hien_trang,
            'layer' => $request->layer,
            'chu_thich' => $request->chu_thich,
            'x' => $request->x,
            'y' => $request->y,
            'geo' => $request->geo,
            'pdf' => isset($pdfPath) ? $pdfPath : $plantingArea->pdf,
        ]);
        $farmName = optional($farm)->farm_name ?? 'N/A';

        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'Cập Nhật',
            'model_type' => 'Khu Vực Trồng',
            'details' => "Cập nhật khu trồng: 
            Cũ - Mã lô: {$oldPlantingArea['ma_lo']} | Năm trồng: {$oldPlantingArea['nam_trong']} | Find: {$oldPlantingArea['find']} 
            Mới - Mã lô: $maLo | Năm trồng: {$request->nam_trong} | Find: {$request->find}"
        ]);
        return redirect()->route('plantingareas.index')->with('message', 'Cập nhật khu vực trồng thành công');
    }
    public function destroy($id)
    {
        $plantingAreas = PlantingArea::find($id);
        $plantingAreas->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }

    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $plantingAreas = PlantingArea::whereIn('id', $request->ids)->get();
        foreach ($plantingAreas as $plantingArea) {
            if ($plantingArea->ingredients()->exists()) {
                return response()->json([
                    'message' => 'Không thể xóa khu vực trồng "' . $plantingArea->ma_lo . '" vì đã có thông tin nguyên liệu liên kết.',
                    'deleted_ids' => $request->ids
                ], 400);
            }
        }
        foreach ($plantingAreas as $plantingArea) {
            if ($plantingArea->pdf && file_exists(public_path($plantingArea->pdf))) {
                unlink(public_path($plantingArea->pdf));
            }
            $farmName = optional($plantingArea->farm)->farm_name ?? 'N/A';
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'Xóa',
                'model_type' => 'Khu Vực Trồng',
                'details' => 'Xóa khu vực trồng - Mã lô: ' . $plantingArea->ma_lo .
                    ', Năm trồng: ' . $plantingArea->nam_trong .
                    ', Nông trường: ' . $farmName .
                    ', Find: ' . $plantingArea->find,
            ]);
        }
        PlantingArea::whereIn('id', $request->ids)->delete();

        return response()->json([
            'message' => 'Xóa thành công khu vực trồng đã chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
}
