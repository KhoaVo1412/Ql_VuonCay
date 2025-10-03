@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0">
            Thêm Đề Xuất
        </h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{route('workps.index')}}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm Đề Xuất</li>
                </ol>
            </nav>
        </div>
    </div>
    <style>
        .select2-search__field {
            height: 26px;
        }

        .select2-container .select2-results__option {
            color: black;
        }

        .select2-container .select2-results__option[aria-disabled="true"] {
            color: #ccc;
            font-weight: normal;
        }
    </style>
    @if(session('error'))
    <div class="alert alert-danger">
        <ul>
            <li>{{ session('error') }}</li>
        </ul>
    </div>
    @endif
    <div class="row">
        <div class="col-xl-12">
            <form id="form-workers" action="{{route('workps.save')}}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-body">
                        <div id="batch-container">
                            <div class="row gy-2">
                                <div class="container pt-3">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Mã Công Việc</label>
                                            <select type="text" class="form-input" name="taskID" id="taskID" required>
                                                <option class="form-control">Chọn mã công việc</option>
                                                @foreach($gentasks as $task)
                                                <option value="{{ $task->id }}">{{ $task->code }} - {{ $task->workName
                                                    }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Tên Phiếu</label>
                                            <input type="text" class="form-input" name="proposaName"
                                                placeholder="Tên phiếu" value="{{old('proposaName')}}" required>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Ngày Gửi</label>
                                            <input type="date" class="form-input" id="sendDate" name="proposalDate"
                                                value="{{old('proposalDate')}}" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Ngày Nhận</label>
                                            <input type="date" class="form-input" id="receiveDate" name="approvalDate"
                                                value="{{old('approvalDate')}}" required>
                                        </div>
                                    </div>
                                </div>
                                <!-- Content Section -->
                                <div class="content-section">
                                    <div class="material-section">
                                        <div class="section-header">
                                            <div class="section-title form-label"
                                                style="margin-bottom: 0; padding-bottom: 0; border-bottom: none;">Đề
                                                Xuất Vật Tư</div>
                                            <button type="button" class="btn-add-row" onclick="addMaterialRow()"
                                                title="Thêm dòng mới">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="material-table-container">
                                            <table class="material-table" id="materialTable">
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
                                                    <tr>
                                                        <td class="editable-cell">
                                                            <select name="items[0][warehouseID]"
                                                                class="editable-select">
                                                                <option value="">Chọn kho</option>
                                                                @foreach($warehouses as $w)
                                                                <option value="{{ $w->id }}">{{ $w->name
                                                                    }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td class="editable-cell">
                                                            <select name="items[0][productID]" class="editable-select">
                                                                <option value="">Chọn vật tư (theo kho)</option>
                                                                <!-- JS sẽ fill từ tồn kho -->
                                                            </select>
                                                        </td>

                                                        <td class="editable-cell">
                                                            <input type="text" name="items[0][quantity]"
                                                                class="editable-input" placeholder="Số lượng">
                                                        </td>
                                                        <td class="editable-cell">
                                                            <select name="items[0][unit]" class="editable-select">
                                                                <option value="">Chọn đơn vị (theo tồn)</option>
                                                                <!-- JS sẽ fill theo vật tư trong kho -->
                                                            </select>
                                                        </td>

                                                        <td class="editable-cell">
                                                            <input type="hidden" name="items[0][created_by]"
                                                                value="{{ auth()->id() }}">
                                                            <input type="text" name="items[0][proposer_name]"
                                                                class="editable-input"
                                                                value="{{ auth()->user()->name }}" readonly>
                                                        </td>
                                                        <td class="editable-cell">
                                                            <input type="text" name="items[0][note]"
                                                                class="editable-input" placeholder="Ghi chú">
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <button type="button" class="btn-remove-row"
                                                                onclick="removeMaterialRow(this)">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <!-- Empty state (hidden by default) -->
                                            <div class="empty-table" id="emptyState" style="display: none;">
                                                <i class="fas fa-inbox" style="margin-right: 8px;"></i>
                                                Chưa có vật tư nào được đề xuất
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Action Buttons -->
                                    <div class="action-buttons">
                                        <button class="btnnn btn-approve" type="submit">
                                            <i class="fas fa-check"></i>
                                            Tạo Đề Xuất
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <script>
            (function(){
            const API_STOCK = (wh) => `/api/stock-items?warehouseID=${encodeURIComponent(wh||'')}`;
            const stockCache = new Map(); // key = warehouseID -> [{stockID,warehouseID,productID,productName,unitID,unitName,quantity}, ...]
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

            // Utils
            const tbody = document.getElementById('materialTableBody');
            function nf(n){ try { return new Intl.NumberFormat('vi-VN').format(+n||0); } catch { return n; } }
            function esc(s){ return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }

            function getRowEls(row){
                return {
                whSel:   row.querySelector('select[name^="items["][name$="[warehouseID]"]'),
                prodSel: row.querySelector('select[name^="items["][name$="[productID]"]'),
                unitSel: row.querySelector('select[name^="items["][name$="[unit]"], select[name^="items["][name$="[unitID]"]'),
                qtyInp:  row.querySelector('input[name^="items["][name$="[quantity]"]'),
                };
            }

            // Đổ danh sách sản phẩm từ stock (gộp theo productID, cộng tồn)
            function fillProductsFromStock(row, stockList){
                const { prodSel } = getRowEls(row);
                if (!prodSel) return;

                if (!stockList || !stockList.length){
                prodSel.innerHTML = `<option value="">Kho chưa có tồn</option>`;
                prodSel.disabled = true;
                return;
                }
                // Gộp theo productID
                const agg = new Map(); // productID -> {name, qty}
                stockList.forEach(s => {
                const k = String(s.productID);
                const cur = agg.get(k) || { name: s.productName || '', qty: 0 };
                cur.qty += (+s.quantity || 0);
                cur.name = cur.name || (s.productName || '');
                agg.set(k, cur);
                });

                let html = `<option value="">Chọn vật tư</option>`;
                agg.forEach((v, pid) => {
                html += `<option value="${esc(pid)}">${esc(v.name)}</option>`;
                // html += `<option value="${esc(pid)}">${esc(v.name)} (tồn: ${esc(nf(v.qty))})</option>`;
                });

                prodSel.disabled = false;
                prodSel.innerHTML = html;
            }
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
            // Đổ đơn vị theo stock (cho product đã chọn)
            function fillUnitsFromStock(row, unitAgg){
                const { unitSel } = getRowEls(row);
                if (!unitSel) return;
                if (!unitAgg || unitAgg.size === 0){
                unitSel.innerHTML = `<option value="">Không có đơn vị tồn</option>`;
                unitSel.disabled = true;
                unitSel.removeAttribute('data-prev');
                return;
                }
                let html = `<option value="">Chọn đơn vị</option>`;
                unitAgg.forEach((v, unitID) => {
                html += `<option value="${esc(unitID)}" data-qty="${esc(v.qty)}">${esc(v.unitName)}</option>`;
                // html += `<option value="${esc(unitID)}" data-qty="${esc(v.qty)}">${esc(v.unitName)} (tồn: ${esc(nf(v.qty))})</option>`;
                });
                unitSel.disabled = false;
                unitSel.innerHTML = html;
                // Giữ lựa chọn cũ nếu hợp lệ
                const prev = unitSel.getAttribute('data-prev');
                if (prev && unitAgg.has(prev)) unitSel.value = prev;
                else unitSel.selectedIndex = 0;
            }
            // function applyQtyMaxFromUnit(row){
            //     const { unitSel, qtyInp } = getRowEls(row);
            //     if (!unitSel || !qtyInp) return;
            //     const opt = unitSel.selectedOptions[0];
            //     const maxQty = opt ? (opt.dataset.qty || '') : '';
            //     if (maxQty) qtyInp.max = maxQty; else qtyInp.removeAttribute('max');
            // }
            // Làm mới 1 dòng theo kho + product
            async function refreshRowFromStock(row){
                const { whSel, prodSel } = getRowEls(row);
                if (!whSel?.value){
                if (prodSel){ prodSel.innerHTML = `<option value="">Chọn kho trước</option>`; prodSel.disabled = true; }
                resetUnit(row, 'Chọn vật tư trước');
                return;
                }
                const list = await fetchStocksByWarehouse(whSel.value);
                fillProductsFromStock(row, list);
                const prevProd = prodSel.getAttribute('data-prev');
                if (prevProd) {
                const hasPrev = list.some(s => String(s.productID) === String(prevProd));
                prodSel.value = hasPrev ? prevProd : '';
                }
                await refreshUnitsForProduct(row, list);
            }
            async function refreshUnitsForProduct(row, stockList){
                const { prodSel } = getRowEls(row);
                if (!prodSel?.value){
                resetUnit(row, 'Chọn vật tư trước');
                return;
                }
                const unitAgg = aggregateUnitsForProduct(stockList, prodSel.value);
                fillUnitsFromStock(row, unitAgg);
                // applyQtyMaxFromUnit(row);
            }
            function resetUnit(row, placeholder){
                const { unitSel, qtyInp } = getRowEls(row);
                if (unitSel){
                unitSel.innerHTML = `<option value="">${placeholder || 'Chọn đơn vị'}</option>`;
                unitSel.disabled = true;
                unitSel.removeAttribute('data-prev');
                }
                if (qtyInp) qtyInp.removeAttribute('max');
            }
            // Sự kiện: đổi kho → refresh product + unit
            document.addEventListener('change', function(e){
                if (e.target.matches('#materialTableBody select[name^="items["][name$="[warehouseID]"]')) {
                const row = e.target.closest('tr');
                const { prodSel, unitSel } = getRowEls(row);
                if (prodSel) prodSel.setAttribute('data-prev', prodSel.value || '');
                if (unitSel) unitSel.setAttribute('data-prev', unitSel.value || '');
                refreshRowFromStock(row);
                }
            });
            // Sự kiện: đổi sản phẩm → refresh unit
            document.addEventListener('change', async function(e){
                if (e.target.matches('#materialTableBody select[name^="items["][name$="[productID]"]')) {
                const row = e.target.closest('tr');
                const { whSel, prodSel, unitSel } = getRowEls(row);
                if (!whSel?.value) return;
                if (unitSel) unitSel.setAttribute('data-prev', unitSel.value || '');

                const list = await fetchStocksByWarehouse(whSel.value);
                await refreshUnitsForProduct(row, list);
                }
            });
            // Sự kiện: đổi đơn vị → set max cho số lượng
            document.addEventListener('change', function(e){
                if (e.target.matches('#materialTableBody select[name^="items["][name$="[unit]"], #materialTableBody select[name^="items["][name$="[unitID]"]')) {
                const row = e.target.closest('tr');
                const sel = e.target;
                sel.setAttribute('data-prev', sel.value || '');
                // applyQtyMaxFromUnit(row);
                }
            });
            // Thêm / Xóa dòng
            function nextRowIndex(){
                let max = -1;
                tbody.querySelectorAll('select[name^="items["][name$="[productID]"]').forEach(sel=>{
                const m = sel.name.match(/^items\[(\d+)\]\[productID\]$/);
                if (m) max = Math.max(max, parseInt(m[1], 10));
                });
                return max + 1;
            }
            window.addMaterialRow = async function addMaterialRow(){
                const idx = nextRowIndex();
                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td class="editable-cell">
                    <select name="items[${idx}][warehouseID]" class="editable-select">
                    <option value="">Chọn kho</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                    @endforeach
                    </select>
                </td>
                <td class="editable-cell">
                    <select name="items[${idx}][productID]" class="editable-select" data-prev="">
                    <option value="">Chọn vật tư (theo kho)</option>
                    </select>
                </td>
                <td class="editable-cell">
                    <input type="text" name="items[${idx}][quantity]" class="editable-input" placeholder="Số lượng">
                </td>
                <td class="editable-cell">
                    <select name="items[${idx}][unit]" class="editable-select" data-prev="">
                    <option value="">Chọn đơn vị (theo tồn)</option>
                    </select>
                </td>
                <td class="editable-cell">
                    <input type="hidden" name="items[${idx}][created_by]" value="{{ auth()->id() }}">
                    <input type="text" name="items[${idx}][proposer_name]" class="editable-input" value="{{ auth()->user()->name }}" readonly>
                </td>
                <td class="editable-cell">
                    <input type="text" name="items[${idx}][note]" class="editable-input" placeholder="Ghi chú">
                </td>
                <td style="text-align: center;">
                    <button type="button" class="btn-remove-row" onclick="removeMaterialRow(this)">
                    <i class="fas fa-trash"></i>
                    </button>
                </td>
                `;
                tbody.appendChild(tr);
                // chưa chọn kho thì để trống; sau khi chọn kho, JS sẽ fill
            };

            window.removeMaterialRow = function removeMaterialRow(btn){
                if (!confirm('Bạn có chắc muốn xóa dòng này?')) return;
                btn.closest('tr')?.remove();
            };

            // Khởi tạo cho dòng đầu: nếu người dùng chọn kho → tự fill
            document.addEventListener('DOMContentLoaded', async function(){
                const firstRow = tbody.querySelector('tr');
                if (firstRow){
                await refreshRowFromStock(firstRow); // nếu kho có sẵn value => fill luôn
                }
            });

            })();
        </script>

    </div>
</section>
<script>
    $(document).ready(function() {
            $('#taskID').select2({
                language: "vi",
                placeholder: "Chọn mã phân công",
                allowClear: true,
                minimumResultsForSearch: 0,
                width: '100%',
            });
            $('#type_of_pus_id').select2({
                language: "vi",
                placeholder: "Chọn Loại Mủ",
                allowClear: true,
                minimumResultsForSearch: 0,
                width: '100%',
            });
            $('#batch_code').select2({
                language: "vi",
                placeholder: "Chọn Lô",
                allowClear: true,
                minimumResultsForSearch: 0,
                width: '100%',
            });
        });
</script>

<style>
    .form-label {
        font-weight: bold;
    }

    .select2-container--default .select2-selection--single {
        height: 37px;
    }

    .table input {
        width: 100%;
        min-width: 150px;
        height: 40px;
        font-size: 16px;
    }

    .read-only-locked {
        background-color: #e9ecef;
        pointer-events: none;
        /* không thể click */
        border-color: #ced4da;
        color: #495057;
    }

    .status-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #374151;
        text-align: center;
        margin-bottom: 20px;
    }

    .header-row {
        display: grid;
        grid-template-columns: auto 1fr 1fr;
        gap: 20px;
        align-items: center;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-input:read-only {
        background: #f9fafb;
        color: #6b7280;
    }

    .btn-receipt-name {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 10px 16px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: left;
        min-width: 120px;
    }

    .btn-receipt-name:hover {
        background: #e5e7eb;
        border-color: #9ca3af;
    }

    /* Content Section */
    /* .content-section {
        padding: 30px;
    } */

    /* Material Request Section */
    .material-section {
        margin-bottom: 10px;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e5e7eb;
    }

    .material-table-container {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        background: white;
        min-height: 200px;
    }

    .material-table {
        width: 100%;
        border-collapse: collapse;
    }

    .material-table th {
        background: #f9fafb;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        font-size: 0.85rem;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
    }

    .material-table td {
        padding: 12px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        color: #6b7280;
    }

    .material-table tr:hover {
        background: #f9fafb;
    }

    .material-table tr:last-child td {
        border-bottom: none;
    }

    /* Empty state */
    .empty-table {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 150px;
        color: #9ca3af;
        font-style: italic;
    }

    /* Reason Section */
    .reason-section {
        margin-bottom: 30px;
    }

    .reason-textarea {
        width: 100%;
        min-height: 120px;
        padding: 15px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.9rem;
        font-family: inherit;
        resize: vertical;
        transition: all 0.2s ease;
        background: white;
    }

    .reason-textarea:focus {
        outline: none;
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .reason-textarea::placeholder {
        color: #9ca3af;
        font-style: italic;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }

    .btnnn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 100px;
        justify-content: center;
    }

    .btn-approve {
        background: #10b981;
        color: white;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }

    .btn-approve:hover {
        background: #059669;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }

    .btn-reject {
        background: #ef4444;
        color: white;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
    }

    .btn-reject:hover {
        background: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Loading Animation */
    .loading {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, .3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Success/Error Messages */
    .message {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        transform: translateX(400px);
        transition: transform 0.5s ease;
        z-index: 1000;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .message.show {
        transform: translateX(0);
    }

    .message.success {
        background: #10b981;
    }

    .message.error {
        background: #ef4444;
    }

    /* Status Badge */
    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-pending {
        background: #fef3c7;
        color: #d97706;
    }

    .status-approved {
        background: #dcfce7;
        color: #166534;
    }

    .status-rejected {
        background: #fecaca;
        color: #991b1b;
    }

    /* Responsive Design */
    @media (max-width: 768px) {



        .btn-receipt-name {
            width: 100%;
            text-align: center;
        }

        .material-table {
            font-size: 0.8rem;
        }

        .material-table th,
        .material-table td {
            padding: 8px;
        }

        .action-buttons {
            flex-direction: column;
            gap: 10px;
        }

        .btnT {
            width: 100%;
        }

        .message {
            left: 10px;
            right: 10px;
            transform: translateY(-100px);
            top: 10px;
        }

        .message.show {
            transform: translateY(0);
        }
    }

    /* Print Styles */
    @media print {

        .action-buttons,
        .reason-section {
            display: none;
        }
    }

    /* Modal for Receipt Name */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .modal.show {
        opacity: 1;
        visibility: visible;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        padding: 30px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .modal-header {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 20px;
        color: #374151;
    }

    .modal-body {
        margin-bottom: 20px;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn-modal {
        padding: 8px 16px;
        border: none;
        border-radius: 6px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-modal.primary {
        background: #3b82f6;
        color: white;
    }

    .btn-modal.secondary {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    /* Add Row Button */
    .btn-add-row {
        background: #10b981;
        color: white;
        border: none;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        font-size: 0.9rem;
    }

    .btn-add-row:hover {
        background: #059669;
        transform: scale(1.1);
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
    }

    /* Section header with add button */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e5e7eb;
    }

    /* Editable table cells */
    .editable-cell {
        position: relative;
    }

    .editable-input,
    .editable-select {
        width: 100%;
        border: 1px solid #7e8490;
        background: transparent;
        padding: 8px;
        border-radius: 4px;
        transition: all 0.2s ease;
        font-size: 0.9rem;
    }

    .editable-input:focus,
    .editable-select:focus {
        border-color: #10b981;
        background: white;
        outline: none;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.1);
    }

    .editable-select {
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 8px center;
        background-repeat: no-repeat;
        background-size: 16px;
        padding-right: 30px;
        appearance: none;
    }

    /* Remove row button */
    .btn-remove-row {
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 4px 8px;
        cursor: pointer;
        font-size: 0.75rem;
        transition: all 0.2s ease;
    }

    .btn-remove-row:hover {
        background: #dc2626;
    }

    @media (max-width: 500px) {
        .material-table-container {
            width: 100%;
            overflow-x: auto;
            /* cho phép kéo ngang trên mobile */
            -webkit-overflow-scrolling: touch;
        }

        .material-table {
            min-width: 640px;
        }

        .material-table th,
        .material-table td {
            padding: 6px 8px;
        }

        .editable-select,
        .editable-input {
            width: 100%;
            min-width: 0;
        }
    }
</style>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const today = new Date().toISOString().split('T')[0]; // yyyy-mm-dd
    document.getElementById("sendDate").value = today;
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection