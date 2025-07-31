@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0"></h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Danh Sách Xe</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<style>
    #swal2-title {
        font-size: 1.125rem !important;
    }
</style>
<!-- Add vehicles Modal -->
<form id="vehicles-form" action="{{ route('vehicles.save') }}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="modal fade" id="create-vehicles" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Tạo Xe Mới</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="row gy-2">
                        <div class="col-xl-12">
                            <label for="unit" class="form-label">Chọn Đơn Vị</label>

                            @if($singleUnit)
                            <select class="form-control" id="unit" name="unit_id" required disabled>
                                <option value="{{ $singleUnit->id }}" selected>{{ $singleUnit->unit_name }}</option>
                            </select>
                            @else
                            <select class="form-control" id="unit" name="unit_id" required>
                                <option value="">Chọn</option>
                                @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" @if(old('unit_id', request()->unit_id) == $unit->id)
                                    selected @endif>
                                    {{ $unit->unit_name }}
                                </option>
                                @endforeach
                            </select>
                            @endif
                        </div>

                        {{-- <div class="col-xl-12">
                            <label for="farm_id" class="form-label">Chọn Nông Trường</label>
                            <select class="form-control" name="farm_id" id="farm_id" required @if ($userFarmId) @endif>
                                @if ($userFarmId && $farmName->first())
                                <option value="{{ $farmName->first()->id }}" selected>
                                    {{ $farmName->first()->farm_name }}
                                </option>
                                @else
                                <option value="">Vui lòng chọn đơn vị</option>
                                @endif
                            </select>
                        </div> --}}
                        <!-- Vehicle Number -->
                        <div class="col-xl-12">
                            <label for="driver_name" class="form-label">Tài Xế</label>
                            <input type="text" class="form-control" name="driver_name" id="driver_name" required
                                placeholder="Tên tài xế" value="{{ old('driver_name') }}">
                        </div>
                        <div class="col-xl-12">
                            <label for="vehicle_type" class="form-label">Loại Xe</label>
                            <input type="text" class="form-control" name="vehicle_type" id="vehicle_type" required
                                placeholder="Nhập loại xe" value="{{ old('vehicle_type') }}">
                        </div>
                        <div class="col-xl-12">
                            <label for="vehicle_number" class="form-label">Số Xe</label>
                            <input type="text" class="form-control" name="vehicle_number" id="vehicle_number" required
                                placeholder="Nhập số xe" value="{{ old('vehicle_number') }}">
                        </div>

                        <!-- Vehicle Name -->
                        <div class="col-xl-12">
                            <label for="vehicle_name" class="form-label">Tên Xe</label>
                            <input type="text" class="form-control" name="vehicle_name" id="vehicle_name" required
                                placeholder="Nhập tên xe" value="{{ old('vehicle_name') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="submit-btn-vehicles">Lưu</button>
                </div>
            </div>
        </div>
    </div>
</form>
{{-- <script>
    $(document).ready(function() {
        const userFarmId = "{{ $userFarmId ?? '' }}"; // Kiểm tra nếu user có farm_id
        const userUnitId = "{{ $userUnitId ?? '' }}"; // Kiểm tra nếu user có unit_id

        // Khi thay đổi đơn vị
        $('#unit').on('change', function() {
            var unitId = $(this).val();
            if (unitId) {
                $.ajax({
                    url: '/get-farms-by-unit/' + unitId,
                    type: 'GET',
                    success: function(data) {
                        $('#farm_id').empty();
                        $('#farm_id').append('<option value="">Chọn Nông Trường</option>'); // Option mặc định

                        if (data.length > 0) {
                            $.each(data, function(key, farm) {
                                $('#farm_id').append('<option value="' + farm.id + '">' + farm.farm_name + '</option>');
                            });
                        } else {
                            $('#farm_id').append('<option value="">Không có nông trường nào</option>');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Không thể tải danh sách nông trường.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            } else {
                $('#farm_id').empty();
                $('#farm_id').append('<option value="">Vui lòng chọn đơn vị</option>');
            }
        });

        if (userUnitId) {
            var unitId = "{{ $userUnitId }}";
            $.ajax({
                url: '/get-farms-by-unit/' + unitId,
                type: 'GET',
                success: function(data) {
                    $('#farm_id').empty();
                    $('#farm_id').append('<option value="">Chọn Nông Trường</option>');
                    $.each(data, function(key, farm) {
                        $('#farm_id').append('<option value="' + farm.id + '">' + farm.farm_name + '</option>');
                    });
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                }
            });
        }
    });
</script> --}}
@include('layouts.alert')

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between align-items-center text-end" style="grid-gap: 3px">
                <h5>Danh Sách Xe</h5>
                @hasanyrole('Admin|Quản Lý Xe')

                <button class="btn btn-sm btn-primary btn-wave waves-light" data-bs-toggle="modal"
                    data-bs-target="#create-vehicles">
                    {{-- <i class="ri-add-line fw-semibold align-middle me-1"></i> --}}
                    Tạo Xe Mới
                </button>
                @endhasanyrole
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 col-9 mb-2">
                        <label class="text-dark fw-bold" for="farm-filter">Đơn vị</label>
                        <select id="filter_unit" class="form-select">
                            <option value="">Chọn đơn vị</option>
                            @foreach ($units as $unit)
                            <option value="{{ $unit->id }}" @if(isset($singleUnit) && $singleUnit->id == $unit->id)
                                selected @endif>
                                {{ $unit->unit_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col d-flex align-items-end mb-2">
                        <button id="search-btn" class="btn btn-primary px-4">Lọc</button>
                    </div>
                </div>
                <div id="buttons-container" class="d-flex justify-content-end gap-2">
                    <button id="edit-selected-btn" class="btn btn-warning" {{--
                        style="display: none; border-radius: 30px; color: #FFFFFF" --}}
                        style="border-radius: 30px; color: #FFFFFF">Không/Hoạt
                        Động</button>
                    <button id="delete-selected-btn" class="btn btn-danger" style="border-radius: 30px;" {{--
                        style="display: none; border-radius: 30px;" --}}>
                        Xóa
                    </button>
                </div>
                {{-- <div style="position: relative;"> --}}
                    {{-- <div class="table-responsive"> --}}
                        <table id="vehicles-table" class="table table-bordered text-nowrap w-100">

                            <thead>
                                <tr>
                                    <th></th>
                                    <th>
                                        <input class="form-check-input check-all" type="checkbox"
                                            id="select-all-vehicles" value="" aria-label="...">
                                    </th>
                                    {{-- <th scope="col">STT</th> --}}
                                    {{-- <th scope="col">Nông Trường</th> --}}
                                    <th scope="col">Đơn Vị</th>
                                    <th scope="col">Số Xe</th>
                                    <th scope="col">Tên Xe</th>
                                    <th scope="col">Tài Xế</th>
                                    <th scope="col">Loại Xe</th>
                                    <th scope="col">Trạng Thái</th>
                                    {{-- <th scope="col">Thao tác</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this section -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>
                                        {{-- <input class="form-check-input check-all" type="checkbox"
                                            id="select-all-vehicles" value="" aria-label="..."> --}}
                                    </th>
                                    {{-- <th scope="col">STT</th> --}}
                                    {{-- <th scope="col">Nông Trường</th> --}}
                                    <th scope="col">Đơn Vị</th>
                                    <th scope="col">Số Xe</th>
                                    <th scope="col">Tên Xe</th>
                                    <th scope="col">Tài Xế</th>
                                    <th scope="col">Loại Xe</th>
                                    <th scope="col">Trạng Thái</th>
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
                Bạn chắc chắn muốn xóa xe đã chọn?
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
            var dataTable = $('#vehicles-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json",
                    "emptyTable": "Không có dữ liệu",
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
                    url: '{{ route('vehicles.index') }}',
                    type: 'GET',
                    data: function(d) {
                        // d.farm_name = $('#farm-filter').val();
                        d.unit_id = $('#filter_unit').val();
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
                    // { data: 'stt', name: 'stt' },
                    // {
                    //     data: 'farm_name',
                    //     name: 'farm_name'
                    // },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'vehicle_number',
                        name: 'vehicle_number'
                    },
                    {
                        data: 'vehicle_name'
                    },
                    {
                        data: 'driver_name',
                        name: 'driver_name'
                    },
                    {
                        data: 'vehicle_type',
                        name: 'vehicle_type'
                    },
                    // {
                    //     data: 'vehicle_number',
                    //     name: 'vehicle_number'
                    // },
                    // {
                    //     data: 'vehicle_name'
                    // },
                    {
                        data: 'status'
                    },
                    // { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                rowCallback: function(row, data) {
                    $(row).attr('data-id', data.id);
                }
            });
            $('#search-btn').on('click', function() {
                dataTable.ajax.reload();
            });
            $('#select-all-vehicles').on('change', function() {
                var checked = $(this).prop('checked');
                $('#vehicles-table tbody .form-check-input').each(function() {
                    var vehicleId = $(this).data('id');
                    if (checked) {
                        selectedRows.add(vehicleId);
                    } else {
                        selectedRows.delete(vehicleId);
                    }
                    $(this).prop('checked', checked);
                });
                toggleButtons();

                console.log([...selectedRows]);
            });
            $('#vehicles-table tbody').on('change', '.form-check-input', function() {
                var vehicleId = $(this).data('id');
                toggleButtons();

                if ($(this).prop('checked')) {
                    selectedRows.add(vehicleId);
                } else {
                    selectedRows.delete(vehicleId);
                }
                console.log([...selectedRows]);
            });
            $('#vehicles-table').on('draw.dt', function() {
                $('#vehicles-table tbody .form-check-input').each(function() {
                    var vehicleId = $(this).data('id');
                    if (selectedRows.has(vehicleId)) {
                        $(this).prop('checked', true);
                    }
                });
            });
            function toggleButtons() {
                var selected = $('#vehicles-table tbody .form-check-input:checked').length;
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
                        url: '/vehicles/edit-multiple',
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
                                selectedRows.clear();
                                $('#confirmModal').modal('hide');
                                $('#buttons-container').hide();
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
            $('#confirmDeleteBtn').one('click', function() {
                $('#deleteModal').modal('hide');

                $.ajax({
                    url: '/vehicles/delete-multiple',
                    type: 'POST',
                    data: {
                        ids: [...selectedRows],
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        response.vehicles.forEach(function(vehicle) {
                            if (vehicle.status === 'update_status') {
                                // Xe có nguyên liệu, hiển thị thông báo để cập nhật trạng thái
                                Swal.fire({
                                    title: vehicle.message,
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: 'Đồng ý',
                                    cancelButtonText: 'Hủy'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $.ajax({
                                            url: '/vehicles/update-status',
                                            type: 'POST',
                                            data: {
                                                id: vehicle.id,
                                                status: 'Không hoạt động',
                                                _token: $('meta[name="csrf-token"]').attr('content') // Thêm token
                                            },
                                            success: function() {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Thành công!',
                                                    text: 'Trạng thái đã được cập nhật.',
                                                    confirmButtonText: 'OK'
                                                }).then(() => {
                                                    selectedRows.clear();
                                                    $('#buttons-container').hide();
                                                    location.reload(); // Tải lại trang
                                                });
                                            },
                                            error: function(xhr) {
                                                console.log(xhr.responseJSON); // Ghi log lỗi
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Lỗi!',
                                                    text: 'Có lỗi khi cập nhật trạng thái: ' + (xhr.responseJSON.message || 'Unknown error'),
                                                    confirmButtonText: 'OK'
                                                });
                                            }
                                        });
                                    }
                                });
                            } else if (vehicle.status === 'deleted') {
                                // Xe đã được xóa, hiển thị thông báo thành công
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Thành công!',
                                    text: vehicle.message,
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    selectedRows.clear();
                                    $('#buttons-container').hide();
                                    location.reload(); // Tải lại trang
                                });
                            }
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

            Swal.fire({
                title: "Xác nhận thay đổi",
                text: "Bạn có chắc chắn muốn thay đổi trạng thái của phương tiện này?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Thay đổi!",
                cancelButtonText: "Hủy"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('vehicles.status') }}',
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
                                    text: 'Trạng thái của phương tiện đã được cập nhật.',
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    timer: 3000
                                });
                            } else {
                                Swal.fire({
                                    text: response.message ||
                                        'Không thể thay đổi trạng thái của phương tiện.',
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
@endsection