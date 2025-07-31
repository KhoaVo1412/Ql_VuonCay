<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
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

    //         if ($user instanceof \App\Models\User) {
    //             $user->remember_token = $token;
    //             $user->save();
    //         }

    //         return response()->json([
    //             'token' => $token,
    //             'message' => 'Đăng nhập thành công.'
    //         ], 200)->header('Authorization', 'Bearer ' . $token);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => 'Lỗi khi xử lý đăng nhập: ' . $e->getMessage()
    //         ], 500);
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

            return response()->json([
                'token' => $token,
                'message' => 'Đăng nhập thành công.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Lỗi khi xử lý đăng nhập: ' . $e->getMessage()
            ], 500);
        }
    }
}
