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
    .add-material {
        color: #6b7280;
        font-size: 0.875rem;
        cursor: pointer;
        padding: 0.5rem;
        border: 1px dashed #d1d5db;
        border-radius: 0.375rem;
        text-align: center;
        transition: all 0.2s;
    }

    .add-material:hover {
        color: #3b82f6;
        border-color: #3b82f6;
        background-color: #f0f9ff;
    }

    .remove-btn {
        padding: 0.75rem;
        background-color: #ef4444;
        color: white;
        border: none;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .remove-btn:hover {
        background-color: #dc2626;
    }

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

                    @foreach($gentasks->taskProductProposals as $proposal)
                    <h5 class="mt-3"> Vật Tư Được Đề Xuất </h5>

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
                    @if($gentasks->pickings->count())
                    <h4 class="mt-4">Nhập sản lượng</h4>

                    @foreach($gentasks->pickings as $picking)
                    <div class="card custom-card mb-4">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Kho</label>
                                    <select name="pickings[{{ $picking->id }}][warehouseID]" class="form-select">
                                        @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ $picking->
                                            warehouseID==$warehouse->id?'selected':'' }}>
                                            {{ $warehouse->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Loại Phiếu</label>
                                    <input type="text" class="form-input" value="{{ $picking->type ?? 'Khai thác' }}"
                                        readonly>
                                    <input type="hidden" name="pickings[{{ $picking->id }}][type]" value="Khai thác">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Ngày Tạo</label>
                                    <input type="date" name="pickings[{{ $picking->id }}][createDate]"
                                        class="form-input"
                                        value="{{ $picking->createDate ? \Carbon\Carbon::parse($picking->createDate)->format('Y-m-d') : now()->toDateString() }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group" style="flex:1;">
                                    <label class="form-label">Ghi chú</label>
                                    <textarea name="pickings[{{ $picking->id }}][desc]" class="form-input"
                                        style="min-height:80px;resize:vertical;">{{ $picking->desc }}</textarea>
                                </div>
                            </div>

                            <div style="border-top:1px solid #e5e7eb; padding-top:1.5rem; margin-top:1.5rem;">
                                <div id="treatmentSteps-{{ $picking->id }}" style="margin-top:1rem;">
                                    @php $rowIndex = 0; @endphp
                                    @forelse($picking->productPickings as $pp)
                                    @php
                                    $catId = $pp->product->categoryID ?? null;
                                    $prodId = $pp->productID;
                                    $unitId = $pp->product->unitID ?? null; // chỉ hiển thị
                                    @endphp
                                    <div class="treatment-step"
                                        style="display:flex;gap:1rem;margin-bottom:1rem;padding:1rem;border:1px solid #e5e7eb;border-radius:.5rem;background:#D4F7D1;">
                                        <div
                                            style="display:flex;align-items:center;justify-content:center;width:2rem;height:2rem;background:#3b82f6;color:#fff;border-radius:50%;font-weight:bold;">
                                            {{ $rowIndex+1 }}
                                        </div>
                                        <div style="flex:1;">
                                            <div class="form-row" style="margin-bottom:.5rem;">
                                                {{-- Danh mục --}}
                                                <div class="form-group">
                                                    <label class="form-label">Danh Mục</label>
                                                    <select
                                                        name="pickings[{{ $picking->id }}][materials][{{ $rowIndex }}][categoryID]"
                                                        class="form-select category-select" data-index="{{ $rowIndex }}"
                                                        data-picking="{{ $picking->id }}"
                                                        data-preselect-product="{{ $prodId }}"
                                                        onchange="onCategoryChange(this)">
                                                        <option value="">Chọn danh mục</option>
                                                        @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" {{ $catId==$category->
                                                            id?'selected':'' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                {{-- Sản phẩm --}}
                                                <div class="form-group">
                                                    <label class="form-label">Tên Vật Tư</label>
                                                    <select
                                                        name="pickings[{{ $picking->id }}][materials][{{ $rowIndex }}][productID]"
                                                        class="form-select product-select" data-index="{{ $rowIndex }}"
                                                        data-picking="{{ $picking->id }}"
                                                        onchange="onProductChange(this)">
                                                        <option value="">Đang tải...</option>
                                                    </select>
                                                    <input type="hidden"
                                                        name="pickings[{{ $picking->id }}][materials][{{ $rowIndex }}][id]"
                                                        value="{{ $pp->id }}">
                                                </div>

                                                {{-- Số lượng --}}
                                                <div class="form-group">
                                                    <label class="form-label">Số Lượng</label>
                                                    <input type="number"
                                                        name="pickings[{{ $picking->id }}][materials][{{ $rowIndex }}][quantity]"
                                                        class="form-input" min="0" step="0.01"
                                                        value="{{ $pp->quantity }}">
                                                </div>

                                                {{-- Đơn vị (hiển thị) --}}
                                                <div class="form-group">
                                                    <label class="form-label">Đơn Vị</label>
                                                    <select class="form-select unit-select" disabled>
                                                        <option value="">Chọn đơn vị</option>
                                                        @foreach($units as $unit)
                                                        <option value="{{ $unit->id }}" {{ $unitId==$unit->id ?
                                                            'selected':'' }}>
                                                            {{ $unit->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="remove-btn btn-danger"
                                            onclick="removeTreatmentStep(this)" style="align-self:center;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    @php $rowIndex++; @endphp
                                    @empty
                                    {{-- Chưa có dòng nào: render 1 dòng trống --}}
                                    <div class="treatment-step"
                                        style="display:flex;gap:1rem;margin-bottom:1rem;padding:1rem;border:1px solid #e5e7eb;border-radius:.5rem;background:#D4F7D1;">
                                        <div
                                            style="display:flex;align-items:center;justify-content:center;width:2rem;height:2rem;background:#3b82f6;color:#fff;border-radius:50%;font-weight:bold;">
                                            1</div>
                                        <div style="flex:1;">
                                            <div class="form-row" style="margin-bottom:.5rem;">
                                                <div class="form-group">
                                                    <label class="form-label">Danh Mục</label>
                                                    <select
                                                        name="pickings[{{ $picking->id }}][materials][0][categoryID]"
                                                        class="form-select category-select" data-index="0"
                                                        data-picking="{{ $picking->id }}"
                                                        onchange="onCategoryChange(this)">
                                                        <option value="">Chọn danh mục</option>
                                                        @foreach($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Tên Vật Tư</label>
                                                    <select name="pickings[{{ $picking->id }}][materials][0][productID]"
                                                        class="form-select product-select" data-index="0"
                                                        data-picking="{{ $picking->id }}"
                                                        onchange="onProductChange(this)">
                                                        <option value="">Chọn sản phẩm</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Số Lượng</label>
                                                    <input type="number"
                                                        name="pickings[{{ $picking->id }}][materials][0][quantity]"
                                                        class="form-input" min="0" step="0.01">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Đơn Vị</label>
                                                    <select class="form-select unit-select" disabled>
                                                        <option value="">Chọn đơn vị</option>
                                                        @foreach($units as $unit)
                                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="remove-btn btn-danger"
                                            onclick="removeTreatmentStep(this)" style="align-self:center;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    @endforelse
                                </div>

                                <div class="add-material" onclick="addTreatmentStep({{ $picking->id }})"
                                    style="margin-top:1rem;">
                                    <i class="fas fa-plus"></i> Thêm Dòng
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif

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
<script>
    function onCategoryChange(select) {
        const step = select.closest('.treatment-step');
        const index = select.dataset.index;
        const pickingId = select.dataset.picking;
        const productSelect = step.querySelector(`select.product-select[data-index="${index}"][data-picking="${pickingId}"]`);
        const unitSelect    = step.querySelector('select.unit-select');
        const unitHidden    = step.querySelector('input.unit-id-hidden');

        productSelect.innerHTML = '<option value="">Đang tải...</option>';
        const categoryID = select.value;

        // reset đơn vị khi chưa chọn danh mục
        if (!categoryID) {
        productSelect.innerHTML = '<option value="">Chọn sản phẩm</option>';
        if (unitSelect) unitSelect.value = '';
        if (unitHidden) unitHidden.value = '';
        return;
        }
        fetch(`/api/products-by-category/${categoryID}`)
        .then(r => r.json())
        .then(list => {
            let html = '<option value="">Chọn sản phẩm</option>';
            list.forEach(p => {
            html += `<option value="${p.id}" data-unit="${p.unitID || ''}">${p.name}</option>`;
            });
            productSelect.innerHTML = html;
            // preselect sản phẩm (khi load lại form edit)
            const pre = select.getAttribute('data-preselect-product');
            if (pre) {
            productSelect.value = pre;
            onProductChange(productSelect);
            console.log(productSelect);
            } else {
            if (unitSelect) unitSelect.value = '';
            if (unitHidden) unitHidden.value = '';
            }
        })
        .catch(() => {
            productSelect.innerHTML = '<option value="">Lỗi tải sản phẩm</option>';
            if (unitSelect) unitSelect.value = '';
            if (unitHidden) unitHidden.value = '';
        });
    }
    function onProductChange(select) {
        const step       = select.closest('.treatment-step');
        const unitSelect = step.querySelector('select.unit-select');
        const unitHidden = step.querySelector('input.unit-id-hidden');

        let unitID = '';
        const opt = select.options[select.selectedIndex];
        if (opt) unitID = opt.getAttribute('data-unit') || '';

        if (unitSelect) {
        const match = Array.from(unitSelect.options).find(o => o.value === unitID);
        unitSelect.value = match ? unitID : '';
        }
        console.log(unitSelect);
        if (unitHidden) unitHidden.value = unitID || '';
    }

    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('.category-select').forEach(sel => {
        if (sel.value) onCategoryChange(sel);
        });
    });
    function addTreatmentStep(pickingId) {
    const container = document.getElementById('treatmentSteps-' + pickingId);
    const index = container.querySelectorAll('.treatment-step').length;
    const tpl = `
        <div class="treatment-step" style="display:flex;gap:1rem;margin-bottom:1rem;padding:1rem;border:1px solid #e5e7eb;border-radius:.5rem;background:#D4F7D1;">
        <div style="display:flex;align-items:center;justify-content:center;width:2rem;height:2rem;background:#3b82f6;color:#fff;border-radius:50%;font-weight:bold;">${index+1}</div>
        <div style="flex:1;">
            <div class="form-row" style="margin-bottom:.5rem;">
            <div class="form-group">
                <label class="form-label">Danh Mục</label>
                <select name="pickings[${pickingId}][materials][${index}][categoryID]"
                        class="form-select category-select"
                        data-index="${index}" data-picking="${pickingId}"
                        onchange="onCategoryChange(this)">
                <option value="">Chọn danh mục</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Tên Vật Tư</label>
                <select name="pickings[${pickingId}][materials][${index}][productID]"
                        class="form-select product-select"
                        data-index="${index}" data-picking="${pickingId}"
                        onchange="onProductChange(this)">
                <option value="">Chọn sản phẩm</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Số Lượng</label>
                <input type="number" name="pickings[${pickingId}][materials][${index}][quantity]" class="form-input" min="0" step="0.01">
            </div>
            <div class="form-group">
                <label class="form-label">Đơn Vị</label>
                <select class="form-select unit-select" disabled>
                <option value="">Chọn đơn vị</option>
                @foreach($units as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
                </select>
            </div>
            </div>
        </div>
        <button type="button" class="remove-btn btn-danger" onclick="removeTreatmentStep(this)" style="align-self:center;">
            <i class="fas fa-trash"></i>
        </button>
        </div>`;
    container.insertAdjacentHTML('beforeend', tpl);
    }
    function removeTreatmentStep(btn){
    btn.closest('.treatment-step').remove();
    // (optional) re-number
    document.querySelectorAll('#'+btn.closest('[id^="treatmentSteps-"]').id+' .treatment-step > div:first-child')
        .forEach((el,i)=> el.textContent = i+1);
    }
    document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.category-select').forEach(sel => { if (sel.value) onCategoryChange(sel); });
    });
</script>
@endsection