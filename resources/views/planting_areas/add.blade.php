@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0"></h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('plantingareas.index') }}">Danh sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm khu vực</li>
                </ol>
            </nav>
        </div>
    </div>
    @include('layouts.alert')

    <div class="row">
        <div class="col-xl-12">
            <form id="form-account" action="{{ route('save-plantingareas') }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex">
                        <h5>Tạo Khu Vực</h5>
                        <!-- <div class="card-title">Thêm khu vực</div> -->
                        <div class="prism-toggle d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">Lưu</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row modal-body gy-4">
                            <div class="col-md-4">
                                <label for="fid" class="form-label">Fid</label>
                                <input type="number" min="0" class="form-control" name="fid" placeholder="Fid"
                                    value="{{ old('fid') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="idmap" class="form-label">ID Map</label>
                                <input type="number" min="0" class="form-control" name="idmap" placeholder="ID Map"
                                    value="{{ old('idmap') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="ma_lo" class="form-label">Mã Lô Cây Trồng</label>
                                <input type="text" class="form-control" name="ma_lo"
                                    placeholder="Mã lô tự động tạo khi nhập: Năm trồng, Nông trường và Find" readonly
                                    value="{{ old('ma_lo') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="unit" class="form-label">Đơn Vị <span style="color: red;">*</span></label>
                                <select class="form-control" id="unit" name="unit_id" required>
                                    <option value="">Chọn Đơn Vị</option>
                                    @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}" @if($singleUnit && $unit->id == $singleUnit->id)
                                        selected @endif>
                                        {{ $unit->unit_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="farm_id" class="form-label">Nông Trường <span
                                        style="color: red;">*</span></label>
                                <select class="form-control" name="farm_id" id="farm_id" required>
                                    <option value="">Chọn đơn vị trước</option>
                                    @if($singleFarm)
                                    <option value="{{ $singleFarm->id }}" selected>{{ $singleFarm->farm_name }}</option>
                                    @else
                                    @foreach ($farm as $farmItem)
                                    <option value="{{ $farmItem->id }}">{{ $farmItem->farm_name }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>


                            <div class="col-md-4">
                                <label for="nha_sx" class="form-label">Nhà Sản Xuất</label>
                                <input type="text" class="form-control" name="nha_sx" placeholder="Nhà Sản Xuất"
                                    value="{{ old('nha_sx') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="quoc_gia" class="form-label">Quốc Gia</label>
                                <input type="text" class="form-control" name="quoc_gia" placeholder="Quốc Gia"
                                    value="{{ old('quoc_gia') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="plot" class="form-label">Plot</label>
                                <input type="text" class="form-control" name="plot" placeholder="Plot"
                                    value="{{ old('plot') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="find" class="form-label">Find <span style="color: red;">*</span></label>
                                <input type="text" class="form-control" name="find" placeholder="Find" required
                                    value="{{ old('find') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="nam_trong" class="form-label">Năm Trồng <span
                                        style="color: red;">*</span></label>
                                <input type="number" min="1900" class="form-control" name="nam_trong"
                                    placeholder="Năm Trồng" required value="{{ old('nam_trong') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="chi_tieu" class="form-label">Chỉ Tiêu</label>
                                <input type="text" class="form-control" name="chi_tieu" placeholder="Chỉ Tiêu"
                                    value="{{ old('chi_tieu') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="dien_tich" class="form-label">Diện Tích</label>
                                <input type="number" min="0" step="any" class="form-control" name="dien_tich"
                                    placeholder="Diện Tích" value="{{ old('dien_tich') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="tapping_y" class="form-label">Tapping Y</label>
                                <input type="text" class="form-control" name="tapping_y" placeholder="Tapping Y"
                                    value="{{ old('tapping_y') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="repl_time" class="form-label">Repl Time</label>
                                <input type="text" class="form-control" name="repl_time" placeholder="Repl Time"
                                    value="{{ old('repl_time') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="webmap" class="form-label">Webmap</label>
                                <input type="text" class="form-control" name="webmap" placeholder="Webmap"
                                    value="{{ old('webmap') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="gwf" class="form-label">GWF</label>
                                <input type="text" class="form-control" name="gwf" placeholder="GWF"
                                    value="{{ old('gwf') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="xa" class="form-label">Xã</label>
                                <input type="text" class="form-control" name="xa" placeholder="Xã"
                                    value="{{ old('xa') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="huyen" class="form-label">Huyện</label>
                                <input type="text" class="form-control" name="huyen" placeholder="Huyện"
                                    value="{{ old('huyen') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="nguon_goc_lo" class="form-label">Nguồn Gốc Lô</label>
                                <input type="text" class="form-control" name="nguon_goc_lo" placeholder="Nguồn Gốc Lô"
                                    value="{{ old('nguon_goc_lo') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="nguon_goc_dat" class="form-label">Nguồn Gốc Đất</label>
                                <input type="text" class="form-control" name="nguon_goc_dat" placeholder="Nguồn Gốc Đất"
                                    value="{{ old('nguon_goc_dat') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="hang_dat" class="form-label">Hạng Đất</label>
                                <input type="text" class="form-control" name="hang_dat" placeholder="Hạng đất"
                                    value="{{ old('hang_dat') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="hien_trang" class="form-label">Hiện Trạng</label>
                                <input type="text" class="form-control" name="hien_trang" placeholder="Hiện trạng"
                                    value="{{ old('hien_trang') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="layer" class="form-label">Layer</label>
                                <input type="text" class="form-control" name="layer" placeholder="Layer"
                                    value="{{ old('layer') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="x" class="form-label">X</label>
                                <input type="text" class="form-control" name="x" placeholder="X" value="{{ old('x') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="y" class="form-label">Y</label>
                                <input type="text" class="form-control" name="y" placeholder="Y" value="{{ old('y') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="pdf" class="form-label">Tệp PDF</label>
                                <input type="file" class="form-control" id="pdf" name="pdf" accept=".pdf">
                            </div>
                            <div class="col-md-12">
                                <label for="chu_thich" class="form-label">Chú Thích</label>
                                <textarea class="form-control" name="chu_thich" rows="2" oninput="autoResize(this)"
                                    value="{{ old('chu_thich') }}" style="overflow: hidden; resize: none;"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label for="geo" class="form-label">GeoJson <span style="color: red;">*</span></label>
                                <textarea class="form-control" name="geo" placeholder="GeoJson" rows="1"
                                    oninput="autoResize(this)" required value="{{ old('geo') }}"
                                    style="overflow: hidden; resize: none;"></textarea>
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
</script>
<script>
    $(document).ready(function () {
        $('#farm_id').select2({
            placeholder: "Chọn Nông Trường",
            allowClear: true,
            minimumResultsForSearch: 0,
            width: '100%',
        });
    });
</script>
<script>
    function loadFarmsByUnit(unitId) {
        if (unitId) {
            $('#farm_id').prop('disabled', true);
            $.ajax({
                url: '/get-farms-by-unit/' + unitId,
                type: 'GET',
                success: function(data) {
                    $('#farm_id').empty();
                    $('#farm_id').append('<option value="">Chọn Nông Trường</option>');
                    
                    // Nếu chỉ có một nông trại, tự động chọn và khóa dropdown
                    if (data.length === 1) {
                        const farm = data[0];
                        $('#farm_id').append('<option value="' + farm.id + '" selected disabled>' + farm.farm_name + '</option>');
                        $('#farm_id').prop('disabled', true); // Khóa dropdown nông trại
                    } else {
                        // Nếu có nhiều nông trại, hiển thị danh sách để chọn
                        $.each(data, function(key, farm) {
                            $('#farm_id').append('<option value="' + farm.id + '">' + farm.farm_name + '</option>');
                        });
                        $('#farm_id').prop('disabled', false); // Mở khóa dropdown nông trại
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: 'Không thể tải danh sách nông trường.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        } else {
            $('#farm_id').empty();
            $('#farm_id').append('<option value="">Chọn đơn vị trước</option>');
            $('#farm_id').prop('disabled', true); // Khóa dropdown nông trại khi không có đơn vị
        }
    }

    $(document).ready(function () {
        // Lắng nghe sự kiện thay đổi đơn vị
        $('#unit').change(function() {
            const unitId = $(this).val();
            loadFarmsByUnit(unitId); // Gọi hàm để tải nông trại theo đơn vị
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
</style>
@endsection