@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0">Thu Mua Sản Lượng</h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('outputs.index') }}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm Thu Mua</li>
                </ol>
            </nav>
        </div>
    </div>
    @include('layouts.alert')

    <div class="row">
        <div class="col-xl-12">
            <form id="form-treatmentslip" action="{{ route('outputs.save') }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row modal-body gy-4">
                            <div class="form-section">

                                {{-- HÀNG 1 --}}
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Mã Phiếu</label>
                                        <input type="text" class="form-input" name="code" placeholder="Tự động tạo"
                                            readonly value="{{ old('code') }}">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Tên Phiếu</label>
                                        <input type="text" class="form-input" name="name" placeholder="Tên phiếu"
                                            value="{{ old('name') }}">
                                    </div>
                                </div>

                                {{-- HÀNG 2 --}}
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Nhà Cung Cấp</label>
                                        <select class="form-select" name="supplier">
                                            <option value="">-- Chọn nhà cung cấp --</option>
                                            <option value="CÔNG TY TTNHH HB" {{ old('supplier')=='CÔNG TY TTNHH HB'
                                                ? 'selected' : '' }}>Hòa Bình Rubber</option>
                                            <option value="CÔNG TY TTNHH BN" {{ old('supplier')=='CÔNG TY TTNHH BN'
                                                ? 'selected' : '' }}>Bàu Non Rubber</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Người Tạo</label>
                                        <input type="text" class="form-input" name="created_by"
                                            placeholder="Tên người lập" value="{{ old('created_by') }}">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Ngày Tạo</label>
                                        <input type="date" class="form-input" name="date" required
                                            value="{{ old('date') }}">
                                    </div>
                                </div>

                                {{-- HÀNG 3 (Ghi chú dài) --}}
                                <div class="form-row">
                                    <div class="form-group" style="flex: 1;">
                                        <label class="form-label">Ghi Chú</label>
                                        <textarea class="form-input" name="desc" placeholder="Ghi chú chi tiết"
                                            style="min-height: 80px; resize: vertical;">{{ old('desc') }}</textarea>
                                    </div>
                                </div>

                                {{-- <input type="hidden" name="type" value="Thu Mua"> --}}

                                <!-- Treatment Plan Section -->
                                <div style="border-top: 1px solid #e5e7eb; padding-top: 1.5rem; margin-top: 1.5rem;">
                                    <script>
                                        const PRODUCTS_DATA = @json($productsGrouped);
                                    </script>
                                    <div id="treatmentSteps" style="margin-top: 1rem;">
                                        <div class="treatment-step"
                                            style="display:flex; gap:1rem; margin-bottom:1rem; padding:1rem; border:1px solid #e5e7eb; border-radius:.5rem; background-color:#D4F7D1;">
                                            <div class="step-number"
                                                style="display:flex; align-items:center; justify-content:center; width:2rem; height:2rem; background:#3b82f6; color:#fff; border-radius:50%; font-weight:bold;">
                                                1</div>

                                            <div style="flex:1;">
                                                <div class="form-row" style="margin-bottom:.5rem;">
                                                    <div class="form-group">
                                                        <label class="form-label">Kho</label>
                                                        <select class="form-select"
                                                            name="invoice_products[0][warehouseID]" required>
                                                            @foreach($warehouses as $w)
                                                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Tên Vật Tư</label>
                                                        <select class="form-select"
                                                            name="invoice_products[0][productID]" required>
                                                            <option value="">-- Chọn vật tư --</option>
                                                            @foreach($productsGrouped as $p)
                                                            <option value="{{ $p['id'] }}"
                                                                data-units='@json($p["units"])'>{{ $p['name'] }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Số Lượng</label>
                                                        <input type="number" step="any" class="form-input"
                                                            name="invoice_products[0][quantity]" value="">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Đơn Vị</label>
                                                        <select class="form-select" name="invoice_products[0][unitID]"
                                                            required>
                                                            <option value="">-- Chọn đơn vị --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-row" style="margin-bottom:0;">
                                                    <div class="form-group">
                                                        <label class="form-label">Chất Lượng</label>
                                                        <input type="text" class="form-input"
                                                            name="invoice_products[0][quality]" value="">
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
                                                onclick="removeTreatmentStep(this)" style="align-self:flex-start;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="add-material" onclick="addTreatmentStep()" style="margin-top: 1rem;">
                                        <i class="fas fa-plus"></i> Thêm Dòng
                                    </div>
                                </div>
                                <div class="prism-toggle d-grid gap-2 d-md-flex">
                                    <button type="submit" class="btn btn-success" id="submit-btn-treatmentslip">Lưu
                                        Thông
                                        Tin</button>
                                </div>
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
    let treatmentIndex = 1; // đang có 1 step (index 0)

    function buildProductOptions() {
        let html = '<option value="">-- Chọn vật tư --</option>';
        PRODUCTS_DATA.forEach(p => {
        html += `<option value="${p.id}" data-units='${JSON.stringify(p.units)}'>${p.name}</option>`;
        });
        return html;
    }

    function fillUnitsForProduct(stepEl) {
        const productSel = stepEl.querySelector('select[name$="[productID]"]');
        const unitSel    = stepEl.querySelector('select[name$="[unitID]"]');
        if (!productSel || !unitSel) return;

        const opt   = productSel.selectedOptions[0];
        const units = opt ? (JSON.parse(opt.dataset.units || '[]')) : [];
        unitSel.innerHTML = '<option value="">-- Chọn đơn vị --</option>';
        units.forEach(u => unitSel.insertAdjacentHTML('beforeend', `<option value="${u.id}">${u.name}</option>`));
    }

    function createStepElement(index) {
        const wrap = document.createElement('div');
        wrap.className = 'treatment-step';
        wrap.style.cssText = 'display:flex; gap:1rem; margin-bottom:1rem; padding:1rem; border:1px solid #e5e7eb; border-radius:.5rem; background-color:#D4F7D1;';

        wrap.innerHTML = `
        <div class="step-number"
            style="display:flex; align-items:center; justify-content:center; width:2rem; height:2rem; background:#3b82f6; color:#fff; border-radius:50%; font-weight:bold;">${index+1}</div>

        <div style="flex:1;">
            <div class="form-row" style="margin-bottom:.5rem;">
            <div class="form-group">
                <label class="form-label">Kho</label>
                <select class="form-select" name="invoice_products[${index}][warehouseID]" required>
                @foreach($warehouses as $w)
                    <option value="{{ $w->id }}">{{ $w->name }}</option>
                @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Tên Vật Tư</label>
                <select class="form-select" name="invoice_products[${index}][productID]" required>
                ${buildProductOptions()}
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Số Lượng</label>
                <input type="number" step="any" class="form-input" name="invoice_products[${index}][quantity]" value="">
            </div>

            <div class="form-group">
                <label class="form-label">Đơn Vị</label>
                <select class="form-select" name="invoice_products[${index}][unitID]" required>
                <option value="">-- Chọn đơn vị --</option>
                </select>
            </div>
            </div>

            <div class="form-row" style="margin-bottom:0;">
            <div class="form-group">
                <label class="form-label">Chất Lượng</label>
                <input type="text" class="form-input" name="invoice_products[${index}][quality]" value="">
            </div>
            <div class="form-group">
                <label class="form-label">Số Lát Dao</label>
                <input type="number" class="form-input" name="invoice_products[${index}][slice]" value="">
            </div>
            <div class="form-group">
                <label class="form-label">Thành Tiền</label>
                <input type="text" class="form-input" name="invoice_products[${index}][price]" value="">
            </div>
            </div>
        </div>

        <button type="button" class="remove-btn btn-danger" onclick="removeTreatmentStep(this)" style="align-self:flex-start;">
            <i class="fas fa-trash"></i>
        </button>
        `;
        return wrap;
    }

    function addTreatmentStep() {
        const container = document.getElementById('treatmentSteps');
        const newIndex = container.querySelectorAll('.treatment-step').length;
        const stepEl = createStepElement(newIndex);
        container.appendChild(stepEl);
        treatmentIndex = newIndex + 1;
    }

    function reindexTreatmentSteps() {
        const steps = document.querySelectorAll('#treatmentSteps .treatment-step');
        steps.forEach((step, i) => {
        const numEl = step.querySelector('.step-number');
        if (numEl) numEl.textContent = i + 1;
        step.querySelectorAll('input[name], select[name]').forEach(el => {
            el.name = el.name.replace(/invoice_products\[\d+\]/, `invoice_products[${i}]`);
        });
        });
        treatmentIndex = steps.length;
    }

    function removeTreatmentStep(btn) {
        const step = btn.closest('.treatment-step');
        if (!step) return;
        step.remove();
        reindexTreatmentSteps();
    }

    // Đổi product -> đổ lại đơn vị hợp lệ
    document.addEventListener('change', function (e) {
        if (e.target.matches('select[name^="invoice_products"][name$="[productID]"]')) {
        const step = e.target.closest('.treatment-step');
        fillUnitsForProduct(step);
        }
    });

    // Init: nếu step đầu có old value product, gọi fillUnitsForProduct để hiện đơn vị
    document.addEventListener('DOMContentLoaded', function () {
        const firstStep = document.querySelector('#treatmentSteps .treatment-step');
        if (firstStep) fillUnitsForProduct(firstStep);
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