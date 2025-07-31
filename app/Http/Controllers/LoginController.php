<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPasswordMail;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PostLogin;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $postLogin = PostLogin::first();
        return view("auth.login", compact('postLogin'))->with('status', 'Mật khẩu đã được cập nhật thành công.');
    }

    public function createuser()
    {

        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('123'),
        ]);
    }

    // public function handle_login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');
    //     $remember = $request->has('remember');
    //     if (Auth::attempt($credentials, $remember) && Auth::user()->block !== 1) {
    //         $user = Auth::user();
    //         if ($user->hasRole('Khách Hàng')) {
    //             return redirect()->route('tx');
    //         }
    //         return redirect()->intended('/');
    //     }

    //     return back()->withErrors([
    //         'email' => 'Tài khoản hoặc mật khẩu không đúng.',
    //     ])->withInput($request->only('email'));
    //     // ])->withInput($request->only('email', 'remember'));
    // }
    public function handle_login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');
        if (Auth::attempt($credentials, $remember) && Auth::user()->block !== 1) {
            $user = Auth::user();
            LoginHistory::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'login_at' => now(),
            ]);
            if ($user->hasRole('Khách Hàng')) {
                if (!$token = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'Đăng nhập thất bại, kiểm tra lại thông tin!!!'], 401);
                }
                if (Auth::attempt($credentials)) {
                    $user = Auth::user();
                    if ($user instanceof \App\Models\User) {
                        $user->remember_token = $token;
                        $user->update();
                    }
                }
                return redirect()->route('tx');
            }
            // if ($user->hasRole('Admin')) {
            //     return response()->json([
            //         'message' => 'Đăng nhập thành công. Bạn muốn vào trang quản lý Admin hay trang truy xuất?',
            //         'redirectOptions' => [
            //             'admin' => route('admin.dashboard'),
            //             'user' => route('tx'),
            //         ],
            //         'token' => JWTAuth::fromUser($user),
            //     ]);
            // }
            // if ($user->hasRole('Admin')) {
            //     return view('auth.select-admin-or-user', compact('user')); // Chuyển tới view hỏi người dùng
            // }

            return redirect()->intended('/');
        }
        return back()->withErrors([
            'email' => 'Tài khoản hoặc mật khẩu không đúng.',
        ])->withInput($request->only('email'));
    }

    public function selectRedirect(Request $request)
    {
        $redirectTo = $request->input('redirect');

        // Kiểm tra và chuyển hướng đến trang tương ứng
        if ($redirectTo == 'admin') {
            return redirect()->route('admin.dashboard'); // Chuyển hướng đến trang quản lý Admin
        } elseif ($redirectTo == 'user') {
            return redirect()->route('tx'); // Chuyển hướng đến trang Truy xuất
        }

        // Nếu không chọn đúng trang, quay lại trang chọn
        return redirect()->route('login')->withErrors(['error' => 'Vui lòng chọn đúng trang.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return redirect('/login');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function forgotIndex()
    {
        return view("auth.forgotPass");
    }
    public function showForgotPasswordForm()
    {
        return view('auth.forgotPass');
    }
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email không tồn tại trong hệ thống.']);
        }

        $token = Str::random(60);

        $user->reset_password_token = $token;
        // $user->reset_password_expired_at = Carbon::now()->subMinute();
        $user->reset_password_expired_at = Carbon::now()->addDay();
        $user->save();

        $resetLink = url('/password/reset/' . $token);

        Mail::to($user->email)->send(new ForgotPasswordMail($resetLink, $user->name));

        return back()->with('status', 'Liên kết đặt lại mật khẩu đã được gửi tới email của bạn.');
    }

    public function showResetForm($token)
    {
        return view('auth.passwords.reset', ['token' => $token]);
    }

    // Xử lý yêu cầu đặt lại mật khẩu
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            // 'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        $user = User::where('reset_password_token', $request->token)->first();

        if (!$user) {
            return back()->withErrors(['token' => 'Token không hợp lệ hoặc đã hết hạn.']);
        }
        if ($request->password !== $request->password_confirmation) {
            return back()->withErrors(['password' => 'Mật khẩu và xác nhận mật khẩu không khớp.']);
        }
        if (Carbon::now()->greaterThan($user->reset_password_expired_at)) {
            $user->reset_password_token = null;
            $user->reset_password_expired_at = null;
            $user->save();
            return back()->withErrors(['token' => 'Token đã hết hạn. Vui lòng gửi lại.']);
        }

        $user->password = Hash::make($request->password);
        $user->reset_password_token = null;
        $user->reset_password_expired_at = null;
        $user->save();

        return redirect()->route('login')->with('status', 'Mật khẩu đã được cập nhật thành công.');
    }
}
