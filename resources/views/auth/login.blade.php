<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <link rel="stylesheet" href="../css/login_ql.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0">
</head>

<body style="overflow: hidden; height: 100vh; background: url('/imgs/homeimg.jpg') no-repeat center center fixed;
    background-size: cover; display: flex; justify-content: center; align-items: center; position: relative;">
    <header>
        <nav class="navbar">
            <span class="hamburger-btn material-symbols-rounded"></span>

            <ul class="links">
                <span class="close-btn material-symbols-rounded"></span>

            </ul>
            <button class="login-btn" style="display: none"></button>
        </nav>
    </header>
    <div class="blur-bg-overlay"></div>
    <div class="form-popup">
        <span class="close-btn material-symbols-rounded"></span>
        <div class="form-box login">
            <div class="form-details">
                <h2>Xin Chào,</h2>
                <p>Vui lòng đăng nhập để truy cập vào hệ thống.</p>
            </div>
            <div class="form-content">
                <h2>ĐĂNG NHẬP</h2>
                <form method="POST" action="/login" id="form-log">
                    @csrf
                    <div class="input-field">
                        <input class="form-control @error('email') is-invalid @enderror" type="text" name="email"
                            value="{{ old('email') }}" required>
                        <label>Tài Khoản</label>
                    </div>
                    {{-- <div class="input-field">
                        <input class="@error('password') is-invalid @enderror" type="password" name="password" required>
                        <label>Mật Khẩu</label>
                    </div> --}}
                    <div class="input-field position-relative">
                        <input id="password" class="@error('password') is-invalid @enderror form-control"
                            type="password" name="password" required>
                        <label for="password">Mật Khẩu</label>

                        <!-- Icon hiện/ẩn mật khẩu -->
                        <span class="toggle-password" onclick="togglePassword()"
                            style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); cursor: pointer;">
                            <i id="eye-icon" class="fa fa-eye-slash"></i>
                        </span>
                    </div>
                    <div class="bottom-link">
                        <a href="#" class="forgot-pass-link" id="signup-link">Quên mật khẩu?</a>
                    </div>
                    {{-- <a href="{{ route('forgotIndex') }}" class="forgot-pass-link" id="signup-link">Quên mật
                        khẩu?</a> --}}
                    @error('email')
                    <div class="invalid-feedback" style="color: red; padding-top:20px">{{ $message }}</div>
                    @enderror
                    <button type="submit">Đăng Nhập</button>
                </form>
                {{-- <div class="bottom-link">
                    Bạn chưa có tài khoản?
                    <a href="#" id="signup-link">Đăng Ký</a>
                </div> --}}
            </div>
        </div>
        <div class="form-box signup">
            <div class="form-details">
                <h2>Quên Mật Khẩu</h2>
                <p> Vui lòng nhập tài khoản của bạn.</p>
            </div>
            <div class="form-content">
                <h2>Quên Mật Khẩu</h2>
                <form action="{{ route('forgotIndex') }}" method="POST">
                    <div class="input-field">
                        <input type="text" required>
                        <label>Nhập Tài Khoản</label>
                    </div>
                    <button type="submit">Gửi</button>
                </form>
                <div class="bottom-link">
                    Bạn đã có tài khoản?
                    <a href="#" id="login-link">Đăng Nhập</a>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('status'))
        Swal.fire({
            icon: 'success',
            title: 'Thành công!',
            text: '{{ session('status') }}',
        });
    @endif

    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Lỗi!',
            text: '{{ $errors->first() }}',
        });
    @endif
</script>
<script>
    const navbarMenu = document.querySelector(".navbar .links");
    const hamburgerBtn = document.querySelector(".hamburger-btn");
    const hideMenuBtn = navbarMenu.querySelector(".close-btn");
    const showPopupBtn = document.querySelector(".login-btn");
    const formPopup = document.querySelector(".form-popup");
    const hidePopupBtn = formPopup.querySelector(".close-btn");
    const signupLoginLink = formPopup.querySelectorAll(".bottom-link a");
    // Show mobile menu
    hamburgerBtn.addEventListener("click", () => {
        navbarMenu.classList.toggle("show-menu");
    });
    // Hide mobile menu
    hideMenuBtn.addEventListener("click", () =>  hamburgerBtn.click());
    // Show login popup
    showPopupBtn.addEventListener("click", () => {
        document.body.classList.toggle("show-popup");
    });
    // Hide login popup
    hidePopupBtn.addEventListener("click", () => showPopupBtn.click());
    // Show or hide signup form
    signupLoginLink.forEach(link => {
        link.addEventListener("click", (e) => {
            e.preventDefault();
            formPopup.classList[link.id === 'signup-link' ? 'add' : 'remove']("show-signup");
        });
    });
</script>
<script>
    function togglePassword() {
        const passwordInput = document.getElementById("password");
        const eyeIcon = document.getElementById("eye-icon");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.classList.remove("fa-eye-slash");
            eyeIcon.classList.add("fa-eye");
        } else {
            passwordInput.type = "password";
            eyeIcon.classList.remove("fa-eye");
            eyeIcon.classList.add("fa-eye-slash");
        }
    }
</script>

</html>