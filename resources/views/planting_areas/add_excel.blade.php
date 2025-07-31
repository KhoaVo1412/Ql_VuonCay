@extends('layouts.app')

<head>
    <link rel="stylesheet" href="{{ asset('css/note-excel.css') }}">
</head>
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0"></h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('plantingareas.index') }}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm Excel</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="{{ route('import-plantingareas') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-header">
                <h5>Thêm Bằng Excel</h5>
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
                    - Các plantation (nông trường) phải đúng theo mẫu dưới, nếu không sẽ bị loại bỏ:
                    {{ $farms }}
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


                {{-- <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Tạo</button>
                    <a href="{{ asset('files/plant_plot_template.xlsx') }}" class="btn btn-success">
                        <i class="fas fa-download"></i> Tải File Mẫu
                    </a>
                </div>
                <br>

                <div class="note-container">
                    <div class="note-title">Lưu ý *</div>

                    <div>- Cột <strong>Plantation</strong> có thể nhập <strong style="color: red">NT</strong>.</div>
                    <div>- Hướng dẫn chèn file PDF vào cột <strong>Tap_tin</strong> trong Excel:
                    </div>

                    <div class="step-title">Bước 1: Chọn một ô</div>
                    <ul>
                        <li class="step-desc">Nhấn vào ô bạn muốn chèn.</li>
                    </ul>

                    <div class="step-title">Bước 2: Mở tab Insert</div>
                    <ul>
                        <li class="step-desc">Chọn tab <em>Insert</em> trên thanh công cụ.</li>
                        <li class="step-desc">Chọn <strong>Text</strong> &gt; <strong>Object</strong> (hoặc
                            <strong>Insert &gt; Object</strong> nếu thấy trực tiếp).
                        </li>
                    </ul>

                    <div class="step-title">Bước 3: Chèn file PDF</div>
                    <ul>
                        <li class="step-desc">Trong hộp thoại <strong>Object</strong>, chọn tab <strong>Create from
                                File</strong>.</li>
                        <li class="step-desc">Nhấn <strong>Browse…</strong> và chọn file PDF bạn muốn chèn.</li>
                        <li class="step-desc">Bấm <strong>OK</strong> để hoàn tất.</li>
                    </ul>
                </div> --}}
            </div>
        </div>
    </form>
</section>
@endsection