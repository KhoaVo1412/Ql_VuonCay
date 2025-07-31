<!DOCTYPE html>
<html>

<head>
    <title></title>

</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table width="600"
                    style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="padding: 30px;">
                            <h2 style="color: #2e7d32;">Tài khoản của bạn đã được tạo</h2>
                            <p>Xin chào <strong>{{$customerName}}</strong>,</p>
                            <p>Chúng tôi vừa tạo tài khoản cho bạn trên hệ thống <strong>Truy Xuất Nguồn Gốc Sản
                                    Phẩm</strong>. Dưới đây là thông tin đăng nhập của bạn:</p>

                            <table style="margin: 20px 0; width: 100%; font-size: 16px;">
                                <tr>
                                    <td style="width: 150px;"><strong>Tên đăng nhập:</strong></td>
                                    <td>{{$accountName}}</td>
                                </tr>
                                <tr>
                                    <td><strong>Mật khẩu:</strong></td>
                                    <td>{{$temporaryPassword}}</td>
                                </tr>
                            </table>

                            <p>Bạn có thể đăng nhập tại:
                                <a href="https://app.horuco.com.vn" style="color: #2e7d32;">[LINK_ĐĂNG_NHẬP]</a>
                            </p>

                            {{-- <p style="margin-top: 20px;">🔒 <strong>Lưu ý:</strong> Vì lý do bảo mật, vui lòng đăng
                                nhập
                                và thay đổi mật khẩu ngay sau lần đăng nhập đầu tiên.</p> --}}

                            <hr style="margin: 30px 0;">
                            <p style="font-size: 13px; color: #aaa;">Nếu bạn có bất kỳ thắc mắc nào, xin vui lòng liên
                                hệ chúng tôi qua email horuco@horuco.com.vn hoặc số điện thoại (84-64) 3872104 -
                                3873482.</p>
                            <p style="font-size: 13px; color: #aaa;">© 2025 HOA BINH RUBBER JOINT STOCK COMPANY</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>