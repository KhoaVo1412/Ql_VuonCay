@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0">
        </h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('plantingareas.index') }}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chỉnh Sửa</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        @if (session('success'))
        <div class="alert alert-light-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('errors'))
        <div class="alert alert-light-danger alert-dismissible fade show" role="alert">
            {{ session('errors') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div class="col-xl-12">
            <form id="form-account" action="{{ route('update-plantingareas', ['id' => $plantingArea->id]) }}"
                method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex">
                        <h5>Chỉnh Sửa Khu Vực Trồng: {{ $plantingArea->ma_lo }}</h5>
                        <!-- <div class="card-title">Chỉnh sửa khu vực trồng: {{ $plantingArea->ma_lo }}</div>
                        <div class="prism-toggle">
                            <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                        </div> -->
                    </div>
                    <div class="card-body">
                        <!-- <div class="row modal-body gy-4"> -->
                        <div class="row modal-body gy-4">
                            <div class="col-md-4">
                                <label for="fid" class="form-label">Fid</label>
                                <input type="number" min="0" class="form-control" name="fid" placeholder="Fid"
                                    value="{{ old('fid', $plantingArea->fid ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="idmap" class="form-label">ID Map</label>
                                <input type="number" min="0" class="form-control" name="idmap" placeholder="ID Map"
                                    value="{{ old('idmap', $plantingArea->idmap ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="ma_lo" class="form-label">Mã Lô Cây Trồng</label>
                                <input type="text" class="form-control" name="ma_lo"
                                    placeholder="Mã lô tự động tạo khi nhập: Năm trồng, Nông trường và Find"
                                    value="{{ old('ma_lo', $plantingArea->ma_lo ?? '') }}" readonly>
                            </div>
                            <!-- Chọn Đơn Vị -->
                            <div class="col-md-4">
                                <label for="unit" class="form-label">Đơn Vị <span style="color: red;">*</span></label>
                                <select class="form-control" id="unit"
                                    style="pointer-events: none; background-color: #e9ecef;">
                                    <option value="">Chọn Đơn Vị</option>
                                    @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit', $selectedUnitId)==$unit->id ?
                                        'selected' : '' }}>
                                        {{ $unit->unit_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Chọn Nông Trường -->
                            <div class="col-md-4">
                                <label for="farm_id" class="form-label">Nông Trường <span
                                        style="color: red;">*</span></label>
                                <select class="form-control" name="farm_id" id="farm_id"
                                    style="pointer-events: none; background-color: #e9ecef;">
                                    <option value="">Chọn Đơn Vị trước</option>
                                    @foreach ($farms as $val)
                                    <option value="{{ $val->id }}" {{ old('farm_id', $plantingArea->farm_id ?? '') ==
                                        $val->id ? 'selected' : '' }}>
                                        {{ $val->farm_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="nha_sx" class="form-label">Nhà Sản Xuất</label>
                                <input type="text" class="form-control" name="nha_sx" placeholder="Nhà Sản Xuất"
                                    value="{{ old('nha_sx', $plantingArea->nha_sx ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="quoc_gia" class="form-label">Quốc Gia</label>
                                <input type="text" class="form-control" name="quoc_gia" placeholder="Quốc Gia"
                                    value="{{ old('quoc_gia', $plantingArea->quoc_gia ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="plot" class="form-label">Plot</label>
                                <input type="text" class="form-control" name="plot" placeholder="Plot"
                                    value="{{ old('plot', $plantingArea->plot ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="find" class="form-label">Find <span style="color: red;">*</span></label>
                                <input type="text" class="form-control" name="find" placeholder="Find"
                                    value="{{ old('find', $plantingArea->find ?? '') }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="nam_trong" class="form-label">Năm Trồng <span
                                        style="color: red;">*</span></label>
                                <input type="number" min="1900" class="form-control" name="nam_trong"
                                    placeholder="Năm Trồng"
                                    value="{{ old('nam_trong', $plantingArea->nam_trong ?? '') }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="chi_tieu" class="form-label">Chỉ Tiêu</label>
                                <input type="text" class="form-control" name="chi_tieu" placeholder="Chỉ Tiêu"
                                    value="{{ old('chi_tieu', $plantingArea->chi_tieu ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="dien_tich" class="form-label">Diện Tích</label>
                                <input type="number" min="0" step="any" class="form-control" name="dien_tich"
                                    placeholder="Diện Tích"
                                    value="{{ old('dien_tich', $plantingArea->dien_tich ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="tapping_y" class="form-label">Tapping Y</label>
                                <input type="text" class="form-control" name="tapping_y" placeholder="Tapping Y"
                                    value="{{ old('tapping_y', $plantingArea->tapping_y ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="repl_time" class="form-label">Repl Time</label>
                                <input type="text" class="form-control" name="repl_time" placeholder="Repl Time"
                                    value="{{ old('repl_time', $plantingArea->repl_time ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="webmap" class="form-label">Webmap</label>
                                <input type="text" class="form-control" name="webmap" placeholder="Webmap"
                                    value="{{ old('webmap', $plantingArea->webmap ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="gwf" class="form-label">GWF</label>
                                <input type="text" class="form-control" name="gwf" placeholder="GWF"
                                    value="{{ old('gwf', $plantingArea->gwf ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="xa" class="form-label">Xã</label>
                                <input type="text" class="form-control" name="xa" placeholder="Xã"
                                    value="{{ old('xa', $plantingArea->xa ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="huyen" class="form-label">Huyện</label>
                                <input type="text" class="form-control" name="huyen" placeholder="Huyện"
                                    value="{{ old('huyen', $plantingArea->huyen ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="nguon_goc_lo" class="form-label">Nguồn Gốc Lô</label>
                                <input type="text" class="form-control" name="nguon_goc_lo" placeholder="Nguồn Gốc Lô"
                                    value="{{ old('nguon_goc_lo', $plantingArea->nguon_goc_lo ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="nguon_goc_dat" class="form-label">Nguồn Gốc Đất</label>
                                <input type="text" class="form-control" name="nguon_goc_dat" placeholder="Nguồn Gốc Đất"
                                    value="{{ old('nguon_goc_dat', $plantingArea->nguon_goc_dat ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="hang_dat" class="form-label">Hạng Đất</label>
                                <input type="text" class="form-control" name="hang_dat" placeholder="Hạng đất"
                                    value="{{ old('hang_dat', $plantingArea->hang_dat ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="hien_trang" class="form-label">Hiện Trạng</label>
                                <input type="text" class="form-control" name="hien_trang" placeholder="Hiện trạng"
                                    value="{{ old('hien_trang', $plantingArea->hien_trang ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="layer" class="form-label">Layer</label>
                                <input type="text" class="form-control" name="layer" placeholder="Layer"
                                    value="{{ old('layer', $plantingArea->layer ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="x" class="form-label">X</label>
                                <input type="text" class="form-control" name="x" placeholder="X"
                                    value="{{ old('x', $plantingArea->x ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="y" class="form-label">Y</label>
                                <input type="text" class="form-control" name="y" placeholder="Y"
                                    value="{{ old('y', $plantingArea->x ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="pdf" class="form-label">Tập Tin (PDF)</label>
                                <input type="file" class="form-control" id="pdf" name="pdf" accept=".pdf">
                                @if($plantingArea->pdf)
                                <label for="current_pdf" class="form-label">Tập Tin PDF Hiện Tại</label>
                                <a href="{{ asset($plantingArea->pdf) }}" target="_blank">Xem Tập Tin</a>
                                @endif
                            </div>
                        </div>
                        <!-- <div class="col-md-12">
                                <label for="chu_thich" class="form-label">Chú Thích</label>
                                <textarea class="form-control" name="chu_thich"
                                    rows="2">{{ old('chu_thich', $plantingArea->chu_thich ?? '') }}</textarea>
                            </div> -->
                        <div class="col-md-12">
                            <label for="chu_thich" class="form-label">Chú Thích</label>
                            <textarea class="form-control" id="chu_thich" name="chu_thich" rows="2"
                                oninput="autoResize(this)"
                                style="overflow: hidden; resize: none;">{{ old('chu_thich', $plantingArea->chu_thich ?? '') }}</textarea>
                        </div>
                        <!-- <div class="col-md-12">
                                <label for="geo" class="form-label">GeoJson <span style="color: red;">*</span></label>
                                <input type="text" class="form-control" name="geo" placeholder="GeoJson"
                                    value="{{ old('geo', $plantingArea->geo ?? '') }}" required>
                            </div> -->
                        <div class="col-md-12">
                            <label for="geo" class="form-label">GeoJson <span style="color: red;">*</span></label>
                            <textarea class="form-control" id="geo" name="geo" placeholder="GeoJson" rows="1"
                                oninput="autoResize(this)" style="overflow: hidden; resize: none;"
                                readonly>{{ old('geo', $plantingArea->geo ?? '') }}</textarea>
                        </div>
                        <div class="mb-2 col-sm-12">
                            <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                        </div>
                    </div>
                </div>
        </div>
        </form>
    </div>
    </div>
</section>
<script>
    function autoResize(textarea) {
        textarea.style.height = 'auto'; // Đặt về auto để tính lại chiều cao
        textarea.style.height = textarea.scrollHeight + 'px'; // Cập nhật chiều cao mới
    }
    document.addEventListener("DOMContentLoaded", function() {
        const textarea = document.getElementById("geo");
        const textareachuthich = document.getElementById("chu_thich");
        if (textarea) {
            autoResize(textarea);
        }
        if (textareachuthich) {
            autoResize(textareachuthich)
        }

    });
</script>
<script>
    function loadFarmsByUnit(unitId, selectedFarmId = null) {
        if (unitId) {
            $.ajax({
                url: '/get-farms-by-unit/' + unitId,
                type: 'GET',
                success: function(data) {
                    $('#farm_id').empty().append('<option value="">Chọn Nông Trường</option>');
                    $.each(data, function(i, farm) {
                        let selected = farm.id == selectedFarmId ? 'selected' : '';
                        $('#farm_id').append('<option value="' + farm.id + '" ' + selected + '>' + farm.farm_name + '</option>');
                    });
                }
            });
        } else {
            $('#farm_id').empty().append('<option value="">Chọn đơn vị trước</option>');
        }
    }

    $(document).ready(function() {
        let initialUnit = $('#unit').val();
        let selectedFarmId = '{{ old("farm_id", $plantingArea->farm_id) }}';
        if (initialUnit) {
            loadFarmsByUnit(initialUnit, selectedFarmId);
        }

        $('#unit').change(function() {
            let unitId = $(this).val();
            loadFarmsByUnit(unitId);
        });
    });
</script>


<style>
    .form-label {
        font-weight: bold;
    }
</style>
@endsection