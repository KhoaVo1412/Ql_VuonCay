<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\Shared\Validate;

class LoginControllerApi extends Controller
{
    // public function login(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json(['error' => $validator->errors()], 400);
    //     }

    //     try {
    //         $credentials = $request->only('email', 'password');

    //         if (!$token = JWTAuth::attempt($credentials)) {
    //             return response()->json(['error' => 'Đăng nhập thất bại, kiểm tra lại thông tin!!!'], 401);
    //         }
    //         $user = Auth::user();
    //         if (!$user) {
    //             return response()->json(['error' => 'Người dùng không tồn tại.'], 404);
    //         }
    //         $userPermissions = $user->getAllPermissions()->pluck('name', 'name')->all();

    //         $formattedPermissions = [];
    //         foreach ($userPermissions as $permission) {
    //             $formattedPermissions[$permission] = "1";
    //         }

    //         // $userRoles = $user->roles ? $user->roles->pluck('name')->all() : [];
    //         // $userRoles = $user->roles ? $user->roles->pluck('name')->all() : [];
    //         // $roles = Role::pluck('name', 'name')->all();
    //         $userRolesList = $user->roles ? $user->roles->pluck('name')->all() : [];
    //         $userRoles = [];
    //         foreach ($userRolesList as $role) {
    //             $userRoles[$role] = "1";
    //         }
    //         if (Auth::attempt($credentials)) {
    //             $user = Auth::user();
    //             if ($user instanceof \App\Models\User) {
    //                 $user->remember_token = $token;
    //                 $user->update();
    //             }
    //         }
    //         return response()->json([
    //             'token' => $token,
    //             'userPermissions' => (object) $formattedPermissions,
    //             'userRoles' => (object) $userRoles,
    //             // 'userPermissions' => $formattedPermissions,
    //             // 'userRoles' => $userRoles,
    //             // 'roles' => $roles,
    //             'token' => $token,
    //             'user' => $user,
    //             'message' => 'Đăng nhập thành công.',

    //         ], 200)->header('Authorization', 'Bearer ' . $token);
    //     } catch (\Exception $e) {
    //         return response()->json('message', 'Lỗi khi xử lý đăng nhập: ' . $e->getMessage());
    //     }
    // }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $credentials = $request->only('email', 'password');

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Đăng nhập thất bại, kiểm tra lại thông tin!!!'], 401);
            }

            $user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'Người dùng không tồn tại.'], 404);
            }
            $roles = $user->roles->pluck('name')->toArray();
            if (!in_array('Khách Hàng', $roles) && !in_array('Admin', $roles)) {
                return response()->json(['error' => 'Bạn không có quyền truy cập.'], 403);
            }

            // Lấy permissions và roles
            $userPermissions = $user->getAllPermissions()->pluck('name', 'name')->all();
            $userRolesList = $user->roles ? $user->roles->pluck('name')->all() : [];

            // Format permissions: null nếu không có
            $formattedPermissions = null;
            if (!empty($userPermissions)) {
                $formattedPermissions = [];
                foreach ($userPermissions as $permission) {
                    $formattedPermissions[$permission] = "1";
                }
            }

            // Format roles: null nếu không có
            $userRoles = null;
            if (!empty($userRolesList)) {
                $userRoles = [];
                foreach ($userRolesList as $role) {
                    $userRoles[$role] = "1";
                }
            }

            // Cập nhật token vào user
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                if ($user instanceof \App\Models\User) {
                    $user->remember_token = $token;
                    $user->update();
                }
            }

            return response()->json([
                'token' => $token,
                'userPermissions' => $formattedPermissions,
                'userRoles' => $userRoles,
                'user' => $user,
                'message' => 'Đăng nhập thành công.',
            ], 200)->header('Authorization', 'Bearer ' . $token);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi xử lý đăng nhập: ' . $e->getMessage()
            ], 500);
        }
    }

    public function logout_api(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user) {
                return response()->json([
                    'message' => 'Đăng xuất thành công, token vẫn hợp lệ.'
                ], 200);
            }
            return response()->json(['error' => 'Người dùng không tồn tại hoặc chưa đăng nhập.'], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi xử lý đăng xuất: ' . $e->getMessage()], 500);
        }
    }
    public function login_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $credentials = $request->only('email', 'password');

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Đăng nhập thất bại, kiểm tra lại thông tin!!!'], 401);
            }

            $user = Auth::user();

            if ($user instanceof \App\Models\User) {
                $user->remember_token = $token;
                $user->save();
            }

            return response()->json([
                'token' => $token,
                'message' => 'Đăng nhập thành công.'
            ], 200)->header('Authorization', 'Bearer ' . $token);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Lỗi khi xử lý đăng nhập: ' . $e->getMessage()
            ], 500);
        }
    }
    public function changePassword(Request $request)
    {
        // Validate the incoming data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'current_password' => 'required',
            'new_password' => 'required|min:3|confirmed',
        ], [
            "email.required" => "Tài khoản không được để trống.",
            "current_password.required" => "Mật khẩu hiện tại không được để trống.",
            "new_password.required" => "Mật khẩu mới không được để trống.",
            "new_password.min" => "Mật khẩu mới không được dưới 3 ký tự.",
            "new_password.confirmed" => "Mật khẩu mới không trùng khớp.",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(), // Only show the first error message
            ], 400);
        }

        try {
            // Find the user by email
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'error' => 'Người dùng không tồn tại.',
                ], 404);
            }

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'error' => 'Mật khẩu hiện tại không chính xác.',
                ], 401);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            Auth::logout();

            return response()->json([
                'message' => 'Mật khẩu đã được thay đổi thành công.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi xử lý thay đổi mật khẩu: ' . $e->getMessage()
            ], 500);
        }
    }
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'error' => 'Email không tồn tại trong hệ thống.',
            ], 404);
        }

        $token = Str::random(60);

        $user->reset_password_token = $token;
        $user->reset_password_expired_at = Carbon::now()->addDay();
        $user->save();

        $resetLink = url('/password/reset/' . $token);

        Mail::to($user->email)->send(new ForgotPasswordMail($resetLink, $user->name));

        return response()->json([
            'message' => 'Liên kết đặt lại mật khẩu đã được gửi tới email của bạn.',
        ], 200);
    }
}
