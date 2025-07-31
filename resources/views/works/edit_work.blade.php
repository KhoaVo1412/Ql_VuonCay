@extends('layouts.app')
@section('content')
<div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
    <h4 class="page-title fw-semibold fs-18 mb-0">Chỉnh sửa: {{ $gentasks->name }}</h4>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0 padding">
                <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('works.index') }}">Danh Sách</a></li>
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
        <form id="form-works" action="{{ route('works.update', ['id' => $gentasks->id]) }}" method="POST"
            enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="card custom-card">
                <div class="card-header justify-content-between d-flex">
                </div>

                <div class="card-body">
                    <div class="row gy-2">

                        <div class="form-row">
                            <!-- Tên công việc -->
                            <div class="col-md-6">
                                <label class="form-lable">Tên Việc</label>
                                <select name="workID" class="form-select" id="edit-workID-select" required>
                                    <option value="">Chọn công việc</option>
                                    @foreach ($works as $w)
                                    <option value="{{ $w->id }}" @if ($gentasks->workID == $w->id) selected @endif>
                                        {{ $w->workName }}
                                    </option>
                                    @endforeach
                                </select>
                                <input type="text" id="edit-work-type-display" class="form-control mt-2" readonly
                                    value="{{ optional($gentasks->work)->workType }}"
                                    placeholder="Loại công việc sẽ hiển thị ở đây">
                            </div>

                            <!-- Người phụ trách -->
                            <div class="col-md-6">
                                <label class="form-lable">Người Phụ Trách</label>
                                <select name="workerID" class="form-select">
                                    @foreach ($workers as $worker)
                                    <option value="{{ $worker->id }}" @if ($gentasks->workerID == $worker->id)
                                        selected @endif>
                                        {{ $worker->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <!-- Ngày bắt đầu -->
                            <div class="col-md-6">
                                <label class="form-lable">Ngày Bắt Đầu</label>
                                <input type="date" name="workDate" class="form-control"
                                    value="{{ $gentasks->workDate }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-lable">Ngày Kết Thúc</label>
                                <input type="date" name="workDate" class="form-control"
                                    value="{{ $gentasks->workDate }}" required>
                            </div>
                        </div>
                        <div class="form-row">

                            <!-- Lô -->
                            <div class="col-md-6">
                                <label class="form-lable">Lô</label>
                                <select name="plotID" class="form-select">
                                    @foreach ($plots as $plot)
                                    <option value="{{ $plot->id }}" @if ($gentasks->plotID == $plot->id) selected
                                        @endif>
                                        {{ $plot->plotName }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-lable">Cây trồng</label>
                                <select name="plantIDs[]" id="plantIDs" class="form-select" multiple required>
                                    @foreach ($gentasks->plants as $plant)
                                    <option value="{{ $plant->id }}" selected>
                                        {{ $plant->variety->varietyName }}
                                        <!-- Display varietyName -->
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <!-- Loại nhiệm vụ -->
                            <div class="col-md-6">
                                <label class="form-lable">Đề Xuất Vật Tư</label>
                                <select name="type" class="form-select">
                                    <option value="1" @if ($gentasks->type == '1') selected
                                        @endif>Đề Xuất Vật Tư</option>
                                    <option value="0" @if ($gentasks->type == '0') selected
                                        @endif>Không Cần Vật Tư</option>
                                </select>
                            </div>

                            <!-- Ưu tiên -->
                            <div class="col-md-6">
                                <label class="form-lable">Mức độ ưu tiên</label>
                                <select name="priority" class="form-select">
                                    <option value="Thấp" @if ($gentasks->priority == 'Thấp') selected @endif>Thấp
                                    </option>
                                    <option value="Trung bình" @if ($gentasks->priority == 'Trung bình') selected
                                        @endif>Trung bình</option>
                                    <option value="Cao" @if ($gentasks->priority == 'Cao') selected @endif>Cao
                                    </option>
                                    <option value="Khẩn cấp" @if ($gentasks->priority == 'Khẩn cấp') selected
                                        @endif>Khẩn cấp</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <!-- Mô tả -->
                    <div class="col-md-12">
                        <label class="form-lable">Mô Tả Công Việc</label>
                        <textarea name="description" class="form-control">{{ $gentasks->description }}</textarea>
                    </div>


                    <!-- Submit Button -->
                    <div class="col-md-12 mt-3">
                        <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    // Initialize Select2 on the plant select element
    $(document).ready(function() {
        $('#plantIDs').select2({
            placeholder: 'Chọn cây trồng',
            allowClear: true
        });

        // Work Type Display Update
        document.getElementById('edit-workID-select').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var workType = selectedOption.getAttribute('data-type');
            document.getElementById('edit-work-type-display').value = workType || 'Loại công việc sẽ hiển thị ở đây';
        });

        // Load Plants Dynamically Based on Plot Selection
        document.getElementById('plotID').addEventListener('change', function() {
            var plotID = this.value;
            if (plotID) {
                fetch('/get-plants-for-plot/' + plotID)
                    .then(response => response.json())
                    .then(data => {
                        var plantSelect = document.getElementById('plantIDs');
                        plantSelect.innerHTML = '';
                        data.forEach(function(plant) {
                            var option = document.createElement('option');
                            option.value = plant.id;
                            option.text = plant.variety.varietyName;
                            plantSelect.appendChild(option);
                        });

                        $('#plantIDs').select2({
                            placeholder: 'Chọn cây trồng',
                            allowClear: true
                        });
                    });
            }
        });
    });
</script>
@endsection