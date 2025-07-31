@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0">
            Chỉnh sửa: {{ $categories->name }}
        </h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chỉnh Sửa </li>
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
            <form id="form-categories" action="{{ route('categories.update', ['id' => $categories->id]) }}"
                method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex">

                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- Tên danh mục -->
                            <div class="col-md-4">
                                <label for="name" class="form-label">Tên Danh Mục</label>
                                <input type="text" class="form-control" name="name" value="{{ $categories->name }}"
                                    required>
                            </div>

                            <!-- Kho -->
                            <div class="col-md-4">
                                <label for="warehouse_id" class="form-label">Kho</label>
                                <select name="warehouse_id" class="form-control" required>
                                    <option value="">-- Chọn kho --</option>
                                    @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ $categories->warehouseID == $warehouse->id
                                        ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Danh mục cha -->
                            {{-- <div class="col-md-4 mt-3">
                                <label for="parentID" class="form-label">Danh Mục Cha</label>
                                <select name="parentID" class="form-control">
                                    <option value="">-- Không có --</option>
                                    @foreach($allCategories as $cat)
                                    @if($cat->id != $categories->id)
                                    <option value="{{ $cat->id }}" {{ $categories->parentID == $cat->id ? 'selected' :
                                        '' }}>
                                        {{ $cat->name }}
                                    </option>
                                    @endif
                                    @endforeach
                                </select>
                            </div> --}}
                            <!-- Trạng thái -->
                            <div class="col-md-4">
                                <label for="status" class="form-label">Trạng Thái</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="Không hoạt động" @if ($categories->status == 'Không hoạt động')
                                        selected
                                        @endif>
                                        Không hoạt động</option>
                                    <option value="Hoạt động" @if ($categories->status == 'Hoạt động') selected
                                        @endif>Hoạt
                                        động</option>
                                </select>
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