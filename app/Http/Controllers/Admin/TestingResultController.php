<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Batch;
use App\Models\TestingResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class TestingResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ranks = TestingResult::whereHas('batch', function ($query) {
            $query->where('status', 1);
        })->select('rank')->distinct()->pluck('rank');
        // dd($ranks);
        return view("testing.index", compact('ranks'));
    }

    public function indexUntested()
    {
        return view("testing.untested");
    }

    public function getData(Request $request)
    {
        $contracts = Batch::where('status', 1);
        // $ranks = TestingResult::select('rank')->distinct()->pluck('rank');

        // dd($contracts->get());

        // if ($request->has('factory') && $request->factory) {
        //     $contracts->whereHas('ingredients', function ($query) use ($request) {
        //         $query->where('receiving_factory', $request->factory);
        //     });
        // }

        if ($request->has('rank') && $request->rank) {
            $contracts->whereHas('testingResult', function ($query) use ($request) {
                $query->where('rank', $request->rank);
            });
        }

        // if ($request->has('type') && $request->type) {
        //     $contracts->where('type', $request->type);
        // }

        if ($request->has('day') || $request->has('month') || $request->has('year')) {
            $contracts->where(function ($query) use ($request) {

                if ($request->has('year') && $request->year) {
                    $query->whereHas('testingResult', function ($relation) use ($request) {
                        $relation->whereYear('ngay_kiem_nghiem', $request->year);
                    });
                }

                if ($request->has('month') && $request->month) {
                    $query->whereHas('testingResult', function ($relation) use ($request) {
                        $relation->whereMonth('ngay_kiem_nghiem', $request->month);
                    });
                }

                if ($request->has('day') && $request->day) {
                    $query->whereHas('testingResult', function ($relation) use ($request) {
                        $relation->whereDay('ngay_kiem_nghiem', $request->day);
                    });
                }
            });
        }


        $result = $contracts->get();

        return DataTables::of($result)
            ->addIndexColumn()
            ->addColumn('loai_mu', function ($row) {
                return $row->ingredients?->first()->typeOfPus?->name_pus ?? '';
            })
            ->addColumn('ngay_gui_mau', function ($row) {
                return $row->testingResult ? \Carbon\Carbon::parse($row->testingResult->ngay_gui_mau)->format('d/m/Y') : "";
            })
            ->addColumn('ngay_kiem_nghiem', function ($row) {
                return $row->testingResult ? \Carbon\Carbon::parse($row->testingResult->ngay_kiem_nghiem)->format('d/m/Y') : "";
            })
            ->addColumn('factory', function ($row) {

                // dd($row->ingredients);
                return $row->ingredients->first()->receiving_factory ?? 'N/A';
            })
            ->addColumn('rank', function ($row) {
                return $row->testingResult ? $row->testingResult->rank : "";
            })
            ->addColumn('actions', function ($row) {
                return '
                    <a href="' . route('testing.edit', $row->id) . '" class="edit-btn"><i class="fa-solid fa-pencil"></i></a>
                    <form action="' . route('testing.destroy', $row->id) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="delete-btn text-danger" style="border:none;background: none;" onclick="return confirm(\'Bạn có chắc chắn muốn xóa?\')"><i class="fa-sharp fa-regular fa-trash"></i></button>
                    </form>
                ';
            })
            ->make(true);
    }

    public function getDataUntested(Request $request)
    {

        // dd($request->all());
        // $contracts = Batch::where('status', 0);
        $contracts = Batch::with('ingredients')
            ->where('status', 0) // Lô chưa kiểm nghiệm
            ->whereHas('ingredients'); // Có nguyên liệu gắn vào
        // ->get();


        // if ($request->has('factory') && $request->factory) {
        //     $contracts->whereHas('ingredients', function ($query) use ($request) {
        //         $query->where('receiving_factory', $request->factory);
        //     });
        // }

        if ($request->has('day') || $request->has('month') || $request->has('year')) {
            $contracts->where(function ($query) use ($request) {
                if ($request->has('year') && $request->year) {
                    $query->whereYear('date_sx', $request->year);
                }
                if ($request->has('month') && $request->month) {
                    $query->whereMonth('date_sx', $request->month);
                }
                if ($request->has('day') && $request->day) {
                    $query->whereDay('date_sx', $request->day);
                }
            });
        }

        $result = $contracts->get();

        return DataTables::of($result)
            ->addIndexColumn()
            ->addColumn('checked', function ($row) {

                return $row->status == 0 ? "Chưa kiểm nghiệm" : "Đã kiểm nghiệm";
            })
            ->addColumn('loai_mu', function ($row) {

                // dd($row->ingredients);
                return $row->ingredients?->first()->typeOfPus?->name_pus ?? '';
            })
            ->addColumn('date_sx', function ($row) {
                return $row->date_sx ? \Carbon\Carbon::parse($row->date_sx)->format('d/m/Y') : "";
            })
            ->addColumn('factory', function ($row) {

                // dd($row->ingredients);
                return $row->ingredients->first()->receiving_factory ?? 'N/A';
            })
            ->addColumn('actions', function ($row) {
                return '
                    <a href="' . route('showun', $row->id) . '" class="edit-btn"><i class="fa-solid fa-pencil"></i></a>

                ';
            })
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $batch = Batch::findOrFail($request->id);
        // if ($request->ngay_gui_mau && $request->ngay_kiem_nghiem) {
        //     $harvestingDate = \Carbon\Carbon::parse($request->ngay_gui_mau);
        //     $endHarvestDate = \Carbon\Carbon::parse($request->ngay_kiem_nghiem);
        //     $today = \Carbon\Carbon::today();
        //     // Ngày cạo phải nhỏ hơn hoặc bằng ngày kết thúc thu cạo
        //     if ($harvestingDate->greaterThan($today)) {
        //         return redirect()->back()->with(['error' => 'Ngày gửi mẫu không được lớn hơn ngày hiện tại.']);
        //     }
        //     if ($endHarvestDate->greaterThan($today)) {
        //         return redirect()->back()->with(['error' => 'Ngày kiểm nghiệm không được lớn hơn ngày hiện tại.']);
        //     }
        //     if ($harvestingDate->greaterThan($endHarvestDate)) {
        //         return redirect()->back()->with(['error' => 'Ngày gửi mẫu không được lớn hơn ngày kiểm nghiệm.']);
        //     }
        // }
        $today = \Carbon\Carbon::today()->startOfDay();
        $ngay_gui_mau = $request->ngay_gui_mau ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->ngay_gui_mau)->format('Y-m-d') : null;
        $ngay_kiem_nghiem = $request->ngay_kiem_nghiem ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->ngay_kiem_nghiem)->format('Y-m-d') : null;
        if ($ngay_gui_mau && \Carbon\Carbon::parse($ngay_gui_mau)->greaterThan($today)) {
            return redirect()->back()->with(['error' => 'Ngày gửi mẫu không được lớn hơn ngày hiện tại.'])->withInput();
        }

        if ($ngay_kiem_nghiem && \Carbon\Carbon::parse($ngay_kiem_nghiem)->greaterThan($today)) {
            return redirect()->back()->with(['error' => 'Ngày kiểm nghiệm không được lớn hơn ngày hiện tại.'])->withInput();
        }

        // Kiểm tra ngày gửi mẫu không được lớn hơn ngày kiểm nghiệm
        if ($ngay_gui_mau && $ngay_kiem_nghiem) {
            if (\Carbon\Carbon::parse($ngay_gui_mau)->greaterThan(\Carbon\Carbon::parse($ngay_kiem_nghiem))) {
                return redirect()->back()->with(['error' => 'Ngày gửi mẫu không được lớn hơn ngày kiểm nghiệm.'])->withInput();
            }
        }
        // $validator = Validator::make($request->all(), [
        //     'svr_impurity' => 'numeric',
        //     'svr_ash' => 'numeric',
        //     'svr_volatile' => 'numeric',
        //     'svr_nitrogen' => 'numeric',
        //     'svr_po' => 'numeric',
        //     'svr_pri' => 'numeric',
        //     'svr_color' => 'numeric',
        //     'svr_vr' => 'numeric',
        // ], [
        //     'svr_impurity.numeric' => 'Chất bẩn phải là số.',
        //     'svr_ash.numeric' => 'Tro phải là số.',
        //     'svr_volatile.numeric' => 'Bay hơi phải là số.',
        //     'svr_nitrogen.numeric' => 'Nito phải là số.',
        //     'svr_po.numeric' => 'PO phải là số.',
        //     'svr_pri.numeric' => 'PRI phải là số.',
        //     'svr_color.numeric' => 'Color phải là số.',
        //     'svr_vr.numeric' => 'VR phải là số.',
        // ]);

        // if ($validator->fails()) {
        //     return redirect()->back()->with(['error' => $validator->errors()->all()]);
        // }

        $batch->update([
            'type' => $request->type,
            'status' => 1
        ]);

        // $testingResult = new TestingResult;
        TestingResult::create([
            'ngay_gui_mau' => $ngay_gui_mau,
            'batch_id' => $request->id,
            'rank' => $request->rank,
            'ngay_kiem_nghiem' => $ngay_kiem_nghiem,
            'svr_impurity' => $request->svr_impurity,
            'svr_ash' => $request->svr_ash,
            'svr_volatile' => $request->svr_volatile,
            'svr_nitrogen' => $request->svr_nitrogen,
            'svr_po' => $request->svr_po,
            'svr_pri' => $request->svr_pri,
            'svr_width' => $request->svr_width,
            'svr_viscous' => $request->svr_viscous,
            'svr_vul' => $request->svr_vul,
            'svr_color' => $request->svr_color,
            'svr_vr' => $request->svr_vr,
            'latex_tsc' => $request->latex_tsc,
            'latex_drc' => $request->latex_drc,
            'latex_nrs' => $request->latex_nrs,
            'latex_nh3' => $request->latex_nh3,
            'latex_mst' => $request->latex_mst,
            'latex_vfa' => $request->latex_vfa,
            'latex_koh' => $request->latex_koh,
            'latex_ph' => $request->latex_ph,
            'latex_coagulant' => $request->latex_coagulant,
            'latex_residue' => $request->latex_residue,
            'latex_mg' => $request->latex_mg,
            'latex_mn' => $request->latex_mn,
            'latex_cu' => $request->latex_cu,
            'latex_acid_boric' => $request->latex_acid_boric,
            'latex_surface_tension' => $request->latex_surface_tension,
            'latex_viscosity' => $request->latex_viscosity,
            'rss_impurity' => $request->rss_impurity,
            'rss_ash' => $request->rss_ash,
            'rss_volatile' => $request->rss_volatile,
            'rss_nitrogen' => $request->rss_nitrogen,
            'rss_po' => $request->rss_po,
            'rss_pri' => $request->rss_pri,
            'rss_vr' => $request->rss_vr,
            'rss_aceton' => $request->rss_aceton,
            'rss_tensile_strength' => $request->rss_tensile_strength,
            'rss_elongation' => $request->rss_elongation,
            'rss_vulcanization' => $request->rss_vulcanization,
        ]);
        $batchCode = $batch->batch_code;
        // dd($batchCode);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'Kiểm Nghiệm',
            'model_type' => 'Kết Quả Kiểm Nghiệm',
            'details' => "Tạo kết quả kiểm nghiệm cho lô hàng: {$batchCode} với các thông số: 
                Ngày gửi mẫu: {$ngay_gui_mau}, 
                Ngày kiểm nghiệm: {$ngay_kiem_nghiem}, 
                Hạng: {$request->rank}, 
                Tạp Chất: {$request->svr_impurity}, 
                Tro: {$request->svr_ash}, 
                Bay Hơi: {$request->svr_volatile}, 
                Nitơ: {$request->svr_nitrogen}, 
                Po: {$request->svr_po}, 
                PRI: {$request->svr_pri}, 
                Độ Nhớt: {$request->svr_viscous}, 
                Lưu Hóa: {$request->svr_vul}, 
                Màu: {$request->svr_color}, 
                Lovibond: {$request->svr_vr}"
        ]);
        return redirect()->route('untested')->with('message', 'Cập nhật kiểm nghiệm thành công');
    }

    /**
     * Display the specified resource.
     */
    public function showun(string $id)
    {
        $batch = Batch::findOrFail($id);
        return view("testing.create", compact('batch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $batch = Batch::findOrFail($id);
        return view("testing.edit", compact('batch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        // Tìm Batch theo ID
        $batch = Batch::findOrFail($id);
        if ($batch->orderExport) {
            // Nếu lô đã được gắn vào lệnh xuất hàng
            return redirect()->route('testing.index')->with('error', 'Lô đã được gắn vào mã lệnh xuất hàng, không thể chỉnh sửa!');
        }
        // Tìm TestingResult liên kết với Batch này
        $testingResult = TestingResult::where('batch_id', $id)->first();

        // $today = \Carbon\Carbon::today();

        // if ($request->ngay_gui_mau && \Carbon\Carbon::parse($request->ngay_gui_mau)->greaterThan($today)) {
        //     return redirect()->back()->with(['error' => 'Ngày gửi mẫu không được lớn hơn ngày hiện tại.']);
        // }

        // if ($request->ngay_kiem_nghiem && \Carbon\Carbon::parse($request->ngay_kiem_nghiem)->greaterThan($today)) {
        //     return redirect()->back()->with(['error' => 'Ngày kiểm nghiệm không được lớn hơn ngày hiện tại.']);
        // }

        // if ($request->ngay_gui_mau && $request->ngay_kiem_nghiem) {
        //     $harvestingDate = \Carbon\Carbon::parse($request->ngay_gui_mau);
        //     $endHarvestDate = \Carbon\Carbon::parse($request->ngay_kiem_nghiem);
        //     if ($harvestingDate->greaterThan($endHarvestDate)) {
        //         return redirect()->back()->with(['error' => 'Ngày gửi mẫu không được lớn hơn ngày kiểm nghiệm.']);
        //     }
        // }
        $today = \Carbon\Carbon::today()->startOfDay();
        $ngay_gui_mau = $request->ngay_gui_mau ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->ngay_gui_mau)->format('Y-m-d') : null;
        $ngay_kiem_nghiem = $request->ngay_kiem_nghiem ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->ngay_kiem_nghiem)->format('Y-m-d') : null;

        if ($ngay_gui_mau && \Carbon\Carbon::parse($ngay_gui_mau)->greaterThan($today)) {
            return redirect()->back()->with(['error' => 'Ngày gửi mẫu không được lớn hơn ngày hiện tại.']);
        }

        if ($ngay_kiem_nghiem && \Carbon\Carbon::parse($ngay_kiem_nghiem)->greaterThan($today)) {
            return redirect()->back()->with(['error' => 'Ngày kiểm nghiệm không được lớn hơn ngày hiện tại.']);
        }

        // Kiểm tra ngày gửi mẫu không được lớn hơn ngày kiểm nghiệm
        if ($ngay_gui_mau && $ngay_kiem_nghiem) {
            if (\Carbon\Carbon::parse($ngay_gui_mau)->greaterThan(\Carbon\Carbon::parse($ngay_kiem_nghiem))) {
                return redirect()->back()->with(['error' => 'Ngày gửi mẫu không được lớn hơn ngày kiểm nghiệm.']);
            }
        }

        $oldTestingResult = $testingResult->getAttributes();
        // Cập nhật thông tin chung của TestingResult
        $testingResult->ngay_gui_mau = $ngay_gui_mau;
        $testingResult->rank = $request->rank;
        $testingResult->ngay_kiem_nghiem = $ngay_kiem_nghiem;

        // Cập nhật thông tin kiểm nghiệm tùy theo loại kiểm nghiệm
        if ($batch->type == 'svr') {
            $testingResult->svr_impurity = $request->svr_impurity;
            $testingResult->svr_ash = $request->svr_ash;
            $testingResult->svr_volatile = $request->svr_volatile;
            $testingResult->svr_nitrogen = $request->svr_nitrogen;
            $testingResult->svr_po = $request->svr_po;
            $testingResult->svr_pri = $request->svr_pri;
            $testingResult->svr_width = $request->svr_width;
            $testingResult->svr_viscous = $request->svr_viscous;
            $testingResult->svr_vul = $request->svr_vul;
            $testingResult->svr_color = $request->svr_color;
            $testingResult->svr_vr = $request->svr_vr;
        } elseif ($batch->type == 'latex') {
            $testingResult->latex_tsc = $request->latex_tsc;
            $testingResult->latex_drc = $request->latex_drc;
            $testingResult->latex_nrs = $request->latex_nrs;
            $testingResult->latex_nh3 = $request->latex_nh3;
            $testingResult->latex_mst = $request->latex_mst;
            $testingResult->latex_vfa = $request->latex_vfa;
            $testingResult->latex_koh = $request->latex_koh;
            $testingResult->latex_ph = $request->latex_ph;
            $testingResult->latex_coagulant = $request->latex_coagulant;
            $testingResult->latex_residue = $request->latex_residue;
            $testingResult->latex_mg = $request->latex_mg;
            $testingResult->latex_mn = $request->latex_mn;
            $testingResult->latex_cu = $request->latex_cu;
            $testingResult->latex_acid_boric = $request->latex_acid_boric;
            $testingResult->latex_surface_tension = $request->latex_surface_tension;
            $testingResult->latex_viscosity = $request->latex_viscosity;
        } elseif ($batch->type == 'rss') {
            $testingResult->rss_impurity = $request->rss_impurity;
            $testingResult->rss_ash = $request->rss_ash;
            $testingResult->rss_volatile = $request->rss_volatile;
            $testingResult->rss_nitrogen = $request->rss_nitrogen;
            $testingResult->rss_po = $request->rss_po;
            $testingResult->rss_pri = $request->rss_pri;
            $testingResult->rss_vr = $request->rss_vr;
            $testingResult->rss_aceton = $request->rss_aceton;
            $testingResult->rss_tensile_strength = $request->rss_tensile_strength;
            $testingResult->rss_elongation = $request->rss_elongation;
            $testingResult->rss_vulcanization = $request->rss_vulcanization;
        }

        $testingResult->save();
        $logDetails = "Cập nhật kết quả kiểm nghiệm cho lô hàng mã: {$batch->batch_code}. ";
        foreach ($testingResult->getAttributes() as $key => $newValue) {
            $oldValue = $oldTestingResult[$key] ?? null;
            if ($oldValue !== $newValue) {
                $logDetails .= "Dữ liệu '{$key}': Cũ - '{$oldValue}' -> Mới - '{$newValue}'. ";
            }
        }

        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'Cập Nhật Kết Quả Đã Kiểm Nghiệm',
            'model_type' => 'Kết Quả Kiểm Nghiệm',
            'details' => $logDetails
        ]);

        return redirect()->route('testing.index')->with('message', 'Cập nhật kiểm nghiệm thành công');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $batch = Batch::findOrFail($id);

        // if ($batch->testingResult) {
        //     // Kiểm tra nếu lô đã có kết quả kiểm nghiệm
        //     return redirect()->route('testing.index')->with('error', 'Lô đã kiểm nghiệm, không thể xóa!');
        // }

        // Kiểm tra nếu lô đã được gắn vào lệnh xuất hàng
        if ($batch->orderExport) {
            // Nếu lô đã được gắn vào lệnh xuất hàng
            return redirect()->route('testing.index')->with('error', 'Lô đã được gắn vào mã lệnh xuất hàng, không thể xóa!');
        }

        // Cập nhật trạng thái lô và lưu lại
        $batch->status = 0;
        $batch->save();

        // Nếu có kết quả kiểm nghiệm, xóa nó
        if ($batch->testingResult) {
            $batch->testingResult->delete();
        }

        // Trả về thông báo thành công
        return redirect()->route('testing.index')->with('message', 'Xóa thành công!');
    }

    public function destroy_old(string $id)
    {
        $batch = Batch::findOrFail($id);

        $batch->status = 0;

        $batch->save();

        $batch->testingResult->delete();

        return redirect()->route('testing.index')->with('message', 'Xóa thành công!');
    }

    public function testing(string $id)
    {
        $batch = Batch::find($id);

        if ($batch) {
            if ($batch->status == 1 && $batch->testingResult) {
                return response()->json([
                    'success' => true,
                    'data' => $batch,
                    'message' => 'Lô đã kiểm nghiệm.'
                ]);
            } elseif ($batch->status == 0) {
                return response()->json([
                    'success' => false,
                    'data' => 'Chưa có dữ liệu',
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'data' => 'Không tìm thấy !',
        ]);
    }
    public function importFiles()
    {
        return view('testing.files');
    }
}
