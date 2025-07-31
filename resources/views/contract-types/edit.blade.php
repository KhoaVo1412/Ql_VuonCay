@extends("layouts.app")

@section('content')

<div class="page-title my-3">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h5></h5>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" style="font-size: 14px;" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb padding">
                    <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="/contract-types">Loại Hợp Đồng</a></li>
                    <li class="breadcrumb-item">Chỉnh Sửa</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- @include("layouts.alert") -->

</div>

@include('layouts.alert')

<div class="card">

    <div class="card-body">
        <div class="row">
            <h5 class="p-b-5">Chỉnh Sửa Loại Hợp Đồng</h5>
            <form method="POST" action="{{ route('contract-types.update', $contractType->id) }}">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="basicInput">Loại Hợp Đồng</label>
                    <input type="text" class="form-control" id="contract_type_type" name="contract_type_type"
                        value="{{$contractType->contract_type_type}}" required>
                </div>

                <div class="form-group">
                    <label for="basicInput">Mã Loại Hợp Đồng</label>
                    <input type="text" class="form-control" id="contract_type_code" name="contract_type_code"
                        value="{{$contractType->contract_type_code}}" required>
                </div>

                <div class="form-group">
                    <label for="basicInput">Tên Loại Hợp Đồng</label>
                    <input type="text" class="form-control" id="contract_type_name" name="contract_type_name"
                        value="{{$contractType->contract_type_name}}" required>
                </div>

                <div class="col-sm-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary me-1 mb-1">Lưu Thay Đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>





@endsection