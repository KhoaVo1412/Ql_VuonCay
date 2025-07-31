@extends('layouts.app')

@section('content')
<div class="page-title my-3">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0"></h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb padding">
                    <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="/cont">Hợp Đồng</a></li>
                    <li class="breadcrumb-item">Sửa Hợp Đồng</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- <div class="row">
                                                                    <div class="col-12 col-md-6 order-md-1 order-last">
                                                                        <h4>Sửa Hợp Đồng</h4>
                                                                    </div>
                                                                    <div class="col-12 col-md-6 order-md-2 order-first">
                                                                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                                                            <ol class="breadcrumb">
                                                                                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                                                                                <li class="breadcrumb-item"><a href="/cont">Hợp Đồng</a></li>
                                                                                <li class="breadcrumb-item">Sửa Hợp Đồng</li>
                                                                            </ol>
                                                                        </nav>
                                                                    </div>
                                                                </div> -->
    @include('layouts.alert')
</div>

<div class="card">
    <div class="card-body">
        <h5 style="margin-bottom: 10px">Sửa Hợp Đồng</h5>
        <form method="POST" action="/cont-update/{{ $contract->id }}" class="row">
            @csrf
            {{-- @method('PUT') --}}

            <div class="form-group col-lg-4">
                <label for="contract_code" class="text-black">Mã Hợp Đồng</label>
                <input type="text" class="form-control" id="contract_code" name="contract_code"
                    value="{{ $contract->contract_code }}" placeholder="Mã hợp đồng" required>
            </div>

            <div class="form-group col-lg-4">
                <label for="contract_type_id" class="text-black">Loại Hợp Đồng</label>
                <select name="contract_type_id" id="contract_type_id" class="form-select" required>
                    <option value="">Chọn loại hợp đồng</option>
                    @foreach ($contractTypes as $contractType)
                    <option value="{{ $contractType->id }}" {{ $contractType->id == $contract->contract_type_id ?
                        'selected' : '' }}>
                        {{ $contractType->contract_type_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <!-- <div class="form-group col-lg-12">
                                                                <label class="text-black">Loại Hợp Đồng</label>
                                                                <div class="row">
                                                                    @foreach ($contractTypes as $contractType)
                                                                    <div class="col-4">
                                                                        <div class="form-check custom-checkbox">
                                                                            <input type="checkbox" name="contract_type_ids[]" value="{{ $contractType->id }}"
                                                                                class="form-check-input" {{ in_array($contractType->id, old('contract_type_ids',
                                                                                    $contract->contractType->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                                            <label class="form-check-label fw-normal text-black">
                                                                                {{ $contractType->contract_type_name }}
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    @endforeach
                                                                </div>
                                                            </div> -->

            <div class="form-group col-lg-4">
                <label for="customer_id" class="text-black">Khách Hàng</label>
                <select name="customer_id" id="customer_id" class="form-select" required>
                    <option value="">Chọn khách hàng</option>
                    @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" {{ $customer->id == $contract->customer_id ? 'selected' : '' }}>
                        {{ $customer->company_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-lg-4">
                <label for="original_contract_number" class="text-black">Hợp Đồng Gốc Số</label>
                <input type="text" class="form-control" id="original_contract_number" name="original_contract_number"
                    value="{{ $contract->original_contract_number }}" placeholder="Hợp đồng gốc số" required>
            </div>

            <div class="form-group col-lg-4">
                <label for="contract_duration_days" class="text-black">Số Ngày Hợp Đồng</label>
                <input type="number" class="form-control" id="contract_days" name="contract_days" min="1"
                    value="{{ $contract->contract_days }}" placeholder="Số ngày hợp đồng" required>
            </div>

            <div class="form-group col-lg-4">
                <label for="delivery_month" class="text-black">Tháng Giao Hàng</label>
                <!-- <input type="month" class="form-control" id="delivery_month" name="delivery_month"
                                                                            value="{{ $contract->delivery_month }}" required> -->
                <div class="datepicker-wrapper">
                    <input type="text" class="form-control datepicker" id="delivery_month" name="delivery_month"
                        placeholder="mm/yyyy" autocomplete="off"
                        value="{{ \Carbon\Carbon::parse($contract->delivery_month)->format('m/Y') }}" required
                        onkeydown="return false;">
                    <i class="fa fa-calendar calendar-icon"></i>
                </div>
            </div>

            <div class="form-group col-lg-4">
                <label for="quantity" class="text-black">Khối Lượng (Tấn)</label>
                <input type="number" class="form-control" id="quantity" name="quantity"
                    value="{{ $contract->quantity }}" placeholder="Khối lượng" required>
            </div>

            {{-- <div class="form-group col-lg-4">
                <label for="export_order_id">Lệnh Xuất Hàng</label>
                <input type="text" class="form-control" id="export_order_id" name="order_export_id"
                    value="{{ $contract->order_export_id }}" placeholder="Lệnh xuất hàng" required>
            </div> --}}

            <div class="form-group col-lg-4">
                <label for="product_type_name" class="text-black">Tên Chủng Loại Sản Phẩm</label>
                <input type="text" class="form-control" id="product_type_name" name="product_type_name"
                    value="{{ $contract->product_type_name }}" placeholder="Tên chủng loại sản phẩm" required>
            </div>

            <div class="form-group col-lg-4">
                <label for="container_closed_date" class="text-black">Ngày Đóng Cont</label>
                <!-- <input type="date" class="form-control" id="container_closing_date" name="container_closing_date"
                                                                            value="{{ $contract->container_closing_date }}" required> -->
                <div class="datepicker-wrapper">
                    <input type="text" class="form-control datetimepicker datepicker" id="container_closing_date"
                        name="container_closing_date" placeholder="dd/mm/yyyy" autocomplete="off"
                        value="{{ \Carbon\Carbon::parse($contract->container_closing_date)->format('d/m/Y') }}" required
                        onkeydown="return false;">
                    <i class="fa fa-calendar calendar-icon" style="cursor: pointer;"></i>
                    <!-- Icon bấm mở DatePicker -->
                </div>
            </div>

            <div class="form-group col-lg-4">
                <label for="delivery_date" class="text-black">Ngày Giao Hàng</label>
                <!-- <input type="date" class="form-control" id="delivery_date" name="delivery_date"
                                                                    value="{{ $contract->delivery_date }}" required> -->
                <div class="datepicker-wrapper">
                    <input type="text" class="form-control datetimepicker datepicker" id="delivery_date"
                        name="delivery_date" placeholder="dd/mm/yyyy" autocomplete="off"
                        value="{{ \Carbon\Carbon::parse($contract->delivery_date)->format('d/m/Y') }}" required
                        onkeydown="return false;">
                    <i class="fa fa-calendar calendar-icon" style="cursor: pointer;"></i>
                    <!-- Icon bấm mở DatePicker -->
                </div>
            </div>

            <div class="form-group col-lg-4">
                <label for="packaging_type" class="text-black">Dạng Đóng Gói</label>
                <!-- <input type="text" class="form-control" id="packaging_type" name="packaging_type"
                                                                    value="{{ $contract->packaging_type }}" placeholder="Dạng đóng gói" required> -->
                <select class="form-select" id="packaging_type" name="packaging_type" required>
                    <option value="">-- Chọn Dạng Đóng Gói --</option>
                    <option value="Hàng rời" {{ (old('packaging_type') ?: $contract->packaging_type) == 'Hàng rời' ?
                        'selected' : '' }}>Hàng rời</option>
                    <option value="Hàng pallet nhựa - đế nhựa" {{ (old('packaging_type') ?: $contract->packaging_type)
                        == 'Hàng pallet nhựa - đế nhựa' ? 'selected' : '' }}>Hàng pallet nhựa -
                        đế nhựa</option>
                    <option value="Pallet nhựa đế gỗ" {{ (old('packaging_type') ?: $contract->packaging_type) == 'Pallet
                        nhựa đế gỗ' ? 'selected' : '' }}>Pallet nhựa đế gỗ</option>
                    <option value="Pallet nhựa - đế sắt" {{ (old('packaging_type') ?: $contract->packaging_type) ==
                        'Pallet nhựa - đế sắt' ? 'selected' : '' }}>Pallet nhựa - đế sắt
                    </option>
                    <option value="Pallet gỗ" {{ (old('packaging_type') ?: $contract->packaging_type) == 'Pallet gỗ' ?
                        'selected' : '' }}>Pallet gỗ</option>
                </select>
            </div>

            <div class="form-group col-lg-4">
                <label for="market" class="text-black">Thị Trường</label>
                <input type="text" class="form-control" id="market" name="market" value="{{ $contract->market }}"
                    placeholder="Thị trường" required>
            </div>

            <div class="form-group col-lg-4">
                <label for="production_trade_unit" class="text-black">Đơn Vị Sản Xuất Thương Mại</label>
                <input type="text" class="form-control" id="production_or_trade_unit" name="production_or_trade_unit"
                    value="{{ $contract->production_or_trade_unit }}" placeholder="Đơn vị sản xuất thương mại" required>
            </div>

            <div class="form-group col-lg-4">
                <label for="third_party_sale" class="text-black">Bán Cho Bên Thứ 3</label>
                <input type="text" class="form-control" id="third_party_sale" name="third_party_sale"
                    value="{{ $contract->third_party_sale }}" placeholder="Bán cho bên thứ 3" required>
            </div>

            {{-- {{$contract->orderExports}} --}}


            <style>
                .add {
                    width: 30px;
                    height: 30px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    border: 1px solid;
                    cursor: pointer;
                    background: red;
                    color: #fff;
                }

                .order-wrap {
                    border: 1px solid #b3b3b3;
                    padding: 10px;
                    border-radius: 10px;
                }

                .select2-container--default .select2-selection--multiple {
                    min-height: 37px !important;
                    border: 1px solid #dce7f1 !important;
                }

                .select2-search__field {
                    height: 22px !important;
                }
            </style>
            <div class="modal fade" id="deleteOrderModal" tabindex="-1" role="dialog"
                aria-labelledby="deleteOrderModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteOrderModalLabel">Xác nhận xóa</h5>
                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button> -->
                        </div>
                        <div class="modal-body">
                            Bạn có chắc chắn muốn xóa lệnh xuất hàng này và trả lại các lô hàng liên quan?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                            <button type="button" class="btn btn-danger" id="confirmDelete">Xóa</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2 align-items-center mb-2">
                <div class="title text-danger fw-bold">Lệnh Xuất Hàng</div>

                <div class="add">
                    <i class="fa-solid fa-plus"></i>
                </div>
            </div>
            <div class="orders row">
                <hr style="color: green; width: 100%; border: 2px solid;">
                @if($contract->orderExports->isEmpty())
                <div class="col-12 order-wrap align-items-center mb-2" data-saved="false">
                    <div class="row align-items-center mb-2">
                        <div class="col-12 col-md-auto mb-2 mb-md-0">
                            <input type="text" name="orders[0][code]" class="order form-control" value="" required>
                        </div>
                        <div class="col">
                            <div class="d-flex align-items-center">
                                <select name="orders[0][batches][]" class="form-select batch-dropdown select2" multiple>
                                    @foreach ($batches as $batch)
                                    <option value="{{ $batch->id }}">{{ $batch->batch_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                @foreach ($contract->orderExports as $item)
                <div class="col-12 order-wrap align-items-center mb-2" data-saved="true">
                    <div class="row align-items-center mb-2">
                        <div class="col-12 col-md-auto mb-2 mb-md-0">
                            <input type="text" name="orders[{{ $loop->index }}][code]" class="order form-control"
                                value="{{ $item->code }}" required>
                        </div>
                        <div class="col">
                            <div class="d-flex align-items-center">
                                <select name="orders[{{ $loop->index }}][batches][]"
                                    class="form-select batch-dropdown select2" multiple>
                                    @foreach ($batches as $batch)
                                    <option value="{{ $batch->id }}" @if(in_array($batch->id,
                                        $item->batches->pluck('id')->toArray())) selected @endif>
                                        {{ $batch->batch_code }}
                                    </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-danger ms-2 delete-order"
                                    style="font-size: 16px;">&times;</button>
                            </div>
                        </div>
                    </div>
                    <div class="gap-2 mt-2">
                        @if ($item->batches && count($item->batches) > 0)
                        @foreach ($item->batches as $batch)
                        <button class="btn btn-success order-btn" style="pointer-events: none" id="{{ $batch->id }}">{{
                            $batch->batch_code }}</button>
                        @endforeach
                        @else
                        <p class="text-danger">Chưa Xuất Hàng.</p>
                        @endif
                    </div>

                    @if ($item->batches && count($item->batches) > 0)
                    <div class="gap-2 mt-4" style="color: black; font-weight: bold; font-size: 18px;">Due Diligence
                        Statement:</div>
                    <button type="button" class="btn btn-primary export-dds mt-2" data-code="{{ $item->code }}">
                        <i class="fa fa-download"></i> DDS
                    </button>
                    <button type="button" class="btn btn-primary ms-2 export-dds2 mt-2" data-code2="{{ $item->code }}">
                        <i class="fa fa-download"></i> DDS-2
                    </button>
                    <button type="button" class="btn btn-primary ms-2 export-dds3 mt-2" data-code3="{{ $item->code }}">
                        <i class="fa fa-download"></i> DDS-3
                    </button>
                    @endif
                </div>
                @endforeach
                @endif
            </div>


            <script>
                $(document).ready(function () {
                        function checkDuplicateOrderCode(orderCode) {
                            let isDuplicate = false;
                            $('input[name^="orders"][name$="[code]"]').each(function () {
                                const existingOrderCode = $(this).val(); // Lấy giá trị mã lệnh
                                if (existingOrderCode === orderCode) {
                                    isDuplicate = true;
                                }
                            });
                            return isDuplicate;
                        }
                        // $(document).on('input', 'input[name^="orders"][name$="[code]"]', function() {
                        //     const orderCode = $(this).val(); // Lấy mã lệnh mới
                        //     if (checkDuplicateOrderCode(orderCode)) {
                        //         $(this).addClass('is-invalid');
                        //         $(this).next('.invalid-feedback').remove(); 
                        //         $(this).after('<div class="invalid-feedback">Mã lệnh xuất hàng này đã tồn tại trong hợp đồng!</div>');
                        //     } else {
                        //         $(this).removeClass('is-invalid');
                        //         $(this).next('.invalid-feedback').remove();
                        //     }
                        // });
                        $('form').on('submit', function (e) {
                            let hasDuplicate = false;
                            let allCodes = [];

                            // Thu thập tất cả mã lệnh
                            $('input[name^="orders"][name$="[code]"]').each(function () {
                                const code = $(this).val().trim();
                                if (code) {
                                    allCodes.push(code);
                                }
                            });

                            // Kiểm tra trùng
                            const uniqueCodes = [...new Set(allCodes)];
                            if (allCodes.length !== uniqueCodes.length) {
                                hasDuplicate = true;
                                const duplicates = allCodes.filter((code, index) => allCodes.indexOf(code) !== index);
                                Swal.fire({
                                    title: 'Lỗi!',
                                    text: 'Có mã lệnh xuất hàng bị trùng: ' + duplicates.join(', ') + '. Vui lòng sửa trước khi gửi!',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                                e.preventDefault(); // Ngăn gửi form
                            }
                        });
                        $(".select2").select2({
                            placeholder: "Chọn mã lô hàng",
                            allowClear: true
                        });
                        $(".add").click(function () {
                            var newOrderInput = `
                                                                                    <div class="col-12 order-wrap align-items-center mb-2">
                                                                                        <div class="row align-items-center mb-2">
                                                                                            <!-- Order Code -->
                                                                                            <div class="col-12 col-md-auto mb-2 mb-md-0" data-saved="false">
                                                                                                <input type="text" name="orders[][code]" class="order form-control" required placeholder="Nhập mã lệnh xuất hàng">
                                                                                            </div>

                                                                                            <!-- Batch Dropdown (Select2) -->
                                                                                            <div class="col">
                                                                                                <div class="d-flex align-items-center">
                                                                                                    <select name="orders[][batches][]" class="form-select batch-dropdown select2" multiple>
                                                                                                        @foreach ($batches as $batch)
                                                                                                            <option value="{{ $batch->id }}">{{ $batch->batch_code }}</option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    <!-- Delete Button -->
                                                                                                    <button type="button" class="btn btn-danger ms-2 delete-order" style="font-size: 16px;">×</button>
                                                                                                </div>

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                `;
                            $(".orders").append(newOrderInput);
                            $(".select2").select2({
                                placeholder: "Chọn mã lô hàng",
                                allowClear: true
                            });
                            disableAlreadySelectedBatches();
                        });
                        // Xử lý xóa dòng
                        $(document).on('click', '.delete-order', function () {
                            var orderElement = $(this).closest('.order-wrap');
                            var orderCode = orderElement.find('input[name^="orders"]').val();
                            var isSaved = orderElement.data('saved') === true;
                            if ($('.order-wrap').length === 1) {
                                Swal.fire({
                                    title: 'Cảnh báo!',
                                    text: 'Không thể xóa dòng này, vì cần ít nhất một dòng!',
                                    icon: 'warning',
                                    confirmButtonText: 'OK'
                                });
                                return;
                            }
                            if (!orderCode || !isSaved) {
                                orderElement.remove();
                                Swal.fire({
                                    title: 'Thành công!',
                                    text: 'Lệnh xuất hàng đã được xóa!',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                });
                                disableAlreadySelectedBatches();
                                return;
                            }
                            $('#deleteOrderModal').modal('show');
                            $('#confirmDelete').off('click').on('click', function () {
                                const selectedBatches = [];
                                $(".select2").each(function () {
                                    const selectedValues = $(this).val() || [];
                                    selectedBatches.push(...selectedValues);
                                });
                                $.ajax({
                                    url: '/delete-order',
                                    method: 'DELETE',
                                    data: {
                                        code: orderCode,
                                        batches: selectedBatches,
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function (response) {
                                        if (response.success) {
                                            orderElement.remove();
                                            $('#deleteOrderModal').modal('hide');
                                            Swal.fire({
                                                title: 'Thành công!',
                                                text: 'Lệnh xuất hàng đã được xóa thành công!',
                                                icon: 'success',
                                                confirmButtonText: 'OK'
                                            });
                                            disableAlreadySelectedBatches();
                                        } else {
                                            Swal.fire({
                                                title: 'Lỗi!',
                                                text: 'Có lỗi xảy ra khi xóa lệnh xuất hàng!',
                                                icon: 'error',
                                                confirmButtonText: 'OK'
                                            });
                                        }
                                    },
                                    error: function () {
                                        Swal.fire({
                                            title: 'Cảnh báo!',
                                            text: 'Đã có lỗi xảy ra. Vui lòng thử lại!',
                                            icon: 'error',
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                });
                            });

                            $('#deleteOrderModal .btn-secondary').on('click', function () {
                                $('#deleteOrderModal').modal('hide');
                            });
                        });

                        function disableAlreadySelectedBatches() {
                            let selectedBatches = [];
                            $(".select2").each(function () {
                                const selectedValues = $(this).val() || [];
                                selectedBatches = selectedBatches.concat(selectedValues);
                            });

                            $(".select2").each(function () {
                                const currentSelect = $(this);
                                const currentValues = currentSelect.val() || [];
                                currentSelect.find('option').each(function () {
                                    const optionValue = $(this).val();
                                    if (selectedBatches.includes(optionValue) && !currentValues.includes(optionValue)) {
                                        $(this).prop('disabled', true);
                                    } else {
                                        $(this).prop('disabled', false);
                                    }
                                });
                                currentSelect.trigger("change.select2");
                            });
                        }
                        @foreach($contract->orderExports as $orderExport)
                            $('input[name="orders[][code]"][value="{{ $orderExport->code }}"]').closest('.order-wrap').find('.select2').val([
                                @foreach($orderExport->batches as $batch)
                                    "{{ $batch->id }}",
                                @endforeach
                                                                                                                                    ]).trigger('change.select2');
                        @endforeach
                        disableAlreadySelectedBatches();
                    });
            </script>

            <script>
                $(document).on('click', '.export-dds', function () {
                        let orderCode = $(this).data('code');
                        let url = `/contract/dds/export/${orderCode}`;

                        window.location.href = url; // Tải file Excel
                    });
                    $(document).on('click', '.export-dds2', function () {
                        let orderCode = $(this).data('code2');
                        let url = `/contract/dds2/export/${orderCode}`;

                        window.location.href = url; // Tải file Excel
                    });
                    $(document).on('click', '.export-dds3', function () {
                        let orderCode = $(this).data('code3');
                        let url = `/contract/dds3/export/${orderCode}`;

                        window.location.href = url; // Tải file Excel
                    });
            </script>

            <div class="col-sm-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-success me-1 mb-1 mt-3">Cập Nhật Hợp Đồng</button>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection