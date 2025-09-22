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
            'Quản Lý',
            'Quản Lý Tài Khoản',
            'Công Nhân',
            'Tổ Trưởng',
            // 'Khách Hàng',
            'Cấu Hình Trang Chủ',
            'Cấu Hình Đăng Nhập',
            'Cấu Hình Map',
            'Quản Lý Kho',
            'Thị Trường Kinh Doanh',

            'Lô',
            'Danh Sách Lô',
            'Thêm Lô',
            'Sửa Lô',
            'Xóa Lô',

            'Quản Lý Vườn Giống',
            'Danh Sách Vườn Giống',
            'Thêm Vườn Giống',
            'Sửa Vườn Giống',
            'Xóa Vườn Giống',

            'Quản Lý Cây Trồng',
            'Danh Sách Cây Trồng',
            'Thêm Cây Trồng',
            'Sửa Cây Trồng',
            'Xóa Cây Trồng',

            'Quản Lý Chức Vụ',
            'Danh Sách Chức Vụ',
            'Thêm Chức Vụ',
            'Sửa Chức Vụ',
            'Xóa Chức Vụ',


            'Quản Lý Tổ',
            'Danh Sách Tổ',
            'Thêm Tổ',
            'Sửa Tổ',
            'Xóa Tổ',

            'Nhà Máy Công Nhân',
            'Quản Lý Công Nhân',
            'Danh Sách Công Nhân',
            'Thêm Công Nhân',
            'Sửa Công Nhân',
            'Xóa Công Nhân',

            'Quản Lý Công Việc',
            'Danh Sách Công Việc',
            'Thêm Công Việc',
            'Sửa Công Việc',
            'Xóa Công Việc',

            'Phân Công',
            'Danh Sách Phân Công',
            'Thêm Phân Công',
            'Sửa Phân Công',
            'Xóa Phân Công',

            'Quản Lý Đề Xuất',
            'Danh Sách Đề Xuất',
            'Thêm Đề Xuất',
            'Xóa Đề Xuất',
            'Sửa Đề Xuất',

            'Quản Lý Đánh Giá',
            'Danh Sách Đánh Giá',
            'Thêm Đánh Giá',
            'Sửa Đánh Giá',
            'Xóa Đánh Giá',

            'Quản Lý Phiếu Cây Bệnh',
            'Danh Sách Phiếu Cây Bệnh',
            'Thêm Phiếu Cây Bệnh',
            'Sửa Phiếu Cây Bệnh',
            'Xóa Phiếu Cây Bệnh',

            'Quản Lý Phiếu Trị',
            'Danh Sách Phiếu Trị',
            'Thêm Phiếu Trị',
            'Sửa Phiếu Trị',
            'Xóa Phiếu Trị',

            'Quản Lý Đề Xuất VT',
            'Danh Sách Đề Xuất VT',
            'Thêm Đề Xuất VT',
            'Sửa Đề Xuất VT',
            'Xóa Đề Xuất VT',

            'Quản Lý Cây Gãy_Đổ',
            'Danh Sách Cây Gãy_Đổ',
            'Thêm Cây Gãy_Đổ',
            'Sửa Cây Gãy_Đổ',
            'Xóa Cây Gãy_Đổ',

            'Quản Lý Khách Hàng',
            'Danh Sách Khách Hàng',
            'Thêm Khách Hàng',
            'Sửa Khách Hàng',
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
