<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
    // public function __construct()
    // {
    //     // $this->middleware('permission:view_role ', ['only' => ['all_roles']]);
    //     // $this->middleware('permission:create_role ', ['only' => ['store']]);
    //     $this->middleware('permission:edit_role', ['only' => ['show', 'update']]);
    //     $this->middleware('permission:delete_role', ['only' => ['delete']]);
    // }
    public function all_roles(Request $request)
    {
        $data = Role::query();

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('check', function ($row) {
                    return '<input class="form-check-input" type="checkbox" id="' . $row->id . '">';
                })
                ->addColumn('stt', function ($row) {
                    static $stt = 1;
                    return $stt++;
                })
                ->editColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('Action', function ($row) {
                    $action = '
                    <a class="btn btn-primary btn-sm mb-2" href="/give-permission/' . $row->id . '">
                        <i class="fa-solid fa-gear"></i>Cài đặt quyền
                    </a>
                    <a href="/edit-roles/' . $row->id . '">
                        <button class="btn btn-primary btn-icon btn-sm mb-2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                    </a>
                    <button class="btn btn-sm btn-danger btn-wave mb-2" type="button" data-bs-toggle="modal"
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
                                Bạn chắc chắn muốn xóa quyền <span style="color:red;">' . $row->name . ' </span>?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Hủy</button>
                                <form action="' . route('delete.roles', $row->id) . '" method="POST" style="display: inline;">
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
                ->rawColumns(['check', 'stt', 'name', 'Action'])
                ->make(true);
        }
        return view('role-permission.roles.all_roles', []);
    }
    public function store(Request $request)
    {
        $data = $request->all();
        $check_name = Role::where('name', $data['name'])->first();
        if ($check_name) {
            Session::put('error', 'Vai Trò đã tồn tại.');
            return redirect()->back();
        }
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:roles,name',
            ]
        ]);
        Role::create([
            'name' => $request->name
        ]);
        Session::put('message', 'Thêm vai trò thành công');
        return redirect('/all-roles');
    }

    public function show(Role $role)
    {
        return view('role-permission.roles.edit', [
            'role' => $role,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:roles,name,' . $role->id
            ]
        ]);
        $data = $request->all();

        $check_name = Role::where('name', $data['name'])->first();
        if ($check_name) {
            Session::put('error', 'Vai trò đã tồn tại.');
            return redirect()->back();
        }
        try {
            $role->update([
                'name' => $request->name
            ]);
            Session::put('message', 'Cập nhật thành công');
        } catch (\Exception $e) {
            Session::put('error', 'Có lỗi xảy ra khi cập nhật quyền.');
        }
        return redirect('/all-roles');
    }

    public function delete($role)
    {
        $role = Role::find($role);
        if (!$role) {
            Session::put('error', 'Có lỗi xảy ra khi cập nhật quyền.');
            return redirect('/all-roles');
        }

        try {
            $role->delete();
            Session::put('message', 'Xóa thành công.');
            return redirect('/all-roles');
        } catch (\Exception $e) {
            Session::put('error', 'Có lỗi xảy ra khi cập nhật quyền.');
            return redirect('/all-roles');
        }
    }

    public function addPermissionToRole($role)
    {
        $permissions = Permission::get();
        $role = Role::findOrFail($role);
        try {
            $rolePermissions = DB::table('role_has_permissions')
                ->where('role_has_permissions.role_id', $role->id)
                ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
                ->all();
            return view('role-permission.roles.addPermission', [
                'title' => 'Set role',
                'role' => $role,
                'permissions' => $permissions,
                'rolePermissions' => $rolePermissions,
            ]);
            return view('role-permission.roles.addPermission', [
                'title' => 'Set role',
                'role' => $role,
                'permissions' => $permissions,
                'rolePermissions' => $rolePermissions,
            ]);
        } catch (\Exception $e) {
            Session::put('error', 'Có lỗi xảy ra khi cập nhật quyền.');
            return redirect('/all-roles');
        }
    }

    public function postPermissionToRole(Request $request, $role)
    {
        $request->validate([
            'permission' => 'required'
        ]);
        $role = Role::findOrFail($role);
        $role->syncPermissions($request->permission);
        return redirect('/all-roles')->with('message', 'Cài Đặt Quyền Thành Công');
    }
}
