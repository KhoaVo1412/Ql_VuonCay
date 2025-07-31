@extends('layouts.app')
@section('content')
    <div class="page-title my-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h4></h4>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb padding">
                        <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                        <li class="breadcrumb-item">File</li>
                    </ol>
                </nav>
            </div>
        </div>

        {{-- @if (session('thanhcong'))
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
    @endif --}}




        @include('layouts.alert')

        <div class="card">
            <div class="card-header">
                <h5>Nhập Thông Tin Chất Lượng SVR Từ File Excel
                </h5>
                <form action="{{ route('import-svr') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    {{-- <br>
                    <br> --}}
                    {{-- <div class="mb-3">
                        <label for="excel_svr" class="form-label"><b>Chọn file nhập:</b></label>
                        <input type="file" name="excel_svr" class="form-control" accept=".xlsx,.xls,.csv" required>
                    </div> --}}
                    <br>
                    <div class="mb-3">
                        <label for="excel_svr" class="form-label text-black" style="cursor: pointer;">
                            <b>Chọn Tài Liệu (Excel):</b>
                        </label>
                        <div class="input-group">
                            <label class="btn btn-secondary choose-file-btn"
                                style="cursor: pointer;background-color: #E6EDF7;border-color: #E6EDF7;color: #7E8B91;">
                                Chọn tệp
                            </label>
                            <!-- Sử dụng opacity: 0 thay vì display: none để vẫn hiển thị thông báo lỗi mặc định -->
                            <input type="file" class="import_excel" name="excel_svr" accept=".xlsx,.xls,.csv"
                                style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; opacity: 0; z-index: 1; cursor: pointer;"
                                required>
                            <input type="text" class="form-control bg-white file-name"
                                placeholder="Không có tệp nào được chọn" readonly>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Tạo</button>
                        {{-- <a href="{{ url()->previous() }}" class="btn btn-danger">
                        <i class="fas fa-times"></i> Hủy
                    </a> --}}
                        <a href="{{ asset('files/file_example_svr.xls') }}" class="btn btn-success">
                            <i class="fas fa-download me-md-2"></i>Tải File Mẫu
                        </a>
                    </div>
                </form>
                {{--
            <hr class="text-muted"> --}}
                {{-- <br>
            <br> --}}
                {{-- <form action="{{ route('import-latex') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h4 class="text-primary">Nhập thông tin chất lượng LATEX từ file excel</h4>
                <div class="mb-3">
                    <label for="excel_latex" class="form-label"><b>Chọn file nhập:</b></label>
                    <input type="file" name="excel_latex" class="form-control" required>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Import LATEX</button>
                    <a href="{{ url()->previous() }}" class="btn btn-danger">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                </div>
            </form>
            <hr class="text-muted">
            <br>
            <br>
            <form action="{{ route('import-rss') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h4 class="text-primary">Nhập thông tin chất lượng RSS từ file excel</h4>
                <div class="mb-3">
                    <label for="excel_rss" class="form-label"><b>Chọn file nhập:</b></label>
                    <input type="file" name="excel_rss" class="form-control" required>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Import RSS</button>
                    <a href="{{ url()->previous() }}" class="btn btn-danger">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                </div>
            </form>
            <hr class="text-muted"> --}}
                {{-- <div class="text-end d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="{{ asset('files/Du_Lieu_Phong_QLCL_SVR.xls') }}" class="btn btn-success">
                    <i class="fas fa-download me-md-2"></i> Tải file SVR mẫu
                </a>
                {{-- <a href="{{ asset('files/Du_Lieu_Phong_QLCL_LATEX.xls') }}" class="btn btn-success">
                    <i class="fas fa-download me-md-2"></i> Tải file LATEX mẫu
                </a>
                <a href="{{ asset('files/Du_Lieu_Phong_QLCL_RSS.xls') }}" class="btn btn-success">
                    <i class="fas fa-download"></i> Tải file RSS mẫu
                </a>
            </div> --}}
            </div>
        </div>

    </div>
@endsection
