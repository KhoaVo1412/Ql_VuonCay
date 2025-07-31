@extends('layouts.app')
@section('content')

<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h3 class="page-title fw-semibold fs-18 mb-0"></h3>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{route('factorys.index')}}">Danh Sách</a></li>
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
            <form id="form-factorys" action="{{ route('factorys.update', ['id' => $factorys->id]) }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex">
                        <h5>Chỉnh Sửa: {{ $factorys->factory_name }}
                        </h5>
                        {{-- <div class="card-title">
                        </div> --}}
                        {{-- <div class="prism-toggle">
                            <button type="submit" class="btn btn-info">
                                <a href="{{route('factorys.index')}}" style="color: #ffffff;">
                                    Danh sách
                                </a>
                            </button>
                            <button type="submit" class="btn btn-secondary">Lưu Thay Đổi</button>
                        </div> --}}
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                <label for="factory_code" class="form-label">Mã</label>
                                <input type="text" class="form-control" name="factory_code" placeholder="Mã"
                                    value="{{ $factorys->factory_code }}" required>
                            </div>

                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                <label for="factory_name" class="form-label">Tên</label>
                                <input type="text" class="form-control" name="factory_name" placeholder="Tên"
                                    value="{{ $factorys->factory_name }}" required>
                            </div>
                            <!-- Trạng thái -->
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                <label for="status" class="form-label">Trạng Thái</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="Không hoạt động" @if($factorys->status == 'Không hoạt động')
                                        selected @endif>Không hoạt động</option>
                                    <option value="Hoạt động" @if($factorys->status == 'Hoạt động') selected
                                        @endif>Hoạt động</option>
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