@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0"></h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{route('all.roles')}}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chỉnh Sửa</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <h5 style="padding: 10px">Chỉnh Sửa Vai Trò</h5>
            <form action="" method="POST">
                @csrf
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="row gy-6">
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <p class="mb-2 text-muted">Tên Vai Trò</p>
                                    <div class="" style="display: flex;gap: 5px;">
                                        <input type="text" name="name" value="{{ $role->name }}" class=" form-control"
                                            placeholder="Enter name" required>
                                        <button type="submit" class="btn btn-primary">Lưu</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection