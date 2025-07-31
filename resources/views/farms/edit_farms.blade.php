@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0">
            Chỉnh sửa: {{ $gardens->gardenName }}
        </h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('farms.index') }}">Danh Sách</a></li>
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
            <form id="form-farms" action="{{ route('farms.update', ['id' => $gardens->id]) }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex">
                        {{-- <div class="card-title">Chỉnh sửa: {{ $farms->farm_name }}</div> --}}
                        {{-- <h5>Chỉnh sửa: {{ $farms->farm_name }}</h5> --}}

                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="code" class="form-label">Mã Vườn</label>
                                <input type="text" class="form-control" name="code" placeholder="Mã"
                                    value="{{ $gardens->code }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="gardenName" class="form-label">Tên Vườn</label>
                                <input type="text" class="form-control" name="gardenName" placeholder="Mã"
                                    value="{{ $gardens->gardenName }}" required>
                            </div>

                            {{-- <div class="col-md-4">
                                <label for="gardenArea" class="form-label">Diện Tích</label>
                                <input type="text" class="form-control" name="gardenArea" placeholder="Tên"
                                    value="{{ $gardens->gardenArea }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="plotCount" class="form-label">Số Lượng</label>
                                <input type="text" class="form-control" name="plotCount" placeholder="Tên"
                                    value="{{ $gardens->plotCount }}" required>
                            </div> --}}

                            <!-- Trạng thái -->
                            <div class="col-md-4">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="Không hoạt động" @if ($gardens->status == 'Không hoạt động') selected
                                        @endif>
                                        Không hoạt động</option>
                                    <option value="Hoạt động" @if ($gardens->status == 'Hoạt động') selected @endif>Hoạt
                                        động</option>
                                </select>
                            </div>
                            <div class="p-t-10 col-sm-12" style="margin-top: 10px">
                                <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</section>
@endsection