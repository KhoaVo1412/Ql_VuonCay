@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0">Chỉnh Sửa: {{$outputs->name}}</h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('outputs.index') }}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chỉnh Sửa Thu Mua</li>
                </ol>
            </nav>
        </div>
    </div>
    @include('layouts.alert')

    <div class="row">
        <div class="col-xl-12">
            <form id="form-treatmentslip" action="{{ route('outputs.update', $outputs->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row modal-body gy-4">
                            <div class="form-section">

                                {{-- HÀNG 1 --}}
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Mã Phiếu</label>
                                        <input type="text" class="form-input" name="code" placeholder="Tự động tạo"
                                            readonly value="{{ old('code', $outputs->code ?? '') }}">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Tên Phiếu</label>
                                        <input type="text" class="form-input" name="name" placeholder="Tên phiếu"
                                            value="{{ old('name', $outputs->name ?? '') }}">
                                    </div>
                                </div>

                                {{-- HÀNG 2 --}}
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Nhà Cung Cấp</label>
                                        <select class="form-select" name="supplier">
                                            <option value="">-- Chọn nhà cung cấp --</option>
                                            <option value="CÔNG TY TTNHH HB" {{ old('supplier', $outputs->supplier) ==
                                                'CÔNG TY TTNHH HB' ? 'selected' : '' }}>Hòa Bình Rubber</option>
                                            <option value="CÔNG TY TTNHH BN" {{ old('supplier', $outputs->supplier) ==
                                                'CÔNG TY TTNHH BN' ? 'selected' : '' }}>Bàu Non Rubber</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Người Tạo</label>
                                        <input type="text" class="form-input" name="created_by"
                                            placeholder="Tên người lập"
                                            value="{{ old('created_by', $outputs->created_by ?? '') }}">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Ngày Tạo</label>
                                        <input type="date" class="form-input" name="date" required
                                            value="{{ old('date', $outputs->date ?? '') }}">
                                    </div>
                                </div>

                                {{-- HÀNG 3 (Ghi chú dài) --}}
                                <div class="form-row">
                                    <div class="form-group" style="flex: 1;">
                                        <label class="form-label">Ghi Chú</label>
                                        <textarea class="form-input" name="desc" placeholder="Ghi chú chi tiết"
                                            style="min-height: 80px; resize: vertical;">{{ old('desc', $outputs->desc ?? '') }}</textarea>
                                    </div>
                                </div>

                                <input type="hidden" name="type" value="Thu Mua">

                                <!-- Treatment Plan Section -->
                                <div style="border-top: 1px solid #e5e7eb; padding-top: 1.5rem; margin-top: 1.5rem;">
                                    <div id="treatmentSteps" style="margin-top: 1rem;">
                                        @php
                                        $oldProducts = old('invoice_products', $outputs->invoice_products ?? []);
                                        @endphp

                                        @if(count($oldProducts) > 0)
                                        @foreach($oldProducts as $index => $item)
                                        <div class="treatment-step"
                                            style="display: flex; gap: 1rem; margin-bottom: 1rem; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background-color: #D4F7D1;">
                                            <div
                                                style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; background-color: #3b82f6; color: white; border-radius: 50%; font-weight: bold;">
                                                {{ $loop->iteration }}</div>
                                            <div style="flex: 1;">
                                                <div class="form-row" style="margin-bottom: 0.5rem;">
                                                    <div class="form-group">
                                                        <label class="form-label">Kho</label>
                                                        <select class="form-select"
                                                            name="invoice_products[{{ $index }}][warehouseID]">
                                                            @foreach($warehouses as $w)
                                                            <option value="{{ $w->id }}" {{ (isset($item['warehouseID'])
                                                                && $item['warehouseID']==$w->id) ? 'selected' : '' }}>{{
                                                                $w->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Tên Vật Tư</label>
                                                        <select class="form-select"
                                                            name="invoice_products[{{ $index }}][productID]">
                                                            <option value="">-- Chọn vật tư --</option>
                                                            @foreach($products as $p)
                                                            <option value="{{ $p->id }}" {{ (isset($item['productID'])
                                                                && $item['productID']==$p->id) ? 'selected' : '' }}>{{
                                                                $p->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Số Lượng</label>
                                                        <input type="number" class="form-input"
                                                            name="invoice_products[{{ $index }}][quantity]"
                                                            value="{{ $item['quantity'] ?? '' }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Đơn Vị</label>
                                                        <select class="form-select"
                                                            name="invoice_products[{{ $index }}][unitID]">
                                                            <option value="">-- Chọn đơn vị --</option>
                                                            @foreach($units as $u)
                                                            <option value="{{ $u->id }}" {{ (isset($item['unitID']) &&
                                                                $item['unitID']==$u->id) ? 'selected' : '' }}>{{
                                                                $u->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-row" style="margin-bottom: 0;">
                                                    <div class="form-group">
                                                        <label class="form-label">Chất Lượng</label>
                                                        <input type="text" class="form-input"
                                                            name="invoice_products[{{ $index }}][quality]"
                                                            placeholder="" value="{{ $item['quality'] ?? '' }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Số Lát Dao</label>
                                                        <input type="number" class="form-input"
                                                            name="invoice_products[{{ $index }}][slice]"
                                                            value="{{ $item['slice'] ?? '' }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Thành Tiền</label>
                                                        <input type="text" class="form-input"
                                                            name="invoice_products[{{ $index }}][price]"
                                                            value="{{ $item['price'] ?? '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="remove-btn btn-danger"
                                                onclick="removeTreatmentStep(this)" style="align-self: flex-start;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        @endforeach
                                        @else
                                        {{-- Nếu không có dữ liệu cũ thì hiện 1 dòng mặc định --}}
                                        <div class="treatment-step"
                                            style="display: flex; gap: 1rem; margin-bottom: 1rem; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background-color: #D4F7D1;">
                                            <div
                                                style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; background-color: #3b82f6; color: white; border-radius: 50%; font-weight: bold;">
                                                1</div>
                                            <div style="flex: 1;">
                                                <div class="form-row" style="margin-bottom: 0.5rem;">
                                                    <div class="form-group">
                                                        <label class="form-label">Kho</label>
                                                        <select class="form-select"
                                                            name="invoice_products[0][warehouseID]">
                                                            @foreach($warehouses as $w)
                                                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Tên Vật Tư</label>
                                                        <select class="form-select"
                                                            name="invoice_products[0][productID]">
                                                            <option value="">-- Chọn vật tư --</option>
                                                            @foreach($products as $p)
                                                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Số Lượng</label>
                                                        <input type="number" class="form-input"
                                                            name="invoice_products[0][quantity]" value="">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Đơn Vị</label>
                                                        <select class="form-select" name="invoice_products[0][unitID]">
                                                            <option value="">-- Chọn đơn vị --</option>
                                                            @foreach($units as $u)
                                                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-row" style="margin-bottom: 0;">
                                                    <div class="form-group">
                                                        <label class="form-label">Chất Lượng</label>
                                                        <input type="text" class="form-input"
                                                            name="invoice_products[0][quality]" placeholder="" value="">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Số Lát Dao</label>
                                                        <input type="number" class="form-input"
                                                            name="invoice_products[0][slice]" value="">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Thành Tiền</label>
                                                        <input type="text" class="form-input"
                                                            name="invoice_products[0][price]" value="">
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="remove-btn btn-danger"
                                                onclick="removeTreatmentStep(this)" style="align-self: flex-start;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        @endif
                                    </div>

                                    <div class="add-material" onclick="addTreatmentStep()" style="margin-top: 1rem;">
                                        <i class="fas fa-plus"></i> Thêm Dòng
                                    </div>
                                </div>

                            </div>
                            <div class="prism-toggle d-grid gap-2 d-md-flex">
                                <button type="submit" class="btn btn-success" id="submit-btn-treatmentslip">Lưu Thông
                                    Tin</button>
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
<script>
    const warehousesData = @json($warehouses);
    const productsData = @json($products);
    const unitsData = @json($units);
</script>
<script>
    let treatmentIndex = 1;

    function addTreatmentStep() {
        treatmentIndex++;
        const container = document.getElementById('treatmentSteps');
        const newStep = document.createElement('div');
        newStep.className = 'treatment-step';
        newStep.style.cssText = 'display: flex; gap: 1rem; margin-bottom: 1rem; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background-color: #D4F7D1;';
        
        newStep.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; background-color: #3b82f6; color: white; border-radius: 50%; font-weight: bold;">${treatmentIndex}</div>
            <div style="flex: 1;">
                <div class="form-row" style="margin-bottom: 0.5rem;">
                    <div class="form-group">
                         <label class="form-label">Kho</label>
                        <select class="form-select" name="invoice_products[${treatmentIndex}][warehouseID]" required>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tên Vật Tư</label>
                        <select class="form-select" name="invoice_products[${treatmentIndex}][productID]" required>
                            <option value="">-- Chọn vật tư --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Số Lượng</label>
                        <input type="number" class="form-input" name="invoice_products[${treatmentIndex}][quantity]">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Đơn Vị</label>
                        <select class="form-select" name="invoice_products[${treatmentIndex}][unitID]">
                            <option value="">-- Chọn đơn vị --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row" style="flex:1">
                    <div class="form-group">
                        <label class="form-label">Chất Lượng</label>
                        <input type="text" class="form-input" name="invoice_products[${treatmentIndex}][quality]" placeholder="">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Số Lát Dao</label>
                       <input type="number" class="form-input" name="invoice_products[${treatmentIndex}][slice]">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Thành Tiền</label>
                        <input type="text" class="form-input" name="invoice_products[${treatmentIndex}][price]">    
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
        treatmentIndex = steps.length;
    }

    // Initialize treatment form
    function initializeTreatmentForm() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('recordDate').value = today;
        document.getElementById('startDate').value = today;
        
        // Set end date default to 7 days later
        const nextWeek = new Date();
        nextWeek.setDate(nextWeek.getDate() + 7);
        document.getElementById('endDate').value = nextWeek.toISOString().split('T')[0];
    }

    // Treatment form submission
    document.addEventListener('DOMContentLoaded', function() {
        const treatmentForm = document.getElementById('treatmentForm');
        if (treatmentForm) {
            treatmentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                alert('Đã tạo phiếu điều trị thành công!');
                this.reset();
                initializeTreatmentForm();
            });
        }        
        setTimeout(initializeTreatmentForm, 100);
    });
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
        /* border-radius: 0.75rem; */
        /* box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); */
        /* padding: 2rem; */
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

@endsection