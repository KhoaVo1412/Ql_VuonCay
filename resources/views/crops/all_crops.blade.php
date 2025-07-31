@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0"></h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Danh Sách Cây Trồng</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Add crops Modal -->
<form id="crops-form" action="{{ route('crops.save') }}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="modal fade" id="create-crops" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Tạo Cây Trồng</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="row gy-2">
                        <!-- Mã cây -->
                        <div class="col-xl-12">
                            <label for="plantCode" class="form-label">Mã Cây</label>
                            <input type="text" class="form-control" name="plantCode" id="plantCode" required
                                placeholder="Mã cây tự động" readonly>
                        </div>

                        <!-- Giống cây -->
                        <div class="col-xl-12">
                            <label for="varietyID" class="form-label">Giống Cây</label>
                            <select name="varietyID" id="varietyID" class="form-control" required>
                                <option value="">-- Chọn giống cây --</option>
                                @foreach($varieties as $variety)
                                <option value="{{ $variety->id }}">{{ $variety->varietyName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Lô -->
                        <div class="col-xl-12">
                            <label for="plotID" class="form-label">Lô</label>
                            <select name="plotID" id="plotID" class="form-control" required>
                                <option value="">-- Chọn lô --</option>
                                @foreach($plots as $plot)
                                <option value="{{ $plot->id }}">{{ $plot->plotName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- RFID -->
                        <div class="col-xl-12">
                            <label for="rfID" class="form-label">Mã RFID</label>
                            <input type="text" class="form-control" name="rfID" id="rfID" placeholder="Nhập mã RFID">
                        </div>

                        <!-- Năm trồng -->
                        <div class="col-xl-12">
                            <label for="year" class="form-label">Năm Trồng</label>
                            <input type="number" min="2000" max="{{ date('Y') }}" class="form-control" name="year"
                                id="year" placeholder="VD: 2024">
                        </div>
                        <div class="col-xl-12">
                            <label for="year" class="form-label">Tình Trạng Cây</label>
                            <input type="text" class="form-control" name="statusTree" placeholder="Tình trạng cây">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success" id="submit-btn-crops">Lưu</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- crops List -->
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="containers mt-2">
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="search-box">
                            <input type="text" class="search-inputs" placeholder="Tìm Kiếm...">
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="card-header d-flex justify-content-between align-items-center" style="grid-gap: 3px">
                <h5>Danh Sách Cây Trồng</h5>
                <button class="btn btn-sm btn-success btn-wave waves-light" data-bs-toggle="modal"
                    data-bs-target="#create-crops">
                    <i class="fa fa-plus"></i> Tạo Cây Trồng
                </button>
            </div> --}}
            <div class="card">
                <div class="card-content">
                    <div class="form-section">
                        <!-- First Filter Row -->
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Lô Trồng</label>
                                <select class="form-select" id="taskplot">
                                    <option value="">Tất cả</option>

                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Diện Tích</label>
                                <select class="form-select" id="taskLot">
                                    <option value=""></option>

                                </select>
                            </div>
                            <div class="form-group" style="display: flex; align-items: end; width: 20%;">
                                <button class="btn btn-success btn-w" onclick="filterTasks()">
                                    <i class="fa-light fa-filter-list"></i>
                                    Lọc
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-body">
                {{-- <div style="position: relative;"> --}}
                    {{-- <div class="table-responsive"> --}}
                        <table id="crops-table" class="table table-bordered text-nowrap w-100">
                            <div id="buttons-container" class="d-flex justify-content-end gap-2">
                                <button id="edit-selected-btn" class="btn btn-warning"
                                    style="border-radius: 7px; color: #FFFFFF; display: none">Không/Hoạt
                                    Động
                                </button>
                                <button id="delete-selected-btn" class="btn btn-danger"
                                    style="border-radius: 7px; display: none;">
                                    Xóa
                                </button>
                                <button id="add-selected-btn" class="btn btn-success" style="border-radius: 7px;"
                                    data-bs-toggle="modal" data-bs-target="#create-crops">
                                    <i class="fa fa-plus"></i>Tạo Cây Trồng
                                </button>
                            </div>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>
                                        <input class="form-check-input check-all" type="checkbox" id="select-all-crops"
                                            value="" aria-label="...">
                                    </th>
                                    {{-- <th scope="col">STT</th> --}}
                                    <th scope="col">Mã Cây</th>
                                    <th scope="col">Giống Cây</th>
                                    <th scope="col">Vị Trí</th>
                                    <th scope="col">Lô</th>
                                    <th scope="col">Năm Trồng</th>
                                    <th scope="col">Tình Trạng Cây</th>
                                    <th scope="col">Trạng Thái</th>
                                    <th scope="col">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this section -->

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    {{-- <th scope="col">STT</th> --}}
                                    <th scope="col">Mã Cây</th>
                                    <th scope="col">Giống Cây</th>
                                    <th scope="col">Vị Trí</th>
                                    <th scope="col">Lô</th>
                                    <th scope="col">Năm Trồng</th>
                                    <th scope="col">Tình Trạng Cây</th>
                                    <th scope="col">Trạng Thái</th>
                                    <th scope="col">Thao tác</th>
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
                Bạn chắc chắn muốn xóa vườn cây đã chọn?
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
            var dataTable = $('#crops-table').DataTable({
                "language": {
                    // "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json",
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
                    url: '{{ route('crops.index') }}',
                    type: 'GET'
                },
                columns: [
                    {
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
                    {
                        data: 'plantCode',
                        name: 'plantCode'
                    },
                    {
                        data: 'varietyID',
                        name: 'varietyID'
                    },
                    {
                        data: 'RF_id',
                        name: 'RF_id'
                    },
                    {
                        data: 'plotID',
                        name: 'plotID'
                    },
                    {
                        data: 'year',
                        name: 'year'
                    },
                    
                    {
                        data: 'statusTree',
                        name: 'statusTree'
                    },
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
            $('#select-all-crops').on('change', function() {
                var checked = $(this).prop('checked');
                $('#crops-table tbody .form-check-input').each(function() {
                    var farmId = $(this).data('id');
                    if (checked) {
                        selectedRows.add(farmId);
                    } else {
                        selectedRows.delete(farmId);
                    }
                    $(this).prop('checked', checked);
                });
                toggleButtons();

                console.log([...selectedRows]);
            });
            $('#crops-table tbody').on('change', '.form-check-input', function() {
                var farmId = $(this).data('id');
                toggleButtons();

                if ($(this).prop('checked')) {
                    selectedRows.add(farmId);
                } else {
                    selectedRows.delete(farmId);
                }
                console.log([...selectedRows]);
            });

            $('#crops-table').on('draw.dt', function() {
                $('#crops-table tbody .form-check-input').each(function() {
                    var farmId = $(this).data('id');
                    if (selectedRows.has(farmId)) {
                        $(this).prop('checked', true);
                    }
                });
            });
            $('#edit-selected-btn').on('click', function() {
                $('#confirmModal').modal('show');
                $('#confirmUpdateBtn').on('click', function() {
                    $.ajax({
                        url: '/crops/edit-multiple',
                        type: 'POST',
                        data: {
                            ids: [...selectedRows], // Chuyển Set thành mảng
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
                        url: '/crops/delete-multiple',
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

            // function toggleButtons() {
            //     var selectedRows = $('#crops-table tbody .form-check-input:checked');

            //     if (selectedRows.length > 0) {
            //         $('#edit-selected-btn').show();
            //         $('#delete-selected-btn').show();
            //     } else {
            //         $('#edit-selected-btn').hide();
            //         $('#delete-selected-btn').hide();
            //     }
            // }

            function toggleButtons() {
                var selected = $('#crops-table tbody .form-check-input:checked').length;
                if (selected > 0) {
                        $('#edit-selected-btn').show();
                        $('#delete-selected-btn').show();
                    } else {
                        $('#edit-selected-btn').hide();
                        $('#delete-selected-btn').hide();
                }
                $('#add-selected-btn').show();
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
                text: "Bạn có chắc chắn muốn thay đổi trạng thái của vườn cây này?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Thay đổi",
                cancelButtonText: "Hủy"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('crops.status') }}',
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
                                    text: 'Trạng thái của vườn cây đã được cập nhật.',
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    timer: 3000
                                });
                            } else {
                                Swal.fire({
                                    text: response.message ||
                                        'Không thể thay đổi trạng thái của vườn cây.',
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
    .card {
        background: white;
        border-radius: 0.75rem;
        /* box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); */
        margin-bottom: 1.5rem;
    }

    .card-header {
        padding: 1.5rem 1.5rem 0;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .card-description {
        color: #64748b;
        font-size: 0.875rem;
    }

    @media (min-width: 1400px) {

        .container {
            max-width: 900px !important;
        }
    }
</style>
@endsection