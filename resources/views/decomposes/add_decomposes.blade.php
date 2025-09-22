@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0">Tạo Phiếu Phân Rã</h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('decomposes.index') }}">Danh sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tạo Phiếu Phân Rã</li>
                </ol>
            </nav>
        </div>
    </div>
    @include('layouts.alert')

    <div class="row">
        <div class="col-xl-12">
            <form id="form-decompose" action="{{ route('decomposes.save') }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row modal-body gy-4">
                            <div class="form-section">
                                <!-- Second Row -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="medicalRecordId" class="form-label required">Mã Phiếu</label>
                                        <input type="text" id="medicalRecordId" class="form-input" name="code"
                                            placeholder="Nhập mã phiếu" required>
                                    </div>
                                    <div class="form-group wide">
                                        <label for="medicalRecordName" class="form-label required">Tên Phiếu</label>
                                        <input type="text" id="medicalRecordName" class="form-input" name="name"
                                            placeholder="Nhập tên phiếu" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="" class="form-label required">Kho</label>
                                        <select id="warehouseID" name="warehouseID" class="form-input" required>
                                            <option value="">Chọn kho</option>
                                            @foreach($warehouses as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group wide">
                                        <label for="diseaseName" class="form-label required">Người Tạo</label>
                                        <input type="text" class="form-input" value="{{ Auth::user()->name }}"
                                            placeholder="" required>
                                    </div>
                                    <input type="hidden" name="userID" value="{{ Auth::id() }}">
                                    <div class="form-group">
                                        <label for="date" class="form-label">Ngày Tạo</label>
                                        <input type="date" id="date" name="date" class="form-input" placeholder="">
                                    </div>
                                </div>

                                <!-- Fourth Row -->
                                <div class="form-row">
                                    <div class="form-group" style="flex: 1;">
                                        <label class="form-label">Ghi Chú</label>
                                        <textarea class="form-input" name="desc" id="desc" placeholder="Ghi chú..."
                                            style="min-height: 80px; resize: vertical;" required></textarea>
                                    </div>
                                </div>

                                <div id="materialContainers">
                                    <div class="material-replacement-container" data-container-id="1">
                                        <div class="container-number">1</div>
                                        <button type="button" class="btn-remove-container"
                                            onclick="removeMaterialContainer(1)" style="display:none">
                                            <i class="fas fa-times"></i>
                                        </button>

                                        <!-- Vật Tư Cần Đổi -->
                                        <div class="replacement-section">
                                            <div class="replacement-title">Vật Tư Cần Đổi</div>
                                            <div class="material-row">
                                                <div class="form-group">
                                                    <label class="form-label">Tên vật tư</label>
                                                    <select class="material-select original-product-select"
                                                        name="materials[0][productID]" required>
                                                        <option value="">Chọn tên vật tư</option>
                                                        @foreach($products as $p)
                                                        <option value="{{ $p->id }}" data-unit="{{ $p->unitID }}"
                                                            data-unit-name="{{ $p->unit->name ?? '' }}">
                                                            {{ $p->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    <!-- NEW: lưu stockID tìm được theo product + unit gốc + warehouse -->
                                                    <input type="hidden" name="materials[0][oldStockID]">
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label">Đơn vị (gốc)</label>
                                                    <select class="material-select original-unit-select"
                                                        name="materials[0][productUnitID]" required>
                                                        <option value="">Chọn đơn vị</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label">Số lượng (trừ)</label>
                                                    <input type="number" class="material-input"
                                                        name="materials[0][quantityProduct]" placeholder="0" min="0"
                                                        required>
                                                </div>

                                                <div class="form-group">
                                                    <button type="button" class="btn-add-material"
                                                        onclick="addNewMaterialContainer()" title="Thêm bảng mới">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="section-divider"></div>

                                        <!-- Vật Tư Mới (Phân rã) -->
                                        <div class="replacement-section">
                                            <div class="replacement-title">Vật Tư Mới (Phân rã)</div>
                                            <div class="material-row">
                                                <div class="form-group">
                                                    <label class="form-label">Tên vật tư</label>
                                                    <select class="material-select new-product-select"
                                                        name="materials[0][productDecomposeID]" required>
                                                        <option value="">Chọn tên vật tư</option>
                                                        @foreach($products as $p)
                                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label">Đơn vị (khác đơn vị gốc)</label>
                                                    <select class="material-select new-unit-select"
                                                        name="materials[0][unitDecomposeID]" required>
                                                        <option value="">Chọn đơn vị</option>
                                                        @foreach($units as $u)
                                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label">Số lượng (cộng)</label>
                                                    <input type="number" class="material-input"
                                                        name="materials[0][quantityDecompose]" placeholder="0" min="0"
                                                        required>
                                                </div>

                                                <div class="form-group">
                                                    <button type="button" class="btn-add-material"
                                                        onclick="addNewMaterialContainer()" title="Thêm bảng mới">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div style="text-align:center;margin-top:15px;">
                                            <button type="button" class="btn-reset-materials"
                                                onclick="resetMaterialContainer(1)" title="Làm mới bảng này">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        </div>
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
    // ====== Cache & helpers cho tồn kho ======
  const __stockCache = new Map(); // key = warehouseID -> list stocks
  function currentWarehouseId(){
    const wh = document.querySelector('select[name="warehouseID"]');
    return wh ? wh.value : '';
  }
  async function fetchStocks(warehouseID){
    const key = String(warehouseID || '');
    if (__stockCache.has(key)) return __stockCache.get(key);
    const res = await fetch(`/api/stock-items?warehouseID=${encodeURIComponent(warehouseID||'')}`);
    const list = await res.json();
    __stockCache.set(key, list);
    return list;
  }
  function formatQty(q){ try { return new Intl.NumberFormat('vi-VN').format(q); } catch { return q; } }

  // Map nhanh: key = `${productID}:${unitID}` -> { stockID, quantity, unitName }
  function indexStocks(list){
    const idx = new Map();
    list.forEach(s => {
      idx.set(`${s.productID}:${s.unitID}`, { stockID: s.stockID, quantity: +s.quantity || 0, unitName: s.unitName });
    });
    return idx;
  }

  // ====== Fallback renderers (nếu chưa có) ======
  window.renderProductOptions = window.renderProductOptions || function () {
    const first = document.querySelector('.material-replacement-container select.original-product-select');
    return first ? Array.from(first.options).slice(1).map(o =>
      `<option value="${o.value}" ${o.dataset?.unit ? `data-unit="${o.dataset.unit}"` : ''} ${o.dataset?.unitName ? `data-unit-name="${o.dataset.unitName}"` : ''}>${o.textContent}</option>`
    ).join('') : '';
  };
  window.renderUnitOptions = window.renderUnitOptions || function () {
    const first = document.querySelector('.material-replacement-container select.new-unit-select');
    return first ? Array.from(first.options).slice(1).map(o =>
      `<option value="${o.value}">${o.textContent}</option>`
    ).join('') : '';
  };

  // ====== UI helpers ======
  function showToast(type, text){
    if (typeof showMessage === 'function') return showMessage(type, text);
    console[type === 'error' ? 'error' : 'log'](text);
  }
  function setUnitFor(scopeEl, unitFieldSuffix, unitId, unitName){
    const unitSelect = scopeEl.querySelector(`select[name$='[${unitFieldSuffix}]']`);
    if (!unitSelect) return;
    unitSelect.innerHTML = unitId
      ? `<option value="${unitId}" selected>${unitName || 'Đơn vị'}</option>`
      : `<option value="">Chọn đơn vị</option>`;
  }
  // Điền đơn vị bên phải: mọi đơn vị trừ unit gốc
  function fillUnitsExcept(unitSelect, exceptUnitId){
    const parser = document.createElement('select');
    parser.innerHTML = `<option value=""></option>${renderUnitOptions()}`;
    const opts = Array.from(parser.options).slice(1);
    const filtered = opts.filter(o => String(o.value) !== String(exceptUnitId));
    unitSelect.innerHTML = '<option value="">Chọn đơn vị</option>' +
      (filtered.length ? filtered.map(o => `<option value="${o.value}">${o.textContent}</option>`).join('')
                       : '<option value="" disabled>Không có đơn vị khác</option>');
  }

  // ====== Trang trí TÊN VẬT TƯ bên trái thành "Tên (tồn)" ======
  function decorateOriginalProductSelect(selectEl, stockIdx){
    Array.from(selectEl.options).forEach((opt, i) => {
      if (i === 0) return; // bỏ placeholder
      const base = opt.dataset.labelBase || opt.textContent.trim();
      if (!opt.dataset.labelBase) opt.dataset.labelBase = base;

      const unitId = opt.dataset.unit || ''; // đơn vị gốc của product
      const key    = `${opt.value}:${unitId}`;
      const info   = stockIdx.get(key);
      const qty    = info ? info.quantity : 0;
      opt.textContent = `${base} (${formatQty(qty)})`;
    });
  }
  function decorateAllOriginalProductSelects(stockIdx){
    document.querySelectorAll('.original-product-select').forEach(sel => decorateOriginalProductSelect(sel, stockIdx));
  }

  // ====== Đồng bộ khi chọn sản phẩm bên trái ======
  function syncLeftFromSelection(container, stockIdx){
    const prodSel   = container.querySelector('.original-product-select');
    const leftRow   = prodSel?.closest('.material-row');
    const qtyInput  = container.querySelector('input[name$="[quantityProduct]"]');
    const hiddenOld = container.querySelector('input[name$="[oldStockID]"]');
    if (!prodSel || !leftRow || !qtyInput || !hiddenOld) return;

    const opt      = prodSel.selectedOptions[0];
    const productId= opt?.value || '';
    const unitId   = opt?.dataset.unit || '';
    const unitName = opt?.dataset.unitName || '';

    // Khoá “Đơn vị (gốc)”
    setUnitFor(leftRow, 'productUnitID', unitId, unitName);

    // Tìm stock theo product + unit + warehouse
    const key  = `${productId}:${unitId}`;
    const info = stockIdx.get(key);

    hiddenOld.value = info?.stockID || '';
    qtyInput.max    = (info?.quantity ?? 0);
  }

  // ====== Làm mới dữ liệu tồn + trang trí + đồng bộ mọi container ======
  async function refreshStocksAndDecorate(){
    const list    = await fetchStocks(currentWarehouseId());
    const stockIdx= indexStocks(list);

    decorateAllOriginalProductSelects(stockIdx);

    document.querySelectorAll('.material-replacement-container').forEach(c => {
      syncLeftFromSelection(c, stockIdx);
      // cập nhật đơn vị bên phải: loại trừ unit gốc
      const leftUnitId   = c.querySelector('.original-product-select')?.selectedOptions?.[0]?.dataset?.unit || '';
      const rightUnitSel = c.querySelector('.new-unit-select');
      if (rightUnitSel) fillUnitsExcept(rightUnitSel, leftUnitId);
    });
  }

  // ====== Event bindings ======
  // Chọn product (trái)
  document.addEventListener('change', async function(e){
    if (!e.target.matches('.original-product-select')) return;
    const container = e.target.closest('.material-replacement-container');

    // Lấy index stock mới nhất rồi sync dòng hiện tại
    const list     = await fetchStocks(currentWarehouseId());
    const stockIdx = indexStocks(list);

    // Trang trí lại select (đảm bảo option hiển thị số tồn)
    decorateOriginalProductSelect(e.target, stockIdx);

    // Đồng bộ đơn vị gốc + stockID + max qty
    syncLeftFromSelection(container, stockIdx);

    // Đơn vị bên phải: loại trừ đơn vị gốc
    const leftUnitId   = e.target.selectedOptions?.[0]?.dataset?.unit || '';
    const rightUnitSel = container.querySelector('.new-unit-select');
    if (rightUnitSel) fillUnitsExcept(rightUnitSel, leftUnitId);
  });

  // Chọn product (phải) → chỉ cần lọc đơn vị ≠ đơn vị gốc
  document.addEventListener('change', function(e){
    if (!e.target.matches('.new-product-select')) return;
    const container   = e.target.closest('.material-replacement-container');
    const leftUnitId  = container.querySelector('.original-product-select')?.selectedOptions?.[0]?.dataset?.unit || '';
    const rightUnitSel= container.querySelector('.new-unit-select');
    if (rightUnitSel) fillUnitsExcept(rightUnitSel, leftUnitId);
  });

  // Đổi kho → xóa cache + refresh toàn bộ
  document.addEventListener('change', function(e){
    if (!e.target.matches('select[name="warehouseID"]')) return;
    __stockCache.clear();
    refreshStocksAndDecorate();
  });

  // ====== Thêm / Xoá / Reset container ======
  let nextIndex = document.querySelectorAll('.material-replacement-container').length || 1;
  let nextId    = Math.max(0, ...Array.from(document.querySelectorAll('.material-replacement-container'))
                          .map(c => +c.dataset.containerId || 0), 0) + 1;

  async function addNewMaterialContainer(){
    const wrap = document.getElementById('materialContainers');
    if (!wrap) { showToast('error','Thiếu #materialContainers trong DOM'); return; }
    const index = nextIndex, id = nextId;

    const div = document.createElement('div');
    div.className = 'material-replacement-container';
    div.setAttribute('data-container-id', id);
    div.innerHTML = `
      <div class="container-number">${wrap.children.length + 1}</div>
      <button type="button" class="btn-remove-container" onclick="removeMaterialContainer(${id})"><i class="fas fa-times"></i></button>

      <div class="replacement-section">
        <div class="replacement-title">Vật Tư Cần Đổi</div>
        <div class="material-row">
          <div class="form-group">
            <label class="form-label">Tên vật tư</label>
            <select class="material-select original-product-select" name="materials[${index}][productID]" required>
              <option value="">Chọn tên vật tư</option>
              ${renderProductOptions()}
            </select>
            <input type="hidden" name="materials[${index}][oldStockID]">
          </div>
          <div class="form-group">
            <label class="form-label">Đơn vị (gốc)</label>
            <select class="material-select original-unit-select" name="materials[${index}][productUnitID]" required>
              <option value="">Chọn đơn vị</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Số lượng (trừ)</label>
            <input type="number" class="material-input" name="materials[${index}][quantityProduct]" placeholder="0" min="0" required>
          </div>
        </div>
      </div>

      <div class="section-divider"></div>

      <div class="replacement-section">
        <div class="replacement-title">Vật Tư Mới (Phân rã)</div>
        <div class="material-row">
          <div class="form-group">
            <label class="form-label">Tên vật tư</label>
            <select class="material-select new-product-select" name="materials[${index}][productDecomposeID]" required>
              <option value="">Chọn tên vật tư</option>
              ${renderProductOptions()}
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Đơn vị (khác đơn vị gốc)</label>
            <select class="material-select new-unit-select" name="materials[${index}][unitDecomposeID]" required>
              <option value="">Chọn đơn vị</option>
              ${renderUnitOptions()}
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Số lượng (cộng)</label>
            <input type="number" class="material-input" name="materials[${index}][quantityDecompose]" placeholder="0" min="0" required>
          </div>
        </div>
      </div>

      <div style="text-align:center;margin-top:15px;">
        <button type="button" class="btn-reset-materials" onclick="resetMaterialContainer(${id})" title="Làm mới bảng này">
          <i class="fas fa-redo"></i>
        </button>
      </div>
    `;
    wrap.appendChild(div);

    // Trang trí tên vật tư (thêm số tồn) + sync dòng mới
    const list     = await fetchStocks(currentWarehouseId());
    const stockIdx = indexStocks(list);
    decorateOriginalProductSelect(div.querySelector('.original-product-select'), stockIdx);
    syncLeftFromSelection(div, stockIdx);

    // Bên phải: loại trừ đơn vị gốc (nếu đã chọn bên trái)
    const leftUnitId   = div.querySelector('.original-product-select')?.selectedOptions?.[0]?.dataset?.unit || '';
    const rightUnitSel = div.querySelector('.new-unit-select');
    if (rightUnitSel) fillUnitsExcept(rightUnitSel, leftUnitId);

    updateRemoveButtons();
    updateDisplayNumbers();

    nextIndex++; nextId++;
  }

  function removeMaterialContainer(containerId){
    const list = document.querySelectorAll('.material-replacement-container');
    if (list.length <= 1) return showToast('error','Phải có ít nhất một bảng vật tư!');
    const el = document.querySelector(`.material-replacement-container[data-container-id="${containerId}"]`);
    if (!el) return;
    if (!confirm('Bạn có chắc muốn xóa bảng vật tư này?')) return;
    el.remove();
    updateRemoveButtons();
    updateDisplayNumbers();
  }

  function resetMaterialContainer(containerId){
    const el = document.querySelector(`.material-replacement-container[data-container-id="${containerId}"]`);
    if (!el) return;
    if (!confirm('Bạn có chắc muốn làm mới bảng vật tư này?')) return;

    // Clear inputs/selects
    el.querySelectorAll('.material-input').forEach(i => i.value='');
    el.querySelectorAll('.material-select').forEach(s => {
      s.selectedIndex = 0;
      if (s.classList.contains('original-unit-select') || s.classList.contains('new-unit-select')) {
        s.innerHTML = '<option value="">Chọn đơn vị</option>';
      }
    });
    const hiddenOld = el.querySelector('input[name$="[oldStockID]"]');
    if (hiddenOld) hiddenOld.value = '';

    // Re-sync để hiện số tồn kèm tên
    refreshStocksAndDecorate();
    showToast('success','Đã làm mới bảng vật tư!');
  }

  function updateRemoveButtons(){
    document.querySelectorAll('.material-replacement-container').forEach((c,i)=>{
      const btn = c.querySelector('.btn-remove-container');
      if (btn) btn.style.display = (i===0?'none':'flex');
    });
  }
  function updateDisplayNumbers(){
    document.querySelectorAll('.material-replacement-container .container-number')
      .forEach((n,i)=> n.textContent = i+1);
  }

  // Expose global
  window.addNewMaterialContainer = addNewMaterialContainer;
  window.removeMaterialContainer = removeMaterialContainer;
  window.resetMaterialContainer  = resetMaterialContainer;

  // Init
  document.addEventListener('DOMContentLoaded', ()=>{
    updateRemoveButtons();
    updateDisplayNumbers();
    refreshStocksAndDecorate(); // nạp tồn, trang trí "Tên (tồn)", sync max & stockID
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