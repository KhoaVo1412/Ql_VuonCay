<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\VerifyApiTokenMobile;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'lang' => \App\Http\Middleware\SetLocale::class,
            'login' => AuthMiddleware::class,
            'apitoken' => VerifyApiTokenMobile::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'role.khachhang' => \App\Http\Middleware\CheckKhachHangRole::class,
            'isAdmin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (UnauthorizedException $e, $request) {
            // Kiểm tra nếu yêu cầu là AJAX hoặc API (trả về JSON)
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Bạn không có quyền thực hiện chức năng này.'
                ], 403);
            }

            // Nếu là yêu cầu web, chuyển hướng hoặc trả về view lỗi tùy chỉnh
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện chức năng này.');
        });
    })->create();
