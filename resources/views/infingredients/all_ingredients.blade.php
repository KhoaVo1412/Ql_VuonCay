@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0">
        </h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Danh Sách Nguyên Liệu</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between align-items-center" style="grid-gap: 3px;">
                <h5>Danh Sách Nguyên Liệu</h5>
                <a href="{{ route('add-ingredients') }}" class="btn btn-sm btn-primary btn-wave waves-light">
                    <i class="ri-add-line fw-semibold align-middle me-1"></i> Tạo thông tin nguyên liệu
                </a>
            </div>
            <div class="card-body">
                {{-- <div class="row mb-3">
                    <div class="col-md-5 mb-2">
                        <label for="farm-filter" class="text-dark fw-bold">Nông Trường</label>
                        <select id="farm-filter" class="form-select">
                            <option value="">-- Chọn nông trường --</option>
                            @foreach ($ingredients as $item)
                            <option value="{{ $item->farm_name ?? '' }}|{{ $item->unit_name ?? '' }}" @if ($defaultFarm
                                && $defaultFarm==$item->farm_name . '|' . $item->unit_name)
                                selected
                                @endif>
                                {{ $item->farm_name ?? '' }} - {{ $item->unit_name ?? '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- <div class="col-md-4 mb-2">
                        <label for="" class="text-dark fw-bold">Ngày tiếp nhận</label>
                        <select id="day" class="form-select">
                            <option value="">-- Chọn ngày --</option>

                        </select>
                    </div>




                    <div class="col-md-4 mb-2">
                        <label for="" class="text-dark fw-bold">Số Xe</label>
                        <select id="vehicle-filter" class="form-select">
                            <option value="">-- Chọn Số Xe --</option>
                            @foreach ($vehicleNumbers as $number)
                            <option value="{{ $number ?? '' }}">{{ $number ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label for="" class="text-dark fw-bold">Tháng Tiếp Nhận</label>
                        <select id="month" class="form-select">
                            <option value="">-- Chọn Tháng --</option>
                            @php
                            $currentM = date('n');
                            for ($i = 1; $i <= $currentM; $i++) { $selected=($i==$currentM) ? 'selected' : '' ;
                                echo "<option value=\" $i\" $selected>
                                $i</option>";
                                }
                                @endphp
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label for="" class="text-dark fw-bold">Năm Tiếp Nhận</label>
                        <select id="year" class="form-select">
                            <option value="">-- Chọn Năm --</option>
                            @php
                            $currentYear = date('Y');
                            for ($i = 2024; $i <= $currentYear; $i++) { $selected=($i==$currentYear) ? 'selected' : '' ;
                                echo "<option value=\" $i\" $selected>
                                $i</option>";
                                }
                                @endphp
                        </select>
                    </div>


                    <div class="col d-flex align-items-end mb-2">
                        <button id="search-btn" class="btn btn-primary px-4 me-2">Lọc</button>
                        <button id="export-excel" class="btn btn-success px-3"><i class="fa fa-download"></i> Xuất
                            Dữ
                            Liệu</button>
                    </div>

                </div> --}}

                <div class="row mb-3">
                    <!-- Dòng 1: Nông trường và Số xe -->
                    <div class="col-md-6 mb-3">
                        <label for="farm-filter" class="text-dark fw-bold">Nông Trường</label>
                        <select id="farm-filter" class="form-select">
                            <option value="">-- Chọn nông trường --</option>
                            @foreach ($ingredients as $item)
                            <option value="{{ $item->farm_name ?? '' }}|{{ $item->unit_name ?? '' }}" @if ($defaultFarm
                                && $defaultFarm==$item->farm_name . '|' . $item->unit_name) selected @endif>
                                {{ $item->farm_name ?? '' }} - {{ $item->unit_name ?? '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="vehicle-filter" class="text-dark fw-bold">Số Xe</label>
                        <select id="vehicle-filter" class="form-select">
                            <option value="">-- Chọn Số Xe --</option>
                            @foreach ($vehicleNumbers as $number)
                            <option value="{{ $number ?? '' }}">{{ $number ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dòng 2: Ngày - Tháng - Năm - 2 nút -->
                    <div class="col-md-3 mb-3">
                        <label for="day" class="text-dark fw-bold">Ngày tiếp nhận</label>
                        <select id="day" class="form-select">
                            <option value="">-- Chọn ngày --</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="month" class="text-dark fw-bold">Tháng Tiếp Nhận</label>
                        <select id="month" class="form-select">
                            <option value="">-- Chọn Tháng --</option>
                            @php
                            for ($i = 1; $i <= 12; $i++) { $selected=$i==date('n') ? 'selected' : '' ;
                                echo "<option value=\" $i\" $selected>Tháng $i</option>";
                                }
                                @endphp
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="year" class="text-dark fw-bold">Năm Tiếp Nhận</label>
                        <select id="year" class="form-select">
                            <option value="">-- Chọn Năm --</option>
                            @php
                            for ($i = 2024; $i <= date('Y'); $i++) { $selected=$i==date('Y') ? 'selected' : '' ;
                                echo "<option value=\" $i\" $selected>$i</option>";
                                }
                                @endphp
                        </select>
                    </div>

                    <div
                        class="col-md-3 col-12 d-flex align-items-end justify-content-between gap-2 mb-3 flex-md-nowrap flex-wrap">
                        <button id="search-btn" class="btn btn-primary flex-fill">Lọc</button>
                        <button id="export-excel" class="btn btn-success flex-fill">
                            <i class="fa fa-download"></i> Xuất Dữ Liệu
                        </button>
                    </div>
                </div>

                <div id="buttons-container" class="d-flex justify-content-end gap-2">
                    <button id="delete-selected-btn" class="btn btn-danger" style="border-radius: 30px;" {{--
                        style="display: none; border-radius: 30px;" --}}>
                        Xóa
                    </button>
                </div>
                {{-- <div style="position: relative;"> --}}
                    {{-- <div class="table-responsive"> --}}
                        <table id="table-human" class="table table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>
                                        <input class="form-check-input check-all" type="checkbox"
                                            id="select-all-ingredients" value="" aria-label="...">
                                    </th>
                                    {{-- <th scope="col">STT</th> --}}
                                    <th scope="col">Nông Trường</th>
                                    <th scope="col">Loại Mủ</th>
                                    <th scope="col">Số Xe Vận Chuyển</th>
                                    <th scope="col">Số Chuyến</th>
                                    <th scope="col">Ngày Bắt Đầu Cạo</th>
                                    <th scope="col">Ngày Kết Thúc Cạo</th>
                                    <th scope="col">Ngày Tiếp Nhận</th>
                                    <th scope="col">Nhà Máy Tiếp Nhận</th>
                                    <th scope="col">Đơn Vị</th>
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
                                    <th scope="col">Nông Trường</th>
                                    <th scope="col">Loại Mủ</th>
                                    <th scope="col">Số Xe Vận Chuyển</th>
                                    <th scope="col">Số Chuyến</th>
                                    <th scope="col">Ngày Bắt Đầu Cạo</th>
                                    <th scope="col">Ngày Kết Thúc Cạo</th>
                                    <th scope="col">Ngày Tiếp Nhận</th>
                                    <th scope="col">Nhà Máy Tiếp Nhận</th>
                                    <th scope="col">Đơn Vị</th>
                                    <th scope="col">Thao Tác</th>
                                </tr>
                            </tfoot>
                        </table>
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
{{-- <script>
    document.addEventListener("DOMContentLoaded", function() {
            const daySelect = document.getElementById("day");
            const monthSelect = document.getElementById("month");
            const yearSelect = document.getElementById("year");

            function updateDays() {
                let month = parseInt(monthSelect.value);
                let year = parseInt(yearSelect.value);
                daySelect.innerHTML = '<option value="">-- Chọn ngày --</option>'; // Reset ngày

                if (!month || !year) return; // Nếu chưa chọn tháng hoặc năm, không làm gì cả

                let daysInMonth = new Date(year, month, 0).getDate(); // Lấy số ngày của tháng

                for (let d = 1; d <= daysInMonth; d++) {
                    let option = document.createElement("option");
                    option.value = d;
                    option.textContent = d;
                    daySelect.appendChild(option);
                }
            }

            // Gán sự kiện khi chọn tháng hoặc năm
            monthSelect.addEventListener("change", updateDays);
            yearSelect.addEventListener("change", updateDays);
        });
</script> --}}

<script>
    $('#export-excel').on('click', function() {
            let farm = $('#farm-filter').val();
            let vehicle = $('#vehicle-filter').val();
            let year = $('#year').val();
            let month = $('#month').val();
            let day = $('#day').val();

            let url =
                `{{ route('ingredients.export') }}?farm_id=${farm}&vehicle_number_id=${vehicle}&day=${day}&year=${year}&month=${month}`;
            window.location.href = url;
            // let url =
            //     `{{ route('ingredients.export') }}?farm_id=${farm}&vehicle_number_id=${vehicle}&year=${year}&month=${month}`;
            // window.location.href = url;
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
            var dataTable = $('#table-human').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json",
                    "emptyTable": "Không có dữ liệu trong bảng",
                },
                processing: true,
                serverSide: true,

                // createdRow: function(row, data, dataIndex) {
                //     $('td', row).css({
                //         "white-space": "normal",
                //         "word-wrap": "break-word",
                //         "max-width": "200px"
                //     });
                // },
                ajax: {
                    url: '{{ route('ingredients.index') }}',
                    data: function(d) {
                        d.farm_id = $('#farm-filter').val();
                        d.vehicle_number = $('#vehicle-filter').val();
                        d.month = $('#month').val();
                        d.year = $('#year').val();
                        d.day = $('#day').val();
                    }
                },
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
                    {
                        data: 'farm_id',
                        name: 'farm_id'
                    },
                    {
                        data: 'type_of_pus_id',
                        name: 'type_of_pus_id'
                    },
                    {
                        data: 'vehicle_number_id',
                        name: 'vehicle_number_id'
                    },
                    {
                        data: 'trip',
                        name: 'trip'
                    },
                    {
                        data: 'harvesting_date',
                        name: 'harvesting_date'
                    },
                    {
                        data: 'end_harvest_date',
                        name: 'end_harvest_date'
                    },
                    {
                        data: 'received_date',
                        name: 'received_date'
                    },
                    {
                        data: 'received_factory_id',
                        name: 'received_factory_id'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                ],
                rowCallback: function(row, data) {
                    $(row).attr('data-id', data.id);
                },
            });
            $('#search-btn').on('click', function() {
                dataTable.ajax.reload();
            });
            $('#select-all-ingredients').on('change', function() {
                var checked = $(this).prop('checked');
                $('#table-human tbody .form-check-input').each(function() {
                    var ingId = $(this).data('id');
                    if (checked) {
                        selectedRows.add(ingId);
                    } else {
                        selectedRows.delete(ingId);
                    }
                    $(this).prop('checked', checked);
                });
                toggleButtons();

                console.log([...selectedRows]);
            });

            $('#table-human tbody').on('change', '.form-check-input', function() {
                var ingId = $(this).data('id');
                toggleButtons();

                if ($(this).prop('checked')) {
                    selectedRows.add(ingId);
                } else {
                    selectedRows.delete(ingId);
                }
                console.log([...selectedRows]);
            });
            $('#table-human').on('draw.dt', function() {
                $('#table-human tbody .form-check-input').each(function() {
                    var ingId = $(this).data('id');
                    if (selectedRows.has(ingId)) {
                        $(this).prop('checked', true);
                    }
                });
            });

            // function toggleButtons() {
            //     var selectedRows = $('#table-human tbody .form-check-input:checked');
            //     if (selectedRows.length > 0) {
            //         $('#delete-selected-btn').show();
            //     } else {
            //         $('#delete-selected-btn').hide();
            //     }
            // }
            function toggleButtons() {
                var selected = $('#table-human tbody .form-check-input:checked').length;
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
                        url: '/ingredients/delete-multiple',
                        type: 'POST',
                        data: {
                            ids: [...selectedRows],
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: 'Dữ liệu đã được xóa thành công.',
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
                                var response = JSON.parse(xhr.responseText);
                                if (response.message) {
                                    errorMessage = response.message;
                                }
                            } catch (e) {
                                console.error('Error parsing response:', e);
                            }

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
                $('#deleteModal').on('hidden.bs.modal', function() {
                    $('#confirmDeleteBtn').off('click');
                });
            });

        });
</script>
{{-- <script>
    function confirmDelete(){
        console.log("Deleting...");
        $('.modal-header, .modal-footer, .modal-body').addClass('hide');
        $('.confirm-delete').removeClass('hide');
        //$('#myModal').modal('hide');
        }

        function openModal(){
        $('.confirm-delete').addClass('hide');
        $('#myModal .modal-header, .modal-footer, .modal-body').removeClass('hide');
        $('#myModal').modal('show');
        }
</> --}}
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.js">
</script>
@endsection