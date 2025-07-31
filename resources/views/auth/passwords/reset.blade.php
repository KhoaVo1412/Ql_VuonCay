<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Quên mật khẩu</title>

    <meta name="description" content="">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link rel="shortcut icon" href="/imgs/favicon.ico" type="image/x-icon">

    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;display=swap"
        rel="stylesheet">
    <style type="text/css">
        .layout-menu-fixed .layout-navbar-full .layout-menu,
        .layout-page {
            padding-top: 0px !important;
        }

        .content-wrapper {
            padding-bottom: 0px !important;
        }
    </style>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="/sneat-1.0.0/assets/js/config.js"></script>
</head>

<body style="overflow: hidden; height: 100vh; background: url('/imgs/cs_img.jpg') no-repeat center center fixed;
    background-size: cover; display: flex; justify-content: center; align-items: center; position: relative;">

    <style>
        .alert-danger {

            text-align: center;
            border: 20px;
            background: #ff2b2b;
            width: 100%;
            height: 30px;
            color: #fefefe;
            border-radius: 20px;
            padding: 5px;
            font-size: 17px;
            margin: 0 0 25px 0;
            align-items: center;

        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.3);
            /* backdrop-filter: blur(1px); */
            z-index: -1;
        }

        /* body {
            background-image: url('/imgs/cs_img.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        } */

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.3);
            z-index: -1;
        }

        .authentication-wrapper {
            position: relative;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border-radius: 10px;
            padding: 30px;
            width: 400px;
            background-color: rgba(255, 255, 255, 0.9);
            /* Background with transparency */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            flex: 1 1 auto;
            padding: 1.5rem 1.5rem;
        }

        .app-brand {
            text-align: center;
            margin-bottom: 20px;
        }

        .app-brand img {
            width: 100px;
        }

        .form-label {
            margin-bottom: 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: rgb(86, 106, 127);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: inherit;
        }

        .form-control {
            display: block;
            width: 100%;
            font-size: 0.9375rem;
            font-weight: 400;
            line-height: 1.53;
            color: rgb(105, 122, 141);
            background-color: rgb(255, 255, 255);
            background-clip: padding-box;
            appearance: none;
            padding: 0.4375rem 0.875rem;
            border-width: 1px;
            border-style: solid;
            border-color: rgb(217, 222, 227);
            border-image: initial;
            border-radius: 0.375rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }



        .w-100 {
            width: 100% !important;
        }

        .d-grid {
            display: grid !important;
        }

        .text-center a {
            color: #6f42c1;
            font-weight: 600;
            text-decoration: none;
        }

        .text-center a:hover {
            text-decoration: underline;
        }

        .btn {
            display: inline-block;
            font-weight: 400;
            line-height: 1.53;
            color: #697a8d;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
            background-color: transparent;
            border: 1px solid transparent;
            padding: 0.4375rem 1.25rem;
            font-size: 0.9375rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease-in-out;
        }

        .btn-primary {
            color: rgb(255, 255, 255);
            background-color: rgb(105, 108, 255);
            box-shadow: rgba(105, 108, 255, 0.4) 0px 0.125rem 0.25rem 0px;
            border-color: rgb(105, 108, 255);
        }

        .mb-3 {
            margin-bottom: 1rem !important;
        }

        @media screen and (max-width: 768px) {
            .card {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-2">
                            <img src="/imgs/logo_bl.png" alt="" style="width: 50%">
                        </div>
                        <!-- /Logo -->
                        <h2 style="text-align: center;">Đặt lại mật khẩu</h2>
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group mb-3">
                                <label for="password" class="form-label">Mật khẩu mới:</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới:</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary mb-3 w-100">Đặt lại mật khẩu</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- SweetAlert Scripts -->
@if (session('status'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Thành công!',
        text: '{{ session('status') }}',
        confirmButtonText: 'OK'
    });
</script>
@endif

@if ($errors->has('token'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Lỗi!',
        text: '{{ $errors->first('token') }}',
        confirmButtonText: 'OK'
    });
</script>
@endif

@if (session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Lỗi!',
        text: '{{ session('error') }}',
        confirmButtonText: 'OK'
    });
</script>
@endif

@if ($errors->has('password'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Lỗi!',
        text: '{{ $errors->first('password') }}',
        confirmButtonText: 'OK'
    });
</script>
@endif

</html>