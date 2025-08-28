@extends('layouts.app')
@section('content')
<div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
    <h5 class="page-title fw-semibold fs-18 mb-0">Chỉnh sửa: {{ $gentasks->workName }}</h5>
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
        @if (session('success'))
        <div class="alert alert-light-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('errors'))
        <div class="alert alert-light-danger alert-dismissible fade show" role="alert">
            <pre>{{ $errors->first() }}</pre>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <form id="form-works" action="{{ route('works.update', ['id' => $gentasks->id]) }}" method="POST"
            enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="card custom-card">
                <div class="card-header justify-content-between d-flex">
                </div>

                <div class="card-body">
                    <div class="row gy-2">

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-lable">Mã Công Việc</label>
                                <input type="text" name="code" class="form-control" value="{{ $gentasks->code }}"
                                    readonly>
                            </div>
                            <div class="form-group">
                                <label class="form-lable">Tên Công Việc</label>
                                <input type="text" name="workName" class="form-control"
                                    value="{{ $gentasks->workName }}" required>
                            </div>

                            {{-- <div class="col-md-6">
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
                            </div> --}}


                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-lable">Loại Công Việc</label>
                                <select name="workID" class="form-select" required>
                                    <option value="">Chọn loại công việc</option>
                                    @foreach ($works as $w)
                                    <option value="{{ $w->id }}" @if ($gentasks->workID == $w->id) selected @endif>
                                        {{ $w->workType }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Người phụ trách -->
                            <div class="form-group">
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
                            <div class="form-group">
                                <label class="form-lable">Ngày Bắt Đầu</label>
                                <input type="date" name="workDate" class="form-control"
                                    value="{{ $gentasks->workDate ? \Carbon\Carbon::parse($gentasks->workDate)->format('Y-m-d') : '' }}"
                                    onclick="this.showPicker()" onfocus="this.showPicker()" required>
                            </div>
                            <div class="form-group">
                                <label class="form-lable">Ngày Kết Thúc</label>
                                <input type="date" name="dateEnd" class="form-control"
                                    value="{{ $gentasks->dateEnd ? \Carbon\Carbon::parse($gentasks->dateEnd)->format('Y-m-d') : '' }}"
                                    onclick="this.showPicker()" onfocus="this.showPicker()" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <!-- Lô -->
                            <div class="form-group">
                                <label class="form-label">Lô</label>
                                <select name="plotID" id="plotID" class="form-select" required>
                                    @foreach ($plots as $plot)
                                    <option value="{{ $plot->id }}" @if ($gentasks->plotID == $plot->id) selected
                                        @endif>
                                        {{ $plot->plotName }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Cây trồng -->
                            <div class="form-group">
                                <label class="form-label">Cây Trồng</label>
                                <select name="plantIDs[]" id="plantIDs" class="form-select" multiple required>
                                    @foreach ($plants as $plant)
                                    <option value="{{ $plant->id }}" @if($gentasks->plants->contains($plant->id))
                                        selected @endif>
                                        {{ $plant->variety->varietyName }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <!-- Loại nhiệm vụ -->
                            <div class="form-group">
                                <label class="form-lable">Đề Xuất Vật Tư</label>
                                <select name="type" class="form-select">
                                    <option value="1" @if ($gentasks->type == '1') selected
                                        @endif>Đề Xuất Vật Tư</option>
                                    <option value="0" @if ($gentasks->type == '0') selected
                                        @endif>Không Cần Vật Tư</option>
                                </select>
                            </div>

                            <!-- Ưu tiên -->
                            <div class="form-group">
                                <label class="form-lable">Mức Độ Ưu Tiên</label>
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

                    <h5 class="mt-3">Danh Sách Đề Xuất Vật Tư</h5>

                    @foreach($gentasks->taskProductProposals as $proposal)
                    <div class="mb-3">
                        <h6>Đề Xuất: {{ $proposal->proposaName }} ({{ $proposal->proposalDate }})</h6>

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 20%">Tên Vật Tư</th>
                                    <th style="width: 10%">Số Lượng</th>
                                    <th style="width: 10%">Đơn Vị</th>
                                    <th style="width: 55%">Ghi Chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proposal->proposalProducts as $index => $pp)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $pp->product->name ?? 'Không có tên' }}</td>
                                    <td>{{ $pp->materialQuantity }}</td>
                                    <td>{{ $pp->unit->name ?? '---' }}</td>
                                    <td>{{ $pp->note }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endforeach

                    <!-- Submit Button -->
                    <div class="col-md-12 mt-3">
                        <button type="submit" class="btn btn-success">Lưu Thay Đổi</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#plantIDs').select2({
            placeholder: 'Chọn cây trồng',
            allowClear: true,
            width: '100%',
        });

        $('#plotID').on('change', function () {
            var plotID = $(this).val();
            if (plotID) {
                fetch('/e-get-plants-for-plot/' + plotID)
                    .then(response => response.json())
                    .then(data => {
                        const plantSelect = $('#plantIDs');
                        plantSelect.empty(); // Xóa hết cây cũ
                        plantSelect.val(null).trigger('change'); // Clear select2

                        data.forEach(function (plant) {
                            const option = new Option(plant.variety.varietyName, plant.id, false, false);
                            plantSelect.append(option);
                        });

                        plantSelect.trigger('change'); // Cập nhật select2
                    });
            }
        });
    });
</script>
@endsection