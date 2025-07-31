@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0"></h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ingredients.index') }}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chỉnh Sửa</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <form id="form-account" action="{{ route('update-ingredients', ['id' => $ingredient->id]) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex">
                        <h5>Chỉnh Sửa Nguyên Liệu</h5>
                        <div class="prism-toggle">
                            <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row modal-body gy-4">

                            <!-- Unit Selection Dropdown -->
                            <div class="col-md-4">
                                <label for="unit_id" class="form-label">Đơn Vị</label>
                                <select class="form-control" name="unit_id" id="unit_id" required>
                                    @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}" {{-- {{ $ingredient->farm->unit_id == $unit->id ?
                                        'selected' : '' }}>
                                        {{ $unit->unit_name }} --}}
                                        value="{{ $unit->id }}">{{ $unit->unit_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Farm Selection Dropdown -->
                            <div class="col-md-4">
                                <label for="farm_id" class="form-label">Nông Trường</label>
                                <select class="form-control" name="farm_id" id="farm_id" required>
                                    <option value="">Chọn Nông Trường</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="received_factory_id" class="form-label">Nhà Máy Tiếp Nhận</label>
                                <select class="form-control" name="received_factory_id" id="received_factory_id"
                                    required>
                                    <option value="">Chọn Nhà Máy</option>
                                    @foreach ($factory as $val)
                                    <option value="{{ $val->id }}" {{ old('received_factory_id', $ingredient->
                                        received_factory_id) == $val->id ? 'selected' : '' }}>
                                        {{ $val->factory_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="type_of_pus_id" class="form-label">Loại Mủ</label>
                                <select class="form-control" name="type_of_pus_id" id="type_of_pus_id" required>
                                    <option value="">Chọn loại mủ</option>
                                    @foreach ($typeofpus as $val)
                                    <option value="{{ $val->id }}" {{ old('type_of_pus_id', $ingredient->type_of_pus_id)
                                        == $val->id ? 'selected' : '' }}>
                                        {{ $val->name_pus }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Vehicle Selection Dropdown -->
                            <div class="col-md-4">
                                <label for="vehicle_number_id" class="form-label">Số Xe</label>
                                <select name="vehicle_number_id" id="vehicle_number_id" class="form-control" required>
                                    <option value="">Chọn Số Xe</option>
                                </select>
                            </div>
                            {{--
                            <!-- Chọn Đơn Vị -->
                            <div class="col-md-3">
                                <label for="unit_id" class="form-label">Đơn Vị</label>
                                <select class="form-control" name="unit_id" id="unit_id" required>
                                    <option value="">Chọn Đơn Vị</option>
                                    @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id', $ingredient->farm->unit_id) ==
                                        $unit->id ? 'selected' : '' }}>
                                        {{ $unit->unit_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Chọn Nông Trường -->
                            <div class="col-md-3">
                                <label for="farm_id" class="form-label">Nông Trường</label>
                                <select class="form-control" name="farm_id" id="farm_id" required>
                                    <option value="">Chọn Nông Trường</option>
                                    <!-- Nông Trường sẽ được load khi chọn Đơn Vị -->
                                </select>
                            </div>

                            <!-- Chọn Số Xe -->
                            <div class="col-md-3">
                                <label for="vehicle_number_id" class="form-label">Số Xe</label>
                                <select name="vehicle_number_id" id="vehicle_number_id" class="form-control" required>
                                    <option value="">Chọn Số Xe</option>
                                    <!-- Số xe sẽ được load khi chọn đơn vị -->
                                </select>
                            </div> --}}
                            <div class="col-md-4">
                                <label for="trip" class="form-label">Số Chuyến</label>
                                <input type="number" min="0" class="form-control" name="trip" placeholder="Chuyến"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="received_date" class="form-label">Ngày Tiếp Nhận</label>
                                <div class="datepicker-wrapper">
                                    <input type="text" class="form-control date-sx-input datetimepicker datepicker"
                                        name="received_date" id="received_date" {{--
                                        value="{{ \Carbon\Carbon::parse($ingredient->received_date)->format('d/m/Y') }}"
                                        --}} placeholder="dd/mm/yyyy" autocomplete="off" onkeydown="return false;"
                                        required>
                                    <i class="fa fa-calendar calendar-icon" style="cursor: pointer;"></i>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="harvesting_date" class="form-label">Ngày Bắt Đầu Cạo</label>
                                <div class="datepicker-wrapper">
                                    <input type="text" class="form-control date-sx-input datetimepicker datepicker"
                                        name="harvesting_date" id="harvesting_date"
                                        value="{{ old('harvesting_date', \Carbon\Carbon::parse($ingredient->harvesting_date)->format('d/m/Y')) }}"
                                        placeholder="dd/mm/yyyy" autocomplete="off" onkeydown="return false;" required>
                                    <i class="fa fa-calendar calendar-icon" style="cursor: pointer;"></i>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="end_harvest_date" class="form-label">Ngày Kết Thúc Cạo</label>
                                <div class="datepicker-wrapper">
                                    <input type="text" class="form-control date-sx-input datetimepicker datepicker"
                                        name="end_harvest_date" id="end_harvest_date"
                                        value="{{ old('end_harvest_date', \Carbon\Carbon::parse($ingredient->end_harvest_date)->format('d/m/Y')) }}"
                                        placeholder="dd/mm/yyyy" autocomplete="off" onkeydown="return false;" required>
                                    <i class="fa fa-calendar calendar-icon" style="cursor: pointer;"></i>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label for="planting_area_id" class="form-label">Chọn Khu Vực Trồng</label>
                                <select id="planting_area_id" name="planting_area_id[]" multiple>
                                    <!-- Will be populated dynamically -->
                                </select>
                            </div>

                            {{-- <div class="table">
                                <div id="hidden-columns_wrapper" class="dataTables_wrapper dt-bootstrap5">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table id="chi_tieu_table"
                                                class="table table-bordered text-nowrap w-100 dataTable"
                                                aria-describedby="hidden-columns_info" style="width: 1210px;">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Mã Lô</th>
                                                        <th scope="col">Giống Cây</th>
                                                    </tr>
                                                </thead>
                                                {{-- <tbody>
                                                    @foreach ($selectedPlantingAreas as $area)
                                                    <tr>
                                                        <td>{{ $area->ma_lo }}</td>
                                                        <td>{{ $area->chi_tieu }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody> -
                                                <tbody id="chi_tieu_table"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                            <table class="table mt-3">
                                <thead>
                                    <tr>
                                        <th>Mã Lô Vùng Trồng</th>
                                        <th>Giống Cây</th>
                                    </tr>
                                </thead>
                                <tbody id="chi_tieu_table"></tbody>
                            </table>

                            {{-- <div class="p-t-10 col-sm-12">
                                <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
{{-- <script>
    $(document).ready(function() {
    // Lọc theo Đơn Vị (Khi chọn Đơn Vị)
    $('#unit_id').on('change', function() {
        const unitId = $(this).val();

        if (unitId) {
            // Lấy danh sách nông trường theo đơn vị đã chọn
            $.ajax({
                url: '{{ route("get-farms-by-unit") }}',
method: 'GET',
data: { unit: unitId },
success: function(response) {
const farmSelect = $('#farm_id');
farmSelect.empty();
farmSelect.append('<option value="">Chọn Nông Trường</option>');
$.each(response.farms, function(index, farm) {
farmSelect.append('<option value="' + farm.id + '">' + farm.farm_name + '</option>');
});

// Lấy danh sách số xe thuộc đơn vị đã chọn
$.ajax({
url: '{{ route("get-vehicles") }}',
method: 'GET',
data: { unit_id: unitId },
success: function(response) {
const vehicleSelect = $('#vehicle_number_id');
vehicleSelect.empty();
vehicleSelect.append('<option value="">Chọn Số Xe</option>');
$.each(response.vehicles, function(index, vehicle) {
vehicleSelect.append('<option value="' + vehicle.id + '">' + vehicle.vehicle_number + '</option>');
});
},
error: function() {
Swal.fire({
icon: 'error',
title: 'Lỗi!',
text: 'Không thể tải danh sách xe.',
confirmButtonText: 'OK'
});
}
});
},
error: function() {
Swal.fire({
icon: 'error',
title: 'Lỗi!',
text: 'Không thể tải danh sách nông trường.',
confirmButtonText: 'OK'
});
}
});
} else {
$('#farm_id').empty().append('<option value="">Chọn Nông Trường</option>');
$('#vehicle_number_id').empty().append('<option value="">Chọn Số Xe</option>');
}
});

// Khi trang được tải, check giá trị cũ và tải lại dữ liệu
const initialUnitId = $('#unit_id').val();
const initialFarmId = $('#farm_id').val();
const initialVehicleNumberId = $('#vehicle_number_id').val();
if (initialUnitId) {
$('#unit_id').trigger('change'); // Trigger để tải nông trường và xe khi có giá trị đơn vị
}
if (initialFarmId) {
$('#farm_id').val(initialFarmId).trigger('change');
}
if (initialVehicleNumberId) {
$('#vehicle_number_id').val(initialVehicleNumberId).trigger('change');
}
});
</script> --}}

{{-- <script>
    $(document).ready(function() {
            $('#unit_id').select2({
                placeholder: "Chọn Đơn Vị",
                allowClear: true,
                minimumResultsForSearch: 0,
                width: '100%',
            });

            $('#unit_id').on('change', function() {
                const unitId = $(this).val();

                if (unitId) {
                    $.ajax({
                        url: '{{ route('get-vehicles') }}',
                        method: 'GET',
                        data: {
                            unit_id: unitId
                        },
                        success: function(response) {
                            const vehicleSelect = $('#vehicle_number_id');
                            vehicleSelect.empty();
                            vehicleSelect.append(
                                '<option value="">Chọn Số Xe</option>'
                            ); // Thêm tùy chọn mặc định

                            if (response.vehicles.length > 0) {
                                $.each(response.vehicles, function(index, vehicle) {
                                    vehicleSelect.append('<option value="' + vehicle
                                        .id + '">' + vehicle.vehicle_number +
                                        '</option>');
                                });
                            } else {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Không có xe',
                                    text: 'Không có xe nào thuộc đơn vị này.',
                                    confirmButtonText: 'OK'
                                });
                            }
                            const initialVehicleNumberId = $('#vehicle_number_id').data(
                                'initial-value');
                            if (initialVehicleNumberId) {
                                $('#vehicle_number_id').val(initialVehicleNumberId).trigger(
                                    'change');
                            }
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: 'Không thể tải danh sách xe.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                } else {
                    $('#vehicle_number_id').empty().append('<option value="">Chọn Số Xe</option>').trigger(
                        'change');
                }
            });
            const initialUnitId = $('#unit_id').val();
            const initialVehicleNumberId = $('#vehicle_number_id').val();
            if (initialUnitId) {
                $('#unit_id').trigger('change');
            }
            if (initialVehicleNumberId) {
                $('#vehicle_number_id').val(initialVehicleNumberId).data('initial-value', initialVehicleNumberId)
                    .trigger('change');
            }
        });
</script>

<script>
    // $(document).ready(function() {
        //     $('#planting_area_id').select2({
        //         placeholder: 'Có thể chọn nhiều mã khu vực',
        //         allowClear: true,
        //         width: '100%'
        //     });

        //     $('#planting_area_id').on('change', function() {
        //         const selectedIds = $(this).val();
        //         if (selectedIds && selectedIds.length > 0) {
        //             $.ajax({
        //                 url: '{{ route('get.chi.tieu') }}',
        //                 type: 'GET',
        //                 data: {
        //                     ids: selectedIds
        //                 },
        //                 success: function(response) {
        //                     $('#chi_tieu_table tbody').empty();
        //                     response.forEach(item => {
        //                         $('#chi_tieu_table tbody').append(`
    //                             <tr>
    //                                 <td>${item.ma_lo}</td>
    //                                 <td>${item.chi_tieu}</td>
    //                             </tr>
    //                         `);
        //                     });
        //                 },
        //                 error: function() {
        //                     alert('Không thể lấy dữ liệu, vui lòng thử lại.');
        //                 }
        //             });
        //         } else {
        //             $('#chi_tieu_table').empty();
        //         }
        //     });
        // });
        $(document).ready(function() {
            $('#planting_area_id').select2({
                placeholder: 'Có thể chọn nhiều mã khu vực',
                allowClear: true,
                width: '100%'
            });

            $('#planting_area_id').on('change', function() {
                const selectedIds = $(this).val();
                if (selectedIds && selectedIds.length > 0) {
                    $.ajax({
                        url: '{{ route('get.chi.tieu') }}',
                        type: 'GET',
                        data: {
                            ids: selectedIds
                        },
                        success: function(response) {
                            response.forEach(item => {
                                let exists = false;
                                $('#chi_tieu_table tbody tr').each(function() {
                                    if ($(this).find('td').first().text() ===
                                        item.ma_lo) {
                                        exists = true;
                                    }
                                });
                                if (!exists) {
                                    $('#chi_tieu_table tbody').append(`
                                <tr>
                                    <td>${item.ma_lo}</td>
                                    <td>${item.chi_tieu}</td>
                                </tr>
                            `);
                                    console.log('new', item.ma_lo);
                                }
                            });
                        },
                        error: function() {
                            alert('Không thể lấy dữ liệu, vui lòng thử lại.');
                        }
                    });
                }
            });
        });
</script> --}}
<script>
    const ingredientId = "{{ $ingredient->id ?? '' }}";
        let oldUnitId = "{{ old('unit_id', $singleUnit->id ?? $ingredient->farm->unit_id) }}";
        let oldFarmId = "{{ old('farm_id', $singleFarm->id ?? $ingredient->farm_id) }}";
        let oldTrip = "{{ old('trip', $ingredient->trip) }}";
        let oldVehicleId = "{{ old('vehicle_number_id', $ingredient->vehicle_number_id) }}";

        let oldPlantingAreaIds = {!! json_encode(old('planting_area_id', $ingredient->plantingAreas->pluck('id'))) !!};

        let oldreceivedDate =
            "{{ old('received_date', \Carbon\Carbon::parse($ingredient->received_date)->format('d/m/Y')) }}";
</script>


<script>
    $(document).ready(function() {
            let hasAlerted = false;
            let isClearing = false;
            let isPlantingLoaded = false;

            $('#unit').select2({
                placeholder: "Chọn Đơn Vị",
                allowClear: true,
                minimumResultsForSearch: 0,
                width: '100%',
            });

            $('#farm_id').select2({
                placeholder: "Chọn Nông Trường",
                allowClear: true,
                minimumResultsForSearch: 0,
                width: '100%',
            });

            $('#vehicle_number_id').select2({
                placeholder: 'Chọn Số Xe',
                allowClear: true,
                width: '100%'
            });

            $('#planting_area_id').select2({
                placeholder: 'Chọn Khu Vực Trồng',
                allowClear: true,
                width: '100%',
            });

            // Khi chọn Đơn Vị
            $('#unit_id').on('change', function() {
                const unit = $(this).val();
                const farmSelect = $('#farm_id');
                const vehicleSelect = $('#vehicle_number_id');
                const receivedDate = $('#received_date');
                const trip = $('[name="trip"]');
                farmSelect.empty().trigger('change');
                vehicleSelect.empty().trigger('change');
                // console.log("oldUnitId", oldUnitId);
                // if (unit) {
                // Lấy nông trường theo đơn vị
                $.ajax({
                    url: '{{ route('get-farms-by-unit') }}',
                    method: 'GET',
                    data: {
                        unit: unit
                    },
                    success: function(response) {
                        // farmSelect.empty();
                        $.each(response.farms, function(index, farm) {
                            farmSelect.append('<option value="' + farm.id +
                                '">' + farm.farm_name + '</option>');
                        });
                        farmSelect.prop('selectedIndex', -1); // Không chọn gì
                        // Lấy các số xe liên quan đến đơn vị
                        $.ajax({
                            url: '{{ route('get-vehicles') }}',
                            method: 'GET',
                            data: {
                                unit_id: unit
                            },
                            success: function(response) {
                                // vehicleSelect.empty();
                                $.each(response.vehicles, function(
                                    index, vehicle) {
                                    vehicleSelect.append(
                                        '<option value="' +
                                        vehicle.id + '">' +
                                        vehicle
                                        .vehicle_number +
                                        '</option>');
                                });
                                vehicleSelect.prop('selectedIndex', -
                                    1); // Không chọn gì
                                if (oldFarmId || oldVehicleId || oldTrip ||
                                    oldreceivedDate) {

                                    if (oldFarmId) {
                                        farmSelect.val(oldFarmId);
                                    } else {
                                        farmSelect.val(null);
                                    }

                                    if (oldVehicleId) {
                                        vehicleSelect.val(oldVehicleId);
                                    } else {
                                        vehicleSelect.val(null);
                                    }

                                    if (oldreceivedDate) {
                                        receivedDate.val(oldreceivedDate);
                                    } else {
                                        receivedDate.val(null);
                                    }

                                    if (oldTrip) {
                                        trip.val(oldTrip);
                                    } else {
                                        trip.val(null);
                                    }

                                    // Chỉ trigger sau khi gán xong
                                    $('#farm_id, #received_date, #vehicle_number_id, [name="trip"]')
                                        .trigger(
                                            'change');
                                }
                                oldFarmId = null;
                                oldVehicleId = null;
                                oldTrip = null;
                                oldreceivedDate = null;
                            },
                            error: function() {
                                if (oldFarmId || oldTrip || oldreceivedDate) {

                                    if (oldFarmId) {
                                        farmSelect.val(oldFarmId);
                                    } else {
                                        farmSelect.val(null);
                                    }

                                    if (oldreceivedDate) {
                                        receivedDate.val(oldreceivedDate);
                                    } else {
                                        receivedDate.val(null);
                                    }

                                    if (oldTrip) {
                                        trip.val(oldTrip);
                                    } else {
                                        trip.val(null);
                                    }

                                    // Chỉ trigger sau khi gán xong
                                    $('#farm_id, #received_date, [name="trip"]')
                                        .trigger(
                                            'change');
                                }

                                oldFarmId = null;
                                oldTrip = null;
                                oldreceivedDate = null;

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Lỗi!',
                                    text: 'Không thể tải danh sách xe.',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    },
                    error: function() {
                        if (oldTrip ||
                            oldreceivedDate) {
                            if (oldreceivedDate) {
                                receivedDate.val(oldreceivedDate);
                            } else {
                                receivedDate.val(null);
                            }

                            if (oldTrip) {
                                trip.val(oldTrip);
                            } else {
                                trip.val(null);
                            }

                            // Chỉ trigger sau khi gán xong #received_date
                            $(' [name="trip"]')
                                .trigger(
                                    'change');

                            oldTrip = null;
                            oldreceivedDate = null;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Không thể tải danh sách nông trường.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
                // } else {
                //     $('#farm_id').empty();
                //     $('#vehicle_number_id').empty();
                // }
            });

            // Khi chọn Nông Trường
            // $('#farm_id').on('change', function() {
            //     const farmId = $(this).val();
            //     isPlantingLoaded = false;
            //     if (farmId) {
            //         $.ajax({
            //             url: '{{ route('get-planting-areas-by-farm') }}',
            //             method: 'GET',
            //             data: {
            //                 farm_id: farmId
            //             },
            //             success: function(response) {
            //                 const plantingAreaSelect = $('#planting_area_id');
            //                 plantingAreaSelect.empty().append(
            //                     '<option value="">Chọn Khu Vực Trồng</option>');
            //                 $.each(response, function(index, area) {
            //                     plantingAreaSelect.append('<option value="' +
            //                         area.id + '">' + area.ma_lo +
            //                         '</option>');
            //                 });
            //             },
            //             error: function() {
            //                 Swal.fire({
            //                     icon: 'error',
            //                     title: 'Lỗi!',
            //                     text: 'Không thể tải dữ liệu khu vực trồng.',
            //                     confirmButtonText: 'OK'
            //                 });
            //             }
            //         });
            //     } else {
            //         $('#planting_area_id').empty().append(
            //             '<option value="">Chọn Khu Vực Trồng</option>');
            //     }
            // });

            $('#planting_area_id').on('change', function(e) {
                const selectedId = $(this).val(); // Lấy ID của khu vực trồng đã chọn
                // console.log("selectedId", selectedId);
                // Nếu không có ID, không làm gì
                if (!selectedId || selectedId.length === 0) {
                    $('#chi_tieu_table').empty();
                    return;
                }

                $.ajax({
                    url: '{{ route('select.chi.tieu') }}', // Đảm bảo route này trả về dữ liệu chi tiết của khu vực trồng
                    type: 'GET',
                    data: {
                        id: selectedId
                    },
                    success: function(response) {
                        const chiTieuTable = $('#chi_tieu_table').empty();
                        // $('#chi_tieu_table').empty();
                        response.forEach(item => {
                            chiTieuTable.append(`
                                            <tr>
                                                <td>${item.ma_lo}</td>
                                                <td>${item.chi_tieu}</td>
                                            </tr>
                        `);
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Không thể lấy dữ liệu chi tiết.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            let debounceTimer = null;
            let currentRequest = null;

            $('#farm_id').on('change', function() {
                clearTimeout(debounceTimer);
                console.log('Giá trị mới:');
                debounceTimer = setTimeout(() => {
                    const plantingAreaSelect = $('#planting_area_id');
                    const receivedDate = $('#received_date').val() ?? "";
                    const vehicle = $('#vehicle_number_id').val() ?? "";
                    const trip = $('[name="trip"]').val() ?? "";
                    const farmId = $('#farm_id').val();
                    plantingAreaSelect.empty().val([]).trigger('change');
                    $('#chi_tieu_table').empty();
                    if (!farmId) return;

                    const dataRequest = [{
                        receivedDate: receivedDate,
                        vehicle: vehicle,
                        trip: trip
                    }];

                    // Nếu đang có request trước đó, huỷ đi
                    if (currentRequest) {
                        currentRequest.abort();
                    }

                    currentRequest = $.ajax({
                        url: '{{ route('get-planting-areas-by-farm') }}',
                        method: 'GET',
                        data: {
                            farm_id: farmId,
                            data_request: dataRequest,
                            ingredientId: ingredientId
                        },
                        success: function(response) {
                            response.forEach(item => {
                                plantingAreaSelect.append(
                                    `<option value="${item.id}">${item.ma_lo}</option>`
                                );
                            });

                            plantingAreaSelect.prop('selectedIndex', -
                                1); // Không chọn gì

                            if (oldPlantingAreaIds && oldPlantingAreaIds.length > 0) {
                                plantingAreaSelect.val(oldPlantingAreaIds).trigger(
                                    'change');
                            } else {
                                plantingAreaSelect.val(null);
                            }

                            oldPlantingAreaIds = [];
                        },
                        error: function(xhr, status) {
                            if (status !==
                                'abort') { // Bỏ qua lỗi nếu là do huỷ request
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Lỗi!',
                                    text: 'Không thể lấy dữ liệu khu vực trồng.',
                                    confirmButtonText: 'OK'
                                });
                            }
                        }
                    });

                }, 300); // Debounce 300ms
            });

            // $('[name="trip"]').on('input', function() {
            //     clearTimeout(debounceTimer);
            //     console.log('Giá trị mới:');
            //     debounceTimer = setTimeout(() => {
            //         const plantingAreaSelect = $('#planting_area_id');
            //         const receivedDate = $('#received_date').val() ?? "";
            //         const vehicle = $('#vehicle_number_id').val() ?? "";
            //         const trip = $('[name="trip"]').val() ?? "";
            //         const farmId = $('#farm_id').val();
            //         plantingAreaSelect.empty().val([]).trigger('change');
            //         $('#chi_tieu_table').empty();
            //         if (!farmId) return;

            //         const dataRequest = [{
            //             receivedDate: receivedDate,
            //             vehicle: vehicle,
            //             trip: trip
            //         }];

            //         // Nếu đang có request trước đó, huỷ đi
            //         if (currentRequest) {
            //             currentRequest.abort();
            //         }

            //         currentRequest = $.ajax({
            //             url: '{{ route('get-planting-areas-by-farm') }}',
            //             method: 'GET',
            //             data: {
            //                 farm_id: farmId,
            //                 data_request: dataRequest,
            //                 ingredientId: ingredientId
            //             },
            //             success: function(response) {
            //                 response.forEach(item => {
            //                     plantingAreaSelect.append(
            //                         `<option value="${item.id}">${item.ma_lo}</option>`
            //                     );
            //                 });

            //                 plantingAreaSelect.prop('selectedIndex', -
            //                     1); // Không chọn gì

            //                 if (oldPlantingAreaIds && oldPlantingAreaIds.length > 0) {
            //                     plantingAreaSelect.val(oldPlantingAreaIds).trigger(
            //                         'change');
            //                 } else {
            //                     plantingAreaSelect.val(null);
            //                 }

            //                 oldPlantingAreaIds = [];
            //             },
            //             error: function(xhr, status) {
            //                 if (status !==
            //                     'abort') { // Bỏ qua lỗi nếu là do huỷ request
            //                     Swal.fire({
            //                         icon: 'error',
            //                         title: 'Lỗi!',
            //                         text: 'Không thể lấy dữ liệu khu vực trồng.',
            //                         confirmButtonText: 'OK'
            //                     });
            //                 }
            //             }
            //         });

            //     }, 300); // Debounce 300ms
            // });


            // Khôi phục giá trị cũ
            if (oldUnitId) {
                $('#unit_id').val(oldUnitId).trigger('change');
            }
        });
</script>
<script>
    $(document).ready(function() {
            $('#type_of_pus_id').select2({
                placeholder: "Chọn Loại Mủ",
                allowClear: true,
                minimumResultsForSearch: 0,
                width: '100%',
            });
            $('#received_factory_id').select2({
                placeholder: "Chọn Nhà Máy",
                allowClear: true,
                minimumResultsForSearch: 0,
                width: '100%',
            });
        });
</script>
<style>
    .form-label {
        font-weight: bold;
    }

    .select2-container .select2-search--inline .select2-search__field {
        height: 25px;
    }

    .select2-container--default .select2-selection--single {
        height: 37px;
    }
</style>
@endsection