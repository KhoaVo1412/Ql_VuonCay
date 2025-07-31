@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold mb-0" style="color: #22573E">Phiếu Cây Gãy/Đỗ</h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Danh Sách Cây Gãy/Đỗ</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Add treesfelleds Modal -->
<form id="treesfelleds-form" action="{{ route('treesfelleds.save') }}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="modal fade" id="create-treesfelleds" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Thêm Phiếu</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="row gy-2">
                        <!-- detectionDate -->
                        <div class="col-md-6">
                            <label class="form-label">Ngày Phát Hiện</label>
                            <input type="date" class="form-control" name="detectionDate" required>
                        </div>

                        <!-- plantID -->
                        <div class="col-md-6">
                            <label class="form-label">Cây Bị Gãy/Đổ</label>
                            <select class="form-select" name="plantID" required>
                                <option value="">-- Chọn mã cây --</option>
                                @foreach($plants as $plant)
                                <option value="{{ $plant->id }}">{{ $plant->plantCode }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- specificLocation -->
                        <div class="col-md-6">
                            <label class="form-label">Vị Trí Cụ Thể</label>
                            <input type="text" class="form-control" name="specificLocation" required>
                        </div>

                        <!-- cause -->
                        <div class="col-md-6">
                            <label class="form-label">Nguyên Nhân</label>
                            <input type="text" class="form-control" name="cause">
                        </div>

                        <!-- treeCondition -->
                        <div class="col-md-6">
                            <label class="form-label">Tình Trạng Cây</label>
                            <input type="text" class="form-control" name="treeCondition">
                        </div>

                        <!-- reportStatus -->
                        <div class="col-md-6">
                            <label class="form-label">Trạng Thái Báo Cáo</label>
                            <select class="form-select" name="reportStatus">
                                <option value="Chưa xử lý">Chưa xử lý</option>
                                <option value="Đã xử lý">Đã xử lý</option>
                            </select>
                        </div>

                        <!-- workerID -->
                        <div class="col-md-6">
                            <label class="form-label">Người Phát Hiện</label>
                            <select class="form-select" name="workerID" required>
                                <option value="">-- Chọn công nhân --</option>
                                @foreach($workers as $worker)
                                <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success" id="submit-btn-treesfelleds">Tạo Báo Cáo</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- treesfelleds List -->
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="search-box">
                        <input type="text" class="search-inputs" placeholder="Tìm kiếm...">
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-content">
                    <div class="form-section">
                        <!-- First Filter Row -->
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Tên Phiếu</label>
                                <select class="form-select" id="taskName">
                                    <option value="">-- Tất cả --</option>
                                    <option value="T1">T1</option>
                                    <option value="T2">T2</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Ngày Ghi Nhận</label>
                                <input type="date" class="form-input datepicker" id="taskStartDate"
                                    placeholder="Chọn Ngày Bắt Đầu">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tên Vườn</label>
                                <select class="form-select" id="taskGarden">
                                    <option value="">-- Tất cả --</option>
                                    <option value="V1">V1</option>
                                    <option value="V2">V2</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tên Lô</label>
                                <select class="form-select" id="taskLot">
                                    <option value="">-- Tất cả --</option>
                                    <option value="L1">L1</option>
                                    <option value="L2">L2</option>
                                </select>
                            </div>
                            <div class="form-group" style="display: flex; align-items: end;">
                                <button class="btn btn-success" onclick="filterTasks()"
                                    style="width: 100%;border-radius: 10px;">
                                    <i class="fas fa-filter"></i>
                                    Lọc
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <table id="treesfelleds-table" class="table table-bordered text-nowrap w-100">
                        <div id="buttons-container" class="d-flex justify-content-end gap-2">
                            <button id="edit-selected-btn" class="btn btn-warning"
                                style="border-radius: 7px; color: #FFFFFF; display: none">Không/Hoạt
                                Động</button>
                            <button id="delete-selected-btn" class="btn btn-danger"
                                style="border-radius: 7px; display: none;">
                                Xóa
                            </button>
                            <button id="add-selected-btn" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#create-treesfelleds" style="border-radius: 7px;">
                                <i class="fa fa-plus"></i>Thêm Phiếu
                            </button>
                        </div>
                        <thead>
                            <tr>
                                <th></th>
                                <th>
                                    <input class="form-check-input check-all" type="checkbox"
                                        id="select-all-treesfelleds" value="" aria-label="...">
                                </th>
                                <th scope="col">Mã Cây</th>
                                <th scope="col">Lô</th>
                                <th scope="col">Vị Trí</th>
                                <th scope="col">Ngày Phát Hiện</th>
                                <th scope="col">Người Phát Hiện</th>
                                <th scope="col">Xử Lý</th>
                                <th scope="col">Trạng Thái</th>
                                <th scope="col">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables will populate this section -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th scope="col">Mã Cây</th>
                                <th scope="col">Lô</th>
                                <th scope="col">Vị Trí</th>
                                <th scope="col">Ngày Phát Hiện</th>
                                <th scope="col">Người Phát Hiện</th>
                                <th scope="col">Xử Lý</th>
                                <th scope="col">Trạng Thái</th>
                                <th scope="col">Thao Tác</th>
                            </tr>
                        </tfoot>
                    </table>
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
                    <button type="button" id="confirmUpdateBtn" class="btn btn-success">Xác Nhận</button>
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
                    Bạn chắc chắn muốn xóa cây ngã đã chọn?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-success">Xác Nhận</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
            var selectedRows = new Set();
            var dataTable = $('#treesfelleds-table').DataTable({
                // "language": {
                //     "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json",
                //     "emptyTable": "Không có dữ liệu",
                // },
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
                    url: '{{ route('treesfelleds.index') }}',
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
                    { data: 'plant_code', name: 'plant.plantCode' },
                    { data: 'plot_name', name: 'plant.plot.name' },
                    { data: 'specific_location', name: 'specificLocation' },
                    { data: 'detection_date', name: 'detectionDate' },
                    { data: 'worker_name', name: 'worker.name' },
                    // { data: 'tree_condition', name: 'treeCondition' },
                    { data: 'report_status', name: 'reportStatus' },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                rowCallback: function(row, data) {
                    $(row).attr('data-id', data.id);
                }
            });
            $('#select-all-treesfelleds').on('change', function() {
                var checked = $(this).prop('checked');
                $('#treesfelleds-table tbody .form-check-input').each(function() {
                    var workId = $(this).data('id');
                    if (checked) {
                        selectedRows.add(workId);
                    } else {
                        selectedRows.delete(workId);
                    }
                    $(this).prop('checked', checked);
                });
                toggleButtons();

                console.log([...selectedRows]);
            });
            $('#treesfelleds-table tbody').on('change', '.form-check-input', function() {
                var workId = $(this).data('id');
                toggleButtons();

                if ($(this).prop('checked')) {
                    selectedRows.add(workId);
                } else {
                    selectedRows.delete(workId);
                }
                console.log([...selectedRows]);
            });

            $('#treesfelleds-table').on('draw.dt', function() {
                $('#treesfelleds-table tbody .form-check-input').each(function() {
                    var workId = $(this).data('id');
                    if (selectedRows.has(workId)) {
                        $(this).prop('checked', true);
                    }
                });
            });
            $('#edit-selected-btn').on('click', function() {
                $('#confirmModal').modal('show');
                $('#confirmUpdateBtn').on('click', function() {
                    $.ajax({
                        url: '/treesfelleds/edit-multiple',
                        type: 'POST',
                        data: {
                            ids: [...selectedRows],
                            status: 'Không hoạt động'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: 'Trạng thái đã được cập nhật.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                selectedRows.clear();
                                $('#confirmModal').modal('hide');
                                //$('#buttons-container').hide();
                                $('#edit-selected-btn').hide();
                                $('#delete-selected-btn').hide();
                                location.reload();
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
                        url: '/treesfelleds/delete-multiple',
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
                                // $('#buttons-container').hide();
                                $('#edit-selected-btn').hide();
                                $('#delete-selected-btn').hide();
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: 'Có lỗi khi xóa dữ liệu.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });

                $('#deleteModal').on('hidden.bs.modal', function() {
                    $('#confirmDeleteBtn').off('click');
                });
            });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function toggleButtons() {
                var selected = $('#treesfelleds-table tbody .form-check-input:checked').length;
                if (selected > 0) {
                    $('#buttons-container').css('visibility', 'visible');
                } else {
                    $('#buttons-container').css('visibility', 'hidden');
                }
            }

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

            Swal.fire({
                title: "Xác nhận thay đổi",
                text: "Bạn có chắc chắn muốn thay đổi trạng thái của cây ngã này?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Thay đổi",
                cancelButtonText: "Hủy"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('treesfelleds.status') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                if (response.status === 'Hoạt động') {
                                    button.removeClass('bg-danger').addClass('bg-success').text(
                                        'Hoạt động');
                                } else {
                                    button.removeClass('bg-success').addClass('bg-danger').text(
                                        'Không hoạt động');
                                }

                                Swal.fire({
                                    text: 'Trạng thái của cây ngã đã được cập nhật.',
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    timer: 3000
                                });
                            } else {
                                Swal.fire({
                                    text: response.message ||
                                        'Không thể thay đổi trạng thái của cây ngã.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    timer: 3000
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                text: 'Không thể thay đổi trạng thái, vui lòng thử lại.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                timer: 3000
                            });
                        }
                    });
                }
            });
        });
</script>

<style>
    .modal-dialog {
        max-width: 90% !important;
        margin: 1.75rem auto;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 12px 16px;
        /* border: 2px solid #e5e7eb; */
        border-radius: 12px;
        font-size: 0.95rem;
        /* transition: all 0.3s ease;
        background: #fafafa; */
        position: relative;
    }

    .card {
        background: white;
        margin-bottom: 0rem !important;

    }

    .card-content {
        padding-left: 1rem;
        padding-top: 1rem;
    }

    .form-row {
        margin-bottom: 0rem;
    }

    .form-group {
        margin-bottom: 0rem;
    }

    .form-section {
        border: none;
        border-radius: 0rem;
        padding: 0;
        margin-bottom: 0rem;
    }

    .form-input.error,
    .form-select.error,
    .form-textarea.error {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .error-message {
        color: #ef4444;
        font-size: 0.85rem;
        margin-top: 5px;
        display: none;
    }

    .form-input.error+.error-message,
    .form-select.error+.error-message,
    .form-textarea.error+.error-message {
        display: block;
    }

    @media (min-width: 1180px) {
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 25px;
        }
    }
</style>
@endsection