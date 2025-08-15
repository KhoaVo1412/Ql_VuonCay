<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\Garden;
use App\Models\Plot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class PlotController extends Controller
{
    public function index(Request $request)
    {
        $gardens = Garden::all();
        $all_plots = Plot::with('garden')->withCount('plants')->orderBy('id', 'desc')->get();
        // dd($all_plots);
        if ($request->ajax()) {
            return DataTables::of($all_plots)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('plotName', function ($row) {
                    return $row->plotName;
                })
                ->editColumn('plotName', function ($row) {
                    return $row->plotName;
                })
                ->addColumn('plants_count', function ($row) {
                    return $row->plants_count ?? '';
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status == 'Hoạt động' ? 'success' : 'danger';
                    $statusText = $row->status == 'Hoạt động' ? 'Hoạt động' : 'Không hoạt động';
                    return '<button class="badge bg-' . $statusClass . ' toggle-status" data-id="' . $row->id . '">' . $statusText . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                       <button class="btn btn-info btn-sm view-map" data-id_plot="' . $row->id . '">
                            <i class="fas fa-map-marker-alt"></i> Map
                        </button>
                            <a href="/edit-plots/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        Bạn có chắc chắn có muốn xóa thông tin <span style="color: red;">' . ($row->plotName ?? 'N/A') . '</span>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/plots/delete/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'stt', 'plotName', 'gardenID', 'plotArea', 'plants_count', 'status', 'action'])
                ->make(true);
        }
        return view('plots.all_plots', compact('gardens', 'all_plots'));
    }
    public function add()
    {
        // $gardens = Garden::all();
        $totalPlants = Plot::withCount('plants')->get()->sum('plants_count');

        return view('plots.add_plots', compact('totalPlants'));
    }
    public function save(Request $request)
    {
        // dd($request->all());
        try {
            $validated = $request->validate([
                'plotCode' => 'required',
                'plotName' => 'required',
                'mapJs' => 'required',
                'status' => 'nullable',
                'fid' => 'nullable|integer',
                'idmap' => 'nullable|string|max:255',
                'year' => 'nullable|integer|min:1900|max:' . date('Y'),
                'chi_tieu' => 'nullable|string|max:255',
                'plotArea' => 'nullable|numeric|min:0',
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
                'mapJs' => 'required',
            ], [
                'plotCode.required' => 'Mã cây trồng là bắt buộc.',
                'plotName.required' => 'Tên lô là bắt buộc.',
                'plotArea.required' => 'Diện tích lô là bắt buộc.',
                'year.required' => 'Năm là bắt buộc.',
                'mapJs.required' => 'Bản đồ là bắt buộc.',
                'fid.integer' => 'Fid phải là một số nguyên.',
                'idmap.max' => 'ID Map không được vượt quá 255 ký tự.',
                'year.integer' => 'Năm trồng phải là một số nguyên.',
                'year.min' => 'Năm trồng phải từ 1900 trở lên.',
                'year.max' => 'Năm trồng không được vượt quá năm hiện tại (' . date('Y') . ').',
                'chi_tieu.max' => 'Chỉ tiêu không được vượt quá 255 ký tự.',
                'plotArea.numeric' => 'Diện tích phải là một số.',
                'plotArea.min' => 'Diện tích không được nhỏ hơn 0.',
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
            ]);
            $existingCode = Plot::where('plotName', $request->plotName)->first();

            if ($existingCode) {
                return redirect()->back()->with(['error' => 'Lô này đã tồn tại!']);
            }
            $currentYear = date('Y');
            if ($request->year > $currentYear) {
                return redirect()->back()->with('error', 'Năm trồng không được lớn hơn năm hiện tại.')->withInput();
            }
            $plotCode = $request->year . '.' . $request->plotName . '.' . $request->find;
            $validated['plotCode'] = $plotCode;
            $validated['status'] = 'Hoạt động';

            Plot::create($validated);
            ActionHistory::create([
                'user_id' => Auth::id(),
                'action_type' => 'Tạo',
                'model_type' => 'Khu Vực Trồng',
                'details' => 'Tạo khu vực trồng mới: Mã lô = ' . $plotCode .
                    ', Tên = ' . $request->plotName .
                    ', Năm trồng = ' . $request->year .
                    ', Find = ' . $request->find,
            ]);
            return redirect()->route('plots.index')->with('message', 'Tạo lô thành công.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Có lỗi xảy ra khi tạo lô: ' . $e->getMessage()])
                ->withInput();
        }
    }
    public function edit($id)
    {
        // $gardens = Garden::all();
        $plots = Plot::find($id);
        $totalPlants = $plots->plants()->count();
        return view('plots.edit_plots', compact('plots', 'totalPlants'));
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'plotCode' => 'required',
            'plotName' => 'required',
            'mapJs' => 'required',
            'status' => 'nullable',
            'fid' => 'nullable|integer',
            'idmap' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'chi_tieu' => 'nullable|string|max:255',
            'plotArea' => 'nullable|numeric|min:0',
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
        ], [
            'plotCode.required' => 'Mã cây trồng là bắt buộc.',
            'plotName.required' => 'Tên lô là bắt buộc.',
            'plotArea.required' => 'Diện tích lô là bắt buộc.',
            'year.required' => 'Năm là bắt buộc.',
            'mapJs.required' => 'Bản đồ là bắt buộc.',
            'fid.integer' => 'Fid phải là một số nguyên.',
            'idmap.max' => 'ID Map không được vượt quá 255 ký tự.',
            'year.integer' => 'Năm trồng phải là một số nguyên.',
            'year.min' => 'Năm trồng phải từ 1900 trở lên.',
            'year.max' => 'Năm trồng không được vượt quá năm hiện tại (' . date('Y') . ').',
            'chi_tieu.max' => 'Chỉ tiêu không được vượt quá 255 ký tự.',
            'plotArea.numeric' => 'Diện tích phải là một số.',
            'plotArea.min' => 'Diện tích không được nhỏ hơn 0.',
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
        ]);

        $plot = Plot::find($id);

        if (!$plot) {
            return redirect()->back()->with('error', 'Lô không tồn tại');
        }
        $plotCode = $request->year . '.' . $request->plotName . '.' . $request->find;
        $plot->update([
            'plotCode' => $plotCode,
            'fid' => $request->fid,
            'idmap' => $request->idmap,
            'nha_sx' => $request->nha_sx,
            'plotName' => $request->plotName,
            'year' => $request->year,
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
            'mapJs' => $request->mapJs,
        ]);
        $oldPlot = $plot->getOriginal();

        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'Cập Nhật',
            'model_type' => 'Khu Vực Trồng',
            'details' => "Cập nhật khu trồng: 
            Cũ - Mã lô: {$oldPlot['llotCode']} | Năm trồng: {$oldPlot['year']} | Find: {$oldPlot['find']} 
            Mới - Mã lô: $plotCode | Năm trồng: {$request->year} | Find: {$request->find}"
        ]);
        return redirect()->route('plots.index')->with('message', 'Cập nhật lô thành công.');
    }

    public function destroy($id)
    {
        $plots = Plot::find($id);
        $plots->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function editMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $plots = Plot::whereIn('id', $request->ids)->get();

        foreach ($plots as $p) {
            $p->status = ($p->status === 'Hoạt động') ? 'Không hoạt động' : 'Hoạt động';
            $p->save();
        }
        return response()->json(['message' => 'Thành Công']);
    }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        $plotsToDelete = Plot::whereIn('id', $request->ids)->get();

        Plot::whereIn('id', $request->ids)->delete();

        foreach ($plotsToDelete as $p) {
            ActionHistory::create([
                'user_id' => Auth::id(),  // ID của người thực hiện hành động
                'action_type' => 'delete',  // Loại hành động "delete"
                'model_type' => 'Plot',  // Model "Plot"
                'details' => "Đã xóa lô: " . $p->plotName . " với mã: " . $p->plotCode,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các lô được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $plot = Plot::find($request->id);
        if ($plot) {
            $plot->status = $plot->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $plot->save();
            return response()->json(['success' => true, 'status' => $plot->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
