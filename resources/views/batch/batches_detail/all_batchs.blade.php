@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0">
            {{-- Danh Sách Lô --}}
        </h5>
        <div class="ms-md-1 ms-0">

            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Danh Sách </li>
                </ol>
            </nav>
        </div>
    </div>
</div>
{{-- <div class="card custom-card" style="padding: 10px">
    <div class="row align-items-end">
        <div class="col-md-2">
            <label class="form-lable" for="month">Chọn tháng:</label>
            <select id="month" class="form-control">
                <option value="">Tất cả</option>
                @for ($m = 1; $m <= 12; $m++) <option value="{{ $m }}">{{ $m }}</option>
                    @endfor
            </select>
        </div>
        <div class="col-md-2">
            <label for="year">Chọn năm:</label>
            <select id="year" class="form-control">
                <option value="">Tất cả</option>
                @for ($y = now()->year; $y >= 2000; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-2">
            <label>&nbsp;</label>
            <button id="filter-btn" class="btn btn-primary btn-block">Lọc</button>
        </div>
        <div class="col-md-6">
            <div>
                <label>&nbsp;</label>
                <a href="{{ route('add-batches') }}" class="btn btn-primary" style="padding:5px; height:38px;">
                    <i class="ri-add-line fw-semibold align-middle me-1"></i> Kết Nối Thông Tin Nguyên Liệu
                </a>
            </div>
        </div>
    </div>
</div> --}}
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div
                class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h5 class="mb-2 mb-md-0 w-100 w-md-50 flex-grow-1">Danh Sách Lô Hàng</h5>
                <div class="d-flex w-100 w-md-auto justify-content-end">
                    <a href="{{ route('add-batches') }}" class="btn btn-sm btn-primary btn-wave waves-light">
                        <i class="ri-add-line fw-semibold align-middle me-1"></i> Kết Nối Thông Tin Nguyên Liệu
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-lg-5 col-md-12 mb-2">
                        <label class="text-dark fw-bold" for="month">Nông Trường</label>
                        <select id="farm-filter" class="form-select">
                            <option value="">-- Chọn Nông Trường --</option>
                            @foreach ($farmName as $farm)
                            {{-- @if (!empty($farm->farm_name))
                            <option value="{{ $farm->farm_name }}">{{ $farm->farm_name }}</option>
                            @endif --}}
                            @if (!empty($farm->farm_name))
                            <option value="{{ $farm->farm_name }}|{{ $farm->unitRelation->unit_name }}">
                                {{ $farm->farm_name }} - {{ $farm->unitRelation->unit_name }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    {{-- <div class="col-md-2 mb-2">
                        <label class="text-dark fw-bold" for="month">Tháng Sản Xuất</label>
                        <select id="month" class="form-select">
                            <option value="">-- Chọn Tháng --</option>
                            @for ($m = 1; $m <= 12; $m++) <option value="{{ $m }}">{{ $m }}</option>
                                @endfor
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="year" class="text-dark fw-bold">Năm Sản Xuất</label>
                        <select id="year" class="form-select">
                            <option value="">-- Chọn Năm --</option>
                            @for ($y = now()->year; $y >= 2024; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div> --}}
                    <div class="col-lg-3 col-md-6 mb-2">
                        <label class="text-dark fw-bold" for="month">Tháng Sản Xuất</label>
                        <select id="month" class="form-select">
                            <option value="">-- Chọn Tháng --</option>
                            @for ($m = 1; $m <= 12; $m++) <option value="{{ $m }}" {{ $m==now()->month ? 'selected' : ''
                                }}>
                                {{ $m }}
                                </option>
                                @endfor
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-2">
                        <label for="year" class="text-dark fw-bold">Năm Sản Xuất</label>
                        <select id="year" class="form-select">
                            <option value="">-- Chọn Năm --</option>
                            @for ($y = now()->year; $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ $y==now()->year ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-lg mb-2 d-flex align-items-end">
                        <button id="filter-btn" class="btn btn-primary w-100">Lọc</button>
                    </div>

                    {{-- <div class="col-md-6 mt-2" style="text-align: right">
                        <div>
                            <a href="{{ route('add-batches') }}" class="btn btn-primary sbtn"
                                style="padding:5px; height:38px;margin: 0 25px 0 0; float: right;">
                                <i class="ri-add-line fw-semibold align-middle me-1"></i> Kết Nối Thông Tin Nguyên Liệu
                            </a>
                        </div>
                    </div> --}}
                </div>
            </div>

            {{-- <div class="card-header d-flex justify-content-between align-items-center" style="grid-gap: 3px;">
                <h5></h5>

                <a href="{{ route('add-batches') }}" class="btn btn-sm btn-primary btn-wave waves-light">
                    <i class="ri-add-line fw-semibold align-middle me-1"></i> Kết Nối Thông Tin Nguyên Liệu
                </a>
            </div> --}}
            <style>
                /* .dt-column-order {
                                                                                                                                                                                                                                                                                                                                                                                                                                                            display: none;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        } */
            </style>
            <div class="card-body">
                {{-- <div style="position: relative;">
                    <div class="table-responsive"> --}}
                        <div id="buttons-container" class="d-flex justify-content-end gap-2">
                            <button id="delete-selected-btn" class="btn btn-danger" {{--
                                style="display: none; border-radius: 30px;" --}} style="border-radius: 30px;">
                                Xóa
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="table-batch" class="table table-bordered" aria-describedby="hidden-columns_info">

                                <thead>
                                    <tr>
                                        {{-- <th scope="col">STT</th> --}}
                                        <th></th>
                                        <th>
                                            <input class="form-check-input check-all" type="checkbox"
                                                id="select-all-batch" value="" aria-label="...">
                                        </th>
                                        <th scope="col" class="text-center">Mã Lô</th>
                                        <th scope="col" class="text-center">Ngày Sản Xuất</th>
                                        <th scope="col" class="text-center">KL Bành (Kg)</th>
                                        <th scope="col" class="text-center">KL Lô Hàng (Tấn)</th>
                                        <th scope="col" class="text-center">Nhà Máy</th>
                                        <th scope="col">Thông tin nguyên liệu</th>
                                        {{-- <th scope="col">Đơn Vị</th>
                                        <th scope="col">Xe Vận Chuyển</th>
                                        <th scope="col">Số Chuyến</th>
                                        <th scope="col">Ngày Tiếp Nhận</th> --}}
                                        <th scope="col">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th scope="col"></th>
                                        <th scope="col" class="text-center">Mã Lô</th>
                                        <th scope="col" class="text-center">Ngày Sản Xuất</th>
                                        <th scope="col" class="text-center">KL Bành (Kg)</th>
                                        <th scope="col" class="text-center">KL Lô Hàng (Tấn)</th>
                                        <th scope="col" class="text-center">Nhà Máy</th>
                                        <th scope="col">Thông tin nguyên liệu</th>
                                        {{-- <th scope="col">Nông Trường</th>
                                        <th scope="col">Đơn Vị</th>
                                        <th scope="col">Xe Vận Chuyển</th>
                                        <th scope="col">Số Chuyến</th>
                                        <th scope="col">Ngày Tiếp Nhận</th> --}}
                                        <th scope="col">Thao Tác</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <script>
                            $(document).ready(function () {
                                    var selectedRows = new Set();
                                    var dataTable = $('#table-batch').DataTable({
                                        "language": {
                                            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json",
                                            "emptyTable": "Không có dữ liệu trong bảng",
                                        },
                                        // scrollX: true,
                                        processing: true,
                                        serverSide: true,
                                        // responsive: true,
                                        order: [1, 'asc'],
                                        responsive: {
                                            details: {
                                                type: 'column',
                                                renderer: function (api, rowIdx, columns) {
                                                    var data = $.map(columns, function (col, i) {
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
                                            url: "{{ route('batches.index') }}",
                                            data: function (d) {
                                                d.farm_filter = $('#farm-filter').val();
                                                d.month = $('#month').val();
                                                d.year = $('#year').val();
                                            }
                                        },
                                        columns: [
                                            // {
                                            //     data: 'stt',
                                            //     name: 'stt'
                                            // },
                                            {
                                                data: null,
                                                name: null,
                                                orderable: false,
                                                searchable: false,
                                                render: function (data, type, row) {
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
                                                data: 'batch_id',
                                                name: 'batch_id'
                                            },
                                            {
                                                data: 'date_sx',
                                                name: 'date_sx',
                                                className: 'text-center'
                                            },
                                            {
                                                data: 'banh_weight',
                                                name: 'banh_weight'
                                            },
                                            {
                                                data: 'batch_weight',
                                                name: 'batch_weight'
                                            },
                                            {
                                                data: 'factory_name',
                                                name: 'factory_name'
                                            },
                                            {
                                                data: 'data',
                                                name: 'data',
                                                className: 'text-center nested-table-container'
                                            },
                                            {
                                                data: 'action',
                                                name: 'action',
                                                orderable: false,
                                                searchable: false,
                                                className: 'text-center'
                                            },
                                        ],
                                        order: [],
                                        columnDefs: [{
                                            className: 'dtr-control text-center',
                                            orderable: false,
                                            searchable: false,
                                            targets: 0,
                                        },
                                        {

                                            targets: 1,
                                            className: 'text-center',
                                        },
                                        {

                                            targets: 2,
                                            className: 'text-center',
                                        },
                                        {

                                            targets: 4,
                                            className: 'text-center',
                                        },
                                        {

                                            targets: 5,
                                            className: 'text-center',
                                        },

                                        ],
                                        rowCallback: function (row, data) {
                                            $(row).attr('data-id', data.id);
                                        }
                                    });

                                    $('#select-all-batch').on('change', function () {
                                        var checked = $(this).prop('checked');
                                        $('#table-batch tbody .form-check-input').each(function () {
                                            var batchId = $(this).data('id');
                                            if (checked) {
                                                selectedRows.add(batchId);
                                            } else {
                                                selectedRows.delete(batchId);
                                            }
                                            $(this).prop('checked', checked);
                                        });
                                        toggleButtons();

                                        console.log([...selectedRows]);
                                    });

                                    $('#table-batch tbody').on('change', '.form-check-input', function () {
                                        var batchId = $(this).data('id');
                                        toggleButtons();

                                        if ($(this).prop('checked')) {
                                            selectedRows.add(batchId);
                                        } else {
                                            selectedRows.delete(batchId);
                                        }
                                        console.log([...selectedRows]);
                                    });
                                    $('#table-batch').on('draw.dt', function () {
                                        $('#table-batch tbody .form-check-input').each(function () {
                                            var batchId = $(this).data('id');
                                            if (selectedRows.has(batchId)) {
                                                $(this).prop('checked', true);
                                            }
                                        });
                                    });
                                    $.ajaxSetup({
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        }
                                    });

                                    $('#delete-selected-btn').on('click', function () {
                                        $('#deleteModal').modal('show');

                                        // Gắn lại sự kiện chỉ một lần khi modal được hiển thị
                                        $('#confirmDeleteBtn').one('click', function () { // Sử dụng .one() để gán sự kiện chỉ 1 lần
                                            // Ẩn modal xác nhận ngay khi người dùng nhấn vào nút xác nhận
                                            $('#deleteModal').modal('hide'); // Ẩn modal ngay lập tức

                                            $.ajax({
                                                url: '/batch/delete-multiple',
                                                type: 'POST',
                                                data: {
                                                    ids: [...selectedRows],
                                                    _token: $('meta[name="csrf-token"]').attr(
                                                        'content') // Nếu cần CSRF token
                                                },
                                                success: function (response) {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Thành công!',
                                                        text: 'Dữ liệu đã được xóa thành công.',
                                                        confirmButtonText: 'OK'
                                                    }).then(() => {
                                                        selectedRows.clear();
                                                        $('#buttons-container')
                                                            .hide(); // Ẩn các nút khác nếu cần
                                                        location.reload(); // Tải lại trang
                                                    });
                                                },
                                                error: function (xhr, status, error) {
                                                    var errorMessage = 'Đã xảy ra lỗi trong quá trình xóa.';

                                                    // Kiểm tra và lấy thông báo lỗi từ phản hồi server (nếu có)
                                                    try {
                                                        var response = JSON.parse(xhr
                                                            .responseText
                                                        ); // Cố gắng phân tích phản hồi JSON từ server
                                                        if (response.message) {
                                                            errorMessage = response
                                                                .message; // Lấy thông báo lỗi từ server
                                                        }
                                                    } catch (e) {
                                                        console.error('Error parsing response:', e);
                                                    }

                                                    // Hiển thị thông báo lỗi chi tiết từ server
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Có lỗi xảy ra!',
                                                        text: errorMessage, // Hiển thị thông báo lỗi chi tiết
                                                        confirmButtonText: 'OK'
                                                    });

                                                    console.error('Error:', error);
                                                    console.error('XHR:', xhr);
                                                }
                                            });
                                        });

                                        // Đảm bảo gỡ sự kiện khi modal bị ẩn để tránh sự kiện trùng lặp
                                        $('#deleteModal').on('hidden.bs.modal', function () {
                                            $('#confirmDeleteBtn').off('click');
                                        });
                                    });


                                    // function toggleButtons() {
                                    //     var selectedRows = $('#table-batch tbody .form-check-input:checked');
                                    //     if (selectedRows.length > 0) {
                                    //         $('#delete-selected-btn').show();
                                    //     } else {
                                    //         $('#delete-selected-btn').hide();
                                    //     }
                                    // }
                                    function toggleButtons() {
                                        var selected = $('#table-batch tbody .form-check-input:checked').length;
                                        if (selected > 0) {
                                            $('#buttons-container').css('visibility', 'visible');
                                        } else {
                                            $('#buttons-container').css('visibility', 'hidden');
                                        }
                                    }
                                    $('#filter-btn').on('click', function () {
                                        console.log("Filtering with month:", $('#month').val(), "year:", $('#year').val());
                                        dataTable.ajax.reload();
                                    });
                                    // $('#form-sub-human').on('submit', function(e) {
                                    //     e.preventDefault();
                                    //     dataTable.ajax.reload();
                                    // });
                                });
                        </script>

                        {{--
                    </div>
                </div> --}}
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
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.js"></script>
<style>
    #table-batch td.dt-type-numeric,
    #table-batch th.dt-type-numeric {
        text-align: center !important;
    }
</style>
@endsection