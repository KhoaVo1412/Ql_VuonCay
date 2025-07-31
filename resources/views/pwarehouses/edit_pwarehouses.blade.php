@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0">Chỉnh sửa Phiếu Kho</h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pwarehouses.index') }}">Danh sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa Phiếu Kho</li>
                </ol>
            </nav>
        </div>
    </div>
    @include('layouts.alert')

    <div class="row">
        <div class="col-xl-12">
            <form id="form-treatmentslip" action="{{ route('pwarehouses.update', $picking->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row modal-body gy-4">
                            <div class="form-section">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label required">Mã Phiếu</label>
                                        <input type="text" name="code" class="form-input"
                                            value="{{ old('code', $picking->code) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label required">Tên Phiếu</label>
                                        <input type="text" name="name" class="form-input"
                                            value="{{ old('name', $picking->name) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label required">Loại Phiếu</label>
                                        <select name="type" class="form-select" required>
                                            <option value="">Chọn loại</option>
                                            <option value="Nhập" {{ $picking->type == 'Nhập' ? 'selected' : '' }}>Nhập
                                            </option>
                                            <option value="Xuất" {{ $picking->type == 'Xuất' ? 'selected' : '' }}>Xuất
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label required">Kho</label>
                                        <select name="warehouseID" class="form-select" required>
                                            <option value="">Chọn kho</option>
                                            @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ $picking->warehouseID ==
                                                $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group wide">
                                        <label class="form-label required">Người Tạo</label>
                                        <select name="createName" class="form-select" required>
                                            <option value="">Chọn người</option>
                                            @foreach($users as $user)
                                            <option value="{{ $user->name }}" {{ $picking->createName == $user->name ?
                                                'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Ngày Tạo</label>
                                        <input type="date" name="createDate" class="form-input"
                                            value="{{ old('createDate', $picking->createDate) }}" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group" style="flex: 1;">
                                        <label class="form-label">Ghi Chú</label>
                                        <textarea name="desc" class="form-input"
                                            required>{{ old('desc', $picking->desc) }}</textarea>
                                    </div>
                                </div>

                                <!-- Vật tư -->
                                <div style="border-top: 1px solid #e5e7eb; padding-top: 1.5rem; margin-top: 1.5rem;">
                                    <div id="treatmentSteps" style="margin-top: 1rem;">
                                        @foreach($picking->productPickings as $i => $material)
                                        <div class="treatment-step"
                                            style="display: flex; gap: 1rem; margin-bottom: 1rem; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background-color: #D4F7D1;">
                                            <div
                                                style="width: 2rem; height: 2rem; background-color: #3b82f6; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                {{ $i + 1 }}</div>
                                            <div style="flex: 1;">
                                                <div class="form-row" style="margin-bottom: 0.5rem;">
                                                    {{-- <div class="form-group">
                                                        <label class="form-label">Danh Mục</label>
                                                        <select name="productPickings[{{ $i }}][categoryID]"
                                                            class="form-select" data-index="{{ $i }}"
                                                            onchange="onCategoryChange(this)">
                                                            <option value="">Chọn danh mục</option>
                                                            @foreach($categories as $category)
                                                            <option value="{{ $category->id }}" {{ $material->
                                                                product->category_id == $category->id ? 'selected' : ''
                                                                }}>{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div> --}}
                                                    <input type="hidden" name="productPickings[{{ $i }}][id]" value="{{ $material->id }}">

                                                    <div class="form-group">
                                                        <label class="form-label">Danh Mục</label>
                                                        <select name="productPickings[{{ $i }}][categoryID]"
                                                            class="form-select category-select" data-index="{{ $i }}"
                                                            onchange="onCategoryChange(this)">
                                                            <option value="">Chọn danh mục</option>
                                                            @foreach($categories as $category)
                                                            <option value="{{ $category->id }}" 
                                                                {{ $material->product->categoryID == $category->id ? 'selected' : ''}}>
                                                                {{ $category->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>

                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Tên Vật Tư</label>
                                                        <select name="productPickings[{{ $i }}][productID]"
                                                            class="form-select product-select" data-index="{{ $i }}"
                                                            required>
                                                            <option value="">Chọn sản phẩm</option>
                                                            @foreach($products as $product)
                                                            <option value="{{ $product->id }}" {{ $product->id ==
                                                                $material->productID ? 'selected' : '' }}>{{
                                                                $product->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Số Lượng</label>
                                                        <input type="number" name="productPickings[{{ $i }}][quantity]"
                                                            class="form-input" value="{{ $material->quantity }}"
                                                            required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Đơn Vị</label>
                                                        <select name="productPickings[{{ $i }}][unitID]"
                                                            class="form-select" required>
                                                            <option value="">Chọn đơn vị</option>
                                                            @foreach($units as $unit)
                                                            <option value="{{ $unit->id }}" {{ $unit->id ==
                                                                $material->product->unitID ? 'selected' : '' }}>{{
                                                                $unit->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="remove-btn btn-danger"
                                                onclick="removeTreatmentStep(this)" style="align-self: flex-start;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        @endforeach
                                    </div>

                                    <div class="add-material" onclick="addTreatmentStep()" style="margin-top: 1rem;">
                                        <i class="fas fa-plus"></i> Thêm Vật Tư
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-success">Cập nhật thông tin</button>
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
<script>
    const categories = @json($categories);
    const units = @json($units);
    const products = @json($products); // gồm id, name, unitID, categoryID

    function addTreatmentStep() {
        const container = document.getElementById('treatmentSteps');
        const steps = document.querySelectorAll('.treatment-step');
        const index = steps.length + 1;  // Dynamically calculate the next index based on existing steps

        const newStep = document.createElement('div');
        newStep.className = 'treatment-step';
        newStep.style.cssText = 'display: flex; gap: 1rem; margin-bottom: 1rem; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background-color: #D4F7D1;';

        newStep.innerHTML = `
            <div class="step-number"
                style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; background-color: #3b82f6; color: white; border-radius: 50%; font-weight: bold;">
                ${index}
            </div>
            <div style="flex: 1;">
                <div class="form-row" style="margin-bottom: 0.5rem;">
                    <!-- Danh Mục -->
                    <div class="form-group">
                        <label class="form-label">Danh Mục</label>
                        <select name="productPickings[${index}][categoryID]" class="form-select category-select" data-index="${index}" onchange="onCategoryChange(this)">
                            <option value="">Chọn danh mục</option>
                        </select>
                    </div>

                    <!-- Tên Vật Tư -->
                    <div class="form-group">
                        <label class="form-label">Tên Vật Tư</label>
                        <select name="productPickings[${index}][productID]" class="form-select product-select" data-index="${index}" onchange="onProductChange(this)" required>
                            <option value="">Chọn sản phẩm</option>
                        </select>
                    </div>

                    <!-- Số lượng -->
                    <div class="form-group">
                        <label class="form-label">Số Lượng</label>
                        <input type="number" name="productPickings[${index}][quantity]" class="form-input" min="1" required>
                    </div>

                    <!-- Đơn vị -->
                    <div class="form-group">
                        <label class="form-label">Đơn Vị</label>
                        <select name="productPickings[${index}][unitID]" class="form-select" required>
                            <option value="">Chọn đơn vị</option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="button" class="remove-btn btn-danger" onclick="removeTreatmentStep(this)" style="align-self: flex-start;">
                <i class="fas fa-trash"></i>
            </button>
        `;

        container.appendChild(newStep);

        // Đổ danh mục
        const categorySelect = newStep.querySelector(`select[name="productPickings[${index}][categoryID]"]`);
        categories.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.id;
            option.textContent = cat.name;
            categorySelect.appendChild(option);
        });

        // Đổ đơn vị
        const unitSelect = newStep.querySelector(`select[name="productPickings[${index}][unitID]"]`);
        units.forEach(unit => {
            const option = document.createElement('option');
            option.value = unit.id;
            option.textContent = unit.name;
            unitSelect.appendChild(option);
        });
    }

    function removeTreatmentStep(button) {
        button.closest('.treatment-step').remove();
        updateTreatmentStepNumbers();
    }

    function updateTreatmentStepNumbers() {
        const steps = document.querySelectorAll('.treatment-step');
        steps.forEach((step, index) => {
            const numberElement = step.querySelector('.step-number');
            numberElement.textContent = index + 1;  // Update step numbers based on the DOM order
        });
    }

    function onCategoryChange(select) {
        const index = select.dataset.index;
        const categoryID = select.value;

        const productSelect = document.querySelector(`select[name="productPickings[${index}][productID]"]`);
        const unitSelect = document.querySelector(`select[name="productPickings[${index}][unitID]"]`);

        productSelect.innerHTML = '<option value="">Đang tải...</option>';
        unitSelect.value = "";

        if (!categoryID) {
            productSelect.innerHTML = '<option value="">Chọn sản phẩm</option>';
            return;
        }

        // Lọc sản phẩm theo danh mục
        const filteredProducts = products.filter(p => p.categoryID == categoryID);
        let options = '<option value="">Chọn sản phẩm</option>';
        filteredProducts.forEach(p => {
            options += `<option value="${p.id}" data-unit="${p.unitID}">${p.name}</option>`;
        });

        productSelect.innerHTML = options;
    }

    function onProductChange(select) {
        const index = select.dataset.index;
        const selectedOption = select.options[select.selectedIndex];
        const unitID = selectedOption.getAttribute('data-unit');
        const unitSelect = document.querySelector(`select[name="productPickings[${index}][unitID]"]`);

        unitSelect.value = "";  // Reset the unit select dropdown before setting

        // Check if unitID is found in unitSelect
        if (unitID && unitSelect) {
            const optionToSelect = Array.from(unitSelect.options).find(opt => opt.value === unitID);
            
            if (optionToSelect) {
                unitSelect.value = unitID;  // Set the unit based on product's unitID
            } else {
                unitSelect.value = "";
                console.warn(`Đơn vị ID ${unitID} không tồn tại trong danh sách.`);
            }
        } else {
            unitSelect.value = "";  // Reset if no product is selected
        }
    }
</script>



<style>
    .modal-dialog {
        max-width: 90% !important;
        margin: 1.75rem auto;
    }

    .page-header {
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .form-row {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .form-group.wide {
        flex: 2;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #374151;
    }

    .form-label.required::after {
        content: " *";
        color: #ef4444;
    }

    .material-request-form {
        background: white;
        max-width: 800px;
        margin: 0 auto;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #374151;
        /* text-align: center; */
    }

    /* Material Items */
    .material-items {
        margin-bottom: 1.5rem;
    }

    .material-item {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        align-items: end;
    }

    .material-item .form-group {
        flex: 1;
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

    /* Request List */
    .request-list {
        margin-top: 2rem;
    }

    .request-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        background-color: white;
    }

    .request-info {
        flex: 1;
    }

    .request-id {
        font-weight: 600;
        color: #374151;
    }

    .request-date {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .request-status {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-pending {
        background-color: #fef3c7;
        color: #d97706;
    }

    .status-approved {
        background-color: #dcfce7;
        color: #166534;
    }

    .status-rejected {
        background-color: #fecaca;
        color: #991b1b;
    }

    .request-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-small {
        padding: 0.5rem;
        font-size: 0.75rem;
    }

    /* Hidden class for view switching */
    .hidden {
        display: none;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container {
            flex-direction: column;
        }

        .sidebar {
            width: 100%;
            height: auto;
        }

        .form-row {
            flex-direction: column;
        }

        .material-item {
            display: block;
            gap: 0.5rem;
        }
    }
</style>
{{-- <script>
    let treatmentStepCounter = 1;

    function addTreatmentStep() {
        treatmentStepCounter++;
        const container = document.getElementById('treatmentSteps');
        const newStep = document.createElement('div');
        newStep.className = 'treatment-step';
        newStep.style.cssText = 'display: flex; gap: 1rem; margin-bottom: 1rem; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background-color: #D4F7D1;';
        
        newStep.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; background-color: #3b82f6; color: white; border-radius: 50%; font-weight: bold;">${treatmentStepCounter}</div>
            <div style="flex: 1;">
                <div class="form-row" style="margin-bottom: 0.5rem;">
                    <div class="form-group">
                        <label class="form-label">Mã Vật Tư</label>
                        <select class="form-select" name="medicine">
                            <option value="">Chọn mã vật tư</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tên Vật Tư</label>
                        <input type="text" class="form-input" name="dosage" placeholder="">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Số Lượng</label>
                        <input type="number" class="form-input" name="treatmentDate">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Đơn Vị</label>
                        <input type="text" class="form-input" name="treatmentDate">
                    </div>
                </div>
               
            </div>
            <button type="button" class="remove-btn" onclick="removeTreatmentStep(this)" style="align-self: flex-start;">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(newStep);
    }
    function removeTreatmentStep(button) {
        button.closest('.treatment-step').remove();
        updateTreatmentStepNumbers();
    }
    function updateTreatmentStepNumbers() {
        const steps = document.querySelectorAll('.treatment-step');
        steps.forEach((step, index) => {
            const numberElement = step.querySelector('div:first-child');
            numberElement.textContent = index + 1;
        });
        treatmentStepCounter = steps.length;
    }
</script> --}}
@endsection