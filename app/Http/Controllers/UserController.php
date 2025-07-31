<?php

namespace App\Http\Controllers;

use App\Models\ActionHistory;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

use App\Models\User;
use Illuminate\Support\Facades\Session;
use OwenIt\Auditing\Models\Audit;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function all_users(Request $request)
    {
        // $x = Audit::select('old_values', 'new_values')->get();
        // return view('test', ['x' => $x]);
        $data = User::all();
        $user_email = User::select('email')->get();
        $roles = Role::pluck('name', 'name')->all();

        if ($request->has('filter_select') && $request->filter_select != null) {
            $data->where('email', $request->filter_select);
        }
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('stt', function ($row) {
                    static $stt = 0;
                    $stt++;
                    return $stt;
                })
                ->editColumn('name', function ($row) {
                    return $row->name;
                })
                ->editColumn('email', function ($row) {
                    return $row->email;
                })
                ->editColumn('password', function ($row) {
                    return $row->password;
                })
                ->editColumn('role', function ($row) {
                    $roles = $row->getRoleNames()->map(function ($rolename) {
                        return '<label class="btn btn-info mx-2">' . $rolename . '</label>';
                    })->join(' ');

                    return $roles;
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('edit.users', $row->id);

                    $actionBtn =
                        '<div class="d-flex">
                        <a href="' . $editUrl . '" style="padding: 0 2px 0 0;">
                            <button class="btn btn-primary btn-icon btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                        </a>
                    </a>
                    <button class="btn btn-sm btn-danger btn-wave" type="button" data-bs-toggle="modal"
                    data-bs-target="#exampleModal' . $row->id . '">
                    <i class="fas fa-trash-alt"></i>
                    </button>
                    </div>

                    <div class="modal fade" id="exampleModal' . $row->id . '" tabindex="-1"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel1">Xác nhận</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="font-size:14px;text-align:center;">
                                    Bạn chắc chắn muốn xóa tài khoản <span style="color: red">' . $row->name . ' </span>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <form action="' . route('delete.users', $row->id) . '" method="POST" style="display: inline;">
                                        ' . csrf_field() . '
                                        ' . method_field('DELETE') . '
                                        <button type="submit" class="btn btn-primary">Xóa</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    ';
                    return $actionBtn;
                })
                ->rawColumns(['stt', 'name', 'email', 'password', 'role', 'action'])
                ->make(true);
        }
        return view('role-permission.users.list', [
            'user_email' => $user_email,
            'roles' => $roles,
        ]);
    }
    public function store(Request $request)
    {
        $data = $request->all();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:1',
            'roles' => 'required|array',
        ], [
            'email.unique' => 'Tài khoản đã tồn tại.',
            'roles.required' => 'Vui lòng chọn ít nhất một vai trò.',
            'password.min' => 'Mật khẩu phải ít nhất 1 ký tự.',
        ]);
        try {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);
            $user->syncRoles($request->roles);

            Session::put('message', 'Tạo tài khoản thành công');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Có lỗi xảy ra khi tài khoản: ' . $e->getMessage()])
                ->withInput();
        }
    }
    public function show(User $users)
    {

        $users = User::where('users.id', $users->id)
            ->select('users.*')
            ->first();
        $roles = Role::pluck('name', 'name')->all();
        // $userRoles = $users->roles->pluck('name', 'name')->all();
        $userRoles = $users ? $users->roles->pluck('name')->toArray() : [];

        return view('role-permission.users.edit', [
            'user' => $users,
            'roles' => $roles,
            'userRoles' => $userRoles,
        ]);
    }

    public function update(Request $request, User $users)
    {
        $request->validate([
            'name' => 'nullable|max:255',
            'email' => 'nullable|email|max:255',
            'password' => 'nullable|string|max:25',
            'roles' => 'required',
        ]);
        $oldData = $users->getOriginal();

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if (!empty($request->password)) {
            $data += [
                'password' => $request->password,
            ];
        }
        $users->update($data);
        $users->syncRoles($request->roles);

        $details = "Đã chỉnh sửa tài khoản: " . $users->name . "\n";

        foreach ($data as $key => $newValue) {
            $oldValue = isset($oldData[$key]) ? $oldData[$key] : 'Không thay đổi';
            if ($oldValue !== $newValue) {
                $details .= " Tên cũ: " . $oldValue . " => Tên mới: " . $newValue . "\n";
            }
        }
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'Cập Nhật',
            'model_type' => 'Người Dùng',
            'details' => $details,
        ]);
        Session::put('message', 'Cập nhật thành công.');
        return redirect()->route('all.users');
    }

    public function delete(Request $request)
    {

        $user = User::find($request->users);
        // dd($user);
        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'delete',
            'model_type' => 'Người Dùng',
            'details' => "Đã xóa tài khoản: " . $user->name,
        ]);
        $user->delete();
        Session::put('message', 'Xóa thành công');
        return redirect()->back();
    }
    public function index()
    {
        return view("users.index");
    }

    public function resetPass()
    {
        return view("auth.passwordreset");
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        // $status = Password::reset(
        //     $request->only('email', 'password', 'password_confirmation', 'token'),
        //     function ($user, $password) {
        //         $user->forceFill([
        //             'password' => Hash::make($password),
        //         ])->save();

        //         $user->setRememberToken(Str::random(60));

        //         event(new PasswordReset($user));
        //     }
        // );

        // return $status === Password::PASSWORD_RESET
        //             ? redirect()->route('login')->with('status', __($status))
        //             : back()->withErrors(['email' => [__($status)]]);
    }

    public function checkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $exists = User::where('email', $request->email)->exists();

        return response()->json(['exists' => $exists]);
    }
}
