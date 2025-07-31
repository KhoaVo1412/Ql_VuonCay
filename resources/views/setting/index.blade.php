@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0"></h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cài đặt</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- <div class="container mt-5"> -->
<div class="mt-3">
    <div class="row">
        <div class="col-md-4">
            <div class="card left-card">
                <div class="card-header">
                    <h5>Cài đặt</h5>
                    <!-- <a href="#" class="menu-item active" data-target="contract">Hợp đồng</a>
                            <a href="#" class="menu-item" data-target="settings">Cấu hình</a> -->
                    <!-- <a href="#" class="menu-item {{ session('active_menu', 'token') == 'token' ? 'active' : '' }}"
                        data-target="token">Token</a> -->
                    {{-- @hasrole('Admin')
                    <a href="#" class="menu-item {{ session('active_menu', 'contract') == 'contract' ? 'active' : '' }}"
                        data-target="contract">Hợp Đồng</a>
                    @endhasrole --}}

                    @hasanyrole('Admin|Cấu Hình Trang Chủ')
                    <a href="#" class="menu-item {{ session('active_menu', 'settings') == 'settings' ? 'active' : '' }}"
                        data-target="settings">Cấu Hình Trang Chủ</a>
                    {{-- <a href="#" class="menu-item {{ session('active_menu') == 'settings' ? 'active' : '' }}"
                        data-target="settings">Cấu Hình Trang Chủ</a> --}}
                    @endhasanyrole

                    @hasanyrole('Admin|Cấu Hình Đăng Nhập')
                    <a href="#" class="menu-item {{ session('active_menu') == 'settings-login' ? 'active' : '' }}"
                        data-target="settings-login">Cấu Hình Trang Đăng Nhập</a>
                    @endhasanyrole

                    {{-- @hasanyrole('Admin|Cấu Hình Map')
                    <a href="#" class="menu-item {{ session('active_menu') == 'app-link' ? 'active' : '' }}"
                        data-target="app-link">Map App</a>
                    @endhasanyrole --}}
                    @hasrole('Admin')
                    <a href="#" class="menu-item" data-target="htr-link">Lịch Sử</a>
                    @endhasrole
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="content-area">
                <!-- Tab Hợp đồng -->
                <!-- <div class="card right-card active" id="token">
                    <div class="card-header">
                        <h5>Token <span style="color: red">*</span></h5>
                        <form action="{{ route('settings.token') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">
                                </label>
                                <input type="text" class="form-control" id="token" name="token_key"
                                    value="{{ $tokens->token_key ? '***' : '' }}">
                            </div>

                            <div class="d-flex mt-2">
                                <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                            </div>
                        </form>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const tokenInput = document.getElementById('token');
                                const fullToken = "{{ $tokens->token_key }}";
                                if (fullToken) {
                                    tokenInput.value = '*****';
                                    tokenInput.addEventListener('focus', function() {
                                        tokenInput.removeAttribute('readonly');
                                        tokenInput.value = '';
                                    });
                                }
                            });
                        </script>
                    </div>
                </div> -->
                {{-- @hasrole('Admin')
                <div class="card right-card" id="contract">
                    <div class="card-header">
                        <h5>Hợp Đồng</h5>
                        <form action="{{ route('settings.wm') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Link vị trí webmap mặc định <span style="color: red">*</span>
                                </label>
                                <textarea rows="5" type="text" class="form-control" id="webmap"
                                    name="webmap">{{ $webmaps->webmap }}</textarea>
                            </div>
                            <div class="d-flex mt-2">
                                <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endhasrole --}}
                <!-- Tab Cấu hình -->
                @hasanyrole('Admin|Cấu Hình Trang Chủ')
                <div class="card right-card" id="settings">
                    <div class="card-header">
                        <h5>Cấu Hình Trang Chủ</h5>
                        @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <form action="{{ route('settings.update') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="title">Tiêu đề</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                    name="title" value="{{ $posts->title }}">

                            </div>
                            <div class="form-group">
                                <label for="desc">Mô tả</label>
                                <textarea class="form-control @error('desc') is-invalid @enderror" id="desc"
                                    name="desc">{{ $posts->desc }}</textarea>

                            </div>
                            <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                        </form>
                    </div>
                </div>
                @endhasanyrole

                @hasanyrole('Admin|Cấu Hình Đăng Nhập')
                <!-- Tab Cấu hình trang login-->
                <div class="card right-card" id="settings-login">
                    <div class="card-header">
                        <h5>Cấu Hình Trang Đăng Nhập</h5>
                        {{-- @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}
                        </div>
                        @endif --}}
                        <form action="{{ route('settings.updateLogin') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="title">Tên app</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ $postlogin->name }}">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="desc">Mô tả</label>
                                <textarea class="form-control @error('desc') is-invalid @enderror" id="desc"
                                    name="desc">{{ $postlogin->desc }}</textarea>
                                @error('desc')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="title">Tên công ty</label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                    id="company_name" name="company_name" value="{{ $postlogin->company_name }}">
                                @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="title">Tên xã</label>
                                <input type="text" class="form-control @error('commune_name') is-invalid @enderror"
                                    id="commune_name" name="commune_name" value="{{ $postlogin->commune_name }}">
                                @error('commune_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="title">Tên đường link</label>
                                <input type="text" class="form-control @error('link') is-invalid @enderror" id="link"
                                    name="link" value="{{ $postlogin->link }}">
                                @error('link')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="title">Nội dung về hỗ trợ kĩ thuật</label>
                                <textarea class="form-control @error('support') is-invalid @enderror" id="support"
                                    name="support">{{ $postlogin->support }}</textarea>
                                @error('support')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                        </form>
                    </div>
                    @endhasanyrole
                </div>
                @hasanyrole('Admin|Cấu Hình Map')
                {{-- app map --}}
                <div class="card right-card" id="app-link">
                    <div class="card-header">

                        <form action="{{ route('settings.saveAllSettings') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <h5>Token <span style="color: red">*</span></h5>
                                <input type="text" class="form-control" id="token" name="token_key"
                                    value="{{ $tokens->token_key ? '***' : '' }}">
                            </div>

                            <div class="form-group mt-3">
                                <h5>App Map</h5>
                                <input type="text" class="form-control" id="appMap" name="appMap"
                                    value="{{ $appmaps->appMap }}">
                            </div>

                            <div class="form-group mt-3">
                                <h5>Run time lite license</h5>
                                <input type="text" class="form-control" id="runtime" name="runtime"
                                    value="{{ $runtimes->runtime }}">
                            </div>

                            <div class="d-flex mt-4">
                                <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                            </div>
                        </form>

                    </div>
                </div>
                @endhasanyrole

                @hasrole('Admin')
                <div class="card right-card" id="htr-link">
                    <div class="card-header">
                        <h5>Lịch Sử Đăng Nhập</h5>
                        <div class="d-flex mt-2">
                            <a href="{{ route('checkLogin.index') }}" class="btn btn-primary">Kiểm Tra Lịch Sử Đăng
                                Nhập</a>
                        </div>
                        <h5 style="padding-top: 20px">Lích Sử Thay Đổi</h5>
                        <div class="d-flex mt-2">
                            <a href="{{ route('checkAction.index') }}" class="btn btn-primary">Kiểm Tra Lịch Sử Thay
                                Đổi</a>
                        </div>
                        </form>
                    </div>
                </div>
                @endhasrole
            </div>
        </div>
    </div>
</div>


<!-- <script>
        $(document).ready(function() {
            $('.menu-item').on('click', function(e) {
                e.preventDefault();
                $('.menu-item').removeClass('active');
                $(this).addClass('active');
                $('.right-card').removeClass('active');
                const target = $(this).data('target');
                $('#' + target).addClass('active');
            });
        });
    </script> -->

<script>
    $(document).ready(function() {
        // Lấy menu đã chọn từ session
        let activeMenu = "{{ session('active_menu', 'contract') }}";

        // Kích hoạt menu item và card tương ứng
        $('.menu-item').removeClass('active');
        $('.menu-item[data-target="' + activeMenu + '"]').addClass('active');

        $('.right-card').removeClass('active');
        $('#' + activeMenu).addClass('active');

        // Khi nhấn vào menu, thay đổi trạng thái
        $('.menu-item').on('click', function(e) {
            e.preventDefault();
            let target = $(this).data('target');

            $('.menu-item').removeClass('active');
            $(this).addClass('active');

            $('.right-card').removeClass('active');
            $('#' + target).addClass('active');

        });
    });
</script>

<style>
    .left-card .menu-item {
        display: block;
        padding: 10px;
        color: #333;
        text-decoration: none;
        /* border-bottom: 1px solid #ddd; */
    }

    .left-card .menu-item.active {
        background-color: hsl(149deg 81% 34% / 72%);
        color: white;
        border-radius: 20px;
    }

    .right-card {
        display: none;
    }

    .right-card.active {
        display: block;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    label {
        font-weight: bold;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control {
        border: 1px solid #ced4da;
        border-radius: 0;
        padding: 0.5rem;
    }

    textarea.form-control {
        height: 100px;
    }

    .btn-primary {
        background-color: #3950a2;
        border-color: #364b98;
        border: none;
        padding: 0.5rem 1rem;
    }

    /* Tạo khung cho thẻ card */
    .card.right-card {
        border-radius: 10px;
        /* Bo tròn các góc */
        background-color: #ffffff;
        /* Màu nền sáng */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* Bóng đổ mờ */
        padding: 20px;
        /* Khoảng cách trong card */
    }

    /* Tiêu đề của các mục */
    .card-header h5 {
        font-size: 18px;
        /* Kích thước chữ tiêu đề */
        font-weight: bold;
        /* Chữ đậm */
        color: #333;
        /* Màu chữ tối */
        margin-bottom: 10px;
        /* Khoảng cách dưới tiêu đề */
    }

    /* Tạo khoảng cách giữa các phần */
    .card-header .d-flex {
        margin-bottom: 15px;
        /* Khoảng cách giữa các nhóm nút */
    }

    /* Tùy chỉnh cho các nút */
    .btn-primary {
        background-color: #007bff;
        /* Màu nền của nút */
        border: none;
        /* Loại bỏ viền */
        color: white;
        /* Màu chữ */
        padding: 10px 20px;
        /* Khoảng cách chữ và viền */
        border-radius: 30px;
        /* Bo tròn các góc của nút */
        font-size: 16px;
        /* Kích thước chữ */
        transition: background-color 0.3s, transform 0.3s;
        /* Hiệu ứng chuyển màu nền và phóng to */
    }

    .btn-primary:hover {
        background-color: #0056b3;
        /* Màu nền khi hover */
        transform: scale(1.05);
        /* Phóng to khi hover */
    }

    /* Tạo khoảng cách giữa các nhóm tiêu đề và nút */
    .card-header h5+.d-flex {
        margin-top: 20px;
        /* Khoảng cách giữa các nhóm */
    }

    /* Tạo khung cho thẻ card */
    .card.right-card {
        border-radius: 10px;
        /* Bo tròn các góc */
        background-color: #ffffff;
        /* Màu nền sáng */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* Bóng đổ mờ */
        padding: 15px 20px;
        /* Khoảng cách trong card */
        max-width: 100%;
    }

    /* Tiêu đề của các mục */
    .card-header h5 {
        font-size: 16px;
        /* Kích thước chữ tiêu đề */
        font-weight: bold;
        /* Chữ đậm */
        color: #333;
        /* Màu chữ tối */
        margin-bottom: 10px;
        /* Khoảng cách dưới tiêu đề */
    }

    /* Tùy chỉnh cho các nút */
    .btn-primary {
        background-color: #007bff;
        /* Màu nền của nút */
        border: none;
        /* Loại bỏ viền */
        color: white;
        /* Màu chữ */
        padding: 8px 15px;
        /* Khoảng cách chữ và viền */
        border-radius: 30px;
        /* Bo tròn các góc của nút */
        font-size: 14px;
        /* Kích thước chữ */
        transition: background-color 0.3s, transform 0.3s;
        /* Hiệu ứng chuyển màu nền và phóng to */
        margin-bottom: 10px;
        /* Khoảng cách dưới các nút */
        display: block;
        /* Nút chiếm toàn bộ chiều ngang */
        width: 100%;
        /* Chiều rộng nút */
    }

    .btn-primary:hover {
        background-color: #2bd044;
        /* Màu nền khi hover */
        transform: scale(1.05);
        /* Phóng to khi hover */
    }

    /* Tạo khoảng cách giữa các nhóm tiêu đề và nút */
    .card-header .d-flex {
        margin-bottom: 15px;
        /* Khoảng cách giữa các nhóm nút */
    }

    /* Giảm độ rộng cho các phần tử ở trên */
    .card-header h5+.d-flex {
        margin-top: 10px;
        /* Khoảng cách giữa các nhóm */
    }

    /* Thêm một chút cách điệu cho các phần tử */
    .card-header .d-flex a {
        margin: 5px 0;
        /* Đảm bảo các nút không quá sát nhau */
    }
</style>
@endsection