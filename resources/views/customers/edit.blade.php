@extends('layouts.app')

@section('content')
<div class="page-title my-3">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h5></h5>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb padding">
                    <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="/customers">Khách Hàng</a></li>
                    <li class="breadcrumb-item">Chỉnh Sửa Khách Hàng</li>
                </ol>
            </nav>
        </div>
    </div>
    @include('layouts.alert')

</div>


<div class="card">

    <div class="card-body">
        <h5 style="margin-bottom: 10px;">Chỉnh Sửa Khách Hàng</h5>
        <form method="POST" action="{{ route('customers.update', $customer->id) }}" class="row">
            @csrf
            @method('PUT')

            <div class="form-group col-lg-4">
                <label for="basicInput">Tên Công Ty</label>
                <input type="text" class="form-control" id="company_name" name="company_name"
                    value="{{ $customer->company_name ?? '' }}">
            </div>
            <div class="form-group col-lg-4">
                <label for="basicInput">Họ Và Tên Khách Hàng</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name"
                    value="{{ $user->name ?? '' }}">
            </div>

            <!-- <div class="form-group col-lg-4">
                    <label for="basicInput">Loại Khách Hàng</label>
                    <select name="customer_type" id="customer_type" class="form-select">
                        <option value="KH dài hạn" {{ $customer->customer_type == 'KH dài hạn' ? 'selected' : '' }}>KH dài
                            hạn</option>
                        <option value="KH ngắn hạn" {{ $customer->customer_type == 'KH ngắn hạn' ? 'selected' : '' }}>KH
                            ngắn hạn</option>
                        <option value="KH phổ thông" {{ $customer->customer_type == 'KH phổ thông' ? 'selected' : '' }}>KH
                            phổ thông</option>
                    </select>
                </div> -->

            <div class="form-group col-lg-4">
                <label for="basicInput">Đia Chỉ</label>
                <input type="text" class="form-control" id="address" name="address"
                    value="{{ $customer->address ?? '' }}">
            </div>
            <style>
                .customer-type-box {
                    border: 1px solid #dce7f1;
                    border-radius: 6px;
                    padding: 10px 15px;
                    background-color: #ffffff;
                    max-height: 200px;
                    overflow-y: auto;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    margin: 0 0 0 1px;
                }

                .customer-type-box .form-check {
                    margin-bottom: 8px;
                }

                .customer-type-box .form-check-label {
                    margin-left: 5px;
                }
            </style>
            <div class="form-group col-lg-12">
                <label>Loại Khách Hàng</label>
                <div class="customer-type-box row">
                    @foreach ($allCustomerTypes as $type)
                    <div class="col-lg-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="customer_type[]"
                                value="{{ $type->id }}" id="type_{{ $type->id }}" {{ in_array($type->id,
                            $customer->customerTypes->pluck('id')->toArray()) ? 'checked' : '' }}>
                            <label class="form-check-label" for="type_{{ $type->id }}">
                                {{ $type->name }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            {{-- <div class="form-group col-lg-4">
                <label for="basicInput">Email</label>
                <input type="text" class="form-control" id="email" name="email" value="{{ $customer->email }}"
            pattern="^[a-zA-Z0-9]+(\.[a-zA-Z0-9]+)*@gmail\.com$"
            title="Email phải có định dạng hợp lệ, ví dụ: example@gmail.com">
    </div> --}}



    <div class="form-group col-lg-4">
        <label for="basicInput">Điện Thoại</label>
        <input type="text" class="form-control" id="phone" name="phone"
            value="{{ $customer->phone ?? '' }}" pattern="0[0-9]{9}" maxlength="10"
            title="Số điện thoại phải bắt đầu bằng 0 và có đúng 10 chữ số">
    </div>
    <div class="form-group col-lg-4">
        <label for="basicInput">Email</label>
        <input type="text" class="form-control" id="email" name="email"
            value="{{ $user->email ?? '' }}" pattern="^[a-zA-Z0-9]+(\.[a-zA-Z0-9]+)*@gmail\.com$"
            title="Email phải có định dạng hợp lệ, ví dụ: example@gmail.com">
    </div>
    <div class="form-group col-lg-4">
        <label for="basicInput">Mật khẩu</label>
        <input type="text" class="form-control" id="password" name="password"
            value="{{ $user->password ?? '' }}">
    </div>
    <div class="form-group">
        <label for="basicInput">Mô Tả</label>

        <textarea name="description" id="description" cols="20" rows="3" class="form-control">{{ $customer->description ?? '' }}</textarea>
    </div>

    <div class="col-sm-12 d-flex justify-content-end">
        <button type="submit" class="btn btn-success me-1 mb-1">Cập Nhật</button>
    </div>
    </form>
</div>
</div>
@endsection