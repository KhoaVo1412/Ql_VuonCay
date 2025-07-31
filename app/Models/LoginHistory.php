<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use OwenIt\Auditing\Contracts\Auditable;

class LoginHistory extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'login_histories';

    protected $fillable = ['user_id', 'ip_address', 'login_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getAuditData(): array
    {
        $data = parent::getAuditData(); // Lấy dữ liệu audit mặc định

        // Thay thế user_id bằng user_name
        if ($this->user) {
            $data['user_name'] = $this->user->name;  // Lưu tên người dùng thay vì ID
        }

        return $data;
    }

    // public function transformAudit(array $data): array
    // {
    //     // === BƯỚC QUAN TRỌNG: Chỉ áp dụng logic này cho sự kiện "deleted" ===
    //     if ($data['event'] !== 'deleted') {
    //         // Với các sự kiện khác (created, updated), không làm gì cả, trả về dữ liệu gốc
    //         return $data;
    //     }

    //     // --- Bắt đầu tùy chỉnh cho sự kiện "deleted" ---

    //     // Lấy tên người dùng đang thực hiện hành động
    //     $userName = Auth::check() ? Auth::user()->name : 'Hệ thống';

    //     // Lấy tiêu đề của bài viết sắp bị xóa từ giá trị cũ
    //     // $this->getOriginal() sẽ lấy giá trị của thuộc tính trước khi có thay đổi
    //     $user_id = $this->getOriginal('user_id');

    //     // Ghi đè lại mảng old_values và new_values để làm cho log rõ ràng hơn

    //     // 1. Dọn dẹp old_values, chỉ giữ lại ID cho dễ truy vết (tùy chọn)
    //     $data['old_values'] = [
    //         'user_name' => $this->user->name,
    //     ];

    //     // 2. Thêm thông tin tùy chỉnh vào new_values
    //     $data['new_values'] = [
    //         'message' => "'{$user_id}' đã bị xóa.",
    //         'deleted_by' => $userName,
    //     ];

    //     // 3. Trả về dữ liệu đã được tùy chỉnh
    //     return $data;
    // }
}
