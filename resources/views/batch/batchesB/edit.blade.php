@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h3 class="page-title fw-semibold fs-18 mb-0"></h3>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('batchesB.index') }}">Danh sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chỉnh Sửa</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <form id="form-account" action="{{ route('update-batchesB', ['id' => $batchesB->id]) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="card custom-card">
                    <div class="card-header d-flex">
                        <h5>Chỉnh Sửa Lô</h5>

                    </div>
                    <div class="card-body">
                        <!-- <div class="row modal-body gy-4"> -->
                        <div class="row">
                            <div class="col-md-5">
                                <label for="batch" class="form-label">Mã Lô</label>
                                <div class="d-flex">
                                    <input type="text" class="form-control" name="batch_code"
                                        value="{{ $batchesB->batch_code }}" placeholder="Mã lô"
                                        style="margin: 0 20px 0 0;" required>
                                    <button type="submit" class="btn btn-primary">Lưu</button>
                                </div>
                            </div>
                            <div class="notice my-4">
                                <p> <strong style="color: #ff0000;">Chú Ý:</strong>
                                    <span>Nhập nhiều mã lô, cách nhau bằng dấu phẩy (Ví dụ:251001, 251002)</span>
                                </p>
                            </div>
                            <div class="notice my-4">
                                <!-- <p>MÃ SỐ LÔ HÀNG NHẬP LÊN ỨNG DỤNG TRUY XUẤT NGUỒN GỐC SẢN PHẨM THEO EUDR QUY ƯỚC LÀ DÃY
                                    SỐ, GỒM: <strong><span style="color: #ff0000;">ABCDE</span></strong>, TRONG ĐÓ:</p>
                                <p><span style="color: #ff0000;"><strong>A:</strong></span> Hai số cuối năm sản xuất ra
                                    lô hàng tại xí nghiệp (ví dụ sản xuất năm 2024 thì A là 24);</p>
                                <p><span style="color: #ff0000;"><strong>B:</strong></span> Mã nhà máy: 30-4: 1; QL: 2;
                                </p>
                                <p><span style="color: #ff0000;"><strong>C:</strong></span> Mã sản phẩm, nhóm các sản
                                    phẩm:</p> -->
                                <!-- <ul> -->
                                <!-- <li>SVR 3L, SVR L, SVR 5: 1;</li>
                                    <li>SVR CV 60, 50: 2;</li>
                                    <li>SVR 10, 20: 3;</li> -->
                                {{-- <li>RSS: 4;</li>
                                <li>Ly tâm: 5;</li>
                                <li>Skim: 6;</li>
                                <li>NL: 7</li>
                                <li>RSS dăm: 8.</li> --}}
                                <!-- </ul> -->
                                <!-- <p><span style="color: #ff0000;"><strong>D:</strong> </span>Mã sản phẩm tuân thủ, không
                                    tuân thủ theo EUDR, PEFC:</p>
                                <ul>
                                    <li>Tuân thủ: 1</li>
                                    <li>Không tuân thủ: 2</li>
                                </ul>
                                <p><span style="color: #ff0000;"><strong>E:</strong> </span>Mã số thứ tự lô hàng.</p>
                                <p>VD: 241110001 ( A=24, B=1, C=1, D=1, E=0001 )</p>
                                <p>Đối với mã thứ tự lô hàng mủ ly tâm Quy định E =&nbsp; Số bồn + Số lần nhập</p>
                                <p>VD: 24151+ số bồn+ số lần nhập</p>
                                <p>24151 + 20 + 99 giải nghĩa bồn số 20 lần nhập 99</p> -->
                                <p>MÃ SỐ LÔ HÀNG NHẬP LÊN ỨNG DỤNG TRUY XUẤT NGUỒN GỐC SẢN PHẨM THEO EUDR QUY ƯỚC LÀ DÃY
                                    SỐ, GỒM: <strong><span style="color: #ff0000;">ABCDEF</span></strong>, TRONG ĐÓ:</p>
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
                            </div>
                        </div>
                    </div>
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
</style>
@endsection