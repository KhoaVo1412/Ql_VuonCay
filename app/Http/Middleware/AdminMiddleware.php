<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để tiếp tục.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $allowedRoles = [
            'Admin',
            'Quản Lý Tài Khoản',
            // 'Khách Hàng',
            'Cấu Hình Trang Chủ',
            'Cấu Hình Đăng Nhập',
            'Cấu Hình Map',
            'Quản Lý Chất Lượng',
            'Thị Trường Kinh Doanh',

            'Nông Trường',
            'Danh Sách Nông Trường',
            'Tạo Nông Trường',
            'Cập Nhật Nông Trường',
            'Xóa Nông Trường',

            'Quản Lý Đơn Vị',
            'Danh Sách Đơn Vị',
            'Tạo Đơn Vị',
            'Cập Nhật Đơn Vị',
            'Xóa Đơn Vị',

            'Quản Lý Xe',
            'Danh Sách Xe',
            'Tạo Xe',
            'Cập Nhật Xe',
            'Xóa Xe',

            'Quản Lý Thông Tin Nguyên Liệu',
            'Danh Sách Nguyên Liệu',
            'Tạo Thông Tin Nguyên Liệu',
            'Cập Nhật Thông Tin Nguyên Liệu',
            'Xóa Thông Tin Nguyên Liệu',


            'Quản Lý Khu Vực Trồng',
            'Danh Sách Khu Vực Trồng',
            'Tạo Khu Vực Trồng',
            'Cập Nhật Khu Vực Trồng',
            'Xóa Khu Vực Trồng',

            'Nhà Máy XNCB',
            'Quản Lý Nhà Máy',
            'Danh Sách Nhà Máy',
            'Tạo Nhà Máy',
            'Cập Nhật Nhà Máy',
            'Xóa Nhà Máy',

            'Quản Lý Mã Lô',
            'Danh Sách Mã Lô',
            'Tạo Mã Lô',
            'Cập Nhật Mã Lô',
            'Xóa Mã Lô',

            'Kết Nối TTNL',
            'Danh Sách TTNL',
            'Tạo TTNL',
            'Cập Nhật TTNL',
            'Xóa TTNL',

            'Quản Lý Lệnh Xuất Hàng',
            'Danh Sách Lệnh Xuất Hàng',
            'Gắn Mã Lô Vào Lệnh Xuất Hàng',
            'Xóa mã Lệnh Xuất Hàng',
            'Cập Nhật Mã Lệnh Xuất Hàng',


            'Danh Sách Quản Lý Chất Lượng',
            'Danh Sách Thông Tin Khác',

            'Quản Lý Hợp Đồng',
            'Danh Sách Hợp Đồng',
            'Tạo Hợp Đồng',
            'Cập Nhật Hợp Đồng',
            'Xóa Hợp Đồng',

            'Quản Lý Loại Hợp Đồng',
            'Danh Sách Loại Hợp Đồng',
            'Tạo Loại Hợp Đồng',
            'Cập Nhật Loại Hợp Đồng',
            'Xóa Loại Hợp Đồng',

            'Quản Lý Khách Hàng',
            'Danh Sách Khách Hàng',
            'Tạo Khách Hàng',
            'Cập Nhật Khách Hàng',
            'Xóa Khách Hàng',
        ];

        if ($user->hasAnyRole($allowedRoles)) {
            return $next($request);
        }
        return redirect()->route('tx')->with('error', 'Bạn không có quyền truy cập trang này.');
        // return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập.');
    }
    // public function handle(Request $request, Closure $next): Response
    // {
    //     if (!Auth::check()) {
    //         return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để tiếp tục.');
    //     }

    //     /** @var \App\Models\User $user */
    //     $user = Auth::user();
    //     $allowedRoles = [
    //         'Admin',
    //         'Nông Trường',
    //         'Quản Lý Tài Khoản',
    //         'Quản Lý Xe',
    //         'Cập Nhật Xe',
    //         // 'Khách Hàng',
    //         'Nhà Máy',
    //         'Cấu Hình Trang Chủ',
    //         'Cấu Hình Đăng Nhập',
    //         'Cấu Hình Map',
    //         'Kế Hoạch Đầu Tư',
    //         'Quản Lý Chất Lượng',
    //         'Thị Trường Kinh Doanh',
    //         'Danh Sách Nông Trường',
    //         'Danh Sách Nhà Máy',
    //         'Danh Sách Quản Lý Chất Lượng',
    //         'Danh Sách Thông Tin Khác',
    //         'Danh Sách Hợp Đồng',
    //         'Xóa Sửa Hợp Đồng',
    //         'Sửa Hợp Đồng',
    //         'Xóa Hợp Đồng',
    //         'Sửa Nhà Máy',
    //         'Xóa Nhà Máy',
    //         'Xóa Sửa Nhà Máy',
    //         'Xóa Sửa Nông Trường',
    //         'Cập Nhật Nông Trường',
    //         'Xóa Nông Trường',
    //     ];

    //     if ($user->hasAnyRole($allowedRoles)) {
    //         return $next($request);
    //     }
    //     return redirect()->route('tx')->with('error', 'Bạn không có quyền truy cập trang này.');
    //     // return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập.');
    // }
}
