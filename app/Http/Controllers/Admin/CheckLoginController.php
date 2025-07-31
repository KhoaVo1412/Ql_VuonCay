<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;

class CheckLoginController extends Controller
{
    public function index(Request $request)
    {
        $checkLogin = LoginHistory::with('user');
        // dd($query);
        $filteredUsers = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'Khách Hàng');
        })->get();
        if ($request->ajax()) {
            if ($request->filled('user_id')) {
                $checkLogin->where('user_id', $request->user_id);
            }
            return DataTables::of($checkLogin)
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
                ->editColumn('ip_address', function ($row) {
                    return $row->ip_address;
                })
                ->addColumn('login_at', function ($row) {
                    return $row->login_at ? Carbon::parse($row->login_at)->format('d/m/Y H:i:s')
                        : 'Không';
                })
                ->rawColumns(['check', 'stt', 'user_id', 'ip_address', 'login_at'])
                ->make(true);
        }
        return view('log-history.checkLogin', compact('filteredUsers'));
    }
    public function destroy(Request $request)
    {
        $item = LoginHistory::find($request->id);

        if ($item) {
            $item->delete();  // Xóa item
            return response()->json(['success' => true]);  // Trả về phản hồi thành công
        }

        return response()->json(['success' => false]);  // Trả về phản hồi thất bại nếu item không tồn tại
    }
}
