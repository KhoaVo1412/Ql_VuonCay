<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CheckActionController extends Controller
{
    public function index(Request $request)
    {
        $query = ActionHistory::query();        // dd($all_farms);
        $modelTypes = ActionHistory::select('model_type')
            ->distinct()
            ->pluck('model_type');
        if ($request->ajax()) {
            $date = $request->input('date') ?? now()->toDateString();
            $modelType = $request->input('model_type');

            $query->whereDate('created_at', $date);


            if ($modelType) {
                $query->where('model_type', $modelType);
            }
            return DataTables::of($query)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="check-' . $row->id . '" data-id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('user_id', function ($row) {
                    return $row->user ? $row->user->name : 'Trống';
                })
                ->editColumn('action_type', function ($row) {
                    switch ($row->action_type) {
                        case 'create':
                            return 'Tạo mới';
                        case 'update':
                            return 'Cập nhật';
                        case 'delete':
                            return 'Xóa';
                        default:
                            return $row->action_type;
                    }
                })
                ->addColumn('model_type', function ($row) {
                    return $row->model_type ? $row->model_type : 'Không';
                })
                ->addColumn('details', function ($row) {
                    return $row->details ? $row->details : 'Không';
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i:s') : 'Không';
                })
                ->rawColumns(['check', 'stt', 'user_id', 'action_type', 'model_type', 'details', 'created_at'])
                ->make(true);
        }
        return view('log-history.checkAction', compact('modelTypes'));
    }
}
