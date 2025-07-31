@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0">Chỉnh Sửa Lô: {{ $crops->plantCode }}</h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('crops.index') }}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chỉnh Sửa</li>
                </ol>
            </nav>
        </div>
    </div>

    <style>
        .tab-content {
            border-right: 1px solid #dee2e6;
            border-left: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
            padding: 5px;
        }

        .col-xl-4 {
            padding-top: 15px;
        }

        .form-label {
            font-weight: bold;
        }
    </style>

    <div class="row">
        <div class="col-xl-12">
            <form id="form-crops" action="{{ route('crops.update', ['id' => $crops->id]) }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex">
                        {{-- <div class="card-title">Chỉnh sửa: {{ $crops->farm_name }}</div> --}}
                        {{-- <h5></h5> --}}
                        {{-- <div class="prism-toggle d-grid gap-2 d-md-flex justify-content-md-end"> --}}
                            {{-- <button type="submit" class="btn btn-info">
                                <a href="{{ route('crops.index') }}" style="color: #ffffff;">
                                    Danh sách
                                </a></button> --}}
                            {{-- <button type="submit" class="btn btn-secondary">Lưu Thay Đổi</button>
                        </div> --}}
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="plantCode" class="form-label">Mã</label>
                                <input type="text" class="form-control" name="plantCode" placeholder="Mã"
                                    value="{{ $crops->plantCode }}" required>
                            </div>

                            {{-- <div class="col-md-4">
                                <label for="farm_name" class="form-label">Tên</label>
                                <input type="text" class="form-control" name="farm_name" placeholder="Tên"
                                    value="{{ $crops->farm_name }}" required>
                            </div> --}}
                            <div class="col-md-4">
                                <label for="varietyID" class="form-label">Giống Cây</label>
                                <select name="varietyID" id="varietyID" class="form-control" required>
                                    @foreach($varietys as $p)
                                    <option value="{{ $p->id }}" @if($p->varietyID == $p->varietyID) selected @endif>
                                        {{ $p->varietyName }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="plotID" class="form-label">Lô</label>
                                <select name="plotID" id="plotID" class="form-control" required>
                                    @foreach($plots as $p)
                                    <option value="{{ $p->id }}" @if($p->plotID == $p->plotID) selected @endif>
                                        {{ $p->plotName }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="RF_id" class="form-label">Mã RFID</label>
                                <input type="text" class="form-control" name="RF_id" placeholder="Mã RFID"
                                    value="{{ $crops->RF_id }}">
                            </div>
                            <div class="col-md-4">
                                <label for="year" class="form-label">Năm Trồng</label>
                                <input type="number" min="2000" max="{{ date('Y') }}" class="form-control" name="year"
                                    placeholder="Mã RFID" value="{{ $crops->year }}">
                            </div>
                            <div class="col-md-4">
                                <label for="statusTree" class="form-label">Tình Trạng Cây</label>
                                <input type="text" class="form-control" name="statusTree" placeholder="Tình trạng"
                                    value="{{ $crops->statusTree }}">
                            </div>
                            <!-- Trạng thái -->
                            <div class="col-md-4">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="Không hoạt động" @if ($crops->status == 'Không hoạt động') selected
                                        @endif>
                                        Không hoạt động</option>
                                    <option value="Hoạt động" @if ($crops->status == 'Hoạt động') selected @endif>Hoạt
                                        động</option>
                                </select>
                            </div>
                            <div class="p-t-10 col-sm-12" style="margin-top: 10px">
                                <button type="submit" class="btn btn-success">Lưu Thay Đổi</button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</section>
@endsection