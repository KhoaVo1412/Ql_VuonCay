@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0"></h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vehicles.index') }}">Danh Sách Xe</a></li>
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
    @include('layouts.alert')
    <div class="row">
        <div class="col-xl-12">
            <form id="form-vehicles" action="{{ route('vehicles.update', ['id' => $vehicle->id]) }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center text-end">
                        <h5>Chỉnh sửa xe: {{ $vehicle->vehicle_name }}</h5>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- Unit Select -->
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                <label for="unit" class="form-label">Chọn Đơn Vị</label>
                                <select class="form-control" id="unit" name="unit_id" required>
                                    <option value="">Chọn</option>
                                    @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ $vehicle->unit_id == $unit->id ? 'selected' : ''
                                        }}>
                                        {{ $unit->unit_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- drive -->
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                <label for="driver_name" class="form-label">Tài Xế</label>
                                <input type="text" class="form-control" name="driver_name" id="driver_name" required
                                    placeholder="Tên tài xế" value="{{ old('driver_name', $vehicle->driver_name) }}">
                            </div>
                            <!--  -->
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                <label for="vehicle_type" class="form-label">Loại Xe</label>
                                <input type="text" class="form-control" name="vehicle_type" id="vehicle_type" required
                                    placeholder="Nhập loại xe"
                                    value="{{ old('vehicle_type', $vehicle->vehicle_type) }}">
                            </div>
                            <!-- Vehicle Number -->
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                <label for="vehicle_number" class="form-label">Số Xe</label>
                                <input type="text" class="form-control" name="vehicle_number" id="vehicle_number"
                                    required placeholder="Nhập số xe"
                                    value="{{ old('vehicle_number', $vehicle->vehicle_number) }}">
                            </div>

                            <!-- Vehicle Name -->
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                <label for="vehicle_name" class="form-label">Tên Xe</label>
                                <input type="text" class="form-control" name="vehicle_name" id="vehicle_name" required
                                    placeholder="Nhập Tên xe" value="{{ old('vehicle_name', $vehicle->vehicle_name) }}">
                            </div>

                            <!-- Status -->
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                <label for="status" class="form-label">Trạng Thái</label>
                                <select class="form-control" name="status" id="status">
                                    <option value="Hoạt động" {{ $vehicle->status == 'Hoạt động' ? 'selected' : ''
                                        }}>Hoạt động</option>
                                    <option value="Không hoạt động" {{ $vehicle->status == 'Không hoạt động' ?
                                        'selected' : '' }}>Không hoạt động</option>
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
<script>
    $(document).ready(function () {
        // Khi chọn Đơn Vị, cập nhật Nông Trường tương ứng
        $('#unit').on('change', function () {
            const unitId = $(this).val();
            // Ajax to fetch farms related to selected unit
            if (unitId) {
                $.ajax({
                    url: '/get-farms-by-unit/' + unitId,
                    type: 'GET',
                    success: function (data) {
                        $('#farm_id').empty();
                        $('#farm_id').append('<option value="">Chọn Nông Trường</option>');
                        $.each(data, function (key, farm) {
                            $('#farm_id').append('<option value="' + farm.id + '">' + farm.farm_name + '</option>');
                        });
                    },
                    error: function () {
                        alert("Không thể tải thông tin nông trường.");
                    }
                });
            } else {
                $('#farm_id').empty().append('<option value="">Chọn Đơn Vị trước</option>');
            }
        });
    });
</script>
@endsection