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
                                <div class="container">
                                    <!-- Header Section -->
                                    <div class="header-section">
                                        {{-- <div class="status-title">
                                            Trạng thái (Đã duyệt, chưa duyệt, Từ chối)
                                            <span class="status-badge status-pending" id="statusBadge">Chờ duyệt</span>
                                        </div> --}}

                                        <div class="header-row">
                                            {{-- <div class="form-group">
                                                <label class="form-label">Phiếu</label>
                                                <button class="btn-receipt-name" onclick="showReceiptModal()">
                                                    Tên phiếu
                                                </button>
                                            </div> --}}
                                            <div class="form-group">
                                                <label class="form-label">Mã Công Việc</label>
                                                <select type="text" class="form-input" name="taskID" id="taskID"
                                                    required>
                                                    <option class="form-control">Chọn mã công việc</option>
                                                    @foreach($gentasks as $task)
                                                    <option value="{{ $task->id }}">{{ $task->code }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Tên Phiếu</label>
                                                <input type="text" class="form-input" name="proposaName"
                                                    placeholder="Tên phiếu" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Ngày Gửi</label>
                                                <input type="date" class="form-input" id="sendDate" name="proposalDate"
                                                    value="2025-06-10" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Ngày Nhận</label>
                                                <input type="date" class="form-input" id="receiveDate"
                                                    name="approvalDate" value="2025-06-15" required>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Content Section -->
                                    <div class="content-section">
                                        <div class="material-section">
                                            <div class="section-header">
                                                <div class="section-title"
                                                    style="margin-bottom: 0; padding-bottom: 0; border-bottom: none;">Đề
                                                    xuất vật tư</div>
                                                <button type="button" class="btn-add-row" onclick="addMaterialRow()"
                                                    title="Thêm dòng mới">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>

                                            <div class="material-table-container">
                                                <table class="material-table" id="materialTable">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 30%;">Tên vật tư</th>
                                                            <th style="width: 10%;">Số lượng</th>
                                                            <th style="width: 20%;">Công việc</th>
                                                            <th style="width: 20%;">Người đề xuất</th>
                                                            <th style="width: 15%;">Ghi chú</th>
                                                            <th style="width: 5%;"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="materialTableBody">
                                                        <!-- Editable rows -->
                                                        <tr>
                                                            <td class="editable-cell">
                                                                <select name="items[0][productID]"
                                                                    class="editable-select">
                                                                    <option value="">Chọn vật tư</option>
                                                                    @foreach($products as $product)
                                                                    <option value="{{ $product->id }}">{{ $product->name
                                                                        }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td class="editable-cell">
                                                                <input type="text" name="items[0][quantity]"
                                                                    class="editable-input" placeholder="Số lượng">
                                                            </td>
                                                            <td class="editable-cell">
                                                                <select name="items[0][job]" class="editable-select">
                                                                    <option value="">Chọn công việc</option>
                                                                    @foreach($works as $work)
                                                                    <option value="{{ $work->id }}">{{ $work->workType
                                                                        }}</option>
                                                                    @endforeach
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

                                        <!-- Reason Section -->
                                        <div class="reason-section">
                                            <div class="section-title">Lý do từ chối</div>
                                            <textarea class="reason-textarea" name="reason"
                                                placeholder="Nhập lý do từ chối đề xuất vật tư này..."></textarea>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="action-buttons">
                                            <button class="btnnn btn-approve" type="submit">
                                                <i class="fas fa-check"></i>
                                                Duyệt
                                            </button>
                                            {{-- <button class="btnnn btn-approve" onclick="approveRequest()"
                                                id="approveBtn">
                                                <i class="fas fa-check"></i>
                                                Duyệt
                                            </button> --}}
                                            {{-- <button class="btnnn btn-reject" onclick="rejectRequest()"
                                                id="rejectBtn">
                                                <i class="fas fa-times"></i>
                                                Từ chối
                                            </button> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <script>
                let rowIndex = 1;
                                    function addMaterialRow() {
                                        const tableBody = document.getElementById('materialTableBody');
                                        const newRow = document.createElement('tr');

                                        newRow.innerHTML = `
                                            <td class="editable-cell">
                                                <select name="items[${rowIndex}][productID]" class="editable-select">
                                                    <option value="">Chọn vật tư</option>
                                                    @foreach($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="editable-cell">
                                                <input type="text" name="items[${rowIndex}][quantity]" class="editable-input" placeholder="Số lượng">
                                            </td>
                                            <td class="editable-cell">
                                                <select name="items[${rowIndex}][job]" class="editable-select">
                                                    <option value="">Chọn công việc</option>
                                                    @foreach($works as $work)
                                                    <option value="{{ $work->id }}">{{ $work->workType }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="editable-cell">
                                                <input type="hidden" name="items[${rowIndex}][created_by]" value="{{ auth()->id() }}">
                                                <input type="text" name="items[${rowIndex}][proposer_name]" class="editable-input"
                                                    value="{{ auth()->user()->name }}" readonly>
                                            </td>
                                            <td class="editable-cell">
                                                <input type="text" name="items[${rowIndex}][note]" class="editable-input" placeholder="Ghi chú">
                                            </td>
                                            <td style="text-align: center;">
                                                <button type="button" class="btn-remove-row" onclick="removeMaterialRow(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        `;
                                        tableBody.appendChild(newRow);

                                        rowIndex++;
                                        newRow.querySelector('.editable-select').focus();
                                        updateRemoveButtons();
                                    }


                                    // Remove material row
                                    function removeMaterialRow(button) {
                                        if (confirm('Bạn có chắc muốn xóa dòng này?')) {
                                            button.closest('tr').remove();
                                            updateRemoveButtons();
                                            showMessage('success', 'Đã xóa dòng!');
                                        }
                                    }

                                    // Update remove buttons visibility
                                    function updateRemoveButtons() {
                                        const rows = document.querySelectorAll('#materialTableBody tr');
                                        rows.forEach((row, index) => {
                                            const removeBtn = row.querySelector('.btn-remove-row');
                                            if (removeBtn) {
                                                // Always show remove button except for first row if it's the only row
                                                removeBtn.style.display = rows.length > 1 ? 'inline-block' : (index === 0 ? 'none' : 'inline-block');
                                            }
                                        });
                                    }

                                    // Get current material data
                                    function getCurrentMaterialData() {
                                        const materials = [];
                                        const rows = document.querySelectorAll('#materialTableBody tr');
                                        
                                        rows.forEach(row => {
                                            const cells = row.querySelectorAll('td');
                                            if (cells.length >= 5) {
                                                const material = {
                                                    name: cells[0].querySelector('select, input')?.value || cells[0].textContent.trim(),
                                                    quantity: cells[1].querySelector('input')?.value || cells[1].textContent.trim(),
                                                    task: cells[2].querySelector('select, input')?.value || cells[2].textContent.trim(),
                                                    requester: cells[3].querySelector('input')?.value || cells[3].textContent.trim(),
                                                    note: cells[4].querySelector('input')?.value || cells[4].textContent.trim()
                                                };
                                                
                                                // Only add if at least name or quantity is filled
                                                if (material.name || material.quantity) {
                                                    materials.push(material);
                                                }
                                            }
                                        });
                                        
                                        console.log(materials); // Hiển thị dữ liệu
                                        return materials;
                                    }
            </script>
        </div>
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
</style>
<style>
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
    .content-section {
        padding: 30px;
    }

    /* Material Request Section */
    .material-section {
        margin-bottom: 30px;
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

        .btn {
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
</style>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const today = new Date().toISOString().split('T')[0]; // yyyy-mm-dd
    document.getElementById("sendDate").value = today;
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection