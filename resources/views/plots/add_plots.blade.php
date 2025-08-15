@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0">Tạo Lô</h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('plots.index') }}">Danh sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm Lô</li>
                </ol>
            </nav>
        </div>
    </div>
    @include('layouts.alert')

    <div class="row">
        <div class="col-xl-12">
            <form id="form-account" action="{{ route('plots.save') }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row modal-body gy-4">
                            <div class="col-md-4 p-t-2">
                                <label for="fid" class="form-label">Fid</label>
                                <input type="number" min="0" class="form-control" name="fid" placeholder="Fid"
                                    value="{{ old('fid') }}">
                            </div>
                            <div class="col-md-4 p-t-2">
                                <label for="idmap" class="form-label">ID Map</label>
                                <input type="number" min="0" class="form-control" name="idmap" placeholder="ID Map"
                                    value="{{ old('idmap') }}">
                            </div>
                            <div class="col-md-4 p-t-2">
                                <label for="plotCode" class="form-label">Mã Lô Cây Trồng</label>
                                <input type="text" class="form-control" name="plotCode"
                                    placeholder="Mã lô tự động tạo khi nhập: Năm trồng và Find" readonly
                                    value="{{ old('plotCode') }}">
                            </div>
                            <!-- Tên Lô -->
                            <div class="col-md-4 p-t-2">
                                <label for="plotName" class="form-label">Tên Lô</label>
                                <input type="text" class="form-control" name="plotName" id="plotName" required
                                    placeholder="Tên lô" value="{{ old('plotName') }}">
                            </div>

                            <div class="col-md-4 p-t-2">
                                <label for="find" class="form-label">Find <span style="color: red;">*</span></label>
                                <input type="text" class="form-control" name="find" placeholder="Find" required
                                    value="{{ old('find') }}">
                            </div>
                            <div class="col-md-4 p-t-2">
                                <label for="year" class="form-label">Năm Trồng
                                    <span style="color: red;">*</span>
                                </label>
                                <input type="number" min="1900" class="form-control" name="year" placeholder="Năm Trồng"
                                    required value="{{ old('year') }}">
                            </div>
                            <div class="col-md-4 p-t-2">
                                <label for="chi_tieu" class="form-label">Chỉ Tiêu</label>
                                <input type="text" class="form-control" name="chi_tieu" placeholder="Chỉ Tiêu"
                                    value="{{ old('chi_tieu') }}">
                            </div>
                            <div class="col-md-4 p-t-2">
                                <label for="plotArea" class="form-label">Diện Tích</label>
                                <input type="number" min="0" step="any" class="form-control" name="plotArea"
                                    placeholder="Diện Tích" value="{{ old('plotArea') }}">
                            </div>
                            <div class="col-md-4 p-t-2">
                                <label for="tapping_y" class="form-label">Tapping Y</label>
                                <input type="text" class="form-control" name="tapping_y" placeholder="Tapping Y"
                                    value="{{ old('tapping_y') }}">
                            </div>
                            <div class="col-md-4 p-t-2">
                                <label for="repl_time" class="form-label">Repl Time</label>
                                <input type="text" class="form-control" name="repl_time" placeholder="Repl Time"
                                    value="{{ old('repl_time') }}">
                            </div>
                            <div class="col-md-4 p-t-2">
                                <label for="webmap" class="form-label">Webmap</label>
                                <input type="text" class="form-control" name="webmap" placeholder="Webmap"
                                    value="{{ old('webmap') }}">
                            </div>
                            <div class="col-md-4 p-t-2">
                                <label for="gwf" class="form-label">GWF</label>
                                <input type="text" class="form-control" name="gwf" placeholder="GWF"
                                    value="{{ old('gwf') }}">
                            </div>
                            <div class="col-md-4 p-t-2">
                                <label for="xa" class="form-label">Xã</label>
                                <input type="text" class="form-control" name="xa" placeholder="Xã"
                                    value="{{ old('xa') }}">
                            </div>
                            <div class="col-md-4 p-t-2">
                                <label for="huyen" class="form-label">Huyện</label>
                                <input type="text" class="form-control" name="huyen" placeholder="Huyện"
                                    value="{{ old('huyen') }}">
                            </div>
                            <div class="col-md-4 p-t-2">
                                <label for="nguon_goc_lo" class="form-label">Nguồn Gốc Lô</label>
                                <input type="text" class="form-control" name="nguon_goc_lo" placeholder="Nguồn Gốc Lô"
                                    value="{{ old('nguon_goc_lo') }}">
                            </div>
                            <div class="col-md-4 p-t-2">
                                <label for="nguon_goc_dat" class="form-label">Nguồn Gốc Đất</label>
                                <input type="text" class="form-control" name="nguon_goc_dat" placeholder="Nguồn Gốc Đất"
                                    value="{{ old('nguon_goc_dat') }}">
                            </div>
                            <div class="col-md-4 p-t-2">
                                <label for="hang_dat" class="form-label">Hạng Đất</label>
                                <input type="text" class="form-control" name="hang_dat" placeholder="Hạng đất"
                                    value="{{ old('hang_dat') }}">
                            </div>
                            <div class="col-md-4 p-t-2">
                                <label for="hien_trang" class="form-label">Hiện Trạng</label>
                                <input type="text" class="form-control" name="hien_trang" placeholder="Hiện trạng"
                                    value="{{ old('hien_trang') }}">
                            </div>
                            <div class="col-md-4 p-t-2">
                                <label for="layer" class="form-label">Layer</label>
                                <input type="text" class="form-control" name="layer" placeholder="Layer"
                                    value="{{ old('layer') }}">
                            </div>
                            <div class="col-md-4 p-t-2">
                                <label for="x" class="form-label">X</label>
                                <input type="text" class="form-control" name="x" placeholder="X" value="{{ old('x') }}">
                            </div>
                            <div class="col-md-4 p-t-2">
                                <label for="y" class="form-label">Y</label>
                                <input type="text" class="form-control" name="y" placeholder="Y" value="{{ old('y') }}">
                            </div>
                            <div class="col-md-12">
                                <label for="chu_thich" class="form-label">Chú Thích</label>
                                <textarea class="form-control" name="chu_thich" rows="2" oninput="autoResize(this)"
                                    value="{{ old('chu_thich') }}" style="overflow: hidden; resize: none;"></textarea>
                            </div>
                            <!-- Map -->
                            <div class="col-md-12">
                                <label for="mapJs" class="form-label">Map <span style="color: red;">*</span></label>
                                <textarea class="form-control" name="mapJs" placeholder="Map" rows="1"
                                    oninput="autoResize(this)" required value="{{ old('mapJs') }}"
                                    style="overflow: hidden; resize: none;"></textarea>
                            </div>

                            <div class="prism-toggle d-grid gap-2 d-md-flex p-">
                                <button type="submit" class="btn btn-success">Lưu Thông Tin</button>
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
        textarea.style.height = 'auto'; 
        textarea.style.height = textarea.scrollHeight + 'px';
    }
</script>

<style>
    .form-label {
        font-weight: bold;
    }

    .select2-container--default .select2-selection--single {
        height: 37px;
    }

    .modal-dialog {
        max-width: 90% !important;
        margin: 1.75rem auto;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-title {
        color: #059669;
        font-size: 1rem;
        font-weight: bold;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
            gap: 1rem;
        }

        .form-group,
        .form-group.wide {
            flex: none;
        }
    }
</style>
@endsection