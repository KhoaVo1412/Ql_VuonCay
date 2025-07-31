@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0">
        </h5>
        <div class="ms-md-1 ms-0">
            <nav class="">
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orderbatchs.index') }}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chỉnh Sửa</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <form id="form-account" action="{{ route('update-orderbatchs') }}" method="POST">
                @csrf
                {{-- <style>
                    .select2-container--default .select2-selection--single .select2-selection__rendered {
                        color: #444;
                        line-height: 30px !important;
                    }
                </style> --}}
                <div class="card custom-card">
                    <div class="card-body">
                        <h5>Lệnh Xuất Hàng - Sửa Mã Lô</h5>

                        <div class="row">
                            <!-- Mã Lệnh -->
                            <div class="form-group col-lg-12">
                                <label for="order_export_id" class="text-black">Mã Lệnh</label>
                                <select class="form-control" name="order_export_id" id="order_export_id" disabled>
                                    <option value="{{ $orderexport->id }}" selected>{{ $orderexport->code }}</option>
                                </select>
                                <input type="hidden" name="order_export_id" value="{{ $orderexport->id }}">
                            </div>
                            <!-- Mã Lô Hàng -->
                            <div class="form-group col-lg-12">
                                <label for="batch_code" class="text-black">Chọn Mã Lô</label>
                                <select id="batch_code" name="batch_code[]" multiple class="form-control">
                                    @foreach ($batchs as $val)
                                    <option value="{{ $val->id }}" @if (in_array($val->id, old('batch_code',
                                        $selectedBatches))) selected @endif
                                        @if ($val->order_export_id && $val->order_export_id != $orderexport->id)
                                        disabled @endif>
                                        {{ $val->batch_code }}
                                        @if ($val->order_export_id && $val->order_export_id != $orderexport->id)
                                        (Đã gán)
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="prism-toggle">
                            <button type="submit" class="btn btn-primary mt-2">Cập nhật</button>
                        </div>
                    </div>
                    {{-- <div class="card-footer d-flex">
                        <button type="submit" class="btn btn-primary">Cập Nhật</button>
                    </div> --}}
                </div>
            </form>
        </div>
    </div>


</section>
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
<script>
    $(document).ready(function() {
            $('#order_export_id').select2({
                placeholder: "Chọn Nông Trường",
                allowClear: true,
                minimumResultsForSearch: 0,
                width: '100%',
            });
            $('#batch_code').select2({
                placeholder: 'Chọn nhiều mã lô',
                allowClear: true,
                width: '100%'
            });
        });
</script>
@endsection