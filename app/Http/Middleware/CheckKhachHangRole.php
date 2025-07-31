<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckKhachHangRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập.');
        }

        $user = Auth::user();
        // if ($user->hasanyRole('Khách Hàng|Admin')) {

        if ($user->hasRole('Khách Hàng')) {
            return $next($request);
        }

        return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập.');
    }
}
