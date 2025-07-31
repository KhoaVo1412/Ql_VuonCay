@extends('layouts.app')
@section('content')
<div class="container-fluid1">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0"></h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Danh Sách Tài Khoản</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Modal -->
    <style>
        .modal-content {
            width: 555px;
        }

        .input-group {
            display: flex;
            align-items: center;
        }

        .input-group .btn {
            border-left: 0;
            /* background-color: #fff; */
            cursor: pointer;
        }
    </style>
    <div>
        <form id="account-form" action="{{ route('store.users') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal fade" id="create-task" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addModalLabel">Tạo Tài Khoản</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-4">
                            <div class="row gy-2">
                                <div class="errMess"></div>
                                <div class="col-md-12">
                                    <label class="form-label" style="font-weight: bold;" for="name">Tên</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ old('name') }}" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label" style="font-weight: bold;" for="email">Email</label>
                                    {{-- <input type="email" class="form-control" name="email" id="email"
                                        value="{{ old('email') }}" required> --}}
                                    <input type="email" class="form-control" name="email" id="email"
                                        value="{{ old('email') }}" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                        title="Vui lòng nhập đúng định dạng email, ví dụ: example@email.com" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label" style="font-weight: bold;" for="password">Mật Khẩu</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password" id="password"
                                            required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label" style="font-weight: bold;" for="roles">Vai Trò</label>
                                    <div class="form-check">
                                        @foreach ($roles as $role)
                                        <div class="mb-2">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <input type="checkbox" class="form-check-input" name="roles[]"
                                                        value="{{ $role }}" id="role_{{ $role }}" @if(in_array($role,
                                                        old('roles', []))) checked @endif>
                                                    <label class="form-check-label" for="role_{{ $role }}">{{ $role
                                                        }}</label>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach

                                    </div>
                                </div>
                                {{-- <div class="col-md-12">
                                    <label class="form-label" style="font-weight: bold;" for="roles">Vai Trò</label>
                                    <div class="form-check">
                                        <div class="row">
                                            @foreach ($roles as $role)
                                            <div class="col-md-6">
                                                <input type="checkbox" class="form-check-input" name="roles[]"
                                                    value="{{ $role }}" id="role_{{ $role }}" @if(in_array($role,
                                                    old('roles', []))) checked @endif>
                                                <label class="form-check-label" for="role_{{ $role }}">{{ $role
                                                    }}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div> --}}

                                {{-- <div class="col-md-12" id="farm-select-container" style="display:none;">
                                    <label class="form-label" style="font-weight: bold;" for="farm_id">Quản Lý Xe Theo
                                        Nông Trường</label>
                                    <select class="form-control" name="farm_id">
                                        <option value="">Tất Cả Nông Trường</option>
                                        @foreach ($farm as $val)
                                        <option value="{{ $val->id }}" @if(old('farm_id')==$val->id) selected @endif>
                                            {{ $val->farm_name }} - {{$val->unitRelation->unit_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div> --}}
                                {{-- <div class="col-md-12" id="farm-select-container" style="display:none;">
                                    <label class="form-label" style="font-weight: bold;" for="farms">Quản Lý Nông
                                        Trường</label>
                                    <select class="form-control" name="farms[]" multiple>
                                        <option value="">Tất Cả Nông Trường</option>
                                        @foreach ($farm as $val)
                                        <option value="{{ $val->id }}" @if(in_array($val->id, old('farms', [])))
                                            selected @endif>
                                            {{ $val->farm_name }} - {{ $val->unitRelation->unit_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div> --}}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary btn_add">Tạo Tài Khoản</button>
                        </div>
                    </div>
                </div>
        </form>
        <script>
            $(document).ready(function() {
                function toggleFarmSelect() {
                    // Check Nông Trường
                    var selectedRoles = $('input[name="roles[]"]:checked').map(function() {
                        return $(this).val();
                    }).get();
    
                    if (selectedRoles.includes('Nông Trường')) {
                        $('#farm-select-container').show();
                    } else {
                        $('#farm-select-container').hide();
                    }
                }
                    $('input[name="roles[]"]').change(function() {
                    toggleFarmSelect();
                });
                    toggleFarmSelect();
            });
        </script>
        <script>
            document.getElementById('togglePassword').addEventListener('click', function() {
                const passwordInput = document.getElementById('password');
                const icon = this.querySelector('i');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password'; 
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });

        </script>
    </div>

</div>
<div class="col-lg-12 stretch-card">
    <div class="card">
        <div class="card custom-card">
            <div class="card-header" style="justify-content: space-between; text-align: center;">
                <div class="card-title">
                </div>
                @include('role-permission.nav_permission')
            </div>
            @include('layouts.alert')
            <div class="card-header" style="justify-content: space-between; text-align: right;">
                <div class="card-title" style="justify-content: space-between;display: flex;">
                    <h5>Tài Khoản Quản Trị</h5>
                    <button class="btn btn-sm btn-primary btn-wave waves-light" data-bs-toggle="modal"
                        data-bs-target="#create-task">
                        <i class="ri-add-line fw-semibold align-middle me-1"></i> Tạo Tài Khoản
                    </button>
                </div>
            </div>
            <div class="card-body">
                {{-- <div class="table-responsive"> --}}
                    <div id="hidden-columns_wrapper" class="dataTables_wrapper dt-bootstrap5">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="data-table" class="table table-bordered text-nowrap w-100 dataTable"
                                    aria-describedby="hidden-columns_info" style="width: 963px;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>
                                                STT
                                            </th>
                                            <th>
                                                Tên
                                            </th>
                                            <th>
                                                Email
                                            </th>
                                            {{-- <th>
                                                Mật Khẩu
                                            </th> --}}
                                            <th>
                                                Vai Trò
                                            </th>
                                            <th>
                                                Thao Tác
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th>
                                                STT
                                            </th>
                                            <th>
                                                Tên
                                            </th>
                                            <th>
                                                Email
                                            </th>
                                            {{-- <th>
                                                Mật Khẩu
                                            </th> --}}
                                            <th>
                                                Vai Trò
                                            </th>
                                            <th>
                                                Thao Tác
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <script type="text/javascript">
                                    $(document).ready(function() {
                                        var dataTable = $('#data-table').DataTable({
                                            "language": {
                                                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json"
                                            },
                                            processing: true,
                                            serverSide: true,
                                            columnDefs: [{
                                                className: 'dtr-control',
                                                orderable: false,
                                                targets: 0,
                                            }],
                                            order: [1, 'asc'],
                                            responsive: {
                                                details: {
                                                    type: 'column',
                                                    renderer: function(api, rowIdx, columns) {
                                                        var data = $.map(columns, function(col, i) {
                                                            return col.hidden ?
                                                                '<li data-dtr-index="' + i + '" data-dt-row="' + rowIdx +
                                                                '" data-dt-column="' + col.columnIndex + '">' +
                                                                '<span class="dtr-title">' + col.title + ':</span> ' +
                                                                '<span class="dtr-data">' + col.data + '</span>' +
                                                                '</li>' :
                                                                '';
                                                        }).join('');

                                                        return data ? $('<ul data-dtr-index="' + rowIdx + '" class="dtr-details"/>')
                                                            .append(data) : false;
                                                    }
                                                }
                                            },
                                            ajax: {
                                                url: "{{ route('all.users') }}",
                                            },
                                            columns: [{
                                                    data: null,
                                                    name: null,
                                                    orderable: false,
                                                    searchable: false,
                                                    render: function(data, type, row) {
                                                        return '';
                                                    }

                                                },
                                                {
                                                    data: 'stt',
                                                    name: 'stt'
                                                },
                                                {
                                                    data: 'name',
                                                    name: 'name'
                                                },
                                                {
                                                    data: 'email',
                                                    name: 'email'
                                                },
                                                // {data: 'password', name: 'password'},
                                                {
                                                    data: 'role',
                                                    name: 'role'
                                                },
                                                {
                                                    data: 'action',
                                                    name: 'action'
                                                },
                                            ]
                                        });
                                        // $('#table_email').on('submit', function(e) {
                                        //     e.preventDefault();
                                        //     dataTable.ajax.reload(); // Tải lại dữ liệu DataTables sau khi thay đổi lọc
                                        // });
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                    {{--
                </div> --}}
            </div>
        </div>
    </div>
</div>
</div>
@endsection