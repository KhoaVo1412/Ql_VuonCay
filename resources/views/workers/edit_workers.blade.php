@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0">Chỉnh sửa: {{ $workers->name }}</h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('workers.index') }}">Danh Sách</a></li>
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
            <form id="form-workers" action="{{ route('workers.update', ['id' => $workers->id]) }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}

                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex">
                        {{-- <h5></h5> --}}
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
                            /* Tạo khung tròn */
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
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="image" class="form-label">Ảnh Công Nhân</label>
                                {{-- <div class="image-container">
                                    <input type="file" class="form-control" name="image" accept="image/*" id="image"
                                        style="display: none;">
                                    <label for="image" class="image-label">
                                        @if ($workers->image)
                                        <img src="{{ asset($workers->image) }}" alt="Current Image"
                                            class="worker-image">
                                        @else
                                        <span class="placeholder">Chọn ảnh</span>
                                        @endif
                                    </label>
                                </div> --}}
                                <div style="justify-content: center;">
                                    <div style="display: flex; grid-gap: 20px; justify-content: center;">
                                        <div id="image-container"
                                            style="position: relative; width: 80px; height: 80px;border: 3px solid rgb(38, 156, 52);border-radius: 50%;">
                                            <img id="profile-image" src="{{ asset($workers->image) }}"
                                                style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                            <input name="image" type="file" id="image-upload" value="" accept="image/*"
                                                style="display: none;">
                                        </div>
                                        <a href="#" id="upload-link"
                                            style="margin-top: 40px; color: rgb(148, 148, 214); font-size: 13px;">
                                            Chọn Ảnh
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Worker Code -->
                            <div class="col-md-4">
                                <label for="code_name" class="form-label">Mã Công Nhân</label>
                                <input type="text" class="form-control" name="code_name" placeholder="Mã Công Nhân"
                                    value="{{ $workers->code_name }}" required>
                            </div>

                            <!-- Worker Name -->
                            <div class="col-md-4">
                                <label for="name" class="form-label">Tên Công Nhân</label>
                                <input type="text" class="form-control" name="name" placeholder="Tên Công Nhân"
                                    value="{{ $workers->name }}" required>
                            </div>

                            <!-- Birth Date -->
                            <div class="col-md-4">
                                <label for="bdate" class="form-label">Ngày Sinh</label>
                                <input type="date" class="form-control" name="bdate" value="{{ $workers->bdate }}"
                                    required>
                            </div>

                            <!-- Citizen ID -->
                            <div class="col-md-4">
                                <label for="cccd" class="form-label">Căn Cước Công Dân</label>
                                <input type="number" class="form-control" name="cccd" value="{{ $workers->cccd }}"
                                    minlength="9" maxlength="12">
                            </div>

                            <!-- Address -->
                            <div class="col-md-4">
                                <label for="address" class="form-label">Địa Chỉ</label>
                                <input type="text" class="form-control" name="address" value="{{ $workers->address }}"
                                    required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tên Tổ</label>
                                <select name="team_id" class="form-select" required>
                                    <option value="">-- Chọn tổ --</option>
                                    @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ $workers->team_id == $team->id ? 'selected' : ''
                                        }}>
                                        {{ $team->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Chức vụ -->
                            <div class="col-md-4">
                                <label class="form-label">Chức Vụ</label>
                                <select name="duty_id" class="form-select">
                                    <option value="">-- Chọn chức vụ --</option>
                                    @foreach($duties as $duty)
                                    <option value="{{ $duty->id }}" {{ $workers->duty_id == $duty->id ? 'selected' : ''
                                        }}>
                                        {{ $duty->dutyName }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Gender -->
                            <div class="col-md-4">
                                <label for="gender" class="form-label">Giới Tính</label>
                                <select name="gender" id="gender" class="form-control" required>
                                    <option value="0" @if ($workers->gender == 0) selected @endif>Nam</option>
                                    <option value="1" @if ($workers->gender == 1) selected @endif>Nữ</option>
                                </select>
                            </div>

                            <!-- Phone -->
                            <div class="col-md-4">
                                <label for="phone" class="form-label">Số Điện Thoại</label>
                                <input type="text" class="form-control" name="phone" value="{{ $workers->phone }}"
                                    required pattern="^\d{10,11}$" title="Số điện thoại phải có 10 hoặc 11 chữ số">
                            </div>


                            <!-- Status -->
                            <div class="col-md-4">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="Đang làm việc" @if ($workers->status == 'Đang làm việc') selected
                                        @endif>
                                        Đang làm việc</option>
                                    <option value="Nghỉ việc" @if ($workers->status == 'Nghỉ việc') selected
                                        @endif>Nghỉ việc</option>
                                </select>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-md-12 mt-3">
                                <button type="submit" class="btn btn-success">Lưu Thay Đổi</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</section>
<script>
    document.getElementById('image').addEventListener('change', function(event) {
    var file = event.target.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.worker-image').src = e.target.result; // Cập nhật ảnh mới
        };
        reader.readAsDataURL(file);
    }
});

</script>
<script>
    document.getElementById('upload-link').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('image-upload').click();
        });

        document.getElementById('image-upload').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-image').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('submit-btn').addEventListener('click', function(event) {
            let isValid = true;
            const inputs = document.querySelectorAll('.modal-body input, .modal-body select');
            inputs.forEach(input => {
                const errorElement = input.nextElementSibling;
                if (!input.value) {
                    isValid = false;
                    input.classList.add('error-border');
                    if (errorElement) {
                        errorElement.style.display = 'block';
                    }
                } else {
                    input.classList.remove('error-border');
                    if (errorElement) {
                        errorElement.style.display = 'none';
                    }
                }
            });
            // If all fields are valid, submit the form
            if (isValid) {
                document.getElementById('account-form').submit();
            } else {
                event.preventDefault();
            }
        });
</script>
@endsection