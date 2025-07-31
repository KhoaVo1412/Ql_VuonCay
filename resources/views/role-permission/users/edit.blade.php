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
                    <li class="breadcrumb-item"><a href="{{route('all.users')}}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chỉnh Sửa</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <h5 style="padding: 10px">Chỉnh Sửa Tài Khoản {{$user->name}}</h5>

            <form action="" method="POST">
                @csrf
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="employees">
                                    <a class="nav-link active" id="info-tab" data-bs-toggle="tab" href="#info"
                                        role="tab" aria-controls="info" aria-selected="true">Thông tin tài khoản</a>
                                </li>
                                <li class="nav-item" role="vaitro">
                                    <a class="nav-link" id="vaitro-tab" data-bs-toggle="tab" href="#vaitro" role="tab"
                                        aria-controls="vaitro" aria-selected="false">Vai trò</a>
                                </li>
                                {{-- <li class="nav-item" role="quyen">
                                    <a class="nav-link" id="quyen-tab" data-bs-toggle="tab" href="#quyen" role="tab"
                                        aria-controls="quyen" aria-selected="false">Quyền</a>
                                </li> --}}
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="info" role="tabpanel"
                                    aria-labelledby="info-tab">
                                    <div class="row gy-4 m-0">
                                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                            <label for="input-label" class="form-label">Tên Người Dùng</label>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ $user->name }}" required>
                                        </div>
                                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                            <label for="input-label" class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control"
                                                value="{{ $user->email }}" required>
                                        </div>
                                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                            <label for="input-placeholder" class="form-label">Mật Khẩu</label>
                                            <input type="text" name="password" class="form-control">
                                        </div>
                                        {{-- <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                            @if($user->hasanyRole('Nông Trường|Admin'))
                                            <h6>Quản Lý Nông Trường</h6>
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="selectAllFarms" name="select_all_farms" value="" {{
                                                            $isFarmsNull ? 'checked' : (old('farms') && in_array('all',
                                                            old('farms')) ? 'checked' : '' ) }}>
                                                        Tất Cả Nông Trường
                                                    </label>
                                                </div>

                                                @foreach ($farm as $unitName => $farms)
                                                <div class="col-6">
                                                    <h6>{{ $unitName }}</h6>
                                                    <div class="row" id="farms_{{ $unitName }}" class="farms-list">
                                                        @foreach ($farms as $val)
                                                        <div class="col-12">
                                                            <label class="form-check-label">
                                                                <input class="form-check-input farm-checkbox"
                                                                    type="checkbox" name="farms[]"
                                                                    value="{{ $val->id }}" {{ in_array($val->id,
                                                                $user->farms->pluck('id')->toArray()) ||
                                                                in_array($val->id, old('farms', [])) ? 'checked' : ''
                                                                }}>
                                                                {{ $val->farm_name
                                                                }}
                                                            </label>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>

                                            <script>
                                                document.getElementById('selectAllFarms').addEventListener('change', function () {
                                                    var farmsCheckboxes = document.querySelectorAll('.farm-checkbox'); 
                                                    var isChecked = this.checked; 

                                                    farmsCheckboxes.forEach(function (checkbox) {
                                                        if (isChecked) {
                                                            checkbox.checked = false; 
                                                            checkbox.disabled = false;
                                                        } else {
                                                            checkbox.checked = false;
                                                            checkbox.disabled = false;
                                                        }
                                                    });
                                                });
                                                // document.getElementById('selectAllFarms').addEventListener('change', function () {
                                                //     var farmsList = document.querySelectorAll('.farms-list');
                                                //     if (this.checked) {
                                                //         farmsList.forEach(function (list) {
                                                //             list.style.display = 'block';  // Show the farms when checked
                                                //         });
                                                //     } else {
                                                //         farmsList.forEach(function (list) {
                                                //             list.style.display = 'none';   // Hide farms when not checked
                                                //         });
                                                //     }
                                                // });
                                            </script>
                                            @endif
                                        </div> --}}
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="vaitro" role="tabpanel" aria-labelledby="vaitro-tab">
                                    <div class="row gy-4 m-0">
                                        <div class="col-xl-12">
                                            <label for="input-text" class="form-label">Chọn Vai Trò</label>
                                            <div class="mb-3">
                                                <button type="button" class="btn btn-primary" id="check-all">Chọn Tất
                                                    Cả</button>
                                                <button type="button" class="btn btn-secondary" id="uncheck-all">Bỏ Chọn
                                                    Tất Cả</button>
                                            </div>
                                            <div class="row">
                                                @foreach ($roles as $role)
                                                <div class="col-xl-12 mb-4">
                                                    <div class="row">
                                                        <div class="col-xl-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="roles[]" value="{{ $role }}"
                                                                    id="role-{{ $role }}" {{ in_array($role, $userRoles)
                                                                    ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="role-{{ $role }}">
                                                                    {{ $role }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mt-2" style="padding: 0 0 0 12px;">
                                    <button type="submit" class="btn btn-primary">Cập Nhật</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#check-all').click(function() {
            $('input[type="checkbox"]').prop('checked', true);
            $('#unchecked-roles').val('');
        });
        $('#uncheck-all').click(function() {
            $('input[type="checkbox"]').prop('checked', false);

            var uncheckedRoles = [];
            $('input[type="checkbox"]').each(function() {
                if (!$(this).prop('checked')) {
                    uncheckedRoles.push($(this).val());
                }
            });
            $('#unchecked-roles').val(uncheckedRoles.join(','));
        });

        $('form').submit(function(e) {
            if ($('input[type="checkbox"]:checked').length == 0) {
                swal.fire({
                    icon: 'error',
                    title: 'Thông Báo !',
                    text: 'Bạn phải chọn ít nhất một quyền.',
                    confirmButtonText: 'Ok'
                });
                e.preventDefault();
            } else {
                if ($('#unchecked-roles').val() == '') {
                    var uncheckedRoles = [];
                    $('input[type="checkbox"]').each(function() {
                        if (!$(this).prop('checked')) {
                            uncheckedRoles.push($(this).val());
                        }
                    });
                    $('#unchecked-roles').val(uncheckedRoles.join(','));
                }
            }
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection