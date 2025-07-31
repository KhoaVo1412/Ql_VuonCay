<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;

class PermissionController extends Controller
{
    public function all_permissions(Request $request)
    {
        $data = Permission::query();

        if ($request->ajax()) {
            return DataTables::of($data)
                // ->addColumn('check', function ($row) {
                //     return '<input class="form-check-input" type="checkbox" id="' . $row->id . '">';
                // })
                ->addColumn('stt', function ($row) {
                    static $stt = 1;
                    return $stt++;
                })
                ->editColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('Action', function ($row) {
                    $action = '
                    <a href="/edit-permissions/' . $row->id . '" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-sm btn-danger btn-wave" type="button" data-bs-toggle="modal"
                    data-bs-target="#exampleModal' . $row->id . '">
                    <i class="fas fa-trash-alt"></i>
                </button>

                <div class="modal fade" id="exampleModal' . $row->id . '" tabindex="-1"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel1">Xác Nhận Xóa</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body" style="font-size:14px;text-align:center;">
                                Bạn chắc chắn muốn xóa quyền <span style="color: red;">' . $row->name . ' </span> ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Hủy</button>
                                <form action="' . route('delete.permissions', $row->id) . '" method="POST" style="display: inline;">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-primary">Xóa</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                ';
                    return $action;
                })
                ->rawColumns(['stt', 'name', 'Action'])
                ->make(true);
        }
        return view('role-permission.permission.all_permission', []);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $check_name = Permission::where('name', $data['name'])->first();
        if ($check_name) {
            Session::put('error', 'Quyền đã tồn tại.');
            return redirect()->back();
        }
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:permissions,name',
            ]
        ]);
        Permission::create([
            'name' => $request->name
        ]);
        Session::put('message', 'Thêm thành công');
        return redirect('/all-permissions');
    }
    public function show(Permission $permission)
    {
        return view('role-permission.permission.edit', [
            'permission' => $permission,
        ]);
    }
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:permissions,name,' . $permission->id
            ]
        ]);
        $data = $request->all();
        $check_name = Permission::where('name', $data['name'])->first();
        if ($check_name) {
            Session::put('error', 'Quyền đã tồn tại.');
            return redirect()->back();
        }
        try {
            $permission->update([
                'name' => $request->name
            ]);
            Session::put('message', 'Cập nhật thành công');
        } catch (\Exception $e) {
            Session::put('error', 'Có lỗi xảy ra khi cập nhật quyền.');
        }
        return redirect('/all-permissions');
    }
    public function delete($permission)
    {
        $permission = Permission::find($permission);
        if (!$permission) {
            Session::put('error', 'Có lỗi xảy ra khi cập nhật quyền.');
            return redirect()->back();
        }
        try {
            $permission->delete();
            Session::put('message', 'Xóa thành công.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::put('error', 'Có lỗi xảy ra khi cập nhật quyền.');
            return redirect('/all-permissions');
        }
    }
}
