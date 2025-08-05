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
                // ->editColumn('farm_name', function ($row) {
                //     return '<a href="/plots/edit/' . $row->id . '">' . $row->farm_name . '</a>';
                // })
                ->addColumn('gardenID', function ($row) {
                    return $row->garden ? $row->garden->gardenName : 'N/A';
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
        return view('plots.all_plots', compact('gardens'));
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
        $request->validate([
            'plotCode' => 'required',
            'plotName' => 'required',
            'plotArea' => 'required',
            'year' => 'required',
            'statusTree' => 'required',
            'mapJs' => 'required',
            'status' => 'nullable',
        ], [
            'plotCode.required' => 'Mã cây trồng là bắt buộc.',
            'plotName.required' => 'Tên lô là bắt buộc.',
            'plotArea.required' => 'Diện tích lô là bắt buộc.',
            'year.required' => 'Năm là bắt buộc.',
            'statusTree.required' => 'Trạng thái cây trồng là bắt buộc.',
            'mapJs.required' => 'Bản đồ là bắt buộc.',
        ]);
        $existingCode = Plot::where('plotName', $request->plotName)->first();

        if ($existingCode) {
            return redirect()->back()->with(['error' => 'Lô này đã tồn tại!']);
        }

        Plot::create([
            'plotCode' => $request->plotCode,
            'plotName' => $request->plotName,
            'plotArea' => $request->plotArea,
            'mapJs' => $request->mapJs,
            'statusTree' => $request->statusTree,
            'year' => $request->year,
            'status' => $request->status ?? 'Hoạt động',
        ]);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'create',
            'model_type' => 'Plot',
            'details' => "Đã tạo lô: " . $request->plotName . " với mã: " . $request->plotCode,
        ]);
        return redirect()->route('plots.index')->with('message', 'Tạo lô thành công.');
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
        $request->validate([
            'plotCode' => 'required',
            'plotName' => 'required',
            'plotArea' => 'required',
            'year' => 'required',
            'statusTree' => 'required',
            'mapJs' => 'required',
            'status' => 'nullable',
        ]);

        $plot = Plot::find($id);

        if (!$plot) {
            return redirect()->back()->with('error', 'Lô không tồn tại');
        }

        // Cập nhật thông tin
        $plot->update([
            'plotCode' => $request->plotCode,
            'plotName' => $request->plotName,
            'plotArea' => $request->plotArea,
            'statusTree' => $request->statusTree,
            'year' => $request->year,
            'status' => $request->status,
            'mapJs' => $request->mapJs,
        ]);

        session()->flash('message', 'Cập nhật lô thành công.');

        return redirect()->route('plots.index'); // Redirect về danh sách hoặc trang thích hợp
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

        foreach ($plots as $farm) {
            $farm->status = ($farm->status === 'Hoạt động') ? 'Không hoạt động' : 'Hoạt động';
            $farm->save();
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

        foreach ($plotsToDelete as $farm) {
            ActionHistory::create([
                'user_id' => Auth::id(),  // ID của người thực hiện hành động
                'action_type' => 'delete',  // Loại hành động "delete"
                'model_type' => 'Plot',  // Model "Plot"
                'details' => "Đã xóa lô: " . $farm->farm_name . " với mã: " . $farm->farm_code,
            ]);
        }
        return response()->json([
            'message' => 'Xóa thành công các lô được chọn.',
            'deleted_ids' => $request->ids
        ]);
    }
    public function toggleStatus(Request $request)
    {
        $farm = Plot::find($request->id);
        if ($farm) {
            $farm->status = $farm->status == 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';
            $farm->save();
            return response()->json(['success' => true, 'status' => $farm->status]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
