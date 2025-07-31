@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0"></h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{route('all.roles')}}">Danh Sách</a> </li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm Quyền Cho Vai Trò</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header" style="justify-content: space-between;">
                <h5>
                    Thêm Quyền Cho Vai Trò: {{$role->name}}
                </h5>
                {{-- <div class="card-title">
                    Thêm Quyền Cho Vai Trò: {{$role->name}}
                </div> --}}
                <form action="" method="POST">
                    @csrf
                    <div class="card-body">
                        <p class="mb-2 text-muted">Chọn Quyền</p>
                        <div class="row">
                            @foreach ($permissions as $permission)
                            <div class="col-3">
                                <label for="">
                                    <input type="checkbox" name="permission[]" value="{{ $permission->name }}"
                                        {{in_array($permission->id, $rolePermissions)?'checked':''}}
                                    />
                                    {{$permission->name}}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn-primary">Cập Nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection