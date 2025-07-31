@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0">
        </h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Danh Sách Mã Lô</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card" style="padding: 10px">
            <div class="card-header justify-content-between d-flex">
                <h5>Danh Sách Mã Lô</h5>
                <div class="prism-toggle">
                    <a href="{{ route('add-batchesB') }}" class="btn btn-primary sbtn">
                        Tạo Mã Lô
                    </a>
                </div>
            </div>
            {{-- <div class="row">
                <div class="col-md-4">
                    <input type="text" id="batch_code_filter" class="form-control" placeholder="Nhập Mã Lô">
                </div>
                <div class="col-md-4">
                    <input type="date" id="date_filter" class="form-control" placeholder="Chọn ngày">
                </div>
                <div class="col-md-4">
                    <button id="filter-btn" class="btn btn-primary">Lọc</button>
                </div>
            </div> --}}
            {{-- <a href="{{ route('add-batchesB') }}" class="btn btn-sm btn-primary btn-wave waves-light">
                <i class="ri-add-line fw-semibold align-middle me-1"></i> Tạo Mã Lô
            </a>
        </div> --}}
        <div class="card-body">
            <div class="row align-items-end mb-2">
                {{-- <div class="col-md-4 t-my t38">
                    <label class="text-dark fw-bold" for="month"></label>
                    <div class="datepicker-wrapper">
                        <input type="text" class="form-control datetimepicker datepicker" id="date_filter"
                            placeholder="dd/mm/yyyy" autocomplete="off" onkeydown="return false;" required>
                        <i class="fa fa-calendar calendar-icon" style="cursor: pointer;"></i>
                    </div>
                </div>
                <div class="col-md-2 t26">
                    <button id="filter-btn" class="btn btn-primary btn-block">Lọc</button>
                </div>
                <div class="col-md-6 mt-2 t34" style="text-align: right">
                    <div>
                        <a href="{{ route('add-batchesB') }}" class="btn btn-primary sbtn"
                            style="padding:5px; height:38px;margin: 0 25px 0 0; float: right;">
                            Tạo Mã Lô
                        </a>
                    </div>
                </div> --}}


                <div class="col-md-5 col-8">
                    <label class="text-dark fw-bold" for="date_filter"></label>
                    <div class="datepicker-wrapper position-relative">
                        <input type="text" class="form-control datetimepicker datepicker" id="date_filter"
                            placeholder="dd/mm/yyyy" autocomplete="off" onkeydown="return false;" required>
                        <i class="fa fa-calendar calendar-icon" style="cursor: pointer;"></i>
                        <i class="fa fa-times-circle clear-icon"
                            style="position: absolute; right: 35px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #aaa;"></i>
                    </div>
                </div>

                <!-- Nút lọc -->
                <div class="col-md-2 col-4 d-flex justify-content-start mt-md-0 mt-2">
                    <button id="filter-btn" class="btn btn-primary w-100">Lọc</button>
                </div>
            </div>

            {{-- <div style="position: relative;"> --}}
                {{-- <div class="table-responsive"> --}}
                    <table id="table-batchB" class="table table-bordered text-nowrap w-100">
                        <div id="buttons-container" class="d-flex justify-content-end gap-2">
                            <button id="delete-selected-btn" class="btn btn-danger" {{--
                                style="display: none; border-radius: 30px;" --}} style="border-radius: 30px;">
                                Xóa
                            </button>
                        </div>
                        <style>
                            th:first-child .sorting::after,
                            th:first-child .sorting::before {
                                display: none !important;
                            }

                            .dt-type-numeric {
                                text-align: left !important;
                            }
                        </style>
                        <thead>
                            <tr>
                                <th></th>
                                <th>
                                    <input class="form-check-input check-all" type="checkbox" id="select-all-batchB"
                                        value="" aria-label="...">
                                </th>
                                {{-- <th scope="col">STT</th> --}}
                                <th scope="col">Mã Lô</th>
                                <th scope="col">Mã QR</th>
                                <th scope="col">Ngày Sản Xuất</th>
                                {{-- <th scope="col">Thao tác</th> --}}
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                {{-- <th scope="col">STT</th> --}}
                                <th scope="col">Mã Lô</th>
                                <th scope="col">Mã QR</th>
                                <th scope="col">Ngày Sản Xuất</th>
                                {{-- <th scope="col">Thao tác</th> --}}
                            </tr>
                        </tfoot>
                    </table>
                    <script>
                        $(document).ready(function() {
                            var selectedRows = new Set();

                            var dataTable = $('#table-batchB').DataTable({
                                "language": {
                                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json",
                                    "emptyTable": "Không có dữ liệu trong bảng",
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
                                    url: "{{ route('batchesB.index') }}",
                                    data: function(d) {
                                        // d.batch_code = $('#batch_code_filter').val(); 
                                        d.date_filter = $('#date_filter').val(); // Lấy ngày lọc
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
                                    {
                                        data: 'check',
                                        name: 'check',
                                        orderable: false,
                                    },
                                    {
                                        data: 'batch_code',
                                        name: 'batch_code'
                                    },
                                    {
                                        data: 'qr_code',
                                        name: 'qr_code'
                                    },
                                    {
                                        data: 'created_at',
                                        name: 'created_at'
                                    },
                                ],
                                rowCallback: function(row, data) {
                                    $(row).attr('data-id', data.id);
                                }

                            });

                            $('#select-all-batchB').on('change', function() {
                                var checked = $(this).prop('checked');
                                $('#table-batchB tbody .form-check-input').each(function() {
                                    var batchbId = $(this).data('id');
                                    if (checked) {
                                        selectedRows.add(batchbId);
                                    } else {
                                        selectedRows.delete(batchbId);
                                    }
                                    $(this).prop('checked', checked);
                                });
                                toggleButtons();
                            });

                            $('#table-batchB tbody').on('change', '.form-check-input', function() {
                                var batchbId = $(this).data('id');
                                toggleButtons();

                                if ($(this).prop('checked')) {
                                    selectedRows.add(batchbId);
                                } else {
                                    selectedRows.delete(batchbId);
                                }
                            });

                            $('#table-batchB').on('draw.dt', function() {
                                $('#table-batchB tbody .form-check-input').each(function() {
                                    var batchbId = $(this).data('id');
                                    if (selectedRows.has(batchbId)) {
                                        $(this).prop('checked', true);
                                    }
                                });
                            });

                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });

                            $('#delete-selected-btn').on('click', function() {
                                $('#deleteModal').modal('show');
                                $('#confirmDeleteBtn').show(); // Ensure the button is shown when modal opens

                                $('#confirmDeleteBtn').on('click', function() {
                                    $.ajax({
                                        url: '/batchB/delete-multiple',
                                        type: 'POST',
                                        data: {
                                            ids: [...selectedRows],
                                            _token: $('meta[name="csrf-token"]').attr(
                                                'content') // Ensure CSRF token is included
                                        },
                                        success: function(response) {
                                            // Check if the response indicates an error (even if status is 200)
                                            if (response.error) {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Có lỗi xảy ra!',
                                                    html: response
                                                    .message, // Use html to render <br> tags correctly
                                                    confirmButtonText: 'OK'
                                                });
                                            } else {
                                                // Full success: all batches deleted
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Thành công!',
                                                    text: response.message ||
                                                        'Dữ liệu đã được xóa thành công.',
                                                    confirmButtonText: 'OK'
                                                }).then(() => {
                                                    selectedRows.clear();
                                                    $('#deleteModal').modal('hide');
                                                    $('#buttons-container').hide();
                                                    location
                                                .reload(); // Reload the page only on full success
                                                });
                                            }
                                        },
                                        error: function(xhr) {
                                            // Handle 400, 500, or other errors
                                            var errorMessage = xhr.responseJSON && xhr.responseJSON
                                                .message ?
                                                xhr.responseJSON.message :
                                                'Có lỗi xảy ra khi xóa dữ liệu!';
                                            $('#confirmDeleteBtn').hide();
                                            $('#deleteModal').modal('hide');
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Có lỗi xảy ra!',
                                                html: errorMessage, // Use html to render <br> tags correctly
                                                confirmButtonText: 'OK'
                                            });
                                            console.error('Error:', xhr.responseJSON || xhr);
                                        }
                                    });
                                });

                                // Clean up the click event handler after modal is closed to avoid duplicate event binding
                                $('#deleteModal').on('hidden.bs.modal', function() {
                                    $('#confirmDeleteBtn').off('click');
                                    $('#confirmDeleteBtn').show();
                                });
                            });

                            function toggleButtons() {
                                var selected = $('#table-batchB tbody .form-check-input:checked').length;
                                if (selected > 0) {
                                    $('#buttons-container').css('visibility', 'visible');
                                } else {
                                    $('#buttons-container').css('visibility', 'hidden');
                                }
                            }
                            $('#filter-btn').on('click', function() {
                                dataTable.ajax.reload();
                            });
                        });
                    </script>

                    <script>
                        function toggleClearIcon(input) {
                            const wrapper = input.closest(".datepicker-wrapper");
                            const clearIcon = wrapper?.querySelector(".clear-icon");

                            if (clearIcon) {
                                clearIcon.style.display = input.value ? "block" : "none";
                            }
                        }

                        // Theo dõi thay đổi giá trị input
                        document.querySelectorAll(".datetimepicker").forEach(function(input) {
                            toggleClearIcon(input); // chạy ban đầu nếu có giá trị sẵn

                            input.addEventListener("change", function() {
                                toggleClearIcon(this);
                            });

                            input.addEventListener("input", function() {
                                toggleClearIcon(this);
                            });

                            input.addEventListener("keydown", function(event) {
                                if (event.key === "Backspace" || event.key === "Delete") {
                                    this.value = "";
                                    this.dispatchEvent(new Event("change"));
                                }
                            });
                        });

                        document.querySelectorAll(".datepicker-wrapper .clear-icon").forEach(function(icon) {
                            icon.addEventListener("click", function() {
                                const input = this.closest(".datepicker-wrapper").querySelector(".datetimepicker");
                                if (input) {
                                    input.value = "";
                                    input.dispatchEvent(new Event("change"));
                                }
                            });
                        });
                    </script>

                    {{--
                </div> --}}
                {{-- </div> --}}
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
                Bạn chắc chắn muốn dữ liệu đã chọn ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-primary">Xác
                    Nhận</button>
            </div>
        </div>
    </div>
</div>
</div>
<style>
    @media (max-width: 768px) {
        .datepicker-wrapper input {
            font-size: 13px;
            height: 38px;
        }

        .datepicker-wrapper input::placeholder {
            font-size: 13px;
            height: 38px;
        }
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.js"></script>
@endsection