@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0">Chỉnh sửa: {{ $plots->plotName }}</h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('plots.index') }}">Danh Sách</a></li>
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
            <form id="form-plots" action="{{ route('plots.update', ['id' => $plots->id]) }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex">

                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- plotCode -->
                            <div class="col-md-4">
                                <label for="plotCode" class="form-label">Mã Lô</label>
                                <input type="text" class="form-control" name="plotCode" placeholder="Mã lô"
                                    value="{{ $plots->plotCode }}" required>
                            </div>
                            <!-- plotName -->
                            <div class="col-md-4">
                                <label for="plotName" class="form-label">Tên Lô</label>
                                <input type="text" class="form-control" name="plotName" placeholder="Tên lô"
                                    value="{{ $plots->plotName }}" required>
                            </div>

                            <!-- plotArea -->
                            <div class="col-md-4">
                                <label for="plotArea" class="form-label">Diện Tích (m²)</label>
                                <input type="number" step="0.01" min="0" class="form-control" name="plotArea"
                                    placeholder="Diện tích" value="{{ $plots->plotArea }}" required>
                            </div>

                            <!-- gardenID -->
                            {{-- <div class="col-md-4">
                                <label for="gardenID" class="form-label">Vườn</label>
                                <select name="gardenID" id="gardenID" class="form-control" required>
                                    @foreach($gardens as $garden)
                                    <option value="{{ $garden->id }}" @if($plots->gardenID == $garden->id) selected
                                        @endif>
                                        {{ $garden->gardenName }}
                                    </option>
                                    @endforeach
                                </select>
                            </div> --}}

                            <!-- plantCount -->
                            <div class="col-md-4">
                                <label for="plantCount" class="form-label">Số Lượng Cây</label>
                                <input type="number" step="1" min="0" class="form-control" name="plantCount"
                                    placeholder="Số lượng cây" value="{{ $totalPlants }}" readonly>
                            </div>
                            {{-- <div class="col-md-4">
                                <label for="plantCount" class="form-label">Số Lượng Cây</label>
                                <input type="number" step="1" min="0" class="form-control" name="plantCount"
                                    placeholder="Số lượng cây" value="{{ $plots->plantCount }}" required>
                            </div> --}}
                            <div class="col-md-4">
                                <label for="year" class="form-label">Năm</label>
                                <input class="form-control" name="year" placeholder="Năm" value="{{$plots->year}}"
                                    required>
                            </div>
                            <!-- status -->
                            <div class="col-md-4">
                                <label for="status" class="form-label">Tình Trạng Cây</label>
                                <input class="form-control" name="statusTree" placeholder="Tình trạng cây"
                                    value="{{$plots->statusTree}}" required>
                            </div>

                            <div class="col-md-4">
                                <label for="status" class="form-label">Trạng Thái</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="1" @if($plots->status == 'Hoạt động') selected @endif>Hoạt động
                                    </option>
                                    <option value="0" @if($plots->status == 'Không hoạt động') selected @endif>Không
                                        hoạt động</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="mapJs" class="form-label">Map</label>
                                <textarea class="form-control" name="mapJs" id="mapJs" required
                                    placeholder="">{{$plots->mapJs }}</textarea>
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