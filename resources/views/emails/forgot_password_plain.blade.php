<!DOCTYPE html>
<html>

<head>
    <title>Yêu cầu đổi mật khẩu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #0073e6;
            font-size: 24px;
            text-align: center;
        }

        p {
            font-size: 16px;
            line-height: 1.5;
        }

        .reset-button {
            display: inline-block;
            margin: 20px 0;
            padding: 12px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #0073e6;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .reset-button:hover {
            background-color: #005bb5;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
{{--

<body>
    <div class="container">
        <h1>Đổi mật khẩu</h1>
        <p>Bạn đã yêu cầu đổi mật khẩu. Nhấn vào liên kết bên dưới để đặt lại mật khẩu:</p>
        <a href="{{ $resetLink }}" class="reset-button">Đổi mật khẩu</a>
        <p class="footer">Nếu bạn không yêu cầu điều này, vui lòng bỏ qua email này.</p>
    </div>
</body> --}}

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table width="600"
                    style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="padding: 30px; text-align: center;">
                            <h2 style="color: #2e7d32;"> [Horuco] - Yêu cầu đặt lại mật khẩu</h2>
                            <p>Xin chào <strong>{{ $userName }}</strong>,</p>
                            <p>Chúng tôi đã nhận được yêu cầu thay đổi mật khẩu cho tài khoản của bạn.</p>
                            <p>Vui lòng nhấn vào nút bên dưới để đặt lại mật khẩu:</p>
                            <a href="{{ $resetLink }}"
                                style="display: inline-block; margin: 20px 0; padding: 12px 24px; background-color: #2e7d32; color: #ffffff; text-decoration: none; border-radius: 4px;">
                                Đặt lại mật khẩu
                            </a>
                            <p>Nếu bạn không yêu cầu thay đổi này, vui lòng bỏ qua email này.</p>
                            <p style="font-size: 12px; color: #888;">Liên kết sẽ hết hạn sau 24h, vì lý do bảo mật.</p>
                            <hr style="margin: 30px 0;">
                            <p style="font-size: 13px; color: #aaa;">Email này được gửi từ hệ thống tự động. Vui lòng
                                không trả lời email này.</p>
                            <p style="font-size: 13px; color: #aaa;">© 2025 HOA BINH RUBBER JOINT STOCK COMPANY</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>