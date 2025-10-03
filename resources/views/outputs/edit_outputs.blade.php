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
                                    <script>
                                        const PRODUCTS_DATA = @json($productsGrouped); // nhẹ & đủ dùng
                                    </script>

                                    @php
                                    // Ưu tiên old(), fallback invoice lines, nếu rỗng → tạo 1 dòng mặc định:
                                    $old = old('invoice_products');
                                    $initialRows = is_array($old) ? $old : (
                                    isset($outputs->invoice_products) ? $outputs->invoice_products->map(fn($ip) => [
                                    'warehouseID' => $ip->warehouseID,
                                    'productID' => $ip->productID,
                                    'unitID' => $ip->unitID,
                                    'quantity' => $ip->quantity,
                                    'quality' => $ip->quality,
                                    'slice' => $ip->slice,
                                    'price' => $ip->price,
                                    ])->toArray() : []
                                    );
                                    if (empty($initialRows)) $initialRows = [[]];
                                    @endphp

                                    <div id="treatmentSteps" style="margin-top:1rem;">
                                        @foreach($initialRows as $index => $item)
                                        @php
                                        $selUnit = $item['unitID'] ?? '';
                                        $selProd = $item['productID'] ?? '';
                                        // kho cố định → dùng hidden
                                        @endphp
                                        <div class="treatment-step" data-selected-unit="{{ $selUnit }}"
                                            style="display:flex; gap:1rem; margin-bottom:1rem; padding:1rem; border:1px solid #e5e7eb; border-radius:.5rem; background:#D4F7D1;">
                                            <div class="step-number"
                                                style="display:flex; align-items:center; justify-content:center; width:2rem; height:2rem; background:#3b82f6; color:#fff; border-radius:50%; font-weight:bold;">
                                                {{ $loop->iteration }}
                                            </div>

                                            <div style="flex:1;">
                                                <div class="form-row" style="margin-bottom:.5rem;">
                                                    {{-- Kho sản lượng: cố định --}}
                                                    <input type="hidden"
                                                        name="invoice_products[{{ $index }}][warehouseID]"
                                                        value="{{ $warehouse->id }}">

                                                    <div class="form-group">
                                                        <label class="form-label">Tên Vật Tư</label>
                                                        <select class="form-select"
                                                            name="invoice_products[{{ $index }}][productID]" required>
                                                            <option value="">-- Chọn vật tư --</option>
                                                            @foreach($productsGrouped as $p)
                                                            <option value="{{ $p['id'] }}"
                                                                data-units='@json($p["units"])' {{
                                                                (string)$selProd===(string)$p['id'] ? 'selected' : ''
                                                                }}>
                                                                {{ $p['name'] }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group" style="min-width:140px;">
                                                        <label class="form-label">Số Lượng</label>
                                                        <input type="number" step="any" class="form-input"
                                                            name="invoice_products[{{ $index }}][quantity]"
                                                            value="{{ $item['quantity'] ?? '' }}">
                                                    </div>

                                                    <div class="form-group" style="min-width:180px;">
                                                        <label class="form-label">Đơn Vị</label>
                                                        <select class="form-select"
                                                            name="invoice_products[{{ $index }}][unitID]" required>
                                                            <option value="">-- Chọn đơn vị --</option>
                                                            {{-- JS sẽ đổ theo product --}}
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-row" style="margin-bottom:0;">
                                                    <div class="form-group">
                                                        <label class="form-label">Chất Lượng</label>
                                                        <input type="text" class="form-input"
                                                            name="invoice_products[{{ $index }}][quality]"
                                                            value="{{ $item['quality'] ?? '' }}">
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
                                                            value="{{ isset($item['price']) ? number_format($item['price'], 0, ',', '.') : '' }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="button" class="remove-btn btn-danger"
                                                onclick="removeTreatmentStep(this)" style="align-self:flex-start;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        @endforeach
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
    // số step hiện có
  let treatmentIndex = document.querySelectorAll('#treatmentSteps .treatment-step').length;

  function buildProductOptions() {
    let html = '<option value="">-- Chọn vật tư --</option>';
    for (const p of PRODUCTS_DATA) {
      html += `<option value="${p.id}" data-units='${JSON.stringify(p.units)}'>${p.name}</option>`;
    }
    return html;
  }

  function fillUnitsForProduct(stepEl) {
    const productSel = stepEl.querySelector('select[name$="[productID]"]');
    const unitSel    = stepEl.querySelector('select[name$="[unitID]"]');
    if (!productSel || !unitSel) return;

    const opt        = productSel.selectedOptions[0];
    const units      = opt ? JSON.parse(opt.dataset.units || '[]') : [];
    const selectedId = stepEl.getAttribute('data-selected-unit') || '';

    let html = '<option value="">-- Chọn đơn vị --</option>';
    for (const u of units) {
      const sel = String(u.id) === String(selectedId) ? 'selected' : '';
      html += `<option value="${u.id}" ${sel}>${u.name}</option>`;
    }
    unitSel.innerHTML = html;
    stepEl.removeAttribute('data-selected-unit');
  }

  function createStepElement(index) {
    const frag = document.createElement('template');
    frag.innerHTML = `
      <div class="treatment-step" style="display:flex; gap:1rem; margin-bottom:1rem; padding:1rem; border:1px solid #e5e7eb; border-radius:.5rem; background:#D4F7D1;">
        <div class="step-number" style="display:flex; align-items:center; justify-content:center; width:2rem; height:2rem; background:#3b82f6; color:#fff; border-radius:50%; font-weight:bold;">${index+1}</div>
        <div style="flex:1;">
          <div class="form-row" style="margin-bottom:.5rem;">
            <input type="hidden" name="invoice_products[${index}][warehouseID]" value="{{ $warehouse->id }}">

            <div class="form-group" >
              <label class="form-label">Tên Vật Tư</label>
              <select class="form-select" name="invoice_products[${index}][productID]" required>
                ${buildProductOptions()}
              </select>
            </div>

            <div class="form-group" style="min-width:140px;">
              <label class="form-label">Số Lượng</label>
              <input type="number" step="any" class="form-input" name="invoice_products[${index}][quantity]" value="">
            </div>

            <div class="form-group" style="min-width:180px;">
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
      </div>
    `.trim();
    return frag.content.firstChild;
  }

  function addTreatmentStep() {
    const container = document.getElementById('treatmentSteps');
    const idx = container.querySelectorAll('.treatment-step').length;
    const el  = createStepElement(idx);
    container.appendChild(el);
    treatmentIndex = idx + 1;
  }

  function reindexTreatmentSteps() {
    const steps = document.querySelectorAll('#treatmentSteps .treatment-step');
    steps.forEach((step, i) => {
      const num = step.querySelector('.step-number');
      if (num) num.textContent = i + 1;
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

  // Đổi product → đổ lại đơn vị
  document.addEventListener('change', (e) => {
    if (e.target.matches('select[name^="invoice_products"][name$="[productID]"]')) {
      const step = e.target.closest('.treatment-step');
      fillUnitsForProduct(step);
    }
  });

  // Init: đổ đơn vị cho tất cả step hiện có (kèm selected unit cũ nếu có)
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('#treatmentSteps .treatment-step').forEach(step => fillUnitsForProduct(step));
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

    @media (max-width: 500px) {
        .treatment-step {
            display: block !important;
        }

        .form-input,
        .form-select {
            width: 100% !important;
        }
    }
</style>

@endsection