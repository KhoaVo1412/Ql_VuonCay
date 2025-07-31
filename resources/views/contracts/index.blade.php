@extends('layouts.app')


@section('content')
<div class="container-fluid1">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0">
        </h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Hợp đồng</li>
                </ol>
            </nav>
        </div>
    </div>
    @include('layouts.alert')
</div>


<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Hợp đồng</h5>
                {{-- <div class="d-flex justify-content-between align-items-center gap-2">
                    <form action="{{ route('export.contracts') }}" method="GET">
                        <div class="d-flex align-items-center gap-2">
                            <select name="year" id="year" class="form-control" style="width: 100%">
                                @for ($i = date('Y'); $i >= 2000; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            <button type="submit" class="btn btn-secondary text-nowrap m-0">Xuất Dữ Liệu</button>
                        </div>
                    </form>
                    <a href="{{ route('contracts.create') }}">
                        <button class="btn btn-primary m-0">Thêm Hợp Đồng</button>
                    </a>
                </div> --}}

                <div class="row justify-content-md-end g-2">
                    <!-- <div class="col-12 col-md-auto">
                                            <select name="month" id="month" class="form-select">
                                                <option value="">-- Chọn Tháng --</option>
                                                @for ($i = 1; $i <= 12; $i++)
    <option value="{{ $i }}">{{ $i }}</option>
    @endfor
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-auto">
                                            <select name="year" id="year" class="form-select">
                                                <option value="">-- Chọn Năm --</option>
                                                @for ($i = date('Y'); $i >= 2024; $i--)
    <option value="{{ $i }}">{{ $i }}</option>
    @endfor
                                            </select>
                                        </div> -->

                    <div class="col-12 col-md-auto">
                        <select id="day" class="form-select">
                            <option value="">-- Chọn ngày --</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-auto">
                        <select name="month" id="month" class="form-select">
                            <option value="">-- Chọn Tháng --</option>
                            @for ($i = 1; $i <= 12; $i++) <option value="{{ $i }}" {{ $i==date('n') ? 'selected' : ''
                                }}>
                                {{ $i }}</option>
                                @endfor
                        </select>
                    </div>
                    <div class="col-12 col-md-auto">
                        <select name="year" id="year" class="form-select">
                            <option value="">-- Chọn Năm --</option>
                            @for ($i = date('Y'); $i >= 2024; $i--)
                            <option value="{{ $i }}" {{ $i==date('Y') ? 'selected' : '' }}>
                                {{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-12 col-md-auto">
                        <button type="submit" id="export-excel" class="btn btn-success w-100"><i
                                class="fa fa-download me-2"></i>
                            Xuất Dữ Liệu</button>
                    </div>
                    <div class="col-12 col-md-auto">
                        <a href="{{ route('contracts.create') }}">
                            <button class="btn btn-primary w-100">Thêm Hợp Đồng</button>
                        </a>
                    </div>
                </div>
            </div>





            <!-- <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                                                                <h5 class="w-100 text-md-start">Hợp đồng</h5>
                                                                <div class="d-flex justify-content-between align-items-center gap-2">
                                                                    <form action="{{ route('export.contracts') }}" method="GET">
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <select name="year" id="year" class="form-control">
                                                                                @for ($i = date('Y'); $i >= 2000; $i--)
    <option value="{{ $i }}">{{ $i }}</option>
    @endfor
                                                                            </select>
                                                                            <button type="submit" class="btn btn-secondary text-nowrap m-0">Xuất Dữ Liệu</button>
                                                                        </div>
                                                                    </form>
                                                                    <a href="{{ route('contracts.create') }}">
                                                                        <button class="btn btn-primary m-0">Thêm Hợp Đồng</button>
                                                                    </a>
                                                                </div>
                                                            </div> -->


            <!-- <div class="buttons justify-content-between flex-wrap gap-2 align-items-center my-3">
                                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                                    <form action="{{ route('export.contracts') }}" method="GET">
                                                                        <label for="year">Chọn Năm</label> <br>
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <select name="year" id="year" class="form-control" style="width: 100%">
                                                                                @for ($i = date('Y'); $i >= 2000; $i--)
    <option value="{{ $i }}">{{ $i }}</option>
    @endfor
                                                                            </select>
                                                                            <button type="submit" class="btn btn-dark text-nowrap m-0">Xuất Dữ Liệu</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                <div class="justify-content-between align-items-center margin-hd">
                                                                    <a href="{{ route('contracts.create') }}">
                                                                        <button class="btn btn-success m-0">Thêm Hợp Đồng</button>
                                                                    </a>
                                                                </div>
                                                            </div> -->
            <div class="card-body">
                <!-- <div style="position: relative;">
                                                                    <div class="table-responsive"> -->
                <div id="buttons-container" class="d-flex justify-content-end gap-2">
                    <button id="delete-selected-btn" class="btn btn-danger" {{--
                        style="display: none; border-radius: 30px;" --}} style="border-radius: 30px;">
                        Xóa
                    </button>
                </div>
                <table id="contract-table" class="table table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th></th>
                            <th>
                                <input class="form-check-input check-all" type="checkbox" id="select-all-contract"
                                    value="" aria-label="...">
                            </th>
                            {{-- <th>STT</th> --}}
                            <th>Mã Hợp Đồng</th>
                            <th>Loại Hợp Đồng</th>
                            <th>Khách Hàng</th>
                            <th>Hợp Đồng Gốc Số</th>
                            <th>Ngày Giao Hàng</th>
                            <th>Số Ngày Hợp Đồng</th>
                            <th>Khối Lượng (Tấn)</th>
                            <th>Lệnh Xuất Hàng</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            {{-- <th>STT</th> --}}
                            <th>Mã Hợp Đồng</th>
                            <th>Loại Hợp Đồng</th>
                            <th>Khách Hàng</th>
                            <th>Hợp Đồng Gốc Số</th>
                            <th>Ngày Giao Hàng</th>
                            <th>Số Ngày Hợp Đồng</th>
                            <th>Khối Lượng (Tấn)</th>
                            <th>Lệnh Xuất Hàng</th>
                            <th>Thao Tác</th>
                        </tr>
                    </tfoot>
                </table>
                <!-- </div>
                                                                </div> -->
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
<script>
    $('#export-excel').on('click', function() {
            let year = $('#year').val();
            let month = $('#month').val();
            let day = $('#day').val();
            let url =
                `{{ route('export.contracts') }}?year=${year}&month=${month}&day=${day}`;
            window.location.href = url;
        });
</script>
<script>
    // Khi thay đổi tháng hoặc năm, cập nhật số ngày
        function updateDaysDropdown() {
            const daySelect = $('#day');
            const month = parseInt($('#month').val());
            const year = parseInt($('#year').val());

            // Xóa tất cả option hiện tại
            daySelect.empty();
            daySelect.append('<option value="">-- Chọn ngày --</option>');

            // Kiểm tra nếu tháng và năm đã được chọn
            if (!month || !year) return;

            // Tính số ngày của tháng
            const daysInMonth = new Date(year, month, 0).getDate();

            for (let i = 1; i <= daysInMonth; i++) {
                daySelect.append(`<option value="${i}">${i}</option>`);
            }
        }

        // Gắn sự kiện khi thay đổi tháng hoặc năm
        $(document).ready(function() {
            $('#month, #year').on('change', updateDaysDropdown);
            updateDaysDropdown(); // Gọi 1 lần khi tải trang nếu cần
        });
</script>
<script>
    $(document).ready(function() {

            var selectedRows = new Set();
            var dataTable = $('#contract-table').DataTable({
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
                    url: '/contracts-get-data',
                    type: 'GET',
                    data: function(d) {
                        d.month = $('#month').val();
                        d.year = $('#year').val();
                        d.day = $('#day').val();
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
                        searchable: false
                    },
                    // {
                    //     data: 'DT_RowIndex',
                    //     name: 'DT_RowIndex',
                    //     orderable: false,
                    //     searchable: false
                    // },
                    {
                        data: 'contract_code',
                        name: 'contract_code'
                    },
                    {
                        data: 'contract_type_id',
                        name: 'contract_type_id'
                    },
                    {
                        data: 'customer_id',
                        name: 'customer_id'
                    },
                    {
                        data: 'original_contract_number',
                        name: 'original_contract_number'
                    },
                    {
                        data: 'delivery_date',
                        name: 'delivery_date'
                    },
                    {
                        data: 'contract_days',
                        name: 'contract_days'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'order_export_id',
                        name: "order_export_id",
                        // defaultContent: ""
                    },

                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }

                ],
                rowCallback: function(row, data) {
                    $(row).attr('data-id', data.id);
                }
            });
            $('#day').on('change', function() {
                dataTable.draw();
            });
            $('#month').on('change', function() {
                dataTable.draw();
            });
            $('#year').on('change', function() {
                dataTable.draw();
            });
            $('#select-all-contract').on('change', function() {
                var checked = $(this).prop('checked');
                $('#contract-table tbody .form-check-input').each(function() {
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

            $('#contract-table tbody').on('change', '.form-check-input', function() {
                var contractId = $(this).data('id');
                toggleButtons();

                if ($(this).prop('checked')) {
                    selectedRows.add(contractId);
                } else {
                    selectedRows.delete(contractId);
                }
                console.log([...selectedRows]);
            });
            $('#contract-table').on('draw.dt', function() {
                $('#contract-table tbody .form-check-input').each(function() {
                    var contractId = $(this).data('id');
                    if (selectedRows.has(contractId)) {
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
                $('#confirmDeleteBtn').on('click', function() {
                    $.ajax({
                        url: '/contracts/delete-multiple',
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
                            if (xhr.status === 400) {
                                const response = xhr.responseJSON;
                                    $('#deleteModal').modal('hide');
 
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Không thể xóa!',
                                    html: `
                                        ${response.message}<br>
                                        <strong>Hợp đồng:</strong> ${response.contracts.join(', ')}
                                    `,
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                $('#deleteModal').modal('hide');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Có lỗi xảy ra!',
                                    text: 'Đã xảy ra lỗi trong quá trình xóa.',
                                    confirmButtonText: 'OK'
                                });
                            }
                        }
                    });
                });
                $('#deleteModal').on('hidden.bs.modal', function() {
                    $('#confirmDeleteBtn').off('click');
                });

            });

            // function toggleButtons() {
            //     var selectedRows = $('#contract-table tbody .form-check-input:checked');
            //     if (selectedRows.length > 0) {
            //         $('#delete-selected-btn').show();
            //     } else {
            //         $('#delete-selected-btn').hide();
            //     }
            // }
            function toggleButtons() {
                var selected = $('#contract-table tbody .form-check-input:checked').length;
                if (selected > 0) {
                    $('#buttons-container').css('visibility', 'visible');
                } else {
                    $('#buttons-container').css('visibility', 'hidden');
                }
            }
        });
</script>
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.js"></script>
<style>
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .card-header h5 {
            width: 100%;
            margin-bottom: 10px;
        }

        .card-header .d-flex {
            width: 100%;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 5px;
        }

        .card-header .d-flex form {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            gap: 5px;
        }

        .card-header .d-flex select {
            flex: 1;
            min-width: 80px;
            max-width: 120px;
        }

        .card-header .d-flex button {
            flex-shrink: 0;
            white-space: nowrap;
        }

        .card-header .d-flex>* {
            min-width: 0;
        }

        .card-header .d-flex a {
            flex-shrink: 0;
            max-width: 100%;
        }

        .card-header .d-flex button {
            flex-shrink: 0;
            white-space: nowrap;
            padding: 8px 12px;
            font-size: 14px;
            width: auto;
        }
    }
</style>
@endsection