@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0">Chỉnh sửa Phiếu Phân Rã</h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('decomposes.index') }}">Danh sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sửa Phiếu Phân Rã</li>
                </ol>
            </nav>
        </div>
    </div>
    @include('layouts.alert')

    <div class="row">
        <div class="col-xl-12">
            <form id="form-decompose" action="{{ route('decomposes.update', $decomposes->id) }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row modal-body gy-4">
                            <div class="form-section">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="medicalRecordId" class="form-label required">Mã Phiếu</label>
                                        <input type="text" id="medicalRecordId" class="form-input" name="code"
                                            value="{{ $decomposes->code }}" placeholder="Nhập mã phiếu" required>
                                    </div>
                                    <div class="form-group wide">
                                        <label for="medicalRecordName" class="form-label required">Tên Phiếu</label>
                                        <input type="text" id="medicalRecordName" class="form-input" name="name"
                                            value="{{ $decomposes->name }}" placeholder="Nhập tên phiếu" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label required">Kho</label>
                                        <select id="warehouseID" name="warehouseID" class="form-input" required>
                                            <option value="">Chọn kho</option>
                                            @foreach($warehouses as $wh)
                                            <option value="{{ $wh->id }}" {{ $decomposes->warehouseID == $wh->id ?
                                                'selected' : '' }}>{{ $wh->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group wide">
                                        <label for="diseaseName" class="form-label required">Người Tạo</label>
                                        <input type="text" class="form-input" value="{{ $decomposes->user->name }}"
                                            placeholder="" required>
                                    </div>
                                    <input type="hidden" name="userID" value="{{ $decomposes->userID }}">
                                    <div class="form-group">
                                        <label for="date" class="form-label">Ngày Tạo</label>
                                        <input type="date" id="date" name="date" class="form-input"
                                            value="{{ $decomposes->date }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group" style="flex: 1;">
                                        <label class="form-label">Ghi Chú</label>
                                        <textarea class="form-input" name="desc" id="desc" placeholder="Ghi chú..."
                                            style="min-height: 80px; resize: vertical;"
                                            required>{{ $decomposes->desc }}</textarea>
                                    </div>
                                </div>
                                <div class="material-section">
                                    <div class="section-title">
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <i class="fas fa-exchange-alt"></i> Phân Rã Vật Tư
                                        </div>
                                        <button type="button" class="btn-add-new-material"
                                            onclick="addNewMaterialContainer()">
                                            <i class="fas fa-plus"></i> Thêm
                                        </button>
                                    </div>

                                    <div id="materialContainers">
                                        @foreach($decomposes->items as $index => $material)
                                        <div class="material-replacement-container"
                                            data-container-id="{{ $index + 1 }}">
                                            <div class="container-number">{{ $index + 1 }}</div>
                                            <button type="button" class="btn-remove-container"
                                                onclick="removeMaterialContainer({{ $index + 1 }})">
                                                <i class="fas fa-times"></i>
                                            </button>

                                            {{-- NGUYÊN LIỆU CẦN ĐỔI --}}
                                            <div class="replacement-section">
                                                <div class="replacement-title">Nguyên Liệu Cần Đổi</div>
                                                <div class="material-row">
                                                    <div class="form-group">
                                                        <label class="form-label">Tên vật tư</label>
                                                        <select class="material-select original-product-select"
                                                            name="materials[{{ $index }}][productID]" required>
                                                            <option value="">Chọn tên vật tư</option>
                                                            @foreach($products as $product)
                                                            <option value="{{ $product->id }}"
                                                                data-unit="{{ $product->unitID }}"
                                                                data-unit-name="{{ $product->unit->name ?? '' }}" {{
                                                                $material->productID == $product->id ? 'selected' : ''
                                                                }}
                                                                >
                                                                {{ $product->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>

                                                        {{-- NEW: lưu stockID tìm theo product + unit gốc + warehouse
                                                        --}}
                                                        <input type="hidden" name="materials[{{ $index }}][oldStockID]"
                                                            value="{{ $material->oldStockID ?? '' }}">
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="form-label">Đơn vị (gốc)</label>
                                                        {{-- JS sẽ khóa về đúng 1 option theo product --}}
                                                        <select class="material-select original-unit-select"
                                                            name="materials[{{ $index }}][productUnitID]" required>
                                                            <option value="">Chọn đơn vị</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="form-label">Số lượng (trừ)</label>
                                                        <input type="number" class="material-input"
                                                            name="materials[{{ $index }}][quantityProduct]" min="0"
                                                            value="{{ $material->quantityProduct }}" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="section-divider"></div>

                                            {{-- VẬT TƯ MỚI (PHÂN RÃ) --}}
                                            <div class="replacement-section">
                                                <div class="replacement-title">Vật Tư Mới (Phân rã)</div>
                                                <div class="material-row">
                                                    <div class="form-group">
                                                        <label class="form-label">Tên vật tư</label>
                                                        <select class="material-select new-product-select"
                                                            name="materials[{{ $index }}][productDecomposeID]" required>
                                                            <option value="">Chọn tên vật tư</option>
                                                            @foreach($products as $product)
                                                            <option value="{{ $product->id }}" {{ $material->
                                                                productDecomposeID == $product->id ? 'selected' : '' }}>
                                                                {{ $product->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="form-label">Đơn vị mới</label>
                                                        {{-- JS sẽ đổ mọi đơn vị trừ đơn vị gốc và preselect =
                                                        unitDecomposeID --}}
                                                        <select class="material-select new-unit-select"
                                                            name="materials[{{ $index }}][unitDecomposeID]" required
                                                            data-preselected="{{ $material->unitDecomposeID }}">
                                                            <option value="">Chọn đơn vị</option>
                                                            @foreach($units as $unit)
                                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="form-label">Số lượng (cộng)</label>
                                                        <input type="number" class="material-input"
                                                            name="materials[{{ $index }}][quantityDecompose]" min="0"
                                                            value="{{ $material->quantityDecompose }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="prism-toggle d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-success" id="submit-btn-decompose">Lưu Thông
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
    const __stockCache = new Map(); // key = warehouseID -> list stocks
  function currentWarehouseId() {
    const wh = document.querySelector('select[name="warehouseID"]');
    return wh ? wh.value : '';
  }
  async function fetchStocks(warehouseID) {
    const key = String(warehouseID || '');
    if (__stockCache.has(key)) return __stockCache.get(key);
    const res = await fetch(`/api/stock-items?warehouseID=${encodeURIComponent(warehouseID||'')}`);
    const list = await res.json();
    __stockCache.set(key, list);
    return list;
  }
  function idxStocks(list) {
    // key = `${productID}:${unitID}` -> { stockID, qty, unitName }
    const m = new Map();
    list.forEach(s => m.set(`${s.productID}:${s.unitID}`, { stockID: s.stockID, qty: +s.quantity || 0, unitName: s.unitName }));
    return m;
  }
  function nf(n){ try { return new Intl.NumberFormat('vi-VN').format(n); } catch { return n; } }
  // ===== Helpers UI =====
  function showToast(type, text) {
    if (typeof showMessage === 'function') return showMessage(type, text);
    console[type === 'error' ? 'error' : 'log'](text);
  }
  function setUnitFor(scopeEl, unitFieldSuffix, unitId, unitName) {
    const unitSelect = scopeEl.querySelector(`select[name$='[${unitFieldSuffix}]']`);
    if (!unitSelect) return;
    unitSelect.innerHTML = unitId
      ? `<option value="${unitId}" selected>${unitName || 'Đơn vị'}</option>`
      : `<option value="">Chọn đơn vị</option>`;
  }
  // Lấy toàn bộ units từ 1 select "new-unit-select"
  function renderAllUnits() {
    const first = document.querySelector('.new-unit-select');
    return first ? Array.from(first.options).slice(1).map(o => ({ id:o.value, name:o.textContent })) : [];
  }
  function fillUnitsExcept(unitSelect, exceptUnitId, preselectValue = null) {
    const all = renderAllUnits();
    const filtered = all.filter(u => String(u.id) !== String(exceptUnitId));
    unitSelect.innerHTML = '<option value="">Chọn đơn vị</option>' +
      (filtered.length ? filtered.map(u => `<option value="${u.id}">${u.name}</option>`).join('')
                       : '<option value="" disabled>Không có đơn vị khác</option>');
    if (preselectValue && filtered.some(u => String(u.id) === String(preselectValue))) {
      unitSelect.value = String(preselectValue);
    }
  }
  // Trang trí label product trái thành "Tên (tồn)"
  function decorateOriginalProductSelect(selectEl, stockIndex) {
    Array.from(selectEl.options).forEach((opt,i)=>{
      if (i===0) return; // placeholder
      const base = opt.dataset.labelBase || opt.textContent.trim();
      if (!opt.dataset.labelBase) opt.dataset.labelBase = base;
      const unitId = opt.dataset.unit || '';
      const key = `${opt.value}:${unitId}`;
      const info = stockIndex.get(key);
      const qty = info ? info.qty : 0;
      opt.textContent = `${base} (${nf(qty)})`;
    });
  }
  function syncLeftContainer(container, stockIndex) {
    const prodSel = container.querySelector('.original-product-select');
    if (!prodSel) return;
    const row = prodSel.closest('.material-row');
    const opt = prodSel.selectedOptions[0];
    const unitId   = opt?.dataset.unit || '';
    const unitName = opt?.dataset.unitName || '';
    // Chọn đơn vị gốc
    setUnitFor(row, 'productUnitID', unitId, unitName);
    // Gán oldStockID + max qty
    const hiddenOld = container.querySelector('input[name$="[oldStockID]"]');
    const qtyInput  = container.querySelector('input[name$="[quantityProduct]"]');
    const key  = `${opt?.value || ''}:${unitId}`;
    const info = stockIndex.get(key);
    if (hiddenOld) hiddenOld.value = info?.stockID || '';
    if (qtyInput)  qtyInput.max    = info ? info.qty : '';
  }
  // Refresh toàn form (khi đổi kho / khởi tạo)
  async function refreshAll() {
    const stocks = await fetchStocks(currentWarehouseId());
    const stockIndex = idxStocks(stocks);
    document.querySelectorAll('.original-product-select').forEach(sel => decorateOriginalProductSelect(sel, stockIndex));
    document.querySelectorAll('.material-replacement-container').forEach(c => {
      syncLeftContainer(c, stockIndex);
      //filter đơn vị ≠ đơn vị gốc, và giữ preselect nếu có
      const leftUnitId   = c.querySelector('.original-product-select')?.selectedOptions?.[0]?.dataset?.unit || '';
      const rightUnitSel = c.querySelector('.new-unit-select');
      const preSel = rightUnitSel?.dataset?.preselected || null;
      if (rightUnitSel) fillUnitsExcept(rightUnitSel, leftUnitId, preSel);
    });
  }
  // ===== Events =====
  // Chọn product trái
  document.addEventListener('change', async (e)=>{
    if (!e.target.matches('.original-product-select')) return;
    const container = e.target.closest('.material-replacement-container');
    const stocks = await fetchStocks(currentWarehouseId());
    const stockIndex = idxStocks(stocks);
    // Cập nhật label cho chính select này
    decorateOriginalProductSelect(e.target, stockIndex);
    // Sync container
    syncLeftContainer(container, stockIndex);
    // Cập nhật đơn vị bên phải (loại trừ đơn vị gốc), giữ preselect nếu có
    const leftUnitId   = e.target.selectedOptions?.[0]?.dataset?.unit || '';
    const rightUnitSel = container.querySelector('.new-unit-select');
    const preSel = rightUnitSel?.value || rightUnitSel?.dataset?.preselected || null;
    if (rightUnitSel) fillUnitsExcept(rightUnitSel, leftUnitId, preSel);
  });
  // Chọn product phải → chỉ cần refilter đơn vị ≠ đơn vị gốc, giữ value hiện tại nếu hợp lệ
  document.addEventListener('change', (e)=>{
    if (!e.target.matches('.new-product-select')) return;
    const container   = e.target.closest('.material-replacement-container');
    const leftUnitId  = container.querySelector('.original-product-select')?.selectedOptions?.[0]?.dataset?.unit || '';
    const rightUnitSel= container.querySelector('.new-unit-select');
    const preSel      = rightUnitSel?.value || rightUnitSel?.dataset?.preselected || null;
    if (rightUnitSel) fillUnitsExcept(rightUnitSel, leftUnitId, preSel);
  });
  // Đổi kho → clear cache + refreshAll
  document.addEventListener('change', (e)=>{
    if (!e.target.matches('select[name="warehouseID"]')) return;
    __stockCache.clear();
    refreshAll();
  });
  // ===== Thêm/Xoá/Reset dòng (giống trang add, nhưng dùng Blade để render options) =====
  let containerCounter = {{ count($decomposes->items) }};
  async function addNewMaterialContainer() {
    containerCounter++;
    const containersDiv = document.getElementById('materialContainers');
    const div = document.createElement('div');
    div.className = 'material-replacement-container';
    div.setAttribute('data-container-id', containerCounter);
    div.innerHTML = `
      <div class="container-number">${containerCounter}</div>
      <button type="button" class="btn-remove-container" onclick="removeMaterialContainer(${containerCounter})">
        <i class="fas fa-times"></i>
      </button>
      <div class="replacement-section">
        <div class="replacement-title">Nguyên Liệu Cần Đổi</div>
        <div class="material-row">
          <div class="form-group">
            <label class="form-label">Tên vật tư</label>
            <select class="material-select original-product-select" name="materials[${containerCounter}][productID]" required>
              <option value="">Chọn tên vật tư</option>
              @foreach($products as $product)
                <option value="{{ $product->id }}" data-unit="{{ $product->unitID }}" data-unit-name="{{ $product->unit->name ?? '' }}">
                  {{ $product->name }}
                </option>
              @endforeach
            </select>
            <input type="hidden" name="materials[${containerCounter}][oldStockID]">
          </div>
          <div class="form-group">
            <label class="form-label">Đơn vị (gốc)</label>
            <select class="material-select original-unit-select" name="materials[${containerCounter}][productUnitID]" required>
              <option value="">Chọn đơn vị</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Số lượng (trừ)</label>
            <input type="number" class="material-input" name="materials[${containerCounter}][quantityProduct]" placeholder="0" min="0" required>
          </div>
        </div>
      </div>
      <div class="section-divider"></div>
      <div class="replacement-section">
        <div class="replacement-title">Vật Tư Mới (Phân rã)</div>
        <div class="material-row">
          <div class="form-group">
            <label class="form-label">Tên vật tư</label>
            <select class="material-select new-product-select" name="materials[${containerCounter}][productDecomposeID]" required>
              <option value="">Chọn tên vật tư</option>
              @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Đơn vị (khác đơn vị gốc)</label>
            <select class="material-select new-unit-select" name="materials[${containerCounter}][unitDecomposeID]" required>
              <option value="">Chọn đơn vị</option>
              @foreach($units as $unit)
                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Số lượng (cộng)</label>
            <input type="number" class="material-input" name="materials[${containerCounter}][quantityDecompose]" placeholder="0" min="0" required>
          </div>
        </div>
      </div>
      <div style="text-align:center;margin-top:15px;">
        <button type="button" class="btn-reset-materials" onclick="resetMaterialContainer(${containerCounter})" title="Làm mới bảng này">
          <i class="fas fa-redo"></i>
        </button>
      </div>
    `;
    containersDiv.appendChild(div);
    const stocks = await fetchStocks(currentWarehouseId());
    const stockIndex = idxStocks(stocks);
    decorateOriginalProductSelect(div.querySelector('.original-product-select'), stockIndex);
    syncLeftContainer(div, stockIndex);
    updateRemoveButtons();
    const firstInput = div.querySelector('.original-product-select');
    if (firstInput) firstInput.focus();
    showToast('success', `Đã thêm bảng vật tư mới #${containerCounter}`);
  }
  function removeMaterialContainer(containerId) {
    const containers = document.querySelectorAll('.material-replacement-container');
    if (containers.length <= 1) {
      showToast('error', 'Phải có ít nhất một bảng vật tư!');
      return;
    }
    if (!confirm('Bạn có chắc muốn xóa bảng vật tư này?')) return;
    const el = document.querySelector(`.material-replacement-container[data-container-id="${containerId}"]`);
    if (!el) return;
    el.remove();
    updateContainerNumbers();
    updateRemoveButtons();
    showToast('success', 'Đã xóa bảng vật tư!');
  }
  function resetMaterialContainer(containerId) {
    if (!confirm('Bạn có chắc muốn làm mới bảng vật tư này?')) return;
    const container = document.querySelector(`.material-replacement-container[data-container-id="${containerId}"]`);
    if (!container) return;
    container.querySelectorAll('.material-input').forEach(i => i.value = '');
    container.querySelectorAll('.material-select').forEach(s => {
      s.selectedIndex = 0;
      if (s.classList.contains('original-unit-select') || s.classList.contains('new-unit-select')) {
        s.innerHTML = '<option value="">Chọn đơn vị</option>';
      }
    });
    const hiddenOld = container.querySelector('input[name$="[oldStockID]"]');
    if (hiddenOld) hiddenOld.value = '';
    refreshAll();
    showToast('success', 'Đã làm mới bảng vật tư!');
  }
  function updateContainerNumbers() {
    const containers = document.querySelectorAll('.material-replacement-container');
    containers.forEach((c, i) => {
      c.querySelector('.container-number').textContent = i + 1;
      c.setAttribute('data-container-id', i + 1);
      const removeBtn = c.querySelector('.btn-remove-container');
      if (removeBtn) removeBtn.setAttribute('onclick', `removeMaterialContainer(${i + 1})`);
      const resetBtn = c.querySelector('.btn-reset-materials');
      if (resetBtn) resetBtn.setAttribute('onclick', `resetMaterialContainer(${i + 1})`);
    });
    containerCounter = containers.length;
  }
  function updateRemoveButtons() {
    document.querySelectorAll('.material-replacement-container').forEach((c, i) => {
      const btn = c.querySelector('.btn-remove-container');
      if (btn) btn.style.display = (i === 0 ? 'none' : 'flex');
    });
  }
  // ===== Init =====
  document.addEventListener('DOMContentLoaded', ()=>{
    updateRemoveButtons();
    refreshAll(); // nạp tồn, trang trí "Tên (tồn)", khoá unit trái, set oldStockID + max, filter unit phải
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
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 20px;
        color: #333;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        animation: slideUp 0.6s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Header */
    .form-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .header-title {
        font-size: 1.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.3);
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }

    .status-pending {
        background: #f59e0b;
    }

    .status-approved {
        background: #10b981;
    }

    .status-rejected {
        background: #ef4444;
    }

    /* Form Content */
    .form-content {
        padding: 30px;
    }

    /* Top Section */
    .top-section {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
        align-items: end;
    }

    .form-group {
        position: relative;
    }

    .form-label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }

    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #fafafa;
    }

    .form-input:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .btn-view-receipt {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 12px 20px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }

    .btn-view-receipt:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
    }

    /* Material Section */
    .material-section {
        margin-bottom: 30px;
    }

    .section-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #059669;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Add New Material Button */
    .btn-add-new-material {
        background: #10b981;
        color: white;
        border: none;
        border-radius: 10px;
        padding: 12px 20px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .btn-add-new-material:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    }

    /* Material Replacement Container */
    .material-replacement-container {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        position: relative;
        animation: slideInContainer 0.5s ease-out;
    }

    @keyframes slideInContainer {
        from {
            opacity: 0;
            transform: translateY(-20px) scale(0.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .replacement-section {
        margin-bottom: 25px;
    }

    .replacement-title {
        font-size: 1rem;
        font-weight: 600;
        color: #059669;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .material-row {
        display: grid;
        grid-template-columns: 1fr 2fr 1fr 1fr auto;
        gap: 15px;
        align-items: end;
        padding: 15px;
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .material-row:hover {
        border-color: #10b981;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.1);
    }

    .material-input {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        background: #fafafa;
    }

    .material-input:focus {
        outline: none;
        border-color: #10b981;
        background: white;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .material-select {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        background: #fafafa;
        cursor: pointer;
    }

    .material-select:focus {
        outline: none;
        border-color: #10b981;
        background: white;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .btn-add-material {
        background: #10b981;
        color: white;
        border: none;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        font-size: 1.2rem;
    }

    .btn-add-material:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    }

    .btn-remove-container {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
    }

    .btn-remove-container:hover {
        background: #dc2626;
        transform: scale(1.1);
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
    }

    .btn-reset-materials {
        background: #6b7280;
        color: white;
        border: none;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 1rem;
    }

    .btn-reset-materials:hover {
        background: #4b5563;
        transform: scale(1.1);
    }

    /* Original Material Table */
    .material-table-container {
        border: 1px solid #e5e7eb;
        border-radius: 15px;
        overflow: hidden;
        background: white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        margin-top: 20px;
    }

    .material-table {
        width: 100%;
        border-collapse: collapse;
    }

    .material-table th {
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        padding: 15px 12px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
        font-size: 0.9rem;
    }

    .material-table td {
        padding: 15px 12px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: top;
    }

    .material-table tr:hover {
        background: #f8fafc;
    }

    .material-table tr:last-child td {
        border-bottom: none;
    }

    /* Reason Section */
    .reason-section {
        margin-bottom: 30px;
        padding: 20px;
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 12px;
    }

    .reason-textarea {
        width: 100%;
        min-height: 100px;
        padding: 15px;
        border: 1px solid #fecaca;
        border-radius: 12px;
        font-size: 0.95rem;
        font-family: inherit;
        resize: vertical;
        transition: all 0.3s ease;
        background: white;
    }

    .reason-textarea:focus {
        outline: none;
        border-color: #ef4444;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
    }

    /* Action Buttons */
    .action-section {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 10px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-approve {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .btn-approve:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    }

    .btn-reject {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    }

    .btn-reject:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
    }

    /* Messages */
    .message {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        transform: translateX(400px);
        transition: transform 0.5s ease;
        z-index: 1000;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .message.show {
        transform: translateX(0);
    }

    .message.success {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .message.error {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        body {
            padding: 10px;
        }

        .container {
            border-radius: 10px;
            margin: 0;
        }

        .form-header {
            padding: 20px 15px;
            flex-direction: column;
            text-align: center;
        }

        .header-title {
            font-size: 1.2rem;
        }

        .form-content {
            padding: 20px 15px;
        }

        .top-section {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .material-row {
            grid-template-columns: 1fr;
            gap: 10px;
            text-align: center;
        }

        .action-section {
            flex-direction: column;
            gap: 10px;
        }

        .btn {
            width: 100%;
            justify-content: center;
            padding: 15px 20px;
            font-size: 1rem;
        }
    }

    /* Divider between sections */
    .section-divider {
        height: 2px;
        background: linear-gradient(90deg, transparent, #d1fae5, transparent);
        margin: 20px 0;
        position: relative;
    }

    .section-divider::after {
        content: "↓";
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        background: #10b981;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        font-weight: bold;
    }

    .container-number {
        position: absolute;
        top: -10px;
        left: 20px;
        background: #10b981;
        color: white;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.9rem;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }
</style>
@endsection