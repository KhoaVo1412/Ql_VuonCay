@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h3 class="page-title fw-semibold fs-18 mb-0"></h3>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('plantingareas.index') }}">Danh sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sửa Excel</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- @if (session('success'))
    <div class="alert alert-light-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif --}}

    {{-- @if (session('errors'))
    <div class="alert alert-light-danger alert-dismissible fade show" role="alert">
        {{ session('errors') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif --}}

    {{--
    <pre>{{ print_r(session('errors'), true) }}</pre> --}}

    {{--
    @if (!empty(session('error')))
    <div class="alert alert-light-danger alert-dismissible fade show" role="alert">
        <ul>
            @foreach (session('error') as $error)
            <li>{{ is_array($error) ? implode(', ', $error) : $error }}</li> <!-- Chắc chắn là chuỗi -->
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif --}}

    <form action="{{ route('edit-import-plantingareas') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-header">
                <h5>Sửa Bằng Excel</h5>
                {{-- <div class="mb-3 form-group">
                    <label for="excel_file" class="form-label text-black">Chọn file:</label>
                    <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls" required>
                </div> --}}
                <br>
                <div class="mb-3">
                    <label for="excel_file" class="form-label text-black" style="cursor: pointer;">
                        <b>Chọn Tài Liệu (Excel):</b>
                    </label>
                    <div class="input-group">
                        <label class="btn choose-file-btn"
                            style="cursor: pointer; background-color: #E6EDF7; border-color: #E6EDF7; color: #7E8B91">
                            Chọn tệp
                        </label>
                        <!-- Sử dụng opacity: 0 thay vì display: none để vẫn hiển thị thông báo lỗi mặc định -->
                        <input type="file" class="import_excel" name="excel_file" accept=".xlsx,.xls"
                            style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; opacity: 0; z-index: 1; cursor: pointer;"
                            required>
                        <input type="text" class="form-control bg-white file-name" style="cursor: pointer;"
                            placeholder="Không có tệp nào được chọn" readonly>
                    </div>
                </div>
                <p style="color:red; font-size:13px;">Lưu ý*:
                    <br>
                    Các plantation (nông trường) phải đúng theo mẫu dưới, nếu không sẽ bị loại bỏ:
                    {{ $farms }}<br>
                </p>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Tạo</button>
                    {{-- <a href="{{ route('plantingareas.index') }}" class="btn btn-danger">
                        <i class="fas fa-times"></i> Hủy
                    </a> --}}
                    <a href="{{ asset('files/plant_plot_template.xlsx') }}" class="btn btn-success">
                        <i class="fas fa-download"></i> Tải File Mẫu
                    </a>
                </div>
                {{--
                <hr class="text-muted">
                <div class="text-end">
                    <a href="{{ asset('files/plant_plot_template.xlsx') }}" class="btn btn-success">
                        <i class="fas fa-download"></i> Tải file mẫu
                    </a>
                </div> --}}
            </div>
        </div>
    </form>
</section>
@endsection