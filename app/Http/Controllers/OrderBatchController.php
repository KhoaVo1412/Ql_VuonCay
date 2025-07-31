<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Farm;
use App\Models\Ingredient;
use App\Models\OrderExport;
use App\Models\PlantingArea;
use App\Models\TypeOfPus;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class OrderBatchController extends Controller
{
    public function index(Request $request)
    {
        $all_orderbatchs = OrderExport::with('batches')->get();
        if ($request->ajax()) {
            return DataTables::of($all_orderbatchs)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                // ->addColumn('stt', function ($row) {
                //     static $stt = 0;
                //     $stt++;
                //     return $stt;
                // })
                ->editColumn('code', function ($row) {
                    return $row->code;
                })
                ->editColumn('batch_code', function ($row) {
                    $batchCodes = $row->batches->pluck('batch_code')->toArray();
                    return implode(', ', $batchCodes);
                })

                ->addColumn('action', function ($row) {
                    $action = '
                        <div class="d-flex gap-1">
                            <a href="/edit-orderbatchs/' . $row->id . '" class="btn btn-sm btn-primary">
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
                                        Bạn có chắc chắn có muốn xóa mã lệnh xuất hàng này ?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <a href="/delete-orderbatchs/' . $row->id . '" class="btn btn-primary">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['check', 'code', 'batch_code', 'action'])
                ->make(true);
        }
        return view('orderbatch.index');
    }
    public function add(Request $request)
    {
        $orderExportsWithBatches = OrderExport::whereHas('batches')->pluck('id')->toArray();

        $orderexport = OrderExport::whereNotIn('id', $orderExportsWithBatches)->get();

        $batchs = Batch::whereNotIn('id', Batch::whereIn('order_export_id', $orderExportsWithBatches)->pluck('id'))->get();

        return view('orderbatch.add', compact('orderexport', 'batchs'));
    }

    public function getBatch(Request $request)
    {
        $selectedIds = $request->input('ids', []);
        $batchs = Batch::whereIn('id', $selectedIds)->get(['id', 'batch_code']);
        return response()->json($batchs);
    }

    public function save(Request $request)
    {
        $orderExportId = $request->input('order_export_id');
        $batchIds = $request->input('batch_code', []);

        if (empty($batchIds)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một mã lô');
        }
        Batch::whereIn('id', $batchIds)->update(['order_export_id' => $orderExportId]);
        Session::put('message', 'Mã lệnh và mã lô hàng đã được lưu thành công!');
        // return redirect()->back()->with('message', 'Mã lệnh và mã lô đã được lưu thành công!');
        return view('orderbatch.index');
    }
    public function edit($id)
    {
        try {
            $orderexport = OrderExport::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('orderbatch.index')->with('error', 'Mã lệnh không tồn tại');
        }
        $batchs = Batch::all();
        $selectedBatches = $orderexport->batches->pluck('id')->toArray();

        return view('orderbatch.edit', compact('orderexport', 'batchs', 'selectedBatches'));
    }

    public function update(Request $request)
    {
        $orderExportId = $request->input('order_export_id');
        $batchIds = $request->input('batch_code', []);
        $request->validate([
            'order_export_id' => 'required|exists:order_exports,id',
            'batch_code' => 'nullable|array',
            'batch_code.*' => 'exists:batches,id',
        ]);

        $currentBatchIds = Batch::where('order_export_id', $orderExportId)->pluck('id')->toArray();

        $batchesToRemove = array_diff($currentBatchIds, $batchIds);

        if (!empty($batchesToRemove)) {
            Batch::whereIn('id', $batchesToRemove)->update(['order_export_id' => null]);
        }
        if (!empty($batchIds)) {
            Batch::whereIn('id', $batchIds)->update(['order_export_id' => $orderExportId]);
        }

        return redirect()->route('orderbatchs.index')->with('message', 'Cập nhật mã lô thành công!');
    }
    // public function edit($id)
    // {
    //     try {
    //         $orderexport = OrderExport::findOrFail($id);
    //     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         return redirect()->route('orderbatch.index')->with('error', 'Mã lệnh không tồn tại');
    //     }
    //     $batchs = Batch::all();
    //     $selectedBatches = $orderexport->batches->pluck('id')->toArray();

    //     return view('orderbatch.edit', compact('orderexport', 'batchs', 'selectedBatches'));
    // }

    // public function update(Request $request)
    // {
    //     $orderExportId = $request->input('order_export_id');
    //     $batchIds = $request->input('batchIds', []);
    //     $removedBatchId = $request->input('remove_code');

    //     if ($removedBatchId) {
    //         $reset = Batch::findOrFail($removedBatchId);
    //         if ($reset) {
    //             $reset->order_export_id = null;
    //             $reset->save();
    //         }
    //     }
    //     if (!empty($batchIds)) {
    //         Batch::whereIn('id', $batchIds)->update(['order_export_id' => $orderExportId]);
    //     }

    //     return response()->json(['success' => true, 'message' => 'Batches updated successfully.']);
    // }
    public function destroy($id)
    {
        $ingredients = OrderExport::find($id);
        $ingredients->delete();
        Session::put('message', 'Xóa thành công.');
        return redirect()->back();
    }
    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        $orderExports = OrderExport::whereIn('id', $request->ids)->get();
        $batchesWithOrderExports = [];
        foreach ($orderExports as $orderExport) {
            if ($orderExport->batches()->exists()) {
                $batches = $orderExport->batches->pluck('batch_code')->toArray();
                $batchesWithOrderExports[$orderExport->id] = $batches;
            }
        }
        if (count($batchesWithOrderExports) > 0) {
            $messages = [];
            foreach ($batchesWithOrderExports as $orderExportId => $batches) {
                $messages[] = 'Mã Lệnh Xuất Hàng này đã được gắn với các mã lô hàng: ' . implode(', ', $batches) . ', không được phép xóa.';
            }
            return response()->json([
                'message' => implode(' ', $messages),
                'error' => true
            ], 400);
        }
        OrderExport::whereIn('id', $request->ids)->delete();
        return response()->json([
            'message' => 'Xóa thành công mã lệnh xuất hàng.',
            'deleted_ids' => $request->ids
        ]);
    }

    public function deleteMultiple_old(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $orderExports = OrderExport::whereIn('id', $request->ids)->get();
        foreach ($orderExports as $orderExport) {
            if ($orderExport->batches()->exists()) {
                return response()->json([
                    'message' => 'Mã Lệnh Xuất Hàng đã được gắn Mã Lô, không được phép xóa.',
                    'error' => true
                ], 400);
            }
        }

        OrderExport::whereIn('id', $request->ids)->delete();

        return response()->json([
            'message' => 'Xóa thành công mã lệnh xuất hàng.',
            'deleted_ids' => $request->ids
        ]);
    }
}
