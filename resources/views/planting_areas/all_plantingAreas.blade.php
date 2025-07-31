@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0"></h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Danh Sách Khu Trồng</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="row">
    @if (session('success'))
    <div class="alert alert-light-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (session('errors'))
    <div class="alert alert-light-danger alert-dismissible fade show" role="alert">
        {{ session('errors') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header" style="grid-gap: 3px;">
                <h5>Danh sách khu vực trồng</h5>

                {{-- <div class="d-grid gap-2 d-md-flex justify-content-md-end flex-flex"> --}}
                    <div class="d-grid gap-1 d-md-flex justify-content-md-end">
                        <select id="filter-farm" class="col-12 col-md-5"
                            style="border: 1px solid #aaa; border-radius: 5px;color: #607080;">
                            <option value="">--Chọn Nông Trường--</option>
                            @foreach ($plantingareas as $item)
                            <option value="{{ $item->farm_name ?? '' }}|{{ $item->unit_name ?? '' }}" @if ($defaultFarm
                                && $defaultFarm==($item->farm_name . '|' . $item->unit_name))
                                selected
                                @endif>
                                {{ $item->farm_name ?? '' }} - {{ $item->unit_name ?? '' }}
                            </option>
                            @endforeach
                        </select>

                        <div class="dong">
                            <a href="{{ route('add-excel') }}" class="t50 btn btn-sm btn-primary btn-wave waves-light">
                                <i class="fa fa-add ri-add-line fw-semibold align-middle me-1"></i> Thêm Bằng Excel
                            </a>
                            <a href="{{ route('edit-excel') }}" class="t50 btn btn-sm btn-primary btn-wave waves-light">
                                <i class="fa fa-file-edit ri-add-line fw-semibold align-middle me-1"></i> Sửa Bằng Excel
                            </a>
                            {{-- <a href="{{ route('add-plantingareas') }}"
                                class="t50 btn btn-sm btn-primary btn-wave waves-light">
                                <i class="fa fa-edit ri-add-line fw-semibold align-middle me-1"></i> Tạo Khu Vực
                            </a> --}}
                        </div>

                        <a href="{{ route('add-plantingareas') }}" class="btn btn-sm btn-primary btn-wave waves-light">
                            <i class="fa fa-edit ri-add-line fw-semibold align-middle me-1"></i> Tạo Khu Vực
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- <div style="position: relative;"> --}}
                        {{-- <div class="table-responsive"> --}}
                            <div id="buttons-container" class="d-flex justify-content-end gap-2">
                                <button id="delete-selected-btn" class="btn btn-danger" {{--
                                    style="display: none; border-radius: 30px;" --}} style="border-radius: 30px;">
                                    Xóa
                                </button>
                            </div>
                            <div id="hidden-columns_wrapper" class="dataTables_wrapper dt-bootstrap5">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table id="table-plantingarea"
                                            class="table table-bordered text-nowrap w-100 dataTable"
                                            aria-describedby="hidden-columns_info" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>
                                                        <input class="form-check-input check-all" type="checkbox"
                                                            id="select-all-plantingarea" value="" aria-label="...">
                                                    </th>
                                                    {{-- <th scope="col">STT</th> --}}
                                                    <th scope="col">Mã Lô Cây Trồng</th>
                                                    <th scope="col">Mã Khu Vực Trồng</th>
                                                    <th scope="col">Nông Trường</th>
                                                    <th scope="col">Đơn Vị</th>
                                                    <th scope="col">Năm Trồng</th>
                                                    <th scope="col">Thao Tác</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th></th>
                                                    <th>
                                                    </th>
                                                    {{-- <th scope="col">STT</th> --}}
                                                    <th scope="col">Mã Lô Cây Trồng</th>
                                                    <th scope="col">Mã Khu Vực Trồng</th>
                                                    <th scope="col">Nông Trường</th>
                                                    <th scope="col">Đơn Vị</th>
                                                    <th scope="col">Năm Trồng</th>
                                                    <th scope="col">Thao Tác</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <script>
                                            $(document).ready(function() {
                                        var selectedRows = new Set();
                                        var dataTable = $('#table-plantingarea').DataTable({
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
                                                url: "{{ route('plantingareas.index') }}",
                                                type: 'GET',
                                                data: function(d) {
                                                    d.farm_id = $('#filter-farm').val(); // Gửi giá trị farm_id lên server
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
                                                //     data: 'stt',
                                                //     name: 'stt'
                                                // },
                                                {
                                                    data: 'ma_lo',
                                                    name: 'ma_lo',
                                                    orderable: true
                                                },
                                                {
                                                    data: 'find',
                                                    name: 'find',
                                                    orderable: true
                                                },
                                                {
                                                    data: 'farm_id',
                                                    name: 'farm_id',
                                                    orderable: true
                                                },
                                                {
                                                    data: 'unit',
                                                    name: 'unit'
                                                },
                                                {
                                                    data: 'nam_trong',
                                                    name: 'nam_trong',
                                                    orderable: true
                                                },

                                                {
                                                    data: 'action',
                                                    name: 'action',
                                                    orderable: false,
                                                    searchable: false
                                                },
                                            ],
                                            rowCallback: function(row, data) {
                                                $(row).attr('data-id', data.id);
                                            }
                                        });
                                        $('#filter-farm').on('change', function() {
                                            dataTable.draw();
                                        });
                                        $('#select-all-plantingarea').on('change', function() {
                                            var checked = $(this).prop('checked');
                                            $('#table-plantingarea tbody .form-check-input').each(function() {
                                                var planId = $(this).data('id');
                                                if (checked) {
                                                    selectedRows.add(planId);
                                                } else {
                                                    selectedRows.delete(planId);
                                                }
                                                $(this).prop('checked', checked);
                                            });
                                            toggleButtons();

                                            console.log([...selectedRows]);
                                        });

                                        $('#table-plantingarea tbody').on('change', '.form-check-input', function() {
                                            var planId = $(this).data('id');
                                            toggleButtons();

                                            if ($(this).prop('checked')) {
                                                selectedRows.add(planId);
                                            } else {
                                                selectedRows.delete(planId);
                                            }
                                            console.log([...selectedRows]);
                                        });
                                        $('#table-plantingarea').on('draw.dt', function() {
                                            $('#table-plantingarea tbody .form-check-input').each(function() {
                                                var farmId = $(this).data('id');
                                                if (selectedRows.has(farmId)) {
                                                    $(this).prop('checked', true);
                                                }
                                            });
                                        });

                                        // function toggleButtons() {
                                        //     var selectedRows = $('#table-plantingarea tbody .form-check-input:checked');
                                        //     if (selectedRows.length > 0) {
                                        //         $('#delete-selected-btn').show();
                                        //     } else {
                                        //         $('#delete-selected-btn').hide();
                                        //     }
                                        // }
                                        function toggleButtons() {
                                            var selected = $('#table-plantingarea tbody .form-check-input:checked').length;
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

                                        $('#delete-selected-btn').on('click', function() {
                                            $('#deleteModal').modal('show');
                                            
                                            $('#confirmDeleteBtn').one('click', function() {
                                                $('#deleteModal').modal('hide');

                                                $.ajax({
                                                    url: '/plantingareas/delete-multiple',
                                                    type: 'POST',
                                                    data: {
                                                        ids: [...selectedRows],
                                                        _token: $('meta[name="csrf-token"]').attr('content')
                                                    },
                                                    success: function(response) {
                                                        Swal.fire({
                                                            icon: 'success',
                                                            title: 'Thành công!',
                                                            text: response.message,
                                                            confirmButtonText: 'OK'
                                                        }).then(() => {
                                                            selectedRows.clear();
                                                            $('#buttons-container').hide();
                                                            location.reload();
                                                        });
                                                    },
                                                    error: function(xhr, status, error) {
                                                        var errorMessage = 'Đã xảy ra lỗi trong quá trình xóa.';

                                                        try {
                                                            // Kiểm tra xem response có phải là JSON và lấy thông báo lỗi
                                                            var response = JSON.parse(xhr.responseText);
                                                            if (response.message) {
                                                                errorMessage = response.message;  // Lấy thông báo lỗi từ server (nếu có)
                                                            }
                                                        } catch (e) {
                                                            console.error('Error parsing response:', e);
                                                        }

                                                        Swal.fire({
                                                            icon: 'error',
                                                            title: 'Có lỗi xảy ra!',
                                                            text: errorMessage,  // Hiển thị thông báo lỗi chi tiết
                                                            confirmButtonText: 'OK'
                                                        });

                                                        console.error('Error:', error);
                                                        console.error('XHR:', xhr);
                                                    }
                                                });
                                            });
                                            $('#deleteModal').on('hidden.bs.modal', function() {
                                                $('#confirmDeleteBtn').off('click');
                                            });
                                        });

                                    });
                                        </script>
                                    </div>
                                </div>
                            </div>
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
    <script>
        function confirmDelete() {
            console.log("Deleting...");
            $('.modal-header, .modal-footer, .modal-body').addClass('hide');
            $('.confirm-delete').removeClass('hide');
            //$('#myModal').modal('hide');
        }

        function openModal() {
            $('.confirm-delete').addClass('hide');
            $('#myModal .modal-header, .modal-footer, .modal-body').removeClass('hide');
            $('#myModal').modal('show');
        }
    </script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.js"></script>
    @endsection