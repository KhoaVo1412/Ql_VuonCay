@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0">Lịch Sử Đăng Nhập</h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Lịch Sử Đăng Nhập</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between align-items-center" style="grid-gap: 3px">
                {{-- <h5>Lịch Sử Đăng Nhập</h5> --}}
            </div>
            <div class="mb-3 d-flex gap-2" style="padding-left: 25px">
                <label for="filter-user-id" class="form-lable" style="font-weight: bold; padding-top: 5px;">Lọc
                    Tài Khoản</label>
                <select id="filter-user-id" class="form-control" style="max-width: 250px;">
                    <option value="">-- Tất cả tài khoản --</option>
                    @foreach($filteredUsers as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Trường nhập ID của mục cần xóa -->
            {{-- <input type="text" id="item-id" placeholder="Nhập ID mục cần xóa">
            <button id="delete-button">Xóa</button> --}}

            <!-- Hiển thị thông báo lỗi hoặc thành công -->
            <div id="message"></div>

            <div class="card-body">
                {{-- <div style="position: relative;"> --}}
                    {{-- <div class="table-responsive"> --}}
                        <table id="check-login-table" class="table table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th></th>
                                    {{-- <th>
                                        <input class="form-check-input check-all" type="checkbox" id="select-all-farms"
                                            value="" aria-label="...">
                                    </th> --}}
                                    <th scope="col">STT</th>
                                    <th scope="col">Tên Tài Khoản</th>
                                    <th scope="col">IP</th>
                                    <th scope="col">Ngày Đăng Nhập</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this section -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th scope="col">STT</th>
                                    <th scope="col">Tên Tài Khoản</th>
                                    <th scope="col">IP</th>
                                    <th scope="col">Ngày Đăng Nhập</th>

                                    {{-- <th scope="col">Thao tác</th> --}}
                                </tr>
                            </tfoot>
                        </table>
                        {{--
                    </div> --}}
                    {{-- </div> --}}
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
            var selectedRows = new Set();
            var dataTable = $('#check-login-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json",
                    "emptyTable": "Không có dữ liệu",
                },
                processing: true,
                serverSide: true,
                // responsive: true,
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
                    url: '{{ route("checkLogin.index") }}',
                    type: 'GET',
                    data: function (d) {
                        d.user_id = $('#filter-user-id').val();
                    }
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
                    // {
                    //     data: 'check',
                    //     name: 'check',
                    //     orderable: false,
                    //     searchable: false
                    // },
                    {
                        data: 'stt',
                        name: 'stt'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'ip_address',
                        name: 'ip_address'
                    },
                    {
                        data: 'login_at',
                        name: 'login_at'
                    },
                    
                ],
                // rowCallback: function(row, data) {
                //     $(row).attr('data-id', data.id);
                // }
            });
$('#filter-user-id').on('change', function () {
    dataTable.draw();
});
        });
</script>
<script>
    $(document).ready(function() {
        $('#delete-button').on('click', function() {
            var itemId = $('#item-id').val();  // Lấy ID từ trường input

            // Kiểm tra nếu ID không rỗng
            if (itemId === "") {
                $('#message').text("Vui lòng nhập ID của mục cần xóa!").css("color", "red");
                return;
            }

            // Hiển thị hộp thoại xác nhận trước khi xóa
            if (confirm("Bạn có chắc chắn muốn xóa mục này?")) {
                $.ajax({
                    url: '/delete-item',  // Địa chỉ API để xóa item
                    method: 'DELETE',  // Phương thức xóa
                    data: {
                        id: itemId,
                        _token: '{{ csrf_token() }}'  // CSRF token để bảo vệ ứng dụng
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#message').text("Xóa thành công!").css("color", "green");
                        } else {
                            $('#message').text("Có lỗi xảy ra hoặc mục không tồn tại!").css("color", "red");
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#message').text("Có lỗi xảy ra, vui lòng thử lại!").css("color", "red");
                    }
                });
            }
        });
    });
</script>
@endsection