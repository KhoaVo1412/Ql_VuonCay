@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0"></h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Danh Sách Mủ</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Add typeofpus Modal -->
<form id="typeofpus-form" action="{{ route('typeofpus.save') }}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="modal fade" id="create-typeofpus" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Tạo Mới</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="row gy-2">
                        <!-- Tên -->
                        <div class="col-xl-12">
                            <label for="code_pus" class="form-label">Mã Mủ</label>
                            <input type="text" class="form-control" name="code_pus" id="code_pus" required
                                placeholder="Nhập mã">
                        </div>
                        <div class="col-xl-12">
                            <label for="name_pus" class="form-label">Loại Mủ</label>
                            <input type="text" class="form-control" name="name_pus" id="name_pus" required
                                placeholder="Nhập tên">
                        </div>
                        <!-- Trạng thái -->
                        {{-- <div class="col-xl-12">
                            <label for="status" class="form-label">Trạng Thái</label>
                            <select class="form-control" name="status" id="status">
                                <option value="Hoạt động">Hoạt động</option>
                                <option value="Không hoạt động">Không hoạt động</option>
                            </select>
                        </div> --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="submit-btn-typeofpus">Lưu</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- typeofpus List -->
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Danh Sách Loại Mủ</h5>
                <button class="btn btn-sm btn-primary btn-wave waves-light" data-bs-toggle="modal"
                    data-bs-target="#create-typeofpus">
                    <i class="ri-add-line fw-semibold align-middle me-1"></i> Tạo Mủ
                </button>
            </div>

            <div class="card-body">
                {{-- <div style="position: relative;"> --}}
                    {{-- <div class="table-responsive"> --}}
                        <div id="buttons-container" class="d-flex justify-content-end gap-2">
                            {{-- <button id="edit-selected-btn" class="btn btn-warning"
                                style="display: none; border-radius: 30px; color: #FFFFFF">Không/Hoạt
                                Động</button> --}}
                            <button id="delete-selected-btn" class="btn btn-danger" {{--
                                style="display: none; border-radius: 30px;" --}} style="border-radius: 30px;">
                                Xóa
                            </button>
                        </div>
                        <table id="typeofpus-table" class="table table-bordered text-nowrap w-100">


                            <thead>
                                <tr>
                                    <th></th>
                                    <th>
                                        <input class="form-check-input check-all" type="checkbox" id="select-all-pus"
                                            value="" aria-label="...">
                                    </th>
                                    {{-- <th scope="col">STT</th> --}}
                                    <th scope="col">Mã</th>
                                    <th scope="col">Tên Mủ</th>
                                    {{-- <th scope="col">Trạng Thái</th> --}}
                                    {{-- <th scope="col">Chức năng</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this section -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th scope="col"></th>
                                    {{-- <th scope="col">STT</th> --}}
                                    <th scope="col">Mã</th>
                                    <th scope="col">Tên Mủ</th>
                                    {{-- <th scope="col">Trạng Thái</th> --}}
                                    {{-- <th scope="col">Chức năng</th> --}}
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
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Xác Nhận Cập Nhật</h5>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn chắc chắn muốn cập nhật trạng thái đã chọn?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" id="confirmUpdateBtn" class="btn btn-primary">Xác Nhận</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Xoa -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác Nhận Xóa</h5>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn chắc chắn muốn xóa loại mủ đã chọn?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-primary">Xác Nhận</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
            var selectedRows = new Set();
            var dataTable = $('#typeofpus-table').DataTable({
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
                    url: '{{ route('typeofpus.index') }}',
                    type: 'GET'
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
                        data: 'check',
                        name: 'check',
                        orderable: false,
                        searchable: false
                    },
                    // { data: 'stt', name: 'stt' },
                    {
                        data: 'code_pus',
                        name: 'code_pus'
                    },
                    {
                        data: 'name_pus',
                        name: 'name_pus'
                    },
                    // { data: 'status', name: 'status' },
                    // { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                rowCallback: function(row, data) {
                    $(row).attr('data-id', data.id);
                }
            });

            $('#select-all-pus').on('change', function() {
                var checked = $(this).prop('checked');
                $('#typeofpus-table tbody .form-check-input').each(function() {
                    var pusId = $(this).data('id');
                    if (checked) {
                        selectedRows.add(pusId);
                    } else {
                        selectedRows.delete(pusId);
                    }
                    $(this).prop('checked', checked);
                });
                toggleButtons();

                console.log([...selectedRows]);
            });
            $('#typeofpus-table tbody').on('change', '.form-check-input', function() {
                var pusId = $(this).data('id');
                toggleButtons();

                if ($(this).prop('checked')) {
                    selectedRows.add(pusId);
                } else {
                    selectedRows.delete(pusId);
                }
                console.log([...selectedRows]);
            });
            $('#typeofpus-table').on('draw.dt', function() {
                $('#typeofpus-table tbody .form-check-input').each(function() {
                    var pusId = $(this).data('id');
                    if (selectedRows.has(pusId)) {
                        $(this).prop('checked', true);
                    }
                });
            });

            // function toggleButtons() {
            //     var selectedRows = $('#typeofpus-table tbody .form-check-input:checked');
            //     if (selectedRows.length > 0) {
            //         $('#edit-selected-btn').show();
            //         $('#delete-selected-btn').show();
            //     } else {
            //         $('#edit-selected-btn').hide();
            //         $('#delete-selected-btn').hide();
            //     }
            // }

            function toggleButtons() {
                var selected = $('#typeofpus-table tbody .form-check-input:checked').length;
                if (selected > 0) {
                    $('#buttons-container').css('visibility', 'visible');
                } else {
                    $('#buttons-container').css('visibility', 'hidden');
                }
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#edit-selected-btn').on('click', function() {
                $('#confirmModal').modal('show');
                $('#confirmUpdateBtn').on('click', function() {
                    $.ajax({
                        url: '/typeofpus/edit-multiple',
                        type: 'POST',
                        data: {
                            ids: [...selectedRows],
                            status: 'Không hoạt động',
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: 'Trạng thái đã được cập nhật.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                selectedRows.clear(); // Reset danh sách đã chọn
                                $('#confirmModal').modal('hide');
                                $('#buttons-container').hide();
                                dataTable.ajax.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: 'Có lỗi khi cập nhật trạng thái.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });
                $('#confirmModal').on('hidden.bs.modal', function() {
                    $('#confirmUpdateBtn').off('click');
                });
            });
            $('#delete-selected-btn').on('click', function() {
                $('#deleteModal').modal('show');
                $('#confirmDeleteBtn').on('click', function() {
                    $.ajax({
                        url: '/typeofpus/delete-multiple',
                        type: 'POST',
                        data: {
                            ids: [...selectedRows]
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: 'Dữ liệu đã được xóa thành công.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                selectedRows.clear();
                                $('#deleteModal').modal('hide');
                                $('#buttons-container').hide();
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Có lỗi xảy ra!',
                                text: 'Đã xảy ra lỗi trong quá trình xóa.',
                                confirmButtonText: 'OK'
                            });
                            console.error('Error:', xhr);
                        }
                    });
                });
                $('#deleteModal').on('hidden.bs.modal', function() {
                    $('#confirmDeleteBtn').off('click');
                });
            });
        });
</script>

<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.5/dist/sweetalert2.min.css" rel="stylesheet">
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.5/dist/sweetalert2.min.js"></script>

<script>
    $(document).on('click', '.toggle-status', function(e) {
            e.preventDefault();

            let button = $(this);
            let id = button.data('id');
            let currentStatus = button.text().trim();

            Swal.fire({
                title: "Xác nhận thay đổi",
                text: `Bạn có chắc chắn muốn chuyển trạng thái từ "${currentStatus}" sang trạng thái mới?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Thay đổi!",
                cancelButtonText: "Hủy"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('typeofpus.status') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                let newStatus = response.status === 'Hoạt động' ? 'Hoạt động' :
                                    'Không hoạt động';

                                button.removeClass('bg-danger bg-success')
                                    .addClass(response.status === 'Hoạt động' ? 'bg-success' :
                                        'bg-danger')
                                    .text(newStatus);

                                Swal.fire({
                                    title: "Thành công!",
                                    text: `Trạng thái đã được cập nhật thành "${newStatus}".`,
                                    icon: "success",
                                    confirmButtonText: "OK",
                                    timer: 3000
                                });
                            } else {
                                Swal.fire({
                                    title: "Lỗi!",
                                    text: response.message ||
                                        "Không thể thay đổi trạng thái.",
                                    icon: "error",
                                    confirmButtonText: "OK",
                                    timer: 3000
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: "Lỗi!",
                                text: "Không thể thay đổi trạng thái, vui lòng thử lại.",
                                icon: "error",
                                confirmButtonText: "OK",
                                timer: 3000
                            });
                        }
                    });
                }
            });
        });
</script>
@endsection