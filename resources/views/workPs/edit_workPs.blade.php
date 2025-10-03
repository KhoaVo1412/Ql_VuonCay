@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-text-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0">Chỉnh sửa: {{ $proposal->proposaName }}</h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('workps.index') }}">Danh Sách</a></li>
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
            <form id="form-edit-proposal" action="{{ route('workps.update', $proposal->id) }}" method="POST">
                @csrf
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row gy-2">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Mã Phân Công</label>
                                    <select class="form-control" name="taskID" required>
                                        @foreach($gentasks as $task)
                                        <option value="{{ $task->id }}" {{ $task->id == $proposal->taskID ? 'selected' :
                                            '' }}>
                                            {{ $task->code }} - {{$task->workName}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Tên Phiếu</label>
                                    <input type="text" class="form-control" name="proposaName"
                                        value="{{ $proposal->proposaName }}" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Ngày Gửi</label>
                                    <input type="date" class="form-control" name="proposalDate"
                                        value="{{ $proposal->proposalDate }}" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Ngày Nhận</label>
                                    <input type="date" class="form-control" name="approvalDate"
                                        value="{{ $proposal->approvalDate }}" required>
                                </div>
                            </div>

                        </div>

                        <hr>
                        <h5>Danh sách vật tư</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 15%;">Kho</th>
                                        <th style="width: 15%;">Tên vật tư</th>
                                        <th style="width: 10%;">Số lượng</th>
                                        <th style="width: 15%;">Đơn vị</th>
                                        <th style="width: 20%;">Người đề xuất</th>
                                        <th style="width: 15%;">Ghi chú</th>
                                        <th style="width: 5%;"></th>
                                    </tr>
                                </thead>
                                <tbody id="materialTableBody">
                                    @foreach($proposal->proposalProducts as $index => $item)
                                    <tr>
                                        <input type="hidden" name="proposalProducts[{{ $index }}][id]"
                                            value="{{ $item->id }}">
                                        <td>
                                            <select name="proposalProducts[{{ $index }}][warehouseID]"
                                                class="form-control">
                                                @foreach($warehouses as $w)
                                                <option value="{{ $w->id }}" {{ $w->id == $item->warehouseID ?
                                                    'selected' : '' }}>
                                                    {{ $w->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="proposalProducts[{{ $index }}][productID]"
                                                class="form-control" data-prev="{{ $item->productID ?? '' }}">
                                                <option value="">Chọn vật tư (theo kho)</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="proposalProducts[{{ $index }}][quantity]"
                                                class="form-control" value="{{ $item->materialQuantity }}" required>
                                        </td>
                                        <td>
                                            <select name="proposalProducts[{{ $index }}][unitID]" class="form-control"
                                                data-prev="{{ $item->unitID ?? $item->unit?->id ?? '' }}">
                                                <option value="">Chọn đơn vị (theo tồn)</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="proposalProducts[{{ $index }}][proposer_name]"
                                                class="form-control" value="{{ auth()->user()->name }}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="proposalProducts[{{ $index }}][note]"
                                                class="form-control" value="{{ $item->note }}">
                                        </td>
                                        <td class="text-center d-flex justify-content-center align-items-center">
                                            <button type="button" class="btn btn-success btn-sm btn-add-row"
                                                style="margin: 0 2px 0 0" onclick="addMaterialRow()"
                                                title="Thêm dòng mới">
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
                        </div>
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-success">Cập nhật</button>
                            <a href="{{ route('workps.index') }}" class="btn btn-secondary">Hủy</a>
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
        const stockCache = new Map(); // key: warehouseID -> array
        async function fetchStocksByWarehouse(warehouseID){
            const key = String(warehouseID || '');
            if (stockCache.has(key)) return stockCache.get(key);
            if (!warehouseID) return [];
            try{
            const res = await fetch(API_STOCK(warehouseID));
            const data = await res.json();
            const list = Array.isArray(data) ? data : [];
            stockCache.set(key, list);
            return list;
            }catch(e){
            console.error('Load stock error:', e);
            return [];
            }
        }
        // ===== Utils =====
        const tbody = document.getElementById('materialTableBody');
        function nf(n){ try { return new Intl.NumberFormat('vi-VN').format(+n||0); } catch { return n; } }
        function esc(s){ return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
        function getRowEls(row){
            return {
            whSel:   row.querySelector('select[name^="proposalProducts["][name$="[warehouseID]"]'),
            prodSel: row.querySelector('select[name^="proposalProducts["][name$="[productID]"]'),
            unitSel: row.querySelector('select[name^="proposalProducts["][name$="[unitID]"], select[name^="proposalProducts["][name$="[unit]"]'),
            qtyInp:  row.querySelector('input[name^="proposalProducts["][name$="[quantity]"]'),
            };
        }
        function aggregateProducts(list){
            const map = new Map();
            list.forEach(s=>{
            const k = String(s.productID);
            const cur = map.get(k) || { name: s.productName||'', qty: 0 };
            cur.qty += (+s.quantity||0);
            cur.name = cur.name || (s.productName||'');
            map.set(k, cur);
            });
            return map;
        }
        function aggregateUnitsForProduct(list, productID){
            const map = new Map(); // unitID -> {unitName, qty}
            list.filter(s => String(s.productID) === String(productID))
                .forEach(s=>{
                const k = String(s.unitID);
                const cur = map.get(k) || { unitName: s.unitName||'', qty: 0 };
                cur.qty += (+s.quantity||0);
                cur.unitName = cur.unitName || (s.unitName||'');
                map.set(k, cur);
                });
            return map;
        }
        function fillProducts(row, list){
            const { prodSel } = getRowEls(row);
            if (!prodSel) return;
            if (!list.length){
            prodSel.innerHTML = `<option value="">Kho chưa có tồn</option>`;
            prodSel.disabled = true;
            return;
            }
            const agg = aggregateProducts(list);
            let html = `<option value="">Chọn vật tư</option>`;
            agg.forEach((v, pid)=>{
            html += `<option value="${esc(pid)}">${esc(v.name)}</option>`;
            // html += `<option value="${esc(pid)}">${esc(v.name)} (tồn: ${esc(nf(v.qty))})</option>`;
            });
            const prev = prodSel.getAttribute('data-prev');
            prodSel.disabled = false;
            prodSel.innerHTML = html;
            if (prev && agg.has(prev)) prodSel.value = prev;
        }
        function fillUnits(row, unitAgg){
            const { unitSel } = getRowEls(row);
            if (!unitSel) return;
            if (!unitAgg || unitAgg.size === 0){
            unitSel.innerHTML = `<option value="">Không có đơn vị tồn</option>`;
            unitSel.disabled = true;
            unitSel.removeAttribute('data-prev');
            return;
            }
            let html = `<option value="">Chọn đơn vị</option>`;
            unitAgg.forEach((v, unitID)=>{
            // html += `<option value="${esc(unitID)}" data-qty="${esc(v.qty)}">${esc(v.unitName)} (tồn: ${esc(nf(v.qty))})</option>`;
            html += `<option value="${esc(unitID)}" data-qty="${esc(v.qty)}">${esc(v.unitName)}</option>`;
            });
            const prev = unitSel.getAttribute('data-prev');
            unitSel.disabled = false;
            unitSel.innerHTML = html;
            if (prev && unitAgg.has(prev)) unitSel.value = prev;
        }
        // function applyQtyMax(row){
        //     const { unitSel, qtyInp } = getRowEls(row);
        //     if (!unitSel || !qtyInp) return;
        //     const opt = unitSel.selectedOptions[0];
        //     const max = opt ? (opt.dataset.qty || '') : '';
        //     if (max) qtyInp.max = max; else qtyInp.removeAttribute('max');
        // }
        async function refreshRow(row){
            const { whSel, prodSel } = getRowEls(row);
            if (!whSel?.value){
            if (prodSel){ prodSel.innerHTML = `<option value="">Chọn kho trước</option>`; prodSel.disabled = true; }
            resetUnit(row, 'Chọn vật tư trước');
            return;
            }
            const list = await fetchStocksByWarehouse(whSel.value);
            fillProducts(row, list);
            const chosenProd = prodSel?.value || prodSel?.getAttribute('data-prev') || '';
            if (!chosenProd){ resetUnit(row, 'Chọn vật tư trước'); return; }
            const unitAgg = aggregateUnitsForProduct(list, chosenProd);
            fillUnits(row, unitAgg);
            // applyQtyMax(row);
        }
        function resetUnit(row, placeholder){
            const { unitSel, qtyInp } = getRowEls(row);
            if (unitSel){
            unitSel.innerHTML = `<option value="">${placeholder||'Chọn đơn vị'}</option>`;
            unitSel.disabled = true;
            unitSel.removeAttribute('data-prev');
            }
            if (qtyInp) qtyInp.removeAttribute('max');
        }
        document.addEventListener('change', function(e){
            if (e.target.matches('#materialTableBody select[name^="proposalProducts["][name$="[warehouseID]"]')) {
            const row = e.target.closest('tr');
            const { prodSel, unitSel } = getRowEls(row);
            if (prodSel) prodSel.setAttribute('data-prev', prodSel.value || prodSel.getAttribute('data-prev') || '');
            if (unitSel) unitSel.setAttribute('data-prev', unitSel.value || unitSel.getAttribute('data-prev') || '');
            refreshRow(row);
            }
        });
        document.addEventListener('change', async function(e){
            if (e.target.matches('#materialTableBody select[name^="proposalProducts["][name$="[productID]"]')) {
            const row = e.target.closest('tr');
            const { whSel, prodSel, unitSel } = getRowEls(row);
            if (!whSel?.value) return;
            if (unitSel) unitSel.setAttribute('data-prev', unitSel.value || unitSel.getAttribute('data-prev') || '');
            const list = await fetchStocksByWarehouse(whSel.value);
            const unitAgg = aggregateUnitsForProduct(list, prodSel.value);
            fillUnits(row, unitAgg);
            // applyQtyMax(row);
            }
        });
        document.addEventListener('change', function(e){
            if (e.target.matches('#materialTableBody select[name^="proposalProducts["][name$="[unitID]"], #materialTableBody select[name^="proposalProducts["][name$="[unit]"]')) {
            const row = e.target.closest('tr');
            const sel = e.target;
            sel.setAttribute('data-prev', sel.value || '');
            // applyQtyMax(row);
            }
        });
        function nextRowIndex(){
            let max = -1;
            tbody.querySelectorAll('select[name^="proposalProducts["][name$="[productID]"]').forEach(sel=>{
            const m = sel.name.match(/^proposalProducts\[(\d+)\]\[productID\]$/);
            if (m) max = Math.max(max, parseInt(m[1], 10));
            });
            return max + 1;
        }
        window.addMaterialRow = function addMaterialRow(){
            const idx = nextRowIndex();
            const tr = document.createElement('tr');
            tr.innerHTML = `
            <input type="hidden" name="proposalProducts[${idx}][id]" value="">
            <td>
                <select name="proposalProducts[${idx}][warehouseID]" class="form-control" required>
                <option value="">Chọn kho</option>
                @foreach($warehouses as $w)
                    <option value="{{ $w->id }}">{{ $w->name }}</option>
                @endforeach
                </select>
            </td>
            <td>
                <select name="proposalProducts[${idx}][productID]" class="form-control" data-prev="">
                <option value="">Chọn vật tư (theo kho)</option>
                </select>
            </td>
            <td>
                <input type="number" name="proposalProducts[${idx}][quantity]" class="form-control"
                    placeholder="Số lượng" step="0.000001" min="0.000001" required>
            </td>
            <td>
                <select name="proposalProducts[${idx}][unitID]" class="form-control" data-prev="">
                <option value="">Chọn đơn vị (theo tồn)</option>
                </select>
            </td>
            <td>
                <input type="text" name="proposalProducts[${idx}][proposer_name]"
                    class="form-control" value="{{ auth()->user()->name }}" readonly>
            </td>
            <td>
                <input type="text" name="proposalProducts[${idx}][note]" class="form-control" placeholder="Ghi chú">
            </td>
            <td class="text-center d-flex justify-content-center align-items-center">
                <button type="button" class="btn btn-success btn-sm btn-add-row"
                        style="margin: 0 2px 0 0" onclick="addMaterialRow()" title="Thêm dòng mới">
                <i class="fas fa-plus"></i>
                </button>
                <button type="button" class="btn btn-danger btn-sm btn-remove-row"
                        onclick="removeMaterialRow(this)">
                <i class="fas fa-trash"></i>
                </button>
            </td>
            `;
            tbody.appendChild(tr);
        };
        window.removeMaterialRow = function removeMaterialRow(button){
            if (confirm('Bạn có chắc muốn xóa dòng này?')) {
            button.closest('tr')?.remove();
            }
        };
        // ===== Init =====
        document.addEventListener('DOMContentLoaded', async function(){
            window.rowIndex = {{ count($proposal->proposalProducts) }};
            tbody.querySelectorAll('tr').forEach(row=>{
            const { prodSel, unitSel } = getRowEls(row);
            if (prodSel) prodSel.setAttribute('data-prev', prodSel.getAttribute('data-prev') || prodSel.value || '');
            if (unitSel) unitSel.setAttribute('data-prev', unitSel.getAttribute('data-prev') || unitSel.value || '');
            });
            for (const row of tbody.querySelectorAll('tr')) {
            await refreshRow(row);
            }
        });
        })();
</script>
<style>
    @media (max-width: 500px) {
        .table thead th {
            font-size: 14px;
            padding: 8px 6px;
        }

        .table td {
            padding: 6px;
        }

        .table .form-control,
        .table .form-select,
        .table .editable-input,
        .table .editable-select {
            font-size: 14px;
            height: 42px;
            padding: 0px 0px;
            min-width: 0;
        }

        .table input[readonly] {
            background: #f8f9fa;
        }

        .table .btn-sm {
            padding: 6px 10px;
            font-size: 14px;
            line-height: 1.2;
        }
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-responsive .table {
        min-width: 680px;
    }
</style>
@endsection