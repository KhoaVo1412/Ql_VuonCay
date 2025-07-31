@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0">Tạo Phiếu Kho</h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pwarehouses.index') }}">Danh sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tạo Phiếu Kho</li>
                </ol>
            </nav>
        </div>
    </div>
    @include('layouts.alert')

    <div class="row">
        <div class="col-xl-12">
            <form id="form-treatmentslip" action="{{ route('pwarehouses.save') }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row modal-body gy-4">
                            <div class="form-section">
                                <!-- Second Row -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="code" class="form-label required">Mã Phiếu</label>
                                        <input type="text" name="code" class="form-input" placeholder="Nhập mã phiếu"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="name" class="form-label required">Tên Phiếu</label>
                                        <input type="text" name="name" class="form-input" placeholder="Nhập tên phiếu"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="name" class="form-label required">Loại Phiếu</label>
                                        <select name="type" id="type" class="form-select" required>
                                            <option value="">Chọn loại</option>
                                            <option value="Nhập">Nhập</option>
                                            <option value="Xuất">Xuất</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="warehouseID" class="form-label required">Kho</label>
                                        <select name="warehouseID" class="form-select" required>
                                            <option value="">Chọn kho</option>
                                            @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group wide">
                                        <label for="createName" class="form-label required">Người Tạo</label>
                                        <select name="createName" class="form-select" required>
                                            <option value="">Chọn người tạo</option>
                                            @foreach($users as $user)
                                            <option value="{{ $user->name }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="quantity" class="form-label">Ngày Tạo</label>
                                        <input type="date" id="startDate" name="createDate" class="form-input" required>
                                    </div>

                                </div>
                                <div class="form-row">
                                    <div class="form-group" style="flex: 1;">
                                        <label class="form-label">Ghi Chú</label>
                                        <textarea class="form-input" name="desc" placeholder="Ghi chú..."
                                            style="min-height: 80px; resize: vertical;" required></textarea>
                                    </div>
                                </div>
                                <div style="border-top: 1px solid #e5e7eb; padding-top: 1.5rem; margin-top: 1.5rem;">
                                    <div id="treatmentSteps" style="margin-top: 1rem;">
                                        <div class="treatment-step"
                                            style="display: flex; gap: 1rem; margin-bottom: 1rem; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background-color: #D4F7D1;">
                                            <div
                                                style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; background-color: #3b82f6; color: white; border-radius: 50%; font-weight: bold;">
                                                1</div>
                                            <div style="flex: 1;">
                                                <div class="form-row" style="margin-bottom: 0.5rem;">
                                                    <!-- Danh Mục -->
                                                    <div class="form-group">
                                                        <label class="form-label">Danh Mục</label>
                                                        <select name="materials[0][categoryID]"
                                                            class="form-select category-select" data-index="0"
                                                            onchange="onCategoryChange(this)">
                                                            <option value="">Chọn danh mục</option>
                                                            @foreach($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <!-- Tên Vật Tư -->
                                                    <div class="form-group">
                                                        <label class="form-label">Tên Vật Tư</label>
                                                        <select name="materials[0][productID]"
                                                            class="form-select product-select" data-index="0"
                                                            onchange="onProductChange(this)" required>
                                                            <option value="">Chọn sản phẩm</option>
                                                        </select>
                                                    </div>

                                                    <!-- Số lượng -->
                                                    <div class="form-group">
                                                        <label class="form-label">Số Lượng</label>
                                                        <input type="number" name="materials[0][quantity]"
                                                            class="form-input" required>
                                                    </div>

                                                    <!-- Đơn Vị -->
                                                    <div class="form-group">
                                                        <label class="form-label">Đơn Vị</label>
                                                        <select name="materials[0][unitID]" class="form-select"
                                                            required>
                                                            <option value="">Chọn đơn vị</option>
                                                            @foreach($units as $unit)
                                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
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
                                    </div>

                                    <div class="add-material" onclick="addTreatmentStep()" style="margin-top: 1rem;">
                                        <i class="fas fa-plus"></i> Thêm Vật Tư
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="prism-toggle d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-success" id="submit-btn-treatmentslip">Lưu Thông
                                Tin</button>
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
    function onCategoryChange(select) {
        const index = select.dataset.index;
        const categoryID = select.value;

        const productSelect = document.querySelector(`select.product-select[data-index='${index}']`);
        const unitSelect = document.querySelector(`select[name="materials[${index}][unitID]"]`);

        productSelect.innerHTML = '<option value="">Đang tải...</option>';
        unitSelect.value = "";

        if (!categoryID) {
            productSelect.innerHTML = '<option value="">Chọn sản phẩm</option>';
            return;
        }

        fetch(`/api/products-by-category/${categoryID}`)
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">Chọn sản phẩm</option>';
                data.forEach(product => {
                    options += `<option value="${product.id}" data-unit="${product.unitID}">${product.name}</option>`;
                });
                productSelect.innerHTML = options;
            })
            .catch(() => {
                productSelect.innerHTML = '<option value="">Lỗi tải sản phẩm</option>';
            });
    }

    function onProductChange(select) {
        const index = select.dataset.index;
        const selectedOption = select.options[select.selectedIndex];
        const unitID = selectedOption.getAttribute('data-unit');
        const unitSelect = document.querySelector(`select[name="materials[${index}][unitID]"]`);

        if (unitID && unitSelect) {
            const optionToSelect = Array.from(unitSelect.options).find(opt => opt.value === unitID);
            if (optionToSelect) {
                unitSelect.value = unitID;
            } else {
                unitSelect.value = ""; // nếu không khớp, không chọn gì cả
                console.warn(`Đơn vị ID ${unitID} không tồn tại trong danh sách.`);
            }
        } else {
            unitSelect.value = "";
        }
    }

</script>



<script>
    const categories = @json($categories);
    const units = @json($units);
    const products = @json($products); // nếu bạn muốn load sẵn toàn bộ
</script>
<script>
    let treatmentStepCounter = 1;
    function addTreatmentStep() {
        const container = document.getElementById('treatmentSteps');
        const index = treatmentStepCounter++;

        const newStep = document.createElement('div');
        newStep.className = 'treatment-step';
        newStep.style.cssText = 'display: flex; gap: 1rem; margin-bottom: 1rem; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background-color: #D4F7D1;';
        newStep.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; background-color: #3b82f6; color: white; border-radius: 50%; font-weight: bold;">${index + 1}</div>
            <div style="flex: 1;">
                <div class="form-row" style="margin-bottom: 0.5rem;">
                    <div class="form-group">
                        <label class="form-label">Danh Mục</label>
                        <select name="materials[${index}][categoryID]" class="form-select category-select" data-index="${index}" onchange="onCategoryChange(this)">
                            <option value="">Chọn danh mục</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tên Vật Tư</label>
                        <select name="materials[${index}][productID]" class="form-select product-select" data-index="${index}" required>
                            <option value="">Chọn sản phẩm</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Số Lượng</label>
                        <input type="number" name="materials[${index}][quantity]" class="form-input" min="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Đơn Vị</label>
                        <select name="materials[${index}][unitID]" class="form-select" required>
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
        const categorySelect = newStep.querySelector(`select[name="materials[${index}][categoryID]"]`);
        categories.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.id;
            option.textContent = cat.name;
            categorySelect.appendChild(option);
        });

        // Đổ đơn vị
        const unitSelect = newStep.querySelector(`select[name="materials[${index}][unitID]"]`);
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
            const numberElement = step.querySelector('div:first-child');
            numberElement.textContent = index + 1;
        });
        treatmentStepCounter = steps.length;
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