@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0">
        </h4>
        <div class="ms-md-1 ms-0">
            <nav class="">
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orderbatchs.index') }}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Lệnh Xuất Hàng
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <form id="form-account" action="{{ route('save-orderbatchs') }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-body">
                        <h5>Lệnh Xuất Hàng - Thêm Mã Lô</h5>

                        <!-- <div class="row modal-body gy-4"> -->
                        <div class="row">
                            <!-- Mã Lệnh -->
                            <div class="form-group col-lg-12">
                                <label for="order_export_id" class="text-black">Mã Lệnh</label>
                                <select class="form-control" name="order_export_id" id="order_export_id" required>
                                    <option value="">Chọn mã lệnh</option>
                                    @foreach ($orderexport as $val)
                                    <option value="{{ $val->id }}">{{ $val->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Mã Lô Hàng -->
                            <div class="form-group col-lg-12">
                                <label for="batch_code" class="text-black">Chọn Mã Lô</label>
                                <select id="batch_code" name="batch_code[]" multiple class="form-control">
                                    @foreach ($batchs as $val)
                                    <option value="{{ $val->id }}" @if ($val->order_export_id) disabled @endif>
                                        {{ $val->batch_code }}
                                        @if ($val->order_export_id)
                                        (Đã gán)
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="prism-toggle">
                                <button type="submit" class="btn btn-primary mt-2">Lưu</button>
                            </div>
                        </div>
                        <!-- <div class="card-header d-flex">
                                        <div class="card-title"></div> -->
                        <!-- <div class="prism-toggle">
                                        <button type="submit" class="btn btn-primary">Lưu</button>
                                    </div> -->
                        <!-- </div> -->
                    </div>

                </div>
            </form>
        </div>
    </div>
</section>
<script>
    $(document).ready(function() {
            $('#order_export_id').select2({
                placeholder: "Chọn Mã",
                allowClear: true,
                minimumResultsForSearch: 0,
                width: '100%',
            });
        });
</script>
<script>
    $(document).ready(function() {
            $('#batch_code').select2({
                placeholder: 'Chọn nhiều mã lô',
                allowClear: true,
                width: '100%'
            });
            $('#order_export_id').on('change', function() {
                const selectedOrderId = $(this).val();
                if (selectedOrderId) {
                    const selectedBatchIds = $('#batch_code').val();
                    if (selectedBatchIds && selectedBatchIds.length > 0) {
                        $.ajax({
                            url: '{{ route('get.batch') }}',
                            type: 'GET',
                            data: {
                                ids: selectedBatchIds
                            },
                            success: function(response) {
                                console.log(response);
                            },
                            error: function() {
                                alert('Không thể lấy dữ liệu, vui lòng thử lại.');
                            }
                        });
                    }
                }
            });
        });
</script>
{{--
<script>
    $(document).ready(function () {
        $('#planting_area_id').multiselect({
            includeSelectAllOption: true,
            nonSelectedText: '',
            buttonWidth: '100%',
        });
    });
</script> --}}
<style>
    .form-label {
        font-weight: bold;
    }

    .select2-container--default .select2-selection--single {
        height: 37px;
    }

    .select2-container .select2-search--inline .select2-search__field {
        height: 25px;
    }

    html,
    body {
        height: 100%;
    }
</style>
@endsection