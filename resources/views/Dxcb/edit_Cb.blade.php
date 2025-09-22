@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-text-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0">Chỉnh sửa: {{ $proposal->proposaName }}</h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('materialproposals.index') }}">Danh Sách</a></li>
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
            <form id="form-edit-proposal" action="{{ route('materialproposals.update', $proposal->id) }}" method="POST">
                @csrf
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row gy-2">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Mã phân công</label>
                                    <select class="form-control" name="diseaseplantID" required>
                                        @foreach($diseaseplants as $d)
                                        <option value="{{ $d->id }}" {{ $d->id == $proposal->diseaseplantID ?
                                            'selected' : ''
                                            }}>
                                            {{ $d->code }} - {{ $d->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Tên phiếu</label>
                                    <input type="text" class="form-control" name="proposaName"
                                        value="{{ $proposal->proposaName }}" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Ngày gửi</label>
                                    <input type="date" class="form-control" name="proposalDate"
                                        value="{{ $proposal->proposalDate->format('Y-m-d') }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Ngày nhận</label>
                                    <input type="date" class="form-control" name="approvalDate"
                                        value="{{ $proposal->approvalDate->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            {{-- <div class="form-group">
                                <label class="form-label">Tình Trạng</label>
                                <select name="status" class="form-control" required>
                                    <option value="Duyệt" {{ $proposal->status=='Duyệt' ? 'selected' : ''
                                        }}>Đã
                                        duyệt</option>
                                    <option value="Chờ duyệt" {{ $proposal->status=='Chờ duyệt' ? 'selected' :
                                        '' }}>Chờ
                                        duyệt</option>
                                    <option value="Từ chối" {{ $proposal->status=='Từ chối' ? 'selected' : ''
                                        }}>Từ chối
                                    </option>
                                </select>
                            </div> --}}
                        </div>

                        <hr>
                        <h5>Danh sách vật tư</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Kho</th>
                                    <th style="width: 20%;">Tên vật tư</th>
                                    <th style="width: 10%;">Số lượng</th>
                                    <th style="width: 15%;">Đơn vị</th>
                                    <th style="width: 20%;">Ghi chú</th>
                                    <th style="width: 10%;">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody id="materialTableBody">
                                @foreach($proposal->items as $index => $item)
                                <tr>
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                    <td>
                                        <select name="items[{{ $index }}][warehouseID]" class="form-control">
                                            @foreach($warehouses as $w)
                                            <option value="{{ $w->id }}" {{ $w->id == $item->warehouseID ?
                                                'selected' : '' }}>
                                                {{ $w->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="items[{{ $index }}][productID]" class="form-control">
                                            @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ $product->id == $item->productID ?
                                                'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $index }}][quantity]" class="form-control"
                                            value="{{ $item->materialQuantity }}" required>
                                    </td>
                                    <td>
                                        <select name="items[{{ $index }}][unitID]" class="form-control"
                                            data-prev="{{ old(" items.$index.unitID", $item->unitID ?? $item->unit?->id)
                                            }}">
                                            @foreach($units as $u)
                                            <option value="{{ $u->id }}" @selected(old("items.$index.unitID", $item->
                                                unitID ?? $item->unit?->id) == $u->id)>
                                                {{ $u->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="items[{{ $index }}][note]" class="form-control"
                                            value="{{ $item->note }}">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-sm btn-add-row"
                                            onclick="addMaterialRow()" title="Thêm dòng mới">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm btn-remove-row"
                                            onclick="removeMaterialRow(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-success">Cập nhật</button>
                            <a href="{{ route('materialproposals.index') }}" class="btn btn-secondary">Hủy</a>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</section>
<script>
    (function(){
    const API_STOCK = (wh) => `/api/stock-items?warehouseID=${encodeURIComponent(wh||'')}`;
    const stockCache = new Map();
    async function fetchStocksByWarehouse(warehouseID){
        const key = String(warehouseID || '');
        if (stockCache.has(key)) return stockCache.get(key);
        if (!warehouseID) return [];
        try {
        const res = await fetch(API_STOCK(warehouseID));
        const data = await res.json();
        stockCache.set(key, Array.isArray(data) ? data : []);
        return stockCache.get(key);
        } catch (e) {
        console.error('Load stock error:', e);
        return [];
        }
    }
    // ============ UTILS ============
    const tbody = document.getElementById('materialTableBody');
    function nf(n){ try { return new Intl.NumberFormat('vi-VN').format(+n||0); } catch { return n; } }
    function esc(s){ return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
    function getRowElems(row){
        return {
        whSel:   row.querySelector('select[name^="items["][name$="[warehouseID]"]'),
        prodSel: row.querySelector('select[name^="items["][name$="[productID]"]'),
        unitSel: row.querySelector('select[name^="items["][name$="[unitID]"], select[name^="items["][name$="[unit]"]'), // hỗ trợ cả 2 kiểu name
        qtyInp:  row.querySelector('input[name^="items["][name$="[quantity]"]'),
        };
    }
    // Gộp tồn theo unitID cho 1 product trong kho
    function aggregateUnitsForProduct(stockList, productID){
        const agg = new Map(); // unitID -> {unitName, qty}
        stockList
        .filter(s => String(s.productID) === String(productID))
        .forEach(s => {
            const k = String(s.unitID);
            const cur = agg.get(k) || { unitName: s.unitName || '', qty: 0 };
            cur.qty += (+s.quantity || 0);
            cur.unitName = cur.unitName || (s.unitName || '');
            agg.set(k, cur);
        });
        return agg;
    }
    function fillUnitOptionsFromStock(row, unitAggMap){
        const { unitSel } = getRowElems(row);
        if (!unitSel) return;
        if (!unitAggMap || unitAggMap.size === 0){
        unitSel.innerHTML = `<option value="">Không có tồn đơn vị trong kho</option>`;
        unitSel.disabled = true;
        unitSel.removeAttribute('data-prev');
        return;
        }
        let html = `<option value="">Chọn đơn vị</option>`;
        unitAggMap.forEach((v, unitID) => {
        html += `<option value="${esc(unitID)}" data-qty="${esc(v.qty)}">${esc(v.unitName)} (tồn: ${esc(nf(v.qty))})</option>`;
        });
        unitSel.disabled = false;
        unitSel.innerHTML = html;
        // Giữ lựa chọn cũ nếu còn hợp lệ
        const prev = unitSel.getAttribute('data-prev');
        if (prev && unitAggMap.has(prev)) {
        unitSel.value = prev;
        } else {
        unitSel.selectedIndex = 0;
        }
    }
    // function applyQtyMaxFromUnit(row){
    //     const { unitSel, qtyInp } = getRowElems(row);
    //     if (!unitSel || !qtyInp) return;
    //     const opt = unitSel.selectedOptions[0];
    //     const qty = opt ? (opt.dataset.qty || '') : '';
    //     if (qty) qtyInp.max = qty; else qtyInp.removeAttribute('max');
    // }
    // Nạp đơn vị từ stock cho 1 dòng (dựa vào kho + vật tư)
    async function refreshUnitsForRow(row){
        const { whSel, prodSel, unitSel, qtyInp } = getRowElems(row);
        // Reset state khi thiếu kho hoặc sản phẩm
        if (!whSel?.value) {
        if (unitSel) {
            unitSel.innerHTML = `<option value="">Chọn kho trước</option>`;
            unitSel.disabled = true;
        }
        if (qtyInp) qtyInp.removeAttribute('max');
        return;
        }
        if (!prodSel?.value) {
        if (unitSel) {
            unitSel.innerHTML = `<option value="">Chọn vật tư trước</option>`;
            unitSel.disabled = true;
        }
        if (qtyInp) qtyInp.removeAttribute('max');
        return;
        }
        const stockList = await fetchStocksByWarehouse(whSel.value);
        const unitAgg = aggregateUnitsForProduct(stockList, prodSel.value);
        fillUnitOptionsFromStock(row, unitAgg);
        // applyQtyMaxFromUnit(row);
    }
    // ============ EVENTS ============
    // Đổi kho / vật tư -> nạp lại đơn vị
    document.addEventListener('change', function(e){
        if (
        e.target.matches('#materialTableBody select[name^="items["][name$="[warehouseID]"]') ||
        e.target.matches('#materialTableBody select[name^="items["][name$="[productID]"]')
        ) {
        const row = e.target.closest('tr');
        const { unitSel } = getRowElems(row);
        // lưu lại lựa chọn hiện tại (nếu có) để giữ sau khi refresh
        if (unitSel) unitSel.setAttribute('data-prev', unitSel.value || '');
        refreshUnitsForRow(row);
        }
    });
    // Đổi đơn vị -> set max số lượng + nhớ lựa chọn
    document.addEventListener('change', function(e){
        if (e.target.matches('#materialTableBody select[name^="items["][name$="[unitID]"], #materialTableBody select[name^="items["][name$="[unit]"]')) {
        const row = e.target.closest('tr');
        const sel = e.target;
        sel.setAttribute('data-prev', sel.value || '');
        // applyQtyMaxFromUnit(row);
        }
    });
    // ============ THÊM / XÓA DÒNG ============
    function nextRowIndex(){
        let max = -1;
        tbody.querySelectorAll('select[name^="items["][name$="[productID]"]').forEach(sel => {
        const m = sel.name.match(/^items\[(\d+)\]\[productID\]$/);
        if (m) max = Math.max(max, parseInt(m[1], 10));
        });
        return max + 1;
    }
    window.addMaterialRow = async function addMaterialRow(){
        const idx = nextRowIndex();
        const tr = document.createElement('tr');
        tr.innerHTML = `
        <input type="hidden" name="items[${idx}][id]" value="">
        <td>
            <select name="items[${idx}][warehouseID]" class="form-control" required>
            <option value="">Chọn kho</option>
            @foreach($warehouses as $w)
                <option value="{{ $w->id }}">{{ $w->name }}</option>
            @endforeach
            </select>
        </td>
        <td>
            <select name="items[${idx}][productID]" class="form-control" required>
            <option value="">Chọn vật tư</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
            @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="items[${idx}][quantity]" class="form-control" placeholder="Số lượng"
                step="0.000001" min="0.000001" required>
        </td>
        <td>
            <select name="items[${idx}][unitID]" class="form-control" data-prev="">
            <option value="">Chọn đơn vị</option>
            </select>
        </td>
        <td>
            <input type="text" name="items[${idx}][note]" class="form-control" placeholder="Ghi chú">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-success btn-sm btn-add-row"
                    onclick="addMaterialRow()" title="Thêm dòng mới">
            <i class="fas fa-plus"></i>
            </button>
            <button type="button" class="btn btn-danger btn-sm btn-remove-row"
                    onclick="removeMaterialRow(this)">
            <i class="fas fa-trash"></i>
            </button>
        </td>
        `;
        tbody.appendChild(tr);
        await refreshUnitsForRow(tr);
        tr.querySelector('select[name^="items["][name$="[warehouseID]"]')?.focus();
        updateRemoveButtons();
    };

    window.removeMaterialRow = function removeMaterialRow(btn){
        if (!confirm('Bạn có chắc muốn xóa dòng này?')) return;
        btn.closest('tr')?.remove();
        updateRemoveButtons();
    };

    function updateRemoveButtons(){
        const rows = tbody.querySelectorAll('tr');
        rows.forEach((row) => {
        const removeBtn = row.querySelector('.btn-remove-row');
        if (removeBtn) removeBtn.style.display = (rows.length > 1 ? 'inline-block' : 'none');
        });
    }

    // ============ KHỞI TẠO ============
    document.addEventListener('DOMContentLoaded', async function(){
        // rowIndex nên = số dòng hiện có (để tiếp tục tăng đúng)
        window.rowIndex = {{ count($proposal->items) }};

        // Nạp lại đơn vị theo stock cho TẤT CẢ dòng đang có (giữ lựa chọn cũ nếu còn tồn)
        const rows = tbody.querySelectorAll('tr');
        for (const row of rows) {
        // lưu current unit để giữ lại sau khi refresh
        const { unitSel } = getRowElems(row);
        if (unitSel) unitSel.setAttribute('data-prev', unitSel.value || unitSel.getAttribute('data-prev') || '');
        await refreshUnitsForRow(row);
        }
        updateRemoveButtons();
    });
    })();
</script>

@endsection