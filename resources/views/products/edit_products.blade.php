@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0">
            Chỉnh sửa: {{ $products->name }}
        </h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Danh Sách</a></li>
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
            <form id="form-products" action="{{ route('products.update', ['id' => $products->id]) }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex">
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="name" class="form-label">Tên Vật Tư</label>
                                <input type="text" class="form-control" name="name" value="{{ $products->name }}"
                                    required>
                            </div>

                            <!-- Danh mục -->
                            <div class="col-md-4">
                                <label for="categoryID" class="form-label">Danh Mục</label>
                                <select name="categoryID" class="form-control" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $products->categoryID == $cat->id ? 'selected' :
                                        '' }}>
                                        {{ $cat->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Đơn vị tính -->
                            <div class="col-md-4">
                                <label for="unitID" class="form-label">Đơn Vị Tính</label>
                                <select name="unitID" class="form-control" required>
                                    <option value="">-- Chọn đơn vị --</option>
                                    @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}" {{ $products->unitID == $unit->id ? 'selected' : ''
                                        }}>
                                        {{ $unit->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Loại -->
                            {{-- <div class="col-md-4 mt-3">
                                <label for="type" class="form-label">Loại sản phẩm</label>
                                <input type="text" name="type" class="form-control" value="{{ $products->type }}">
                            </div> --}}

                            <!-- Trạng thái -->
                            <div class="col-md-4">
                                <label for="status" class="form-label">Trạng Thái</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="Không hoạt động" @if ($products->status == 'Không hoạt động')
                                        selected
                                        @endif>
                                        Không hoạt động</option>
                                    <option value="Hoạt động" @if ($products->status == 'Hoạt động') selected
                                        @endif>Hoạt
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