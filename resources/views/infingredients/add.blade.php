@extends('layouts.app')
@section('content')
<section style="">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0">
        </h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ingredients.index') }}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <form id="form-account" action="{{ route('save-ingredients') }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex">
                        <h5>Thêm Thông Tin Nguyên Liệu</h5>
                        <div class="prism-toggle">
                            <button type="submit" class="btn btn-primary">Lưu</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row modal-body gy-4">
                            <!-- Chọn Đơn Vị -->
                            <div class="col-md-4">
                                <label for="unit" class="form-label">Đơn Vị</label>
                                <select class="form-control" name="unit_id" id="unit" required>
                                    <option value="">Chọn Đơn Vị</option>
                                    @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Chọn Nông Trường -->
                            <div class="col-md-4">
                                <label for="farm_id" class="form-label">Nông Trường</label>
                                <select class="form-control" name="farm_id" id="farm_id" required>
                                    <option value="">Chọn Nông Trường</option>
                                    <!-- Nông Trường sẽ được load sau khi chọn đơn vị -->
                                </select>
                            </div>
                            <!-- Nhà Máy Tiếp Nhận -->
                            <div class="col-md-4">
                                <label for="received_factory_id" class="form-label">Nhà Máy Tiếp Nhận</label>
                                <select class="form-control" name="received_factory_id" id="received_factory_id"
                                    required>
                                    {{-- @if ($singleFactory)
                                    <option value="{{ $singleFactory->id }}" selected>
                                        {{ $singleFactory->factory_name }}
                                    </option>
                                    @else --}}
                                    <option value="">Chọn nhà máy</option>
                                    @foreach ($factory as $val)
                                    <option value="{{ $val->id }}" {{ old('received_factory_id')==$val->id ? 'selected'
                                        : '' }}>
                                        {{ $val->factory_name }}
                                    </option>
                                    @endforeach
                                    {{-- @endif --}}
                                </select>

                            </div>
                            <!-- Loại Mủ -->
                            <div class="col-md-4">
                                <label for="type_of_pus_id" class="form-label">Loại Mủ</label>
                                <select class="form-control" name="type_of_pus_id" id="type_of_pus_id" required>
                                    <option value="">Chọn loại mủ</option>
                                    @foreach ($typeofpus as $val)
                                    <option value="{{ $val->id }}" {{ old('type_of_pus_id')==$val->id ? 'selected' : ''
                                        }}>
                                        {{ $val->name_pus }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Chọn Số Xe -->
                            <div class="col-md-4">
                                <label for="vehicle_number_id" class="form-label">Số Xe</label>
                                <select class="form-control select2" name="vehicle_number_id" id="vehicle_number_id"
                                    required>
                                    <option value="">Chọn Số Xe</option>
                                    <!-- Số xe sẽ được load khi chọn đơn vị -->
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="trip" class="form-label">Số Chuyến</label>
                                <input type="number" min="0" class="form-control" name="trip" placeholder="Chuyến"
                                    required>
                            </div>



                            <!-- Ngày Tiếp Nhận -->
                            <div class="col-md-4">
                                <label for="received_date" class="form-label">Ngày Tiếp Nhận</label>
                                <div class="datepicker-wrapper">
                                    <input type="text" class="form-control date-sx-input datetimepicker datepicker"
                                        name="received_date" id="received_date" placeholder="dd/mm/yyyy"
                                        autocomplete="off" onkeydown="return false;" required>
                                    <i class="fa fa-calendar calendar-icon" style="cursor: pointer;"></i>
                                </div>
                            </div>

                            <!-- Ngày Bắt Đầu Cạo -->
                            <div class="col-md-4">
                                <label for="harvesting_date" class="form-label">Ngày Bắt Đầu Cạo</label>
                                <div class="datepicker-wrapper">
                                    <input type="text" class="form-control date-sx-input datetimepicker datepicker"
                                        name="harvesting_date" id="harvesting_date" placeholder="dd/mm/yyyy"
                                        value="{{ old('harvesting_date') }}" autocomplete="off"
                                        onkeydown="return false;" required>
                                    <i class="fa fa-calendar calendar-icon" style="cursor: pointer;"></i>
                                </div>
                            </div>

                            <!-- Ngày Kết Thúc Cạo -->
                            <div class="col-md-4">
                                <label for="end_harvest_date" class="form-label">Ngày Kết Thúc Cạo</label>
                                <div class="datepicker-wrapper">
                                    <input type="text" class="form-control date-sx-input datetimepicker datepicker"
                                        name="end_harvest_date" id="end_harvest_date" placeholder="dd/mm/yyyy"
                                        value="{{ old('end_harvest_date') }}" autocomplete="off"
                                        onkeydown="return false;" required>
                                    <i class="fa fa-calendar calendar-icon" style="cursor: pointer;"></i>
                                </div>
                            </div>


                            <!-- Chọn Khu Vực Trồng -->
                            <div class="col-md-12">
                                <label for="planting_area_id" class="form-label">Chọn Khu Vực Trồng</label>
                                <select id="planting_area_id" name="planting_area_id[]" multiple required>
                                    <!-- Will be populated dynamically -->
                                </select>
                            </div>

                            <table class="table mt-3">
                                <thead>
                                    <tr>
                                        <th>Mã Lô Vùng Trồng</th>
                                        <th>Giống Cây</th>
                                    </tr>
                                </thead>
                                <tbody id="chi_tieu_table"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>

            <script>
                let oldUnitId = "{{ old('unit_id', $singleUnit->id ?? 'null') }}";
                    let oldFarmId = "{{ old('farm_id', $singleFarm->id ?? 'null') }}";
                    let oldTrip = "{{ old('trip') }}";
                    let oldVehicleId = "{{ old('vehicle_number_id') }}";
                    let oldPlantingAreaIds = {!! json_encode(old('planting_area_id', [])) !!};
                    let oldreceivedDate = "{{ old('received_date') }}";
            </script>


            <script>
                $(document).ready(function () {
                        let hasAlerted = false;
                        let isClearing = false;
                        let isPlantingLoaded = false;

                        $('#unit').select2({
                            language: "vi",
                            placeholder: "Chọn Đơn Vị",
                            allowClear: true,
                            minimumResultsForSearch: 0,
                            width: '100%',
                        });

                        $('#farm_id').select2({
                            language: "vi",
                            placeholder: "Chọn Nông Trường",
                            allowClear: true,
                            minimumResultsForSearch: 0,
                            width: '100%',
                        });

                        $('#vehicle_number_id').select2({
                            language: "vi",
                            placeholder: 'Chọn Số Xe',
                            allowClear: true,
                            width: '100%'
                        });

                        $('#planting_area_id').select2({
                            language: "vi",
                            placeholder: 'Chọn Khu Vực Trồng',
                            allowClear: true,
                            width: '100%',
                        });

                        // Khi chọn Đơn Vị
                        $('#unit').on('change', function () {
                            const unit = $(this).val();
                            const farmSelect = $('#farm_id');
                            const vehicleSelect = $('#vehicle_number_id');
                            const receivedDate = $('#received_date');
                            const trip = $('[name="trip"]');
                            farmSelect.empty().trigger('change');
                            vehicleSelect.empty().trigger('change');
                            farmSelect.append('<option value="">Đang tải nông trường...</option>');
                            vehicleSelect.append('<option value="">Đang tải xe...</option>');

                            farmSelect.prop('disabled', true);
                            vehicleSelect.prop('disabled', true);
                            // if (unit) {
                            $.ajax({
                                url: '{{ route("get-farms-by-unit") }}',
                                method: 'GET',
                                data: {
                                    unit: unit
                                },
                                success: function (response) {
                                    farmSelect.empty();

                                    $.each(response.farms, function (index, farm) {
                                        farmSelect.append('<option value="' + farm.id +
                                            '">' + farm.farm_name + '</option>');
                                    });
                                    farmSelect.prop('disabled', false);
                                    farmSelect.prop('selectedIndex', -1); // Không chọn gì
                                    // Lấy các số xe liên quan đến đơn vị
                                    $.ajax({
                                        url: '{{ route("get-vehicles") }}',
                                        method: 'GET',
                                        data: {
                                            unit_id: unit
                                        },
                                        success: function (response) {
                                            vehicleSelect.empty();
                                            $.each(response.vehicles, function (
                                                index, vehicle) {
                                                vehicleSelect.append(
                                                    '<option value="' +
                                                    vehicle.id + '">' +
                                                    vehicle
                                                        .vehicle_number +
                                                    '</option>');
                                            });
                                            vehicleSelect.prop('disabled', false);
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
                                        error: function () {
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

                                                // Chỉ trigger sau khi gán xong #received_date
                                                $('#farm_id, [name="trip"]')
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
                                error: function () {
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

                                        // Chỉ trigger sau khi gán xong
                                        $(' #received_date, [name="trip"]')
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

                        $('#planting_area_id').on('change', function (e) {
                            const selectedId = $(this).val(); // ID của khu vực trồng đã chọn
                            // console.log("selectedId", selectedId);
                            if (!selectedId || selectedId.length === 0) {
                                $('#chi_tieu_table').empty();
                                return;
                            }

                            $.ajax({
                                url: '{{ route("select.chi.tieu") }}',
                                type: 'GET',
                                data: {
                                    id: selectedId
                                },
                                success: function (response) {
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
                                error: function () {
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
                        // #received_date
                        $('#farm_id').on('change', function () {
                            clearTimeout(debounceTimer);

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
                                    url: '{{ route("get-planting-areas-by-farm") }}',
                                    method: 'GET',
                                    data: {
                                        farm_id: farmId,
                                        data_request: dataRequest
                                    },
                                    success: function (response) {
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
                                    error: function (xhr, status) {
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

                        // $('[name="trip"]').on('input', function () {
                        //     clearTimeout(debounceTimer);

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
                        //             url: '{{ route("get-planting-areas-by-farm") }}',
                        //             method: 'GET',
                        //             data: {
                        //                 farm_id: farmId,
                        //                 data_request: dataRequest
                        //             },
                        //             success: function (response) {
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
                        //             error: function (xhr, status) {
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
                            $('#unit').val(oldUnitId).trigger('change');
                        } else {
                            $('#unit').val(null);
                        }
                    });
            </script>

        </div>
    </div>
</section>
<script>
    $(document).ready(function () {
            $('#type_of_pus_id').select2({
                language: "vi",
                placeholder: "Chọn Loại Mủ",
                allowClear: true,
                minimumResultsForSearch: 0,
                width: '100%',
            });
            // $('#received_factory_id').select2({
            //     language: "vi",
            //     placeholder: "Chọn Nhà Máy",
            //     allowClear: true,
            //     minimumResultsForSearch: 0,
            //     width: '100%',
            // });
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection