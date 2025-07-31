@extends('layouts.app')

@section('content')
<div class="page-title my-3">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0"></h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="/certi">Chứng Chỉ</a></li>
                    <li class="breadcrumb-item">Chỉnh Sửa</li>
                </ol>
            </nav>
        </div>
    </div>
    {{-- <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h4>Chứng chỉ</h4>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="/certi">Chứng Chỉ</a></li>
                    <li class="breadcrumb-item">Chỉnh Sửa Chứng Chỉ</li>
                </ol>
            </nav>
        </div>
    </div> --}}
</div>

<div class="row">
    <div class="col-md-6 col-12">
        <div class="card">
            @include('layouts.alert')
            <div class="card-header">
                <h5>Chỉnh Sửa Thông Tin Chứng Chỉ</h5>
            </div>
            <div class="card-content">
                <div class="card-body pt-0">
                    <form class="form form-vertical" action="{{ route('certi.update', $certificate->id) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <!-- Thêm method PUT -->
                        <div class="form-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="file_name" class="text-black"><b>Tên Chứng Chỉ</b></label>
                                        <input type="text" id="file_name" class="form-control" name="name"
                                            placeholder="Tên chứng chỉ" value="{{ old('name', $certificate->name) }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <!-- <label for="attachment" class="text-black">File Đính Kèm</label>
                                                        <input type="file" id="attachment" class="form-control" name="attachment"> -->
                                        <label for="attachment" class="form-label text-black" style="cursor: pointer;">
                                            <b>File Đính Kèm:</b>
                                        </label>
                                        <div class="input-group">
                                            <label class="btn choose-file-btn"
                                                style="cursor: pointer; background-color: #E6EDF7; border-color: #E6EDF7; color: #7E8B91">
                                                Chọn tệp
                                            </label>
                                            <!-- Sử dụng opacity: 0 thay vì display: none để vẫn hiển thị thông báo lỗi mặc định -->
                                            <input type="file" class="import_excel" name="attachment" id="attachment"
                                                style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; opacity: 0; z-index: 1; cursor: pointer;"
                                                required>
                                            <input type="text" class="form-control bg-white file-name"
                                                style="cursor: pointer;" id="attachment"
                                                placeholder="Không có tệp nào được chọn" name="attachment" readonly>
                                        </div>
                                        @if ($certificate->file_name)
                                        <div class="mt-3"><label class="text-black"><b>File Hiện Tại:</b></label>
                                            <a href="{{ asset('certificates/' . $certificate->file_name) }}"
                                                target="_blank">
                                                {{ $certificate->file_name }}
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary me-1 mb-1">Lưu Thay Đổi</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection