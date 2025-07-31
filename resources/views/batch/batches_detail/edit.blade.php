@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0"></h5>
        <div class="ms-md-1 ms-0 padding">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('batches.index') }}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chỉnh Sửa</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <form id="form-account" action="{{ route('update-batches', ['id' => $batch->id]) }}" method="POST">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex">
                        <h5>Chỉnh Sửa Kết Nối Nguyên Liệu</h5>
                        {{-- <div class="card-title">Chỉnh sửa thông tin mã lô</div> --}}
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">Lưu</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="batch-container">
                            <div class="row batchRow">
                                <input type="hidden" name="batch_id" value="{{ $batch->id }}">
                                <div class="col-md-6 form-group">
                                    <label for="batch_code" class="text-black">Mã Lô</label>
                                    <input type="text" class="form-control" name="batch_code"
                                        value="{{ $batch->batch_code }}" readonly>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="date_sx" class="text-black">Ngày Sản Xuất</label>
                                    {{-- <input type="date" class="form-control" name="date_sx"
                                        value="{{ $batchIngredient->batch->date_sx }}" required> --}}
                                    <div class="datepicker-wrapper">
                                        <input type="text" class="form-control datetimepicker datepicker" name="date_sx"
                                            id="date_sx" placeholder="dd/mm/yyyy"
                                            value="{{ \Carbon\Carbon::parse($batch->date_sx)->format('d/m/Y') }}"
                                            autocomplete="off" onkeydown="return false;" required>
                                        <i class="fa fa-calendar calendar-icon" style="cursor: pointer;"></i>
                                    </div>
                                </div>
                                {{-- <div class="col-md-2 d-flex align-items-end form-group">
                                    <button type="button" class="btn btn-success addBatchRow">+</button>
                                    <button type="button" class="btn btn-danger removeBatchRow ms-2">-</button>
                                </div> --}}
                            </div>
                        </div>

                        {{-- <div class="row" style="padding-top:20px">
                            <div class="col-md-6 form-group">
                                <label for="batch_weight" class="text-black">Khối Lượng Lô Hàng (Tấn)</label>
                                <input class="form-control" type="number" name="batch_weight" min="0" step="any"
                                    value="{{ $batchIngredient->batch->batch_weight }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="banh_weight" class="text-black">Khối Lượng Bành (Kg)</label>
                                <input class="form-control" type="number" name="banh_weight" min="0" step="any"
                                    value="{{ $batchIngredient->batch->banh_weight }}" required>
                            </div>
                        </div> --}}
                        <div class="row" style="padding-top:20px">
                            <div class="col-md-6 form-group">
                                <label for="batch_weight" class="text-black">Khối Lượng Lô Hàng (Tấn)</label>
                                <select class="form-control" name="batch_weight" required>
                                    <option value="">Chọn khối lượng lô hàng</option>
                                    <option value="2.4" {{ $batch->batch_weight == '2.4' ? 'selected' : '' }}>
                                        2.4 Tấn</option>
                                    <option value="2.52" {{ $batch->batch_weight == '2.52' ? 'selected' : '' }}>
                                        2.52 Tấn</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="banh_weight" class="text-black">Khối Lượng Bành (Kg)</label>
                                <select class="form-control" name="banh_weight" required>
                                    <option value="">Chọn khối lượng bành</option>
                                    <option value="33.33" {{ $batch->banh_weight == '33.33' ? 'selected' : '' }}>
                                        33.33 Kg</option>
                                    <option value="35" {{ $batch->banh_weight == '35' ? 'selected' : '' }}>35 Kg
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="detailsTable" class="table">
                                <thead>
                                    <tr>
                                        <th>Loại Mủ</th>
                                        <th>Ngày Tiếp Nhận</th>
                                        <th>Nông Trường</th>
                                        <th>Số Xe</th>
                                        <th>Số Chuyến</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($relatedIngredients as $ingredient)
                                    <tr>
                                        <input type="hidden" name="ingredient_ids[]"
                                            value="{{ $ingredient->ingredient->id }}">
                                        <td>
                                            <input type="text" class="form-control typeOfPus-input"
                                                name="type_of_pus_ids[]" placeholder="Chọn Loại Mủ"
                                                value="{{ $ingredient->ingredient->typeOfPus->name_pus }}" required>
                                        </td>
                                        <td>
                                            {{-- <input type="text" class="form-control received-date-input"
                                                name="received_dates[]"
                                                value="{{ $ingredient->ingredient->received_date }}" required> --}}
                                            <input type="text" class="form-control received-date-input"
                                                name="received_dates[]"
                                                value="{{ \Carbon\Carbon::parse($ingredient->ingredient->received_date)->format('d/m/Y') }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control farm-input"
                                                value="{{ $ingredient->ingredient->farm->farm_name }}" required>
                                            <input type="hidden" class="form-control farm-input" name="farm_ids[]"
                                                value="{{ $ingredient->ingredient->farm->id }}" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control vehicle-input" name="vehicle_ids[]"
                                                value="{{ $ingredient->ingredient->vehicle->vehicle_number }}" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control trip-input" name="batches[]"
                                                value="{{ $ingredient->ingredient->trip }}" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger deleteRow">Xóa</button>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="deleted_ingredient_ids[]" id="deleted_ingredient_ids" value="">
                        <button type="button" id="addRow" class="btn btn-success mt-2">Thêm Dòng</button>
                    </div>
                </div>
            </form>

            <style>
                .table input {
                    width: 100%;
                    min-width: 150px;
                    height: 40px;
                    font-size: 16px;
                }
            </style>
            <script>
                var productionDate = "{{ $batch->date_sx }}";
                    console.log(productionDate);
            </script>
            <script>
                $(document).ready(function() {
                        const ingredientsData = @json($ingredients);
                        const productionDate = new Date("{{ $batch->date_sx }}");
                        let deletedIngredientIds = [];

                        function normalizeString(str) {
                            return str ? str.normalize('NFC').toLowerCase() : '';
                        }

                        function setupAutocomplete(input, suggestions, callback) {
                            input.autocomplete({
                                source: suggestions,
                                minLength: 0,
                                select: function(event, ui) {
                                    input.val(ui.item.value);
                                    if (callback) callback(ui.item.value);
                                    return false;
                                }
                            }).autocomplete('search', '');
                        }

                        function formatDateToDDMMYYYY(dateStr) {
                            if (!dateStr) return '';
                            const date = new Date(dateStr);
                            if (isNaN(date.getTime())) return '';
                            const day = String(date.getDate()).padStart(2, '0');
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const year = date.getFullYear();
                            return `${day}/${month}/${year}`;
                        }

                        function parseDateFromDDMMYYYY(dateStr) {
                            if (!dateStr) return null;
                            const parts = dateStr.split('/');
                            if (parts.length !== 3) return null;
                            return new Date(`${parts[2]}-${parts[1]}-${parts[0]}`);
                        }

                        function fillRowData(row, changedField) {
                            const selectedTypeOfPus = normalizeString(row.find('.typeOfPus-input').val());
                            const selectedDate = row.find('.received-date-input').val();
                            const selectedFarm = normalizeString(row.find('.farm-input').val());
                            const selectedVehicle = normalizeString(row.find('.vehicle-input').val());
                            if (changedField === 'typeOfPus' && selectedTypeOfPus) {
                                if (!row.find('.received-date-input').val()) {
                                    const dateSuggestions = ingredientsData
                                        .filter(ingredient => normalizeString(ingredient.type_of_pus?.name_pus) ===
                                            selectedTypeOfPus)
                                        .map(ingredient => formatDateToDDMMYYYY(ingredient.received_date))
                                        .filter((v, i, a) => a.indexOf(v) === i);
                                    setupAutocomplete(row.find('.received-date-input'), dateSuggestions);
                                }
                            }
                            if (changedField === 'receivedDate' && selectedDate) {
                                if (!row.find('.farm-input').val()) {
                                    const farmSuggestions = ingredientsData
                                        .filter(ingredient => normalizeString(ingredient.type_of_pus?.name_pus) ===
                                            selectedTypeOfPus &&
                                            formatDateToDDMMYYYY(ingredient.received_date) === selectedDate)
                                        .map(ingredient =>
                                            `${ingredient.farm?.farm_name} - ${ingredient.farm?.unit_relation?.unit_name}`)
                                        .filter((v, i, a) => a.indexOf(v) === i);
                                    setupAutocomplete(row.find('.farm-input'), farmSuggestions);
                                }
                            }

                            if (changedField === 'farm' && selectedFarm) {
                                if (!row.find('.vehicle-input').val()) {
                                    const vehicleSuggestions = ingredientsData
                                        .filter(ingredient => normalizeString(ingredient.type_of_pus?.name_pus) ===
                                            selectedTypeOfPus &&
                                            formatDateToDDMMYYYY(ingredient.received_date) === selectedDate &&
                                            normalizeString(
                                                `${ingredient.farm?.farm_name} - ${ingredient.farm?.unit_relation?.unit_name}`
                                            ) === normalizeString(row.find('.farm-input').data('fullFarm')))
                                        .map(ingredient => ingredient.vehicle?.vehicle_number)
                                        .filter((v, i, a) => a.indexOf(v) === i);
                                    setupAutocomplete(row.find('.vehicle-input'), vehicleSuggestions);
                                }
                            }
                            if (changedField === 'vehicle' && selectedVehicle) {
                                if (!row.find('.trip-input').val()) {
                                    const tripSuggestions = ingredientsData
                                        .filter(ingredient => normalizeString(ingredient.type_of_pus?.name_pus) ===
                                            selectedTypeOfPus &&
                                            formatDateToDDMMYYYY(ingredient.received_date) === selectedDate &&
                                            normalizeString(
                                                `${ingredient.farm?.farm_name} - ${ingredient.farm?.unit_relation?.unit_name}`
                                            ) === normalizeString(row.find('.farm-input').data('fullFarm')) &&
                                            normalizeString(ingredient.vehicle?.vehicle_number) === selectedVehicle)
                                        .map(ingredient => ingredient.trip)
                                        .filter((v, i, a) => a.indexOf(v) === i);
                                    setupAutocomplete(row.find('.trip-input'), tripSuggestions);
                                }
                            }
                        }
                        function getSelectedIngredients(currentRow) {
                            const selectedIngredients = [];
                            $('#detailsTable tbody tr').each(function(index, row) {
                                if ($(row).is(currentRow)) return;
                                const typeOfPus = normalizeString($(row).find('.typeOfPus-input').val());
                                const receivedDate = $(row).find('.received-date-input').val();
                                const farm = normalizeString($(row).find('.farm-input').val());
                                const vehicle = normalizeString($(row).find('.vehicle-input').val());
                                const trip = normalizeString($(row).find('.trip-input').val());
                                if (typeOfPus && receivedDate && farm && vehicle && trip) {
                                    selectedIngredients.push({
                                        typeOfPus,
                                        receivedDate,
                                        farm,
                                        vehicle,
                                        trip,
                                        rowIndex: index
                                    });
                                }
                            });
                            return selectedIngredients;
                        }
                        function checkForDuplicateIngredient(currentRow) {
                            const typeOfPus = normalizeString(currentRow.find('.typeOfPus-input').val());
                            const receivedDate = currentRow.find('.received-date-input').val();
                            const farm = normalizeString(currentRow.find('.farm-input').val());
                            const vehicle = normalizeString(currentRow.find('.vehicle-input').val());
                            const trip = normalizeString(currentRow.find('.trip-input').val());
                            if (!typeOfPus || !receivedDate || !farm || !vehicle || !trip) return false;
                            const selectedIngredients = getSelectedIngredients(currentRow);
                            const duplicate = selectedIngredients.find(ingredient => {
                                return ingredient.typeOfPus === typeOfPus &&
                                    ingredient.receivedDate === receivedDate &&
                                    ingredient.farm === farm &&
                                    ingredient.vehicle === vehicle &&
                                    ingredient.trip === trip;
                            });
                            if (duplicate) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Nguyên liệu đã được chọn!',
                                    text: `Nguyên liệu này đã được chọn ở dòng ${duplicate.rowIndex + 1}. Vui lòng chọn nguyên liệu khác.`,
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    currentRow.find('.received-date-input').val('');
                                    currentRow.find('.farm-input').val('');
                                    currentRow.find('.vehicle-input').val('');
                                    currentRow.find('.trip-input').val('');
                                    currentRow.find('.farm-input').data('fullFarm', '');
                                    currentRow.find('.received-date-input').focus();
                                });
                                return true;
                            }
                            return false;
                        }
                        $(document).on('focus', '.typeOfPus-input', function() {
                            const input = $(this);
                            const suggestions = ingredientsData
                                .map(ingredient => ingredient.type_of_pus?.name_pus)
                                .filter((v, i, a) => a.indexOf(v) === i);

                            setupAutocomplete(input, suggestions, function(selectedTypeOfPus) {
                                const row = input.closest('tr');
                                row.find('.received-date-input').val('');
                                row.find('.farm-input').val('');
                                row.find('.vehicle-input').val('');
                                row.find('.trip-input').val('');
                                row.find('.farm-input').data('fullFarm', '');
                                fillRowData(row, 'typeOfPus');
                            });
                        });
                        $(document).on('focus', '.received-date-input', function() {
                            const input = $(this);
                            const row = input.closest('tr');
                            const selectedTypeOfPus = normalizeString(row.find('.typeOfPus-input').val());
                            const suggestions = ingredientsData
                                .filter(ingredient => normalizeString(ingredient.type_of_pus?.name_pus) ===
                                    selectedTypeOfPus)
                                .map(ingredient => formatDateToDDMMYYYY(ingredient.received_date))
                                .filter((v, i, a) => a.indexOf(v) === i);

                            setupAutocomplete(input, suggestions, function(selectedDate) {
                                row.find('.farm-input').val('');
                                row.find('.vehicle-input').val('');
                                row.find('.trip-input').val('');
                                row.find('.farm-input').data('fullFarm', '');
                                const selectedDateObj = parseDateFromDDMMYYYY(selectedDate);
                                if (selectedDateObj && selectedDateObj > productionDate) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Ngày không hợp lệ!',
                                        text: 'Ngày tiếp nhận phải nhỏ hơn hoặc bằng ngày sản xuất.',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        input.val('');
                                    });
                                    return;
                                }
                                fillRowData(row, 'receivedDate');
                            });
                        });
                        // $(document).on('focus', '.farm-input', function() {
                        //     const input = $(this);
                        //     const row = input.closest('tr');
                        //     const selectedTypeOfPus = normalizeString(row.find('.typeOfPus-input').val());
                        //     const selectedDate = row.find('.received-date-input').val();

                        //     const suggestions = ingredientsData
                        //         .filter(ingredient =>
                        //             normalizeString(ingredient.type_of_pus?.name_pus) === selectedTypeOfPus &&
                        //             formatDateToDDMMYYYY(ingredient.received_date) === selectedDate)
                        //         .map(ingredient =>
                        //             `${ingredient.farm?.farm_name} - ${ingredient.farm?.unit_relation?.unit_name}`)
                        //         .filter((v, i, a) => a.indexOf(v) === i);

                        //     setupAutocomplete(input, suggestions, function(selectedFarm) {
                        //         const [selectedFarmName, selectedUnitName] = selectedFarm.split(' - ');
                        //         row.find('.farm-input').val(selectedFarmName);
                        //         row.find('.farm-input').data('fullFarm', selectedFarm);
                        //         row.find('.vehicle-input').val('');
                        //         row.find('.trip-input').val('');
                        //         fillRowData(row, 'farm');
                        //     });
                        // });
                        // $(document).on('focus', '.vehicle-input', function() {
                        //     const input = $(this);
                        //     const row = input.closest('tr');
                        //     const selectedTypeOfPus = normalizeString(row.find('.typeOfPus-input').val());
                        //     const selectedDate = row.find('.received-date-input').val();
                        //     const fullFarm = row.find('.farm-input').data('fullFarm');
                        //     const suggestions = ingredientsData
                        //         .filter(ingredient =>
                        //             normalizeString(ingredient.type_of_pus?.name_pus) === selectedTypeOfPus &&
                        //             formatDateToDDMMYYYY(ingredient.received_date) === selectedDate &&
                        //             normalizeString(
                        //                 `${ingredient.farm?.farm_name} - ${ingredient.farm?.unit_relation?.unit_name}`
                        //             ) === normalizeString(fullFarm))
                        //         .map(ingredient => ingredient.vehicle?.vehicle_number)
                        //         .filter((v, i, a) => a.indexOf(v) === i);

                        //     setupAutocomplete(input, suggestions, function(selectedVehicle) {
                        //         row.find('.trip-input').val('');
                        //         fillRowData(row, 'vehicle');
                        //     });
                        // });
                        $(document).on('focus', '.farm-input', function() {
                            const input = $(this);
                            const row = input.closest('tr');
                            const selectedTypeOfPus = normalizeString(row.find('.typeOfPus-input').val());
                            const selectedDate = row.find('.received-date-input').val();

                            const suggestions = ingredientsData
                                .filter(ingredient =>
                                    normalizeString(ingredient.type_of_pus?.name_pus) === selectedTypeOfPus &&
                                    formatDateToDDMMYYYY(ingredient.received_date) === selectedDate
                                )
                                .map(ingredient =>
                                    `${ingredient.farm?.farm_name} - ${ingredient.farm?.unit_relation?.unit_name}`
                                )
                                .filter((v, i, a) => a.indexOf(v) === i);

                            setupAutocomplete(input, suggestions, function(selectedFarm) {
                                const [selectedFarmName, selectedUnitName] = selectedFarm.split(' - ');

                                console.log("selectedFarmName", selectedFarmName);
                                row.find('.farm-input').val(selectedFarmName);
                                row.find('.farm-input').data('fullFarm', selectedFarm);
                                row.find('.vehicle-input').val('');
                                row.find('.trip-input').val('');

                                const matchedIngredient = ingredientsData.find(ingredient => {
                                    console.log("ingredient", ingredient)
                                    return (
                                        normalizeString(
                                            `${ingredient.farm?.farm_name} - ${ingredient.farm?.unit_relation?.unit_name}`
                                        ) === normalizeString(selectedFarm) &&
                                        normalizeString(ingredient.type_of_pus?.name_pus) ===
                                        selectedTypeOfPus &&
                                        formatDateToDDMMYYYY(ingredient.received_date) ===
                                        selectedDate
                                    );
                                });

                                if (matchedIngredient) {
                                    row.find('input[name="farm_ids[]"]').val(matchedIngredient.farm.id);
                                } else {
                                    row.find('input[name="farm_ids[]"]').val('');
                                }

                                fillRowData(row, 'farm');
                            });
                        });
                        $(document).on('focus', '.vehicle-input', function() {
                            const input = $(this);
                            const row = input.closest('tr');
                            const selectedTypeOfPus = normalizeString(row.find('.typeOfPus-input').val());
                            const selectedDate = row.find('.received-date-input').val();
                            const fullFarm = row.find('.farm-input').data('fullFarm'); // Lấy thông tin đầy đủ của nông trường

                            const suggestions = ingredientsData
                                .filter(ingredient =>
                                    normalizeString(ingredient.type_of_pus?.name_pus) === selectedTypeOfPus &&
                                    formatDateToDDMMYYYY(ingredient.received_date) === selectedDate &&
                                    normalizeString(
                                        `${ingredient.farm?.farm_name} - ${ingredient.farm?.unit_relation?.unit_name}`
                                    ) === normalizeString(fullFarm)) // Lọc theo nông trường đầy đủ
                                .map(ingredient => ingredient.vehicle?.vehicle_number)
                                .filter((v, i, a) => a.indexOf(v) === i);

                            setupAutocomplete(input, suggestions, function(selectedVehicle) {
                                row.find('.trip-input').val('');
                                fillRowData(row, 'vehicle');
                            });
                        });
                        $(document).on('focus', '.trip-input', function() {
                            const input = $(this);
                            const row = input.closest('tr');
                            const selectedTypeOfPus = normalizeString(row.find('.typeOfPus-input').val());
                            const selectedDate = row.find('.received-date-input').val();
                            const fullFarm = row.find('.farm-input').data('fullFarm');
                            const selectedVehicle = normalizeString(row.find('.vehicle-input').val());
                            const suggestions = ingredientsData
                                .filter(ingredient =>
                                    normalizeString(ingredient.type_of_pus?.name_pus) === selectedTypeOfPus &&
                                    formatDateToDDMMYYYY(ingredient.received_date) === selectedDate &&
                                    normalizeString(
                                        `${ingredient.farm?.farm_name} - ${ingredient.farm?.unit_relation?.unit_name}`
                                    ) === normalizeString(fullFarm) &&
                                    normalizeString(ingredient.vehicle?.vehicle_number) === selectedVehicle)
                                .map(ingredient => ingredient.trip)
                                .filter((v, i, a) => a.indexOf(v) === i);

                            setupAutocomplete(input, suggestions, function() {
                                checkForDuplicateIngredient(row);
                            });
                        });
                        $('#addRow').on('click', function() {
                            const lastRow = $('#detailsTable tbody tr').last();
                            const typeOfPusValue = lastRow.find('.typeOfPus-input').val().trim();

                            const isLastRowComplete = lastRow.find('.typeOfPus-input').val() &&
                                lastRow.find('.received-date-input').val() &&
                                lastRow.find('.farm-input').val() &&
                                lastRow.find('.vehicle-input').val() &&
                                lastRow.find('.trip-input').val();

                            if (!isLastRowComplete) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Vui lòng điền đầy đủ thông tin!',
                                    text: 'Điền đầy đủ thông tin cho dòng cuối cùng trước khi thêm dòng mới.',
                                    confirmButtonText: 'OK'
                                });
                                return;
                            }
                            const newRow = `
                                <tr>
                                    <td><input type="text" class="form-control typeOfPus-input read-only-locked" name="type_of_pus_ids[]" placeholder="Chọn Loại Mủ" value="${typeOfPusValue}" required></td>
                                    <td><input type="text" class="form-control received-date-input" name="received_dates[]" placeholder="Chọn Ngày Tiếp Nhận" required></td>
                                    <td>
                                        <input type="text" class="form-control farm-input" placeholder="Chọn Nông Trường" required>
                                        <input type="hidden" name="farm_ids[]" class="farm-id-hidden" value="">
                                    </td>
                                    <td><input type="text" class="form-control vehicle-input" name="vehicle_ids[]" placeholder="Chọn Số Xe" required></td>
                                    <td><input type="text" class="form-control trip-input" name="batches[]" placeholder="Số Chuyến" required></td>
                                    <td><button type="button" class="btn btn-danger deleteRow">Xóa</button></td>
                                </tr>
                            `;
                            $('#detailsTable tbody').append(newRow);
                            const newRowElement = $('#detailsTable tbody tr').last();
                            fillRowData(newRowElement, 'typeOfPus');
                        });

                        $(document).on('click', '.deleteRow', function() {
                            const row = $(this).closest('tr');
                            const rowIndex = row.index();
                            const rowCount = $('#detailsTable tbody tr').length;

                            if (rowIndex === 0) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Không thể xóa!',
                                    text: 'Cần giữ lại ít nhất 1 dòng nguyên liệu đầu tiên.',
                                    confirmButtonText: 'OK'
                                });
                                return;
                            }

                            if (rowCount > 1) {
                                Swal.fire({
                                    title: 'Bạn có chắc muốn xóa dòng này?',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: 'Có, xóa!',
                                    cancelButtonText: 'Hủy'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        const ingredientId = row.data('ingredient-id');
                                        if (ingredientId) {
                                            deletedIngredientIds.push(ingredientId);
                                            $('form').append(
                                                `<input type="hidden" name="deleted_ingredient_ids[]" value="${ingredientId}">`
                                            );
                                        }

                                        row.remove();
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Đã xóa!',
                                            text: 'Dòng thông tin đã được xóa.',
                                            timer: 1200,
                                            showConfirmButton: false
                                        });
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Không thể xóa!',
                                    text: 'Cần giữ lại ít nhất 1 dòng thông tin.',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });

                        $('#detailsTable tbody tr').each(function() {
                            const row = $(this);
                            const ingredientId = row.find('input[name="ingredient_ids[]"]').val();
                            if (ingredientId) {
                                row.data('ingredient-id', ingredientId);
                            }
                            checkForDuplicateIngredient(row);
                        });

                        $('form').on('submit', function(e) {
                            let hasDuplicates = false;
                            $('#detailsTable tbody tr').each(function() {
                                if (checkForDuplicateIngredient($(this))) {
                                    hasDuplicates = true;
                                }
                            });
                            if (hasDuplicates) {
                                e.preventDefault();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Có nguyên liệu trùng lặp!',
                                    text: 'Vui lòng kiểm tra và chọn nguyên liệu khác.',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    });
            </script>

        </div>
    </div>
</section>
<script>
    $(document).ready(function() {
            $('#farm_id').select2({
                language: "vi",
                placeholder: "Chọn Nông Trường",
                allowClear: true,
                minimumResultsForSearch: 0,
                width: '100%',
            });
            $('#type_of_pus_id').select2({
                language: "vi",
                placeholder: "Chọn Loại Mủ",
                allowClear: true,
                minimumResultsForSearch: 0,
                width: '100%',
            });
            // $('#vehicle_number').select2({
            //     placeholder: "Chọn Xe",
            //     allowClear: true,
            //     minimumResultsForSearch: 0,
            //     width: '100%',
            // });
        });
</script>
<script>
    $(document).ready(function() {
        function batchesOptions(selectedIds = [], currentBatchId = null) {
            const batches = @json($batches); // Danh sách tất cả các lô
            const batchIdsWithIngredients = @json($batchIdsWithIngredients); // Danh sách các lô đã có nguyên liệu

            // Lọc các lô đã có nguyên liệu và những lô đã chọn
            return batches
                .filter(batch => {
                    // Kiểm tra lô chưa có nguyên liệu và chưa được chọn
                    return !batchIdsWithIngredients.includes(batch.id) &&
                        (!selectedIds.includes(batch.id) || batch.id === currentBatchId);
                })
                .map(batch => {
                    // Trả về danh sách các option với Mã Lô
                    return `<option value="${batch.id}">${batch.batch_code}</option>`;
                })
                .join('');
        }

        function refreshBatchDropdowns(currentSelect = null) {
            const selectedValues = [];
            // Lấy tất cả các giá trị đã chọn từ các dropdown hiện tại
            $('.batchRow select[name="batch_code[]"]').each(function() {
                const val = $(this).val();
                if (val) selectedValues.push(parseInt(val));
            });

            // Cập nhật lại mỗi dropdown lô
            $('.batchRow select[name="batch_code[]"]').each(function() {
                const $dropdown = $(this);
                const currentVal = $dropdown.val() ? parseInt($dropdown.val()) : null;
                const optionsHtml = batchesOptions(selectedValues, currentVal);

                // Cập nhật lại các options trong dropdown
                $dropdown.html('<option value="">Chọn Mã Lô</option>' + optionsHtml);
                $dropdown.val(currentVal).trigger('change.select2');
            });
        }

        $(document).on('click', '.addBatchRow', function() {
            const container = $('#batch-container');
            let existingIds = container.find('.datetimepicker').map(function() {
                return parseInt($(this).attr('id').split('_')[2]);
            }).get();

            let newId = 1;
            while (existingIds.includes(newId)) {
                newId++;
            }

            const selectedIds = [];
            // Lấy danh sách các giá trị đã chọn từ các dropdown
            $('select[name="batch_code[]"]').each(function() {
                const val = $(this).val();
                if (val) selectedIds.push(parseInt(val));
            });

            const newRow = `
                <div class="row batchRow mt-2">
                    <div class="col-md-6 form-group">
                        <label for="batch_code" class="text-black">Mã Lô</label>
                        <select class="form-control select2" name="batch_code[]" required style="width: 100%">
                            <option value="">Chọn Mã Lô</option>
                            ${batchesOptions(selectedIds)} <!-- Tạo lại danh sách lô không trùng -->
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="date_sx" class="text-black">Ngày Sản Xuất</label>
                        <div class="datepicker-wrapper">
                            <input type="text" class="form-control date-sx-input datetimepicker datepicker"
                                name="date_sx[]" id="date_sx_${newId}" placeholder="dd/mm/yyyy" autocomplete="off"
                                onkeydown="return false;" required>
                            <i class="fa fa-calendar calendar-icon" style="cursor: pointer;"></i>
                        </div>
                    </div>
                    
                </div>
            `;
            // dong 688688
            // <div class="col-md-2 d-flex align-items-end form-group">
            //             <button type="button" class="btn btn-success addBatchRow">+</button>
            //             <button type="button" class="btn btn-danger removeBatchRow ms-2">-</button>
            //         </div>
            container.append(newRow);

            // Khởi tạo lại select2 và datetimepicker cho dòng mới
            $(`#date_sx_${newId}`).closest('.batchRow').find('.select2').select2({
                language: "vi",
                placeholder: "Chọn giá trị",
                allowClear: true,
                width: '100%'
            });
            initDateTimePicker(`#date_sx_${newId}`);
            refreshBatchDropdowns(); // Làm mới dropdown sau khi thêm dòng
        });

        // Remove batch row
        $(document).on('click', '.removeBatchRow', function() {
            const row = $(this).closest('.batchRow');
            const rowCount = $('#batch-container .batchRow').length;

            if (rowCount > 1) {
                Swal.fire({
                    title: 'Bạn có chắc muốn xóa dòng này?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Có, xóa đi!',
                    cancelButtonText: 'Hủy',
                }).then((result) => {
                    if (result.isConfirmed) {
                        row.remove();
                        refreshBatchDropdowns();
                        Swal.fire({
                            icon: 'success',
                            title: 'Đã xóa!',
                            text: 'Dòng Mã Lô đã được xóa.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Không thể xóa!',
                    text: 'Cần giữ lại ít nhất một dòng Mã Lô.',
                    confirmButtonText: 'OK'
                });
            }
        });

        // Làm mới dropdown mỗi khi chọn thay đổi
        $(document).on('change', 'select[name="batch_code[]"]', function() {
            const $this = $(this);
            setTimeout(() => {
                refreshBatchDropdowns($this);
            }, 300);
        });
    });
</script>

<style>
    .form-label {
        font-weight: bold;
    }

    .select2-container--default .select2-selection--single {
        height: 37px;
    }

    .read-only-locked {
        background-color: #e9ecef;
        pointer-events: none;
        /* không thể click */
        border-color: #ced4da;
        color: #495057;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection