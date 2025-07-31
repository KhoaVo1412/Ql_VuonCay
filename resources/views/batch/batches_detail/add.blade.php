@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0">
        </h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('batches.index') }}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm</li>
                </ol>
            </nav>
        </div>
    </div>
    <style>
        .select2-search__field {
            height: 26px;
        }

        .select2-container .select2-results__option {
            color: black;
        }

        .select2-container .select2-results__option[aria-disabled="true"] {
            color: #ccc;
            font-weight: normal;
        }
    </style>
    @if(session('error'))
    <div class="alert alert-danger">
        <ul>
            <li>{{ session('error') }}</li>
        </ul>
    </div>
    @endif
    <div class="row">
        <div class="col-xl-12">
            <form id="form-account" action="{{ route('save-batches') }}" method="POST">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex">
                        <h5> Kết Nối Thông Tin Nguyên Liệu</h5>
                        <div class="prism-toggle d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">Lưu</button>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- <div class="card-title" style="font-weight: bold;">Chọn Mã Lô Và Ngày Sản Xuất</div> --}}
                        <div id="batch-container">
                            <div class="row batchRow">
                                <div class="col-md-6 form-group">
                                    <label for="batch_code" class="text-black">Mã Lô</label>
                                    {{-- @dd(old('batch_code')); --}}
                                    <select class="form-control select2" name="batch_code[]" required
                                        style="width: 100%">
                                        <option value="">Chọn Mã Lô</option>
                                        @foreach ($batches as $batch)
                                        <option value="{{ $batch->id }}" @if (in_array($batch->id,
                                            $batchIdsWithIngredients)) disabled @endif
                                            @if (in_array($batch->id, old('batch_code', []))) selected @endif>
                                            {{ $batch->batch_code }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="date_sx" class="text-black">Ngày Sản Xuất</label>
                                    <div class="datepicker-wrapper">
                                        <input type="text" class="form-control date-sx-input datetimepicker datepicker"
                                            name="date_sx[]" id="date_sx_1" placeholder="dd/mm/yyyy" autocomplete="off"
                                            onkeydown="return false;" required value="{{ old('date_sx.0') }}">
                                        <i class="fa fa-calendar calendar-icon" style="cursor: pointer;"></i>
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-end form-group">
                                    <button type="button" class="btn btn-success addBatchRow">+</button>
                                    <button type="button" class="btn btn-danger removeBatchRow ms-2">-</button>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="padding-top:20px">
                            <div class="col-md-6 form-group">
                                <label for="batch_weight" class="text-black">Khối Lượng Lô Hàng (Tấn)</label>
                                <select class="form-control" name="batch_weight" required>
                                    <option value="">Chọn khối lượng lô hàng</option>
                                    <option value="2.4" {{ old('batch_weight')=='2.4' ? 'selected' : '' }}>2.4 Tấn
                                    </option>
                                    <option value="2.52" {{ old('batch_weight')=='2.52' ? 'selected' : '' }}>2.52
                                        Tấn
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="banh_weight" class="text-black">Khối Lượng Bành (Kg)</label>
                                <select class="form-control" name="banh_weight" required>
                                    <option value="">Chọn khối lượng bành</option>
                                    <option value="33.33" {{ old('banh_weight')=='33.33' ? 'selected' : '' }}>33.33
                                        Kg
                                    </option>
                                    <option value="35" {{ old('banh_weight')=='35' ? 'selected' : '' }}>35 Kg
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- <div class="row" style="padding-top:20px">
                            <div class="col-md-6 form-group">
                                <label for="batch_weight" class="text-black">Khối Lượng Lô Hàng (Tấn)</label>
                                <input class="form-control" type="number" min="0" step="any" name="batch_weight"
                                    placeholder="Nhập khối lượng lô" required value="{{ old('batch_weight') }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="banh_weight" class="text-black">Khối Lượng Bành (Tấn)</label>
                                <input class="form-control" type="number" min="0" step="any" name="banh_weight"
                                    placeholder="Nhập khối lượng bành" required value="{{ old('banh_weight') }}">
                            </div>
                        </div> --}}
                        <div class="table-responsive">
                            <table id="detailsTable" class="table">
                                <thead>
                                    <tr>
                                        <th>Loại Mủ</th>
                                        <th>Ngày Tiếp Nhận</th>
                                        <th>Nông Trường</th>
                                        <th>Số Xe</th>
                                        <th>Số Chuyến</th>
                                        <th>Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control typeOfPus-input"
                                                name="batches[0][ingredients][0][type_of_pus]"
                                                placeholder="Chọn Loại Mủ" required
                                                value="{{ old('batches.0.ingredients.0.type_of_pus') }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control received-date-input"
                                                name="batches[0][ingredients][0][received_date]"
                                                placeholder="Chọn Ngày Tiếp Nhận" required
                                                value="{{ old('batches.0.ingredients.0.received_date') }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control farm-input"
                                                name="batches[0][ingredients][0][farm]" placeholder="Chọn Nông Trường"
                                                required value="{{ old('batches.0.ingredients.0.farm') }}">
                                            <input type="hidden" class="farm-id-input"
                                                name="batches[0][ingredients][0][farm_ids]">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control vehicle-input"
                                                name="batches[0][ingredients][0][vehicle]" placeholder="Chọn Số Xe"
                                                required value="{{ old('batches.0.ingredients.0.vehicle') }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control trip-input"
                                                name="batches[0][ingredients][0][trip]" placeholder="Số Chuyến" required
                                                value="{{ old('batches.0.ingredients.0.trip') }}">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger deleteRow">Xóa</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <button type="button" id="addRow" class="btn btn-success mt-2">Thêm Dòng</button>
                        <script>
                            $(document).ready(function() {
                                    const ingredientsData = @json($ingredients);
                                    let warningShown = false;

                                    function setupAutocompleteObject(input, suggestions, callback) {
                                        input.autocomplete({
                                            source: suggestions,
                                            minLength: 0,
                                            select: function(event, ui) {
                                                input.val(ui.item.value);
                                                if (callback) callback(ui.item);
                                                return false;
                                            }
                                        }).autocomplete('search', '');
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

                                    function getProductionDate() {
                                        const dateSx = $('.date-sx-input').val();
                                        if (!dateSx) return null;

                                        const parts = dateSx.split('/');
                                        if (parts.length === 3) {
                                            return new Date(`${parts[2]}-${parts[1]}-${parts[0]}`);
                                        }
                                        return null;
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

                                    function filterIngredientsByDate(productionDate) {
                                        if (!productionDate) return [];
                                        return ingredientsData.filter(ingredient => {
                                            const receivedDate = new Date(ingredient.received_date);
                                            return receivedDate <= productionDate;
                                        });
                                    }

                                    // New function to collect selected ingredients from all rows
                                    function getSelectedIngredients(currentRow) {
                                        const selectedIngredients = [];
                                        $('#detailsTable tbody tr').each(function(index, row) {
                                            if ($(row).is(currentRow)) return; // Skip the current row

                                            const typeOfPus = $(row).find('.typeOfPus-input').val();
                                            const receivedDate = $(row).find('.received-date-input').val();
                                            const farm = $(row).find('.farm-input').val();
                                            const vehicle = $(row).find('.vehicle-input').val();
                                            const trip = $(row).find('.trip-input').val();

                                            // Only add if all fields are filled
                                            if (typeOfPus && receivedDate && farm && vehicle && trip) {
                                                selectedIngredients.push({
                                                    typeOfPus,
                                                    receivedDate,
                                                    farm,
                                                    vehicle,
                                                    trip,
                                                    rowIndex: index // For reference in the warning message
                                                });
                                            }
                                        });
                                        return selectedIngredients;
                                    }

                                    // New function to check for duplicate ingredients
                                    function checkForDuplicateIngredient(currentRow) {
                                        const typeOfPus = currentRow.find('.typeOfPus-input').val();
                                        const receivedDate = currentRow.find('.received-date-input').val();
                                        const farm = currentRow.find('.farm-input').val();
                                        const vehicle = currentRow.find('.vehicle-input').val();
                                        const trip = currentRow.find('.trip-input').val();
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
                                                // Clear the row's inputs
                                                currentRow.find('.received-date-input').val('');
                                                currentRow.find('.farm-input').val('');
                                                currentRow.find('.vehicle-input').val('');
                                                currentRow.find('.trip-input').val('');
                                                currentRow.find('.received-date-input').focus();
                                            });
                                            return true;
                                        }
                                        return false;
                                    }

                                    // Khi người dùng tập trung vào ô loại mủ
$(document).on('focus', '.typeOfPus-input', function() {
    const input = $(this);
    const productionDate = getProductionDate();
    let warningShown = false; // Local scope for warning flag

    if (!productionDate && !warningShown) {
        Swal.fire({
            icon: 'warning',
            title: 'Vui lòng chọn Ngày Sản Xuất trước!',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                input.val('');
                $('.date-sx-input').focus();
                warningShown = true;
            }
        });
        return;
    }

    const filteredIngredients = filterIngredientsByDate(productionDate);
    const suggestions = filteredIngredients
        .map(ingredient => ingredient.type_of_pus?.name_pus)
        .filter((v, i, a) => a.indexOf(v) === i); // Ensure uniqueness

    setupAutocomplete(input, suggestions, function(selectedTypeOfPus) {
        const row = input.closest('tr');
        row.find('.received-date-input').val(''); // Clear date
        row.find('.farm-input').val(''); // Clear farm
        row.find('.vehicle-input').val(''); // Clear vehicle
        row.find('.trip-input').val(''); // Clear trip
        row.find('.unit-input').val(''); // Clear unit
        row.find('.farm-id-input').val(''); // Clear farm ID

        // Hiển thị gợi ý ngày tiếp nhận sau khi chọn loại mủ
        const suggestionsDate = filteredIngredients
            .filter(ingredient => ingredient.type_of_pus?.name_pus === selectedTypeOfPus)
            .map(ingredient => formatDateToDDMMYYYY(ingredient.received_date))
            .filter((v, i, a) => a.indexOf(v) === i); // Ensure uniqueness

        setupAutocomplete(row.find('.received-date-input'), suggestionsDate);
    });
});

// Khi người dùng tập trung vào ô ngày tiếp nhận
$(document).on('focus', '.received-date-input', function() {
    const input = $(this);
    const row = input.closest('tr');
    const selectedTypeOfPus = row.find('.typeOfPus-input').val();
    const productionDate = getProductionDate();

    if (!productionDate || !selectedTypeOfPus) {
        input.val('');
        return;
    }

    const filteredIngredients = filterIngredientsByDate(productionDate);
    const suggestions = filteredIngredients
        .filter(ingredient => ingredient.type_of_pus?.name_pus === selectedTypeOfPus)
        .map(ingredient => formatDateToDDMMYYYY(ingredient.received_date))
        .filter((v, i, a) => a.indexOf(v) === i); // Ensure uniqueness

    setupAutocomplete(input, suggestions, function(selectedDate) {
        const selectedDateFormatted = formatDateToDDMMYYYY(selectedDate);
        const productionDateFormatted = formatDateToDDMMYYYY(productionDate);

        if (selectedDateFormatted > productionDateFormatted) {
            Swal.fire({
                icon: 'warning',
                title: 'Ngày Tiếp Nhận phải nhỏ hơn hoặc bằng Ngày Sản Xuất!',
                confirmButtonText: 'OK'
            }).then(() => {
                input.val('');
                row.find('.farm-input').val('');
                row.find('.unit-input').val('');
                row.find('.farm-id-input').val('');
                row.find('.vehicle-input').val('');
                row.find('.trip-input').val('');
                row.find('.received-date-input').focus();
            });
            return;
        }

        // Clear subsequent fields
        row.find('.farm-input').val('');
        row.find('.unit-input').val('');
        row.find('.farm-id-input').val('');
        row.find('.vehicle-input').val('');
        row.find('.trip-input').val('');

        // Hiển thị gợi ý nông trường ngay sau khi chọn ngày tiếp nhận
        updateFarmSuggestions(row, null, null, selectedTypeOfPus, selectedDate);
    });
});

// Khi người dùng tập trung vào ô nông trường
$(document).on('focus', '.farm-input', function() {
    const input = $(this);
    const row = input.closest('tr');
    const selectedTypeOfPus = row.find('.typeOfPus-input').val();
    const selectedDate = row.find('.received-date-input').val();
    const productionDate = getProductionDate();

    if (!productionDate || !selectedTypeOfPus || !selectedDate) {
        input.val('');
        return;
    }

    const selectedDateFormatted = formatDateToDDMMYYYY(selectedDate);
    const productionDateFormatted = formatDateToDDMMYYYY(productionDate);

    if (selectedDateFormatted > productionDateFormatted) {
        Swal.fire({
            icon: 'warning',
            title: 'Ngày Tiếp Nhận phải nhỏ hơn hoặc bằng Ngày Sản Xuất!',
            confirmButtonText: 'OK'
        }).then(() => {
            input.val('');
            row.find('.received-date-input').val('');
            row.find('.unit-input').val('');
            row.find('.farm-id-input').val('');
            row.find('.vehicle-input').val('');
            row.find('.trip-input').val('');
            row.find('.received-date-input').focus();
        });
        return;
    }

    // Hiển thị gợi ý nông trường
    updateFarmSuggestions(row, null, null, selectedTypeOfPus, selectedDate);
});

// Khi người dùng tập trung vào ô số xe
$(document).on('focus', '.vehicle-input', function() {
    const input = $(this);
    const row = input.closest('tr');
    const selectedTypeOfPus = row.find('.typeOfPus-input').val();
    const selectedDate = row.find('.received-date-input').val();
    const selectedFarmName = row.find('.farm-input').val();
    const selectedUnitName = row.find('.unit-input').val();
    const productionDate = getProductionDate();

    if (!productionDate || !selectedTypeOfPus || !selectedDate || !selectedFarmName || !selectedUnitName) {
        input.val('');
        return;
    }

    const selectedDateFormatted = formatDateToDDMMYYYY(selectedDate);
    const productionDateFormatted = formatDateToDDMMYYYY(productionDate);

    if (selectedDateFormatted > productionDateFormatted) {
        Swal.fire({
            icon: 'warning',
            title: 'Ngày Tiếp Nhận phải nhỏ hơn hoặc bằng Ngày Sản Xuất!',
            confirmButtonText: 'OK'
        }).then(() => {
            input.val('');
            row.find('.received-date-input').val('');
            row.find('.farm-input').val('');
            row.find('.unit-input').val('');
            row.find('.farm-id-input').val('');
            row.find('.vehicle-input').val('');
            row.find('.trip-input').val('');
            row.find('.received-date-input').focus();
        });
        return;
    }

    // Hiển thị gợi ý số xe
    updateVehicleList(row, selectedFarmName, selectedUnitName, selectedTypeOfPus, selectedDate);
});

// Hiển thị gợi ý nông trường
function updateFarmSuggestions(row, selectedFarm, selectedUnitName, selectedTypeOfPus, selectedDate) {
    const productionDate = getProductionDate();
    if (!productionDate) return;

    const filteredIngredients = filterIngredientsByDate(productionDate);
    const suggestions = filteredIngredients
        .filter(ingredient => 
            ingredient.type_of_pus?.name_pus === selectedTypeOfPus &&
            formatDateToDDMMYYYY(ingredient.received_date) === selectedDate)
        .map(ingredient => ({
            label: `${ingredient.farm?.farm_name || ''} - ${ingredient.farm?.unit_relation?.unit_name || ''}`,
            farm_id: ingredient.farm?.id || null,
            unit_name: ingredient.farm?.unit_relation?.unit_name || ''
        }))
        .filter((v, i, a) => a.findIndex(t => t.label === v.label) === i); // Ensure uniqueness

    setupAutocompleteObject(row.find('.farm-input'), suggestions, function(selectedFarm) {
        const label = selectedFarm.label;
        const farmId = selectedFarm.farm_id;
        const selectedUnitName = selectedFarm.unit_name;

        const selectedFarmName = label.split(' - ')[0];

        row.find('.farm-input').val(selectedFarmName);
        row.find('.unit-input').val(selectedUnitName);
        row.find('.farm-id-input').val(farmId);
        row.find('.vehicle-input').val('');
        row.find('.trip-input').val('');

        // Cập nhật danh sách xe sau khi chọn nông trường
        updateVehicleList(row, selectedFarmName, selectedUnitName, selectedTypeOfPus, selectedDate);
    });
}

// Cập nhật danh sách xe
function updateVehicleList(row, selectedFarmName, selectedUnitName, selectedTypeOfPus, selectedDate) {
    const productionDate = getProductionDate();
    if (!productionDate) return;

    const filteredIngredients = filterIngredientsByDate(productionDate);
    const suggestions = filteredIngredients
        .filter(ingredient =>
            ingredient.farm?.farm_name === selectedFarmName &&
            ingredient.farm?.unit_relation?.unit_name === selectedUnitName &&
            ingredient.type_of_pus?.name_pus === selectedTypeOfPus &&
            formatDateToDDMMYYYY(ingredient.received_date) === selectedDate)
        .map(ingredient => ingredient.vehicle?.vehicle_number)
        .filter((v, i, a) => a.indexOf(v) === i && v); // Ensure uniqueness and non-empty

    setupAutocomplete(row.find('.vehicle-input'), suggestions);
}
                                    $(document).on('focus', '.trip-input', function() {
                                        const input = $(this);
                                        const row = input.closest('tr');
                                        const selectedTypeOfPus = row.find('.typeOfPus-input').val();
                                        const selectedDate = row.find('.received-date-input').val();
                                        const selectedFarm = row.find('.farm-input').val();
                                        const selectedVehicle = row.find('.vehicle-input').val();
                                        const productionDate = getProductionDate();
                                        if (!productionDate) return;
                                        const filteredIngredients = filterIngredientsByDate(productionDate);
                                        const suggestions = filteredIngredients
                                            .filter(ingredient => ingredient.type_of_pus?.name_pus === selectedTypeOfPus &&
                                                formatDateToDDMMYYYY(ingredient.received_date) === selectedDate && ingredient.farm
                                                ?.farm_name === selectedFarm &&
                                                ingredient.vehicle?.vehicle_number === selectedVehicle)
                                            .map(ingredient => ingredient.trip)
                                            .filter((v, i, a) => a.indexOf(v) === i);

                                        setupAutocomplete(input, suggestions, function() {
                                            // Check for duplicates after selecting the trip
                                            checkForDuplicateIngredient(row);
                                        });
                                    });
                                    $('#addRow').on('click', function() {
                                        const firstRow = $('#detailsTable tbody tr:first');
                                        const isFirstRowComplete = firstRow.find('.typeOfPus-input').val() &&
                                            firstRow.find('.received-date-input').val() &&
                                            firstRow.find('.farm-input').val() &&
                                            firstRow.find('.vehicle-input').val() &&
                                            firstRow.find('.trip-input').val();
                                        if (!isFirstRowComplete) {
                                            Swal.fire({
                                                icon: 'warning',
                                                title: 'Vui lòng điền đầy đủ thông tin trong dòng đầu tiên!',
                                                confirmButtonText: 'OK'
                                            });
                                            return;
                                        }
                                        const productionDate = getProductionDate();
                                        if (!productionDate) {
                                            Swal.fire({
                                                icon: 'warning',
                                                title: 'Vui lòng chọn Ngày Sản Xuất trước!',
                                                confirmButtonText: 'OK'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $('.date-sx-input').focus();
                                                }
                                            });
                                            return;
                                        }
                                        const batchIndex = 0;
                                        const ingredientIndex = $('#detailsTable tbody tr').length;
                                        let firstTypeOfPus = $('#detailsTable tbody tr:first .typeOfPus-input').val() || '';
                                        const newRow = `
                                        <tr>
                                            <td><input type="text" class="form-control typeOfPus-input read-only-locked" name="batches[${batchIndex}][ingredients][${ingredientIndex}][type_of_pus]" value="${firstTypeOfPus}" placeholder="Chọn Loại Mủ" required></td>
                                            <td><input type="text" class="form-control received-date-input" name="batches[${batchIndex}][ingredients][${ingredientIndex}][received_date]" placeholder="Chọn Ngày Tiếp Nhận" required></td>
                                            <td><input type="text" class="form-control farm-input" name="batches[${batchIndex}][ingredients][${ingredientIndex}][farm]" placeholder="Chọn Nông Trường" required>
                                                 <input type="hidden" class="farm-id-input"
                                                    name="batches[${batchIndex}][ingredients][${ingredientIndex}][farm_ids]"></td>
                                            <td><input type="text" class="form-control vehicle-input" name="batches[${batchIndex}][ingredients][${ingredientIndex}][vehicle]" placeholder="Chọn Số Xe" required></td>
                                            <td><input type="text" class="form-control trip-input" name="batches[${batchIndex}][ingredients][${ingredientIndex}][trip]" placeholder="Số Chuyến" required></td>
                                            <td><button type="button" class="btn btn-danger deleteRow">Xóa</button></td>
                                        </tr>
                                    `;
                                        $('#detailsTable tbody').append(newRow);
                                    });

                                    $(document).on('click', '.deleteRow', function() {
                                        var row = $(this).closest('tr');
                                        var rowIndex = row.index();
                                        var rowCount = $('#detailsTable tbody tr').length;
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
                                                text: "Hành động này không thể hoàn tác!",
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonText: 'Xác nhận',
                                                cancelButtonText: 'Hủy',
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    row.remove();
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Đã xóa!',
                                                        text: 'Dòng đã được xóa thành công.',
                                                        timer: 1500,
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

                                    $('.date-sx-input').on('change', function() {
                                        warningShown = false;
                                        $('#detailsTable tbody tr').each(function() {
                                            $(this).find('.typeOfPus-input').val('');
                                            $(this).find('.received-date-input').val('');
                                            $(this).find('.farm-input').val('');
                                            $(this).find('.vehicle-input').val('');
                                            $(this).find('.trip-input').val('');
                                        });
                                    });
                                });
                        </script>
                    </div>
                </div>
            </form>
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
            $('#batch_code').select2({
                language: "vi",
                placeholder: "Chọn Lô",
                allowClear: true,
                minimumResultsForSearch: 0,
                width: '100%',
            });
        });
</script>
<script>
    $(document).ready(function() {
            function refreshBatchDropdowns(currentSelect = null) {
                const selectedValues = [];

                // Lưu danh sách đã chọn
                $('.batchRow select[name="batch_code[]"]').each(function() {
                    const val = $(this).val();
                    if (val) selectedValues.push(parseInt(val));
                });

                // Duyệt tất cả dropdowns
                $('.batchRow select[name="batch_code[]"]').each(function() {
                    const $dropdown = $(this);
                    const val = $dropdown.val();
                    const currentVal = val ? parseInt(val) : null;

                    // Loại các mã đã chọn, nhưng giữ lại giá trị hiện tại của dropdown này
                    const filteredSelected = selectedValues.filter(id => id !== currentVal);

                    const optionsHtml = batchesOptions(filteredSelected);

                    $dropdown.html('<option value="">Chọn Mã Lô</option>' + optionsHtml);
                    $dropdown.val(currentVal).trigger('change.select2');
                });
            }

            $('.select2').select2({
                language: "vi",
                placeholder: "Chọn giá trị",
                allowClear: true,
                width: '100%'
            });
            $(document).on('click', '.addBatchRow', function() {
                const container = $('#batch-container');

                let existingIds = container.find('.datetimepicker').map(function() {
                    return parseInt($(this).attr('id').split('_')[2]); // Lấy số từ ID
                }).get();

                let newId = 1;
                while (existingIds.includes(newId)) {
                    newId++;
                }

                const selectedIds = [];
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
                           ${batchesOptions(selectedIds)}
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
                    <div class="col-md-2 d-flex align-items-end form-group">
                        <button type="button" class="btn btn-success addBatchRow">+</button>
                        <button type="button" class="btn btn-danger removeBatchRow ms-2">-</button>
                    </div>
                </div>
            `;

                container.append(newRow);
                $('.select2').select2();
                initDateTimePicker(`#date_sx_${newId}`);
                refreshBatchDropdowns();

                $(document).on('change', 'select[name="batch_code[]"]', function() {
                    const $this = $(this);
                    setTimeout(() => {
                        refreshBatchDropdowns($this);
                    }, 300); // Cho select2 đóng xong rồi mới cập nhật
                });
            });
            $(document).on('click', '.removeBatchRow', function() {
                const row = $(this).closest('.batchRow');
                const rowCount = $('#batch-container .batchRow').length;

                if (rowCount > 1) {
                    Swal.fire({
                        title: 'Bạn có chắc muốn xóa dòng này?',
                        // text: "Dữ liệu sẽ bị mất nếu chưa lưu.",
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

            // function batchesOptions() {
            //     const batches = @json($batches);
            //     const batchIdsWithIngredients = @json($batchIdsWithIngredients);
            //     return batches.map(batch => {
            //         const disabled = batchIdsWithIngredients.includes(batch.id) ? 'disabled' : '';
            //         return `<option value="${batch.id}" ${disabled}>${batch.batch_code}</option>`;
            //     }).join('');
            // }

            function batchesOptions(selectedIds = []) {
                const batches = @json($batches);
                const batchIdsWithIngredients = @json($batchIdsWithIngredients);

                return batches
                    .filter(batch => {
                        return !batchIdsWithIngredients.includes(batch.id) && !selectedIds.includes(batch.id);
                    })
                    .map(batch => {
                        return `<option value="${batch.id}">${batch.batch_code}</option>`;
                    })
                    .join('');
            }
        });
</script>
<style>
    .form-label {
        font-weight: bold;
    }

    .select2-container--default .select2-selection--single {
        height: 37px;
    }

    .table input {
        width: 100%;
        min-width: 150px;
        height: 40px;
        font-size: 16px;
    }

    .read-only-locked {
        background-color: #e9ecef;
        pointer-events: none;
        /* không thể click */
        border-color: #ced4da;
        color: #495057;
    }
</style>
<script>
    const ingredientsData = @json($ingredients);
        console.log(ingredientsData);
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection