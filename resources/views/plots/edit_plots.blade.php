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
                            <div class="col-md-4">
                                <label for="fid" class="form-label">Fid</label>
                                <input type="number" min="0" class="form-control" name="fid" placeholder="Fid"
                                    value="{{ old('fid', $plots->fid ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="idmap" class="form-label">ID Map</label>
                                <input type="number" min="0" class="form-control" name="idmap" placeholder="ID Map"
                                    value="{{ old('idmap', $plots->idmap ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="plotCode" class="form-label">Mã Lô Cây Trồng</label>
                                <input type="text" class="form-control" name="plotCode"
                                    placeholder="Mã lô tự động tạo khi nhập: Năm trồng, Nông trường và Find"
                                    value="{{ old('plotCode', $plots->plotCode ?? '') }}" readonly>
                            </div>
                            <!-- plantCount -->
                            <div class="col-md-4">
                                <label for="plantCount" class="form-label">Số Lượng Cây</label>
                                <input type="number" step="1" min="0" class="form-control" placeholder="Số lượng cây"
                                    value="{{ $totalPlants }}" readonly>
                            </div>

                            <div class="col-md-4">
                                <label for="status" class="form-label">Trạng Thái</label>
                                <select name="status" class="form-control" required>
                                    <option value="Hoạt động" @if($plots->status == 'Hoạt động') selected @endif>Hoạt
                                        động
                                    </option>
                                    <option value="Không hoạt động" @if($plots->status == 'Không hoạt động') selected
                                        @endif>Không
                                        hoạt động</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="find" class="form-label">Find <span style="color: red;">*</span></label>
                                <input type="text" class="form-control" name="find" placeholder="Find"
                                    value="{{ old('find', $plots->find ?? '') }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="year" class="form-label">Năm Trồng <span
                                        style="color: red;">*</span></label>
                                <input type="number" min="1900" class="form-control" name="year" placeholder="Năm Trồng"
                                    value="{{ old('year', $plots->year ?? '') }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="chi_tieu" class="form-label">Chỉ Tiêu</label>
                                <input type="text" class="form-control" name="chi_tieu" placeholder="Chỉ Tiêu"
                                    value="{{ old('chi_tieu', $plots->chi_tieu ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="plotArea" class="form-label">Diện Tích</label>
                                <input type="number" min="0" step="any" class="form-control" name="plotArea"
                                    placeholder="Diện Tích" value="{{ old('plotArea', $plots->plotArea ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="tapping_y" class="form-label">Tapping Y</label>
                                <input type="text" class="form-control" name="tapping_y" placeholder="Tapping Y"
                                    value="{{ old('tapping_y', $plots->tapping_y ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="repl_time" class="form-label">Repl Time</label>
                                <input type="text" class="form-control" name="repl_time" placeholder="Repl Time"
                                    value="{{ old('repl_time', $plots->repl_time ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="webmap" class="form-label">Webmap</label>
                                <input type="text" class="form-control" name="webmap" placeholder="Webmap"
                                    value="{{ old('webmap', $plots->webmap ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="gwf" class="form-label">GWF</label>
                                <input type="text" class="form-control" name="gwf" placeholder="GWF"
                                    value="{{ old('gwf', $plots->gwf ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="xa" class="form-label">Xã</label>
                                <input type="text" class="form-control" name="xa" placeholder="Xã"
                                    value="{{ old('xa', $plots->xa ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="huyen" class="form-label">Huyện</label>
                                <input type="text" class="form-control" name="huyen" placeholder="Huyện"
                                    value="{{ old('huyen', $plots->huyen ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="nguon_goc_lo" class="form-label">Nguồn Gốc Lô</label>
                                <input type="text" class="form-control" name="nguon_goc_lo" placeholder="Nguồn Gốc Lô"
                                    value="{{ old('nguon_goc_lo', $plots->nguon_goc_lo ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="nguon_goc_dat" class="form-label">Nguồn Gốc Đất</label>
                                <input type="text" class="form-control" name="nguon_goc_dat" placeholder="Nguồn Gốc Đất"
                                    value="{{ old('nguon_goc_dat', $plots->nguon_goc_dat ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="hang_dat" class="form-label">Hạng Đất</label>
                                <input type="text" class="form-control" name="hang_dat" placeholder="Hạng đất"
                                    value="{{ old('hang_dat', $plots->hang_dat ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="hien_trang" class="form-label">Hiện Trạng</label>
                                <input type="text" class="form-control" name="hien_trang" placeholder="Hiện trạng"
                                    value="{{ old('hien_trang', $plots->hien_trang ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="layer" class="form-label">Layer</label>
                                <input type="text" class="form-control" name="layer" placeholder="Layer"
                                    value="{{ old('layer', $plots->layer ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="x" class="form-label">X</label>
                                <input type="text" class="form-control" name="x" placeholder="X"
                                    value="{{ old('x', $plots->x ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="y" class="form-label">Y</label>
                                <input type="text" class="form-control" name="y" placeholder="Y"
                                    value="{{ old('y', $plots->x ?? '') }}">
                            </div>
                            <div class="col-md-12">
                                <label for="chu_thich" class="form-label">Chú Thích</label>
                                <textarea class="form-control" id="chu_thich" name="chu_thich" rows="2"
                                    oninput="autoResize(this)"
                                    style="overflow: hidden; resize: none;">{{ old('chu_thich', $plots->chu_thich ?? '') }}</textarea>
                            </div>
                            <div class="col-md-12">
                                <label for="mapJs" class="form-label">MapJs <span style="color: red;">*</span></label>
                                <textarea class="form-control" id="mapJs" name="mapJs" placeholder="MapJs" rows="1"
                                    oninput="autoResize(this)" style="overflow: hidden; resize: none;"
                                    readonly>{{ old('mapJs', $plots->mapJs ?? '') }}</textarea>
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
    <script>
        function autoResize(textarea) {
        textarea.style.height = 'auto'; // Đặt về auto để tính lại chiều cao
        textarea.style.height = textarea.scrollHeight + 'px'; // Cập nhật chiều cao mới
    }
    document.addEventListener("DOMContentLoaded", function() {
        const textarea = document.getElementById("mapJs");
        const textareachuthich = document.getElementById("chu_thich");
        if (textarea) {
            autoResize(textarea);
        }
        if (textareachuthich) {
            autoResize(textareachuthich)
        }

    });
    </script>
</section>
@endsection