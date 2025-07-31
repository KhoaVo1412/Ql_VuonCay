@extends('layouts.app')
@section('content')
    <div class="page-title my-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h5></h5>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="#">File</a></li>
                    </ol>
                </nav>
            </div>
        </div>

        @if (session('thanhcong'))
            <div class="alert alert-light-success alert-dismissible fade show" role="alert">
                {{ session('thanhcong') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('loi'))
            <div class="alert alert-light-danger alert-dismissible fade show" role="alert">
                {{ session('loi') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @include('layouts.alert')

        <div class="container-fluid1">
            <div class="card">
                <div class="card-header">
                    {{-- <h5>File</h5> --}}
                    <h5>Nhập Thông Tin Nguyên Liệu</h5>
                    <form action="{{ route('importIng.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <br>
                        {{-- <div class="mb-3">
                        <label for="import_ing" class="form-label"><b>Chọn Tài Liệu (Excel):</b></label>
                        <input type="file" class="form-control" name="file" accept=".xlsx" required>
                    </div> --}}
                        <div class="mb-3">
                            <label for="import_ing" class="form-label text-black" style="cursor: pointer;">
                                <b>Chọn Tài Liệu (Excel):</b>
                            </label>
                            <div class="input-group">
                                <label class="btn choose-file-btn"
                                    style="cursor: pointer; background-color: #E6EDF7; border-color: #E6EDF7; color: #7E8B91">
                                    Chọn tệp
                                </label>
                                <!-- Sử dụng opacity: 0 thay vì display: none để vẫn hiển thị thông báo lỗi mặc định -->
                                <input type="file" class="import_excel" name="file" accept=".xlsx"
                                    style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; opacity: 0; z-index: 1; cursor: pointer;"
                                    required>
                                <input type="text" class="form-control bg-white file-name" style="cursor: pointer;"
                                    placeholder="Không có tệp nào được chọn" readonly>
                            </div>
                            <p class="mt-2" style="color:red; font-size:13px;">Lưu Ý *:
                                <br>
                                Cột Khu Vực Trồng phải ngăn cách bằng dấu ',' viết liền không cách. Cột Nông Trường có thể
                                nhập NT.
                                <br>
                            </p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Tạo</button>
                            <div class="text-end d-grid gap-2 d-md-flex justify-content-md-end">
                                {{-- <a href="{{ route('download.sample') }}" class="btn btn-success">
                                <i class="fas fa-download me-md-2"></i> Tải tệp mẫu
                            </a> --}}

                                <a href="{{ asset('files/file_example_ttnl.xlsx') }}" class="btn btn-success">
                                    <i class="fas fa-download me-md-2"></i> Tải File Mẫu
                                </a>
                            </div>
                        </div>
                    </form>
                    <hr class="text-muted">
                    <br>
                    <br>
                    {{-- <div class="text-end d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="" class="btn btn-success">
                        <i class="fas fa-download me-md-2"></i> Tải file mẫu
                    </a>
                </div> --}}
                </div>
            </div>
        </div>

    </div>
@endsection
