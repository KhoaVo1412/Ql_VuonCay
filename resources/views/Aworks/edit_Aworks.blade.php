@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0">
            Chỉnh sửa: {{ $aworks->workName }}
        </h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('aworks.index') }}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chỉnh Sửa</li>
                </ol>
            </nav>
        </div>
    </div>

    <style>
        .tab-content {
            border-right: 1px solid #dee2e6;
            border-left: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
            padding: 5px;
        }

        .col-xl-4 {
            padding-top: 15px;
        }

        .form-label {
            font-weight: bold;
        }
    </style>

    <div class="row">
        <div class="col-xl-12">
            <form id="form-aworks" action="{{ route('aworks.update', ['id' => $aworks->id]) }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex">

                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="workCode" class="form-label">Mã Loại Công Việc</label>
                                <input type="text" class="form-control" name="workCode" placeholder="Mã"
                                    value="{{ $aworks->workCode }}" required>
                            </div>
                            {{-- <div class="col-md-6">
                                <label for="" class="form-label">Tên Công Việc</label>
                                <input type="text" class="form-control" name="workName" placeholder="Tên"
                                    value="{{ $aworks->workName }}" required>
                            </div> --}}
                            <div class="col-md-6">
                                <label for="" class="form-label">Loại Công Việc</label>
                                <input type="text" class="form-control" name="workType" placeholder="Loại"
                                    value="{{ $aworks->workType }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="Ngày Tạo" class="form-label">Ngày Tạo</label>
                                <input type="date" class="form-control" name="workDate" placeholder="Ngày"
                                    value="{{ $aworks->workDate }}" required>
                            </div>

                            <!-- Trạng thái -->
                            {{-- <div class="col-md-4">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="Không hoạt động" @if ($aworks->status == 'Không hoạt động') selected
                                        @endif>
                                        Không hoạt động</option>
                                    <option value="Hoạt động" @if ($aworks->status == 'Hoạt động') selected @endif>Hoạt
                                        động</option>
                                </select>
                            </div> --}}
                            <div class="p-t-10 col-sm-12" style="margin-top: 10px">
                                <button type="submit" class="btn btn-success">Lưu Thay Đổi</button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</section>
@endsection