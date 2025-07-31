<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\ApiKey;
use App\Models\AppMap;
use App\Models\Posts;
use App\Models\PostLogin;
use App\Models\Runtimes;
use App\Models\Webmap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
    public function index()
    {
        if (!session()->has('message') && !session()->has('error')) {
            // session(['active_menu' => 'contract']); // Chỉ reset nếu chưa có giá trị
            session(['active_menu' => 'settings']); // Chỉ reset nếu chưa có giá trị
        }

        $posts = Posts::first() ?? Posts::create(['title' => 'Cao su Hòa Bình QR - Phần mềm truy xuất nguồn gốc sản phẩm', 'desc' => 'CAO SU BÌNH LONG']);
        $tokens = ApiKey::first() ?? ApiKey::create(['token_key' => '']);
        $appmaps = AppMap::first() ?? AppMap::create(['appMap' => 'https://horuco.maps.arcgis.com/home/item.html?id=5ec8ed782e5146d1909f76e14e293059']);
        $runtimes = Runtimes::first() ?? Runtimes::create(['runtime' => 'runtimelite,1000,rud5650463498,none,HC5X0H4AH5G7LMZ59025']);
        $postlogin = PostLogin::first() ?? PostLogin::create([
            'name' => 'HỆ THỐNG TRUY XUẤT NGUỒN GỐC CAO SU HÒA BÌNH',
            'desc' => 'Hệ thống Truy xuất nguồn gốc cao su là một công nghệ hoặc nền tảng thông tin được thiết kế để theo dõi và quản lý chuỗi cung ứng của sản phẩm cao su, từ giai đoạn trồng trọt, thu hoạch, chế biến cho đến tiêu thụ sản phẩm cuối cùng. Mục tiêu của hệ thống này là tính minh bạch, độ tin cậy và sự an toàn của sản phẩm cao su trong suốt vòng đời của nó, đồng thời tuân thủ các quy định về môi trường và quy định EURD',
            'company_name' => 'CÔNG TY CỔ PHẦN CAO SU HÒA BÌNH',
            'commune_name' => 'Xã Hòa Bình – Xuyên Mộc – Bà Rịa Vũng Tàu',
            'link' => 'http://www.horuco.com.vn/',
            'support' => 'Công ty TNHH Thái Hưng Infotech

            Điện thoại: 0901339986

            Email: info@thaihunginfotech.com',
        ]);
        $posts = Posts::first() ?? Posts::create(['title' => 'Cao su Hòa Bình QR - Phần mềm truy xuất nguồn gốc sản phẩm', 'desc' => 'CAO SU BÌNH LONG']);
        $webmaps = Webmap::first();
        return view("setting.index", compact('posts', 'webmaps', 'postlogin', 'tokens', 'appmaps', 'runtimes'));
    }
    public function update(Request $request)
    {
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'desc' => 'nullable|string',
            ], [
                'title.required' => 'Vui lòng nhập tiêu đề.',
                'title.max' => 'Tiêu đề không được vượt quá :max ký tự.'
            ]);

            $post = Posts::first();
            if ($post) {
                $post->update($data);
            }

            session(['active_menu' => 'settings']);

            return redirect()->back()->with('message', 'Cập nhật thành công!');
        } catch (ValidationException $e) {
            session(['active_menu' => 'settings']); // Giữ nguyên tab khi có lỗi
            return redirect()->back()->with('error', 'Cập nhật thất bại!');
        }
    }
    // public function updateWm(Request $request)
    // {
    //     $request->validate([
    //         'webmap' => 'nullable|string',
    //         'webapp' => 'nullable|string',
    //     ]);
    //     $json = json_decode($request->webmap, true);
    //     session(['active_menu' => 'contract']);
    //     if (!$json || !isset($json['data']['url'])) {
    //         return redirect()->back()->with('error', 'Dữ liệu không hợp lệ hoặc thiếu url rút gọn.');
    //     }

    //     $shortUrl = $json['data']['url'];

    //     $webmaps = Webmap::first();
    //     // dd($webmaps);
    //     if ($webmaps) {
    //         $webmaps->update([
    //             'webmap' => $shortUrl,
    //         ]);
    //     }

    //     return redirect()->back()->with('message', 'Cập nhật bản đồ thành công!');
    // }
    public function updateWm(Request $request)
    {
        $request->validate([
            'webmap' => 'nullable|string',
            'webapp' => 'nullable|string',
        ]);

        $json = json_decode($request->webmap, true);

        // session(['active_menu' => 'contract']);
        session(['active_menu' => 'settings']);

        if (!$json || !isset($json['data']['url']) || !isset($json['data']['long_url'])) {
            return redirect()->back()->with('error', 'Dữ liệu không hợp lệ hoặc thiếu url rút gọn hoặc long_url.');
        }

        $shortUrl = $json['data']['url'];
        $longUrl = $json['data']['long_url'];

        $webmaps = Webmap::first();

        if ($webmaps) {
            $webmaps->update([
                'webmap' => $shortUrl,
                'webapp' => $longUrl,
            ]);
        }
        return redirect()->back()->with('message', 'Cập nhật bản đồ thành công!');
    }
    public function updateToken(Request $request)
    {
        $request->validate([
            'token_key' => 'nullable|string',
        ]);
        $newToken = $request->input('token_key');
        session(['active_menu' => 'app-link']);

        if ($newToken !== '***') {
            $tokens = ApiKey::first();
            $tokens->token_key = $newToken;
            $tokens->save();
        }

        ActionHistory::create([
            'user_id' => Auth::id(),
            'action_type' => 'Cập nhật',
            'model_type' => 'API Token',
            'details' => 'Người dùng đã cập nhật token API.',
        ]);
        return redirect()->back()->with('message', 'Cập nhật token thành công!');
    }
    public function updateAppMap(Request $request)
    {
        $request->validate([
            'appMap' => 'nullable|string',
        ]);

        $appmaps = AppMap::first();
        session(['active_menu' => 'app-link']);
        // dd($tokens);
        if ($appmaps) {
            $oldValue = $appmaps->appMap;
            $newValue = $request->appMap;
            $appmaps->update([
                'appMap' => $request->appMap,
            ]);
            if ($oldValue !== $newValue) {
                ActionHistory::create([
                    'user_id' => Auth::id(),
                    'action_type' => 'Cập nhật',
                    'model_type' => 'App Map',
                    'details' => "Cập nhật App Map từ: '{$oldValue}' → '{$newValue}'",
                ]);
            }
        }

        return redirect()->back()->with('message', 'Cập nhật app map thành công!');
    }
    public function updateruntime(Request $request)
    {
        $request->validate([
            'runtime' => 'nullable|string',
        ]);

        $runtimes = Runtimes::first();
        session(['active_menu' => 'app-link']);
        // dd($tokens);
        if ($runtimes) {
            $oldValue = $runtimes->runtime;
            $newValue = $request->runtime;
            $runtimes->update([
                'runtime' => $request->runtime,
            ]);
            if ($oldValue !== $newValue) {
                ActionHistory::create([
                    'user_id' => Auth::id(),
                    'action_type' => 'Cập nhật',
                    'model_type' => 'Run time lite lincese',
                    'details' => "Cập nhật Run time từ: '{$oldValue}' → '{$newValue}'",
                ]);
            }
        }

        return redirect()->back()->with('message', 'Cập nhật runtime thành công!');
    }
    public function saveAllSettings(Request $request)
    {
        $request->validate([
            'token_key' => 'nullable|string',
            'appMap' => 'nullable|string',
            'runtime' => 'nullable|string',
        ]);

        session(['active_menu' => 'app-link']);

        // =================== TOKEN ===================
        if ($request->filled('token_key') && $request->token_key !== '***') {
            $tokenModel = ApiKey::first();
            if ($tokenModel) {
                $oldToken = $tokenModel->token_key;
                $newToken = $request->token_key;

                if ($oldToken !== $newToken) {
                    $tokenModel->token_key = $newToken;
                    $tokenModel->save();

                    ActionHistory::create([
                        'user_id' => Auth::id(),
                        'action_type' => 'Cập nhật',
                        'model_type' => 'API Token',
                        'details' => "Cập nhật Token API từ: '{$oldToken}' → '{$newToken}'",
                    ]);
                }
            }
        }

        // =================== APP MAP ===================
        if ($request->has('appMap')) {
            $appMapModel = AppMap::first();
            if ($appMapModel) {
                $oldMap = $appMapModel->appMap;
                $newMap = $request->appMap;

                if ($oldMap !== $newMap) {
                    $appMapModel->update(['appMap' => $newMap]);

                    ActionHistory::create([
                        'user_id' => Auth::id(),
                        'action_type' => 'Cập nhật',
                        'model_type' => 'App Map',
                        'details' => "Cập nhật App Map từ: '{$oldMap}' → '{$newMap}'",
                    ]);
                }
            }
        }

        // =================== RUNTIME ===================
        if ($request->has('runtime')) {
            $runtimeModel = Runtimes::first();
            if ($runtimeModel) {
                $oldRuntime = $runtimeModel->runtime;
                $newRuntime = $request->runtime;

                if ($oldRuntime !== $newRuntime) {
                    $runtimeModel->update(['runtime' => $newRuntime]);

                    ActionHistory::create([
                        'user_id' => Auth::id(),
                        'action_type' => 'Cập nhật',
                        'model_type' => 'Run time lite license',
                        'details' => "Cập nhật Run time từ: '{$oldRuntime}' → '{$newRuntime}'",
                    ]);
                }
            }
        }

        return redirect()->back()->with('message', 'Cập nhật cấu hình thành công!');
    }

    public function updateLogin(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'desc' => 'required|string',
                'company_name' => 'required|string|max:255',
                'commune_name' => 'required|string|max:255',
                'link' => 'required|string|max:255',
                'support' => 'required|string',
            ], [
                'name.required' => 'Vui lòng nhập tên app.',
                'name.max' => 'Tên App không được vượt quá :max ký tự.',
                'desc.required' => 'Vui lòng nhập mô tả.',
                'company_name.required' => 'Vui lòng nhập tên công ty.',
                'company_name.max' => 'Tên công ty không được vượt quá :max ký tự.',
                'commune_name.required' => 'Vui lòng nhập tên xã.',
                'commune_name.max' => 'Xã không được vượt quá :max ký tự.',
                'link.required' => 'Vui lòng nhập đường link',
                'link.max' => 'Đường link không được vượt quá :max ký tự.',
                'support.required' => 'Vui lòng nhập hỗ trợ kĩ thuật.'
            ]);

            $post = PostLogin::first();

            if ($post) {
                $oldData = $post->only([
                    'name',
                    'desc',
                    'company_name',
                    'commune_name',
                    'link',
                    'support'
                ]);
                $post->update($data);
                $changes = [];
                foreach ($data as $key => $newValue) {
                    $oldValue = $oldData[$key] ?? '';
                    if ($oldValue !== $newValue) {
                        $changes[] = "'{$oldValue}' → '{$newValue}'";
                    }
                }
                if (!empty($changes)) {
                    $details = "Cập nhật thông tin đăng nhập:\n" . implode("\n", $changes);

                    ActionHistory::create([
                        'user_id' => Auth::id(),
                        'action_type' => 'Cập nhật',
                        'model_type' => 'Thông tin đăng nhập',
                        'details' => $details,
                    ]);
                }
            }

            session(['active_menu' => 'settings-login']);

            return redirect()->back()->with('message', 'Cập nhật thành công!');
        } catch (ValidationException $e) {
            session(['active_menu' => 'settings-login']); // Giữ nguyên tab khi có lỗi
            return redirect()->back()->with('error', 'Cập nhật thất bại!');
        }
    }
}
