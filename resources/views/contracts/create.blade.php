@extends('layouts.app')

@section('content')
<div class="page-title my-3">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0"></h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb padding">
                    <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="/cont">Hợp Đồng</a></li>
                    <li class="breadcrumb-item">Thêm Hợp Đồng</li>
                </ol>
            </nav>
        </div>
    </div>
    {{-- <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h4>Thêm Hợp Đồng</h4>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="/cont">Hợp Đồng</a></li>
                    <li class="breadcrumb-item">Thêm Hợp Đồng</li>
                </ol>
            </nav>
        </div>
    </div> --}}
    <style>
        .custom-checkbox .form-check-input {
            width: 20px;
            height: 20px;
            border-radius: 5px;
            border: 2px solid #ccc;
            position: relative;
            transition: all 0.3s ease;
        }

        .custom-checkbox .form-check-input:checked {
            background-color: #007bff;
            border-color: #007bff;
            background-image: url('data:image/svg+xml,%3csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"%3e%3cpath fill="white" d="M12.293 3.293a1 1 0 0 0-1.414 0L6 8.586 4.121 6.707a1 1 0 1 0-1.415 1.414l2.828 2.828a1 1 0 0 0 1.415 0l6-6a1 1 0 0 0 0-1.414z"/%3e%3c/svg%3e');
            background-size: 12px;
            background-position: center;
            background-repeat: no-repeat;
        }

        .custom-checkbox .form-check-input:hover {
            border-color: #007bff;
        }

        .custom-checkbox .form-check-label {
            padding-left: 10px;
            font-size: 16px;
            color: #333;
            font-weight: 600;
            cursor: pointer;
        }

        .custom-checkbox {
            margin-bottom: 15px;
        }

        .custom-checkbox .col-4 {
            display: flex;
            align-items: center;
        }

        .form-group.col-lg-12 {
            margin-top: 20px;
        }
    </style>
    @include('layouts.alert')
</div>

<div class="card">
    <div class="card-body">
        <h5 style="margin-bottom: 10px;">Thêm Hợp Đồng</h5>
        <form method="POST" action="{{ route('contstore') }}" class="row">
            @csrf
            <div class="form-group col-lg-4">
                <label for="contract_code" class="text-black">Mã Hợp Đồng</label>
                <input type="text" class="form-control" id="contract_code" name="contract_code"
                    value="{{ old('contract_code') }}" placeholder="Mã hợp đồng" required>
            </div>
            <div class="form-group col-lg-4">
                <label for="contract_type_id" class="text-black">Loại Hợp Đồng</label>
                <select name="contract_type_id" id="contract_type_id" class="form-select" required>
                    <option value="">Chọn Loại Hợp Đồng</option>
                    @foreach ($contractTypes as $contractType)
                    <option value="{{ $contractType->id }}" {{ old('contract_type_id')==$contractType->id ? 'selected' :
                        '' }}>
                        {{ $contractType->contract_type_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            {{-- <div class="form-group col-lg-12">
                <label class="text-black">Loại Hợp Đồng</label>
                <div class="row">
                    @foreach ($contractTypes as $contractType)
                    <div class="col-4">
                        <div class="form-check custom-checkbox">
                            <input type="checkbox" name="contract_type_ids[]" id="contract_type_{{ $contractType->id }}"
                                value="{{ $contractType->id }}" class="form-check-input" {{ in_array($contractType->id,
                            old('contract_type_ids', [])) ? 'checked' : '' }}>
                            <label class="form-check-label fw-normal text-black"
                                for="contract_type_{{ $contractType->id }}">
                                {{ $contractType->contract_type_name }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div> --}}
            <div class="form-group col-lg-4">
                <label for="customer_id" class="text-black">Khách Khàng</label>
                <select name="customer_id" id="customer_id" class="form-select" required>
                    <option value="">Chọn Khách Hàng</option>
                    @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" {{ old('customer_id')==$customer->id ? 'selected' : '' }}>
                        {{ $customer->company_name }}
                    </option>
                    @endforeach
                </select>
            </div>



            <div class="form-group col-lg-4">
                <label for="original_contract_number" class="text-black">Hợp Đồng Gốc Số</label>
                <input type="text" class="form-control" id="original_contract_number" name="original_contract_number"
                    min="0" value="{{ old('original_contract_number') }}" placeholder="Hợp đồng gốc số" required>
            </div>

            <div class="form-group col-lg-4">
                <label for="contract_duration_days" class="text-black">Số Ngày Hợp Đồng</label>
                <input type="number" class="form-control" id="contract_days" name="contract_days" min="0"
                    value="{{ old('contract_days') }}" placeholder="Số ngày hợp đồng" required>
            </div>
            <div class="form-group col-lg-4">
                <label for="delivery_month" class="text-black">Tháng Giao Hàng</label>
                {{-- <input type="month" class="form-control" id="delivery_month" name="delivery_month"
                    value="{{ old('delivery_month') }}" required> --}}
                <div class="datepicker-wrapper">
                    <input type="text" class="form-control datepicker" id="delivery_month" name="delivery_month"
                        placeholder="mm/yyyy" value="{{ old('delivery_month') }}" autocomplete="off" required
                        onkeydown="return false;">
                    <i class="fa fa-calendar calendar-icon"></i>
                </div>
            </div>
            <div class="form-group col-lg-4">
                <label for="quantity" class="text-black">Khối Lượng (Tấn)</label>
                <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Khối lượng" min="0"
                    step="any" value="{{ old('quantity') }}" required>
            </div>
            {{-- <div class="form-group col-lg-4">
                <label for="export_order_id">Lệnh Xuất Hàng</label>
                <input type="text" class="form-control" id="export_order_id" name="order_export_id"
                    placeholder="Lệnh xuất hàng" required>
            </div> --}}
            <div class="form-group col-lg-4">
                <label for="product_type_name" class="text-black">Tên Chủng Loại Sản Phẩm</label>
                <input type="text" class="form-control" id="product_type_name" name="product_type_name"
                    value="{{ old('product_type_name') }}" placeholder="Tên chủng loại sản phẩm" required>
            </div>

            <div class="form-group col-lg-4">
                <label for="container_closed_date" class="text-black">Ngày Đóng Cont</label>
                {{-- <input type="date" class="form-control" id="container_closing_date" name="container_closing_date"
                    value="{{ old('container_closing_date') }}" required> --}}
                <div class="datepicker-wrapper">
                    <input type="text" class="form-control datetimepicker datepicker" id="container_closing_date"
                        name="container_closing_date" placeholder="dd/mm/yyyy" autocomplete="off"
                        value="{{ old('container_closing_date') }}" required onkeydown="return false;">
                    <i class="fa fa-calendar calendar-icon" style="cursor: pointer;"></i>
                </div>
            </div>

            <div class="form-group col-lg-4">
                <label for="delivery_date" class="text-black">Ngày Giao Hàng</label>
                {{-- <input type="date" class="form-control" id="delivery_date" name="delivery_date"
                    value="{{ old('delivery_date') }}" required> --}}
                <div class="datepicker-wrapper">
                    <input type="text" class="form-control datetimepicker datepicker" id="delivery_date"
                        name="delivery_date" placeholder="dd/mm/yyyy" value="{{ old('delivery_date') }}"
                        autocomplete="off" required onkeydown="return false;">
                    <i class="fa fa-calendar calendar-icon" style="cursor: pointer;"></i>
                    <!-- Icon bấm mở DatePicker -->
                </div>
            </div>

            {{-- <div class="form-group col-lg-4">
                <label for="packaging_type" class="text-black">Dạng Đóng Gói</label>
                <input type="text" class="form-control" id="packaging_type" name="packaging_type"
                    value="{{ old('packaging_type') }}" placeholder="Dạng đóng gói" required>
            </div> --}}

            <div class="form-group col-lg-4">
                <label for="packaging_type" class="text-black">Dạng Đóng Gói</label>
                <select class="form-select" id="packaging_type" name="packaging_type" required>
                    <option value="">-- Chọn Dạng Đóng Gói --</option>
                    <option value="Hàng rời" {{ old('packaging_type')=='Hàng Rời' ? 'selected' : '' }}>Hàng rời
                    </option>
                    <option value="Hàng pallet nhựa - đế nhựa" {{ old('packaging_type')=='Hàng Pallet Nhựa - Đế Nhựa'
                        ? 'selected' : '' }}>Hàng pallet nhựa
                        - đế nhựa</option>
                    <option value="Pallet nhựa đế gỗ" {{ old('packaging_type')=='Pallet Nhựa Đế Gỗ' ? 'selected' : ''
                        }}>Pallet nhựa đế gỗ</option>
                    <option value="Pallet nhựa - đế sắt" {{ old('packaging_type')=='Pallet Nhựa - Đế Sắt' ? 'selected'
                        : '' }}>Pallet nhựa - đế sắt
                    </option>
                    <option value="Pallet gỗ" {{ old('packaging_type')=='Pallet Gỗ' ? 'selected' : '' }}>Pallet gỗ
                    </option>
                </select>
            </div>

            <div class="form-group col-lg-4">
                <label for="market" class="text-black">Thị Trường</label>
                <input type="text" class="form-control" id="market" name="market" placeholder="Thị trường"
                    value="{{ old('market') }}" required>
            </div>

            <div class="form-group col-lg-4">
                <label for="production_trade_unit" class="text-black">Đơn Vị Sản Xuất Thương Mại</label>
                <input type="text" class="form-control" id="production_or_trade_unit"
                    value="{{ old('production_or_trade_unit') }}" name="production_or_trade_unit"
                    placeholder="Đơn vị sản xuất thương mại" required>
            </div>

            <div class="form-group col-lg-4">
                <label for="third_party_sale" class="text-black">Bán Cho Bên Thứ 3</label>
                <input type="text" class="form-control" id="third_party_sale" name="third_party_sale"
                    value="{{ old('third_party_sale') }}" placeholder="Bán cho bên thứ 3" required>
            </div>
            <div class="orders row">
                <hr style="color: green; width: 100%; border: 2px solid;">
                <div id="order-container">
                    <!-- Initial order row -->
                    <div class="col-12 order-wrap align-items-center mb-2">
                        <div class="col-md-5">
                            <label class="fw-bold">Lệnh xuất hàng <span class="text-danger">lần 1</span></label>
                            <input type="text" name="orders[0][code]" class="form-control"
                                placeholder="Nhập lệnh xuất hàng" required>
                        </div>
                        <div class="col-md-5">
                            <label class="fw-bold">Mã lô hàng <span class="text-danger">lần 1</span></label>
                            <select name="orders[0][batches][]" class="form-select batch-dropdown select2" multiple>
                                @foreach ($batches as $batch)
                                <option value="{{ $batch->id }}">{{ $batch->batch_code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2" style="padding-top: 23px">
                            <button type="button" class="btn btn-primary add-order"><i class="fa fa-plus"></i> Thêm
                                lệnh</button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                        // Khởi tạo Select2
                        $(".select2").select2({
                            language: "vi",
                            placeholder: "Chọn mã lô hàng",
                            allowClear: true
                        });

                        const orderContainer = document.getElementById("order-container");
                        const addOrderButton = document.querySelector(".add-order");

                        let orderIndex = 1;
                        let soLan = 2;

                        // Thêm dòng mới
                        addOrderButton.addEventListener("click", function () {
                            const newOrder = document.createElement("div");
                            newOrder.classList.add("col-12", "order-wrap", "align-items-center", "mb-2");
                            const batches = document.querySelectorAll(".batch-dropdown option");
                            let availableBatches = '';

                            batches.forEach(batch => {
                                availableBatches += `<option value="${batch.value}">${batch.text}</option>`;
                            });

                            newOrder.innerHTML = `
                    <div class="col-md-5">
                        <label class="fw-bold">Lệnh Xuất Hàng<span class="text-danger"> lần ${soLan}</span></label>
                        <input type="text" name="orders[${orderIndex}][code]" class="form-control" placeholder="Nhập lệnh xuất hàng" required>
                    </div>
                    <div class="col-md-5">
                        <label class="fw-bold">Mã Lô Hàng<span class="text-danger"> lần ${soLan}</span></label>
                        <select name="orders[${orderIndex}][batches][]" class="form-select select2" multiple>
                            ${availableBatches}
                        </select>
                    </div>
                    <div class="col-md-2" style="padding-top: 23px">
                        <button type="button" class="btn btn-danger remove-order">×</button>
                    </div>`;

                            orderContainer.appendChild(newOrder);
                            orderIndex++;
                            soLan++;

                            $(".select2").select2({
                                placeholder: "Chọn mã lô hàng",
                                allowClear: true
                            });
                            hideOptions();
                        });

                        // Kiểm tra trùng mã lệnh khi nhập
                        $(document).on('input', 'input[name^="orders"][name$="[code]"]', function () {
                            const currentInput = $(this);
                            const orderCode = currentInput.val().trim();
                            let allCodes = [];

                            // Thu thập tất cả mã lệnh
                            $('input[name^="orders"][name$="[code]"]').each(function () {
                                const code = $(this).val().trim();
                                if (code) {
                                    allCodes.push(code);
                                }
                            });

                            // Kiểm tra trùng
                            const codeCount = allCodes.filter(code => code === orderCode).length;
                            if (orderCode && codeCount > 1) {
                                currentInput.addClass('is-invalid');
                                currentInput.next('.invalid-feedback').remove();
                                currentInput.after('<div class="invalid-feedback">Mã lệnh xuất hàng này đã tồn tại!</div>');
                                Swal.fire({
                                    title: 'Cảnh báo!',
                                    text: `Mã lệnh xuất hàng "${orderCode}" đã trùng. Vui lòng nhập mã khác!`,
                                    icon: 'warning',
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                currentInput.removeClass('is-invalid');
                                currentInput.next('.invalid-feedback').remove();
                            }
                        });

                        // Xử lý xóa dòng
                        orderContainer.addEventListener("click", function (e) {
                            if (e.target.classList.contains("remove-order")) {
                                e.target.closest(".order-wrap").remove();
                                updateOrderIndexes();
                                hideOptions();
                            }
                        });

                        // Cập nhật lại số thứ tự của các dòng
                        function updateOrderIndexes() {
                            const allOrders = document.querySelectorAll('.order-wrap');
                            soLan = 1;

                            allOrders.forEach((order, index) => {
                                const label = order.querySelectorAll('label');
                                const input = order.querySelector('input[name^="orders"]');
                                const select = order.querySelector('select[name^="orders"]');

                                label[0].innerHTML = `Lệnh Xuất Hàng<span class="text-danger"> lần ${index + 1}</span>`;
                                label[1].innerHTML = `Mã Lô Hàng<span class="text-danger"> lần ${index + 1}</span>`;
                                input.name = `orders[${index}][code]`;
                                select.name = `orders[${index}][batches][]`;

                                soLan = index + 2;
                            });
                        }

                        // Hàm ẩn các option đã được chọn trong tất cả các dropdown
                        function hideOptions() {
                            let selectedValues = [];

                            $(".select2").each(function () {
                                const val = $(this).val();
                                if (val) {
                                    selectedValues = selectedValues.concat(val);
                                }
                            });

                            $(".select2").each(function () {
                                const $thisSelect = $(this);
                                const currentValues = $thisSelect.val() || [];

                                $thisSelect.find('option').each(function () {
                                    const value = $(this).val();
                                    if (selectedValues.includes(value) && !currentValues.includes(value)) {
                                        $(this).prop('disabled', true);
                                    } else {
                                        $(this).prop('disabled', false);
                                    }
                                });

                                $thisSelect.trigger("change.select2");
                            });
                        }

                        // Xử lý khi thay đổi lựa chọn trong dropdown
                        $(document).on("change", ".select2", function () {
                            hideOptions();
                        });

                        // Cập nhật lại dropdown khi mở
                        $(document).on('select2:open', '.select2', function () {
                            hideOptions();
                        });
                    });
            </script>


            {{--
            <script>
                $(document).ready(function () {
                        $(".select2").select2({
                            placeholder: "Chọn mã lô hàng",
                            allowClear: true
                        });

                        // Gắn sự kiện khi người dùng mở dropdown
                        $(document).on("select2:opening", ".select2", function () {
                            const $thisSelect = $(this);

                            // Lấy danh sách tất cả batch đã được chọn ở các dropdown khác
                            const selectedValues = [];
                            $(".select2").not($thisSelect).each(function () {
                                const vals = $(this).val(); // trả về array
                                if (vals) {
                                    selectedValues.push(...vals);
                                }
                            });

                            // Lấy tất cả option ban đầu từ 1 select template (batch-dropdown)
                            const allOptions = $(".batch-dropdown option");

                            // Xóa toàn bộ option hiện tại
                            $thisSelect.empty();

                            // Thêm lại những option chưa được chọn ở các dropdown khác
                            allOptions.each(function () {
                                const val = $(this).val();
                                const text = $(this).text();
                                const isSelected = selectedValues.includes(val);
                                if (!isSelected) {
                                    const option = new Option(text, val, false, false);
                                    $thisSelect.append(option);
                                }
                            });

                            // Trigger update lại UI
                            $thisSelect.trigger("change");
                        });
                    });
            </script> --}}

            {{--
            <script>
                document.addEventListener("DOMContentLoaded", function () {

                        $(".select2").select2({
                            placeholder: "Chọn mã lô hàng",
                            allowClear: true
                        });

                        const orderContainer = document.getElementById("order-container");
                        const addOrderButton = document.querySelector(".add-order");

                        let orderIndex = 1;

                        addOrderButton.addEventListener("click", function () {
                            const newOrder = document.createElement("div");
                            newOrder.classList.add("col-12", "order-wrap", "align-items-center", "mb-2");

                            // Không cần gắn option ở đây nữa
                            newOrder.innerHTML = `
                        <div class="col-md-5">
                            <label class="fw-bold">Lệnh Xuất Hàng</label>
                            <input type="text" name="orders[${orderIndex}][code]" class="form-control" placeholder="Nhập lệnh xuất hàng" required>
                        </div>
                        <div class="col-md-5">
                            <label class="fw-bold">Mã Lô Hàng</label>
                            <select name="orders[${orderIndex}][batches][]" class="form-select select2" multiple></select>
                        </div>
                        <div class="col-md-2" style="padding-top: 23px">
                            <button type="button" class="btn btn-danger remove-order">&times;</button>
                        </div>`;

                            orderContainer.appendChild(newOrder);
                            orderIndex++;

                            // Kích hoạt select2 cho dropdown mới
                            $(newOrder).find(".select2").select2({
                                placeholder: "Chọn mã lô hàng",
                                allowClear: true
                            });
                        });

                        // Xóa dòng lệnh
                        orderContainer.addEventListener("click", function (e) {
                            if (e.target.classList.contains("remove-order")) {
                                e.target.closest(".order-wrap").remove();
                            }
                        });

                        // Gắn sự kiện khi mở dropdown (lọc option tại đây)
                        // $(document).on("select2:opening", ".select2", function() {

                        //     const $thisSelect = $(this);

                        //     // Lấy giá trị đã chọn từ các dropdown khác
                        //     const selectedValues = [];
                        //     $(".select2").not($thisSelect).each(function() {
                        //         const vals = $(this).val();
                        //         if (vals) selectedValues.push(...vals);
                        //     });

                        //     // Lấy option từ dropdown mẫu
                        //     const allOptions = $(".batch-dropdown option");
                        //     allOptions.each(function() {
                        //         const val = $(this).val();
                        //         const text = $(this).text();
                        //         if (!selectedValues.includes(val)) {
                        //             const option = new Option(text, val, false, false);
                        //             $thisSelect.append(option);
                        //         }
                        //     });

                        //     // Cập nhật UI
                        //     $thisSelect.trigger("change");
                        // });

                        $(document).on("change", ".select2", function () {
                            const $thisSelect = $(this);
                            const selectedValues = $thisSelect.val();

                            // Lấy option từ dropdown mẫu
                            $(".batch-dropdown option").each(function () {
                                const val = $(this).val();
                                const text = $(this).text();

                                if (selectedValues.includes(val)) {
                                    // Nếu giá trị được chọn, xóa khỏi danh sách các option
                                    $(this).prop("disabled", true);
                                } else {
                                    // Nếu không được chọn, thêm lại vào danh sách các option
                                    $(this).prop("disabled", false);
                                }
                            });
                        });
                    });
            </script> --}}


            <style>
                .order-wrap {
                    display: flex;
                    gap: 10px;
                    align-items: center;
                }

                .add-order {
                    min-width: 100px;
                }

                .remove-order {
                    min-width: 40px;
                }

                .select2-container--default .select2-selection--multiple {
                    min-height: 38px !important;
                    border: 1px solid #dce7f1 !important;

                }

                .select2-container--default .select2-search--inline .select2-search__field {
                    margin-top: 5px !important;
                    height: 20px !important;
                }

                .select2-results__option[aria-disabled=true] {
                    color: #aaa !important;
                    font-style: italic;
                    background-color: #f8f9fa;
                    cursor: not-allowed;
                }
            </style>
            <div class="col-sm-12 d-flex">
                <button type="submit" class="btn btn-success me-1 mb-1">Tạo Hợp Đồng</button>
            </div>
        </form>
    </div>
</div>

@endsection