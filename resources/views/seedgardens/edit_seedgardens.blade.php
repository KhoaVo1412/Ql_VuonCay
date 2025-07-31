@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0">Chỉnh sửa: {{ $variety->varietyName }}</h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('seedgardens.index') }}">Danh Sách</a></li>
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
            <form id="form-seedgardens" action="{{ route('seedgardens.update', ['id' => $variety->id]) }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex">

                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- Tên giống -->
                            <div class="col-md-4">
                                <label for="varietyName" class="form-label">Tên Giống</label>
                                <input type="text" class="form-control" name="varietyName" placeholder="Tên giống"
                                    value="{{ $variety->varietyName }}" required>
                            </div>

                            <!-- Nguồn gốc -->
                            <div class="col-md-4">
                                <label for="origin" class="form-label">Nguồn Gốc</label>
                                <input type="text" class="form-control" name="origin" placeholder="Nguồn gốc"
                                    value="{{ $variety->origin }}">
                            </div>
                            <!-- Trạng thái -->
                            <div class="col-md-4">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="Hoạt động" @if ($variety->status == 'Hoạt động') selected @endif>Hoạt
                                        động
                                    </option>
                                    <option value="Không hoạt động" @if ($variety->status == 'Không hoạt động') selected
                                        @endif>Không
                                        hoạt động
                                    </option>
                                </select>
                            </div>
                            <!-- Mô tả -->
                            <div class="col-md-12">
                                <label for="desc" class="form-label">Mô Tả</label>
                                <textarea class="form-control" name="desc" rows="3"
                                    placeholder="Mô tả">{{ $variety->desc }}</textarea>
                            </div>


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