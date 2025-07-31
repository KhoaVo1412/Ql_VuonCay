@extends('layouts.app')
@section('content')
    <div class="page-title my-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb mb-0 padding">
                        <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                        <li class="breadcrumb-item">Import</li>
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

        @if (!empty(session('errors')))
            <div class="alert alert-light-danger alert-dismissible fade show" role="alert">
                <ul>
                    @foreach (session('errors') as $error)
                        <li>{{ $error['message'] }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Import Lô Hàng Kết Nối Thông Tin Nguyên Liệu</h5>

            <form action="{{ route('storeBatchIng') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <br>
                {{-- <div class="mb-3">
                    <label for="import_exc" class="form-label"><b>Chọn Tài Liệu (Excel):</b></label>
                    <input type="file" name="excel_file" class="form-control" accept=".xlsx" required>
                </div> --}}
                <div class="mb-3">
                    <label for="import_exc" class="form-label text-black" style="cursor: pointer;">
                        <b>Chọn Tài Liệu (Excel):</b>
                    </label>
                    <div class="input-group">
                        <label class="btn choose-file-btn"
                            style="cursor: pointer; background-color: #E6EDF7; border-color: #E6EDF7; color: #7E8B91">
                            Chọn tệp
                        </label>
                        <!-- Sử dụng opacity: 0 thay vì display: none để vẫn hiển thị thông báo lỗi mặc định -->
                        <input type="file" class="import_excel" name="excel_file" accept=".xlsx"
                            style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; opacity: 0; z-index: 1; cursor: pointer;"
                            required>
                        <input type="text" class="form-control bg-white file-name" style="cursor: pointer;"
                            placeholder="Không có tệp nào được chọn" readonly>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Tạo</button>
                    <div class="text-end d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="/files/file_mau_lohang_ttnl.xlsx" class="btn btn-success">
                            <i class="fas fa-download me-md-2"></i> Tải File Mẫu
                        </a>
                    </div>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-danger">
                    <i class="fas fa-times"></i> Hủy
                </a> --}}
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
@endsection
