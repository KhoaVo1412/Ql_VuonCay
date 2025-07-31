@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0">Thêm Nhân Sự</h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('workers.index') }}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm Nhân Sự</li>
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
            <form id="form-workers" action="{{ route('workers.save')}}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex">
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4" style="text-align: center">
                                <label class="form-label">Ảnh Công Nhân</label>
                                <div class="image-container">
                                    <!-- Input file để chọn ảnh -->
                                    <input type="file" class="form-control" name="image" accept="image/*" id="image"
                                        style="display: none;">

                                    <!-- Label hiển thị ảnh hoặc icon camera -->
                                    <label for="image" class="image-label">
                                        <span class="placeholder">
                                            <i class="fa-solid fa-camera" style="color:black; font-size: 20px;"></i>
                                        </span>
                                        <!-- Ảnh sẽ được hiển thị trong input -->
                                        <img id="preview-image" src="" alt="Current Image" class="worker-image"
                                            style="display: none;">
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Mã Công Nhân</label>
                                <input type="text" class="form-input" name="code_name" placeholder="Mã công nhân"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tên Công Nhân</label>
                                <input type="text" class="form-input" name="name" required>
                            </div>
                            <div class="col-md-4">

                                <label class="form-label">Ngày Sinh</label>
                                <input type="date" class="form-input" name="bdate" placeholder="Ngày sinh" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Căn Cước Công Dân</label>
                                <input type="number" class="form-input" name="cccd" minlength="9" maxlength="12"
                                    placeholder="CCCD">
                            </div>
                            <div class="col-md-4"> <label class="form-label">Địa Chỉ</label>
                                <input type="text" class="form-input" name="address" placeholder="Địa chỉ" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tổ *</label>
                                <select class="form-select" name="team_id" required>
                                    <option value="">-- Chọn tổ --</option>
                                    @foreach($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Chức Vụ</label>
                                <select class="form-select" name="duty_id">
                                    <option value="">-- Chọn chức vụ --</option>
                                    @foreach($duties as $duty)
                                    <option value="{{ $duty->id }}">{{ $duty->dutyName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Giới Tính</label>
                                <select class="form-select" name="gender" required>
                                    <option value="">--Chọn giới tính--</option>
                                    <option value="0">Nam</option>
                                    <option value="1">Nữ</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Số Điện Thoại</label>
                                <input type="text" class="form-input" name="phone" required pattern="^\d{10,11}$"
                                    title="Số điện thoại phải có 10 hoặc 11 chữ số" placeholder="Số điện thoại">
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <button type="submit" class="btn btn-success">Lưu Thay Đổi</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <style>
        .image-container {
            position: relative;
            display: inline-block;
        }

        .image-label {
            display: block;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
        }

        .worker-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .placeholder {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            text-align: center;
            background-color: #ddd;
            color: #fff;
            font-size: 12px;
            border-radius: 50%;
        }
    </style>
</section>
<script>
    document.getElementById('image').addEventListener('change', function(event) {
    var file = event.target.files[0];
    var reader = new FileReader();
    
    reader.onload = function(e) {
        var previewImage = document.getElementById('preview-image');
        previewImage.src = e.target.result;  // Đặt ảnh cho element img
        previewImage.style.display = 'block'; // Hiển thị ảnh
    };
    
    if (file) {
        reader.readAsDataURL(file); // Đọc ảnh và hiển thị
    }
});

</script>
@endsection