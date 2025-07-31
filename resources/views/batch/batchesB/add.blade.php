@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h3 class="page-title fw-semibold fs-18 mb-0">
        </h3>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('batchesB.index') }}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tạo Lô</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <form id="form-account" action="{{ route('save-batchesB') }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex">
                        <h5>Tạo Mã Lô</h5>
                    </div>
                    <div class="card-body">
                        <!-- <div class="row modal-body gy-4"> -->
                        <div class="row">
                            <div class="col-md-8">
                                <label for="batch" class="form-label">Mã Lô Hàng</label>
                                <div class="d-flex">
                                    <input type="text" class="form-control" name="batch_codes"
                                        value="{{ old('batch_codes') }}"
                                        placeholder="Nhập các mã lô, cách nhau bằng dấu phẩy"
                                        style="margin: 0 20px 0 0;" />
                                    <button type="submit" class="btn btn-primary">Lưu</button>
                                </div>
                            </div>

                            {{-- <div class="col-md-4">
                                <label for="received_date" class="form-label">Ngày Nhận</label>
                                <input type="date" class="form-control" name="received_date" placeholder="Ngày Nhận">
                            </div> --}}
                            <div class="notice my-4">
                                <p> <strong style="color: #ff0000;">Chú Ý:</strong>
                                    <span>Nhập nhiều mã lô, cách nhau bằng dấu phẩy (Ví dụ: 251001, 251002)</span>
                                </p>
                            </div>
                            <div class="notice my-4">
                                <p>MÃ SỐ LÔ HÀNG NHẬP LÊN ỨNG DỤNG TRUY XUẤT NGUỒN GỐC SẢN PHẨM THEO EUDR QUY ƯỚC LÀ DÃY
                                    SỐ, GỒM: <strong><span style="color: #ff0000;">ABCDEF</span></strong>, TRONG ĐÓ:</p>
                                {{-- <p><span style="color: #ff0000;"><strong>A:</strong></span> Hai số cuối năm sản
                                    xuất ra
                                    lô hàng tại xí nghiệp (ví dụ sản xuất năm 2024 thì A là 24);</p>
                                <p><span style="color: #ff0000;"><strong>B:</strong></span> Mã nhà máy: HB : 2;
                                </p> --}}
                                <p><span style="color: #ff0000;"><strong>A & B:</strong></span> Hai số đầu là năm sản
                                    xuất
                                    ra lô
                                    hàng tại nhà máy (ví dụ sản xuất năm 2025 thì A và B là 25);</p>
                                <p><span style="color: #ff0000;"><strong>C:</strong></span> 0-4 là Mủ SVR CV, 5-9 là Mủ
                                    SVR 3L</p>
                                <p><span style="color: #ff0000;"><strong>D, E & F:</strong></span> Mã số thứ tự lô
                                    hàng.</p>
                                <p>VD: 252001 ( <span style="color: #ff0000;"><strong>(A & B)</strong></span> = 25,
                                    <span style="color: #ff0000;"><strong>C</strong></span>=2,
                                    <span style="color: #ff0000;"><strong>(D, E & F)</strong></span> = 001 )
                                </p>
                                {{-- <p><span style="color: #ff0000;"><strong>C:</strong></span> Mã sản phẩm, nhóm các
                                    sản
                                    phẩm:</p>
                                <ul>
                                    <li>SVR CV60: 1</li>
                                    <li>SVR CV50: 2</li>
                                    <li>SVR 3L: 3</li>
                                    <li>SVR 5: 4</li>
                                    <li>SVR 10: 5</li>
                                    <li>SVR 20: 6</li>
                                    {{-- <li>RSS: 4;</li>
                                    <li>Ly tâm: 5;</li>
                                    <li>Skim: 6;</li>
                                    <li>NL: 7</li>
                                    <li>RSS dăm: 8.</li> --}}
                                    {{--
                                </ul>
                                <p><span style="color: #ff0000;"><strong>D:</strong> </span>Mã sản phẩm tuân thủ, không
                                    tuân thủ theo EUDR, PEFC:</p>
                                <ul>
                                    <li>Tuân thủ: 1</li>
                                    <li>Không tuân thủ: 2</li>
                                </ul>
                                <p><span style="color: #ff0000;"><strong>E:</strong> </span>Mã số thứ tự lô hàng.</p>
                                {{-- <p>VD: 241110001 ( A=24, B=1, C=1, D=1, E=0001 )</p>
                                <p>VD: 242110001 ( A=24, B=2, C=1, D=1, E=0001 )</p> --}}
                                {{-- <p>Đối với mã thứ tự lô hàng mủ ly tâm Quy định E =&nbsp; Số bồn + Số lần nhập</p>
                                <p>VD: 24151+ số bồn+ số lần nhập</p>
                                <p>24151 + 20 + 99 giải nghĩa bồn số 20 lần nhập 99</p> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script>
    $(document).ready(function () {
        $('#farm_id').select2({
            placeholder: "Chọn Nông Trường",
            allowClear: true,
            minimumResultsForSearch: 0,
            width: '100%',
        });
        $('#type_of_pus_id').select2({
            placeholder: "Chọn Loại Mủ",
            allowClear: true,
            minimumResultsForSearch: 0,
            width: '100%',
        });
        $('#vehicle_number_id').select2({
            placeholder: "Chọn Xe",
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

    .select2-container--default .select2-selection--single {
        height: 37px;
    }

    ul.custom-list {
        list-style-type: none;
        /* Bỏ dấu chấm */
    }
</style>
@endsection