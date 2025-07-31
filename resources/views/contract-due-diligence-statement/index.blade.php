@extends('layouts.app')
@section('content')
<style>
    .select2-container--default .select2-selection--single {
        height: 38px !important;
        border-radius: 5px;
        border: 1px solid #ced4da;
        padding: 6px 12px;
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal !important;
        font-size: 16px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
    }

    .select2 {
        width: 100% !important;
    }

    html,
    body {
        height: 100%;
    }
</style>
<div class="page-title my-3">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0"></h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb padding">
                    <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                    <li class="breadcrumb-item">Due Diligence Statement
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h4>Due Diligence Statement</h4>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="/contract-dueDiligenceStatement">Due Diligence Statement</a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div> -->

    @include('layouts.alert')
</div>


<div class="card">
    <div class="card-header">
        <h5 class="p-b-5">Due Diligence Statement</h5>

        <div class="col-md-12 mb-2 form-group">
            <label for="" class="text-black">Mã Lô Cần Xuất</label>
            <select id="code" name="code" class="form-control select2" required>
                <option value="">-- Chọn Mã Lô Cần Xuất --</option>
                @foreach ($malo as $ml)
                <option value="{{ $ml->id }}">{{ $ml->batch_code }}</option>
                @endforeach
            </select>
        </div>

        {{-- <div class="d-flex justify-content-end"> --}}
            <button type="submit" class="btn btn-primary" id="exportExcel"><i class="fa fa-download"></i> Tải File
                Excel</button>
            {{--
        </div> --}}
    </div>
</div>


<script>
    $(document).ready(function() {
            $('#code').select2({
                placeholder: {
                    id: '-1',
                },
                width: '100%'
            });
        })

        document.getElementById('exportExcel').addEventListener('click', function() {
            let batchId = document.getElementById('code').value;
            if (batchId) {
                window.location.href = "{{ route('duedilistate.export', ':id') }}".replace(':id', batchId);
            } else {
                alert("Vui lòng chọn mã lô trước khi xuất Excel!");
            }
        });
</script>
@endsection