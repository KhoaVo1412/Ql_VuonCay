@extends("layouts.app")
@section('content')
<div class="page-title my-3">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h4>Tài khoản</h4>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="/account">Tài khoản</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cập nhật thông tin</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="col-md-6 col-12">
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="mb-3">
                    <h5>Thông tin tài khoản</h5>
                </div>
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <form class="form form-horizontal" method="POST" action="{{ route('account.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="first-name-horizontal">Tên</label>
                            </div>
                            <div class="col-md-8 form-group">
                                <input type="text" id="first-name-horizontal"
                                    class="form-control @error('name') is-invalid @enderror" name="name"
                                    placeholder="Tên" value="{{ old('name', Auth::user()->name) }}">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="email-horizontal">Email</label>
                            </div>
                            <div class="col-md-8 form-group">
                                <input type="email" id="email-horizontal"
                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                    placeholder="Email" value="{{ old('email', Auth::user()->email) }}">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <script>
                                function togglePassword(element) {
                                    var x = element.previousElementSibling ;
                                    if (x.type === "password") {

                                        x.type = "text";
                                        element.classList.remove('fa-eye');
                                        element.classList.add('fa-eye-slash');
                                    } else {
                                        x.type = "password";
                                        element.classList.remove('fa-eye-slash');
                                        element.classList.add('fa-eye');
                                    }
                                }
                            </script>
                            <div class="col-md-4">
                                <label for="password-horizontal">Mật khẩu mới</label>
                            </div>
                            <div class="col-md-8 form-group position-relative">
                                <input type="password" id="password-horizontal"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    placeholder="Mật khẩu mới">
                                <i class="fa-solid fa-eye" onclick="togglePassword(this)"
                                    style="cursor: pointer; position: absolute;right: 22px;top: 50%;transform: translateY(-50%);"></i>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="confirm-password-horizontal">Xác nhận mật khẩu</label>
                            </div>
                            <div class="col-md-8 form-group position-relative">
                                <input type="password" id="confirm-password-horizontal"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                    name="password_confirmation" placeholder="Xác nhận mật khẩu">
                                <i class="fa-solid fa-eye" onclick="togglePassword(this)"
                                    style="cursor: pointer; position: absolute;right: 22px;top: 50%;transform: translateY(-50%);"></i>
                                @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Nút cập nhật -->
                            <div class="col-sm-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-success me-1 mb-1">Cập nhật</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection