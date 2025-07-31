<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerifyApiTokenMobile
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('token');
        if (!$token) {
            return response()->json(['error' => 'Bạn chưa đăng nhập, vui lòng đăng nhập.'], 401);
        }
        try {
            $user = JWTAuth::setToken(str_replace('Bearer ', '', $token))->authenticate();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token có thể đã hết hạn, không hợp lệ hoặc đã thay đổi.'], 401);
        }
        return $next($request);
    }
}
