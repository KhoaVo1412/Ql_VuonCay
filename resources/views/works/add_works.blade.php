@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0">
            Thêm Công Việc
        </h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{route('works.index')}}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm Công Việc</li>
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
            <form id="form-works" action="{{route('works.save')}}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-body">
                        <div id="batch-container">
                            <div class="row gy-2">
                                <div class="form-row">

                                    <div class="form-group">
                                        <label class="form-label">Tên Việc</label>
                                        <select name="workID" class="form-select" id="workID-select" required>
                                            <option value="">Chọn công việc</option>
                                            @foreach($works as $work)
                                            <option value="{{ $work->id }}" data-type="{{ $work->workType }}">{{
                                                $work->workName }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" id="work-type-display" class="form-control mt-2" readonly
                                            placeholder="Loại công việc sẽ hiển thị ở đây">

                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Người phụ trách</label>
                                        <select name="workerID" class="form-select" required>
                                            @foreach($workers as $worker)
                                            <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">

                                    <div class="form-group">
                                        <label class="form-label">Ngày bắt đầu</label>
                                        <input type="date" name="workDate" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Ngày Kết Thúc</label>
                                        <input type="date" name="dateEnd" class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Chọn Lô</label>
                                        <select name="plotID" id="plotID" class="form-select" required>
                                            <option value="">-- Chọn lô --</option>
                                            @foreach ($plots as $plot)
                                            <option value="{{ $plot->id }}">{{ $plot->plotName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Chọn cây</label>
                                        <select name="plantIDs[]" id="plantIDs" class="form-select" multiple required>
                                            {{-- load động --}}
                                        </select>

                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Đề Xuất Vật Tư</label>
                                        <select name="type" class="form-select" required>
                                            <option value="1">Đề Xuất Vật Tư</option>
                                            <option value="0">Không Cần Vật Tư</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Mức độ ưu tiên</label>
                                        <select name="priority" class="form-select" required>
                                            <option value="Thấp">Thấp</option>
                                            <option value="Trung bình">Trung bình</option>
                                            <option value="Cao">Cao</option>
                                            <option value="Khẩn cấp">Khẩn cấp</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Mô tả công việc</label>
                                    <textarea name="description" class="form-control"
                                        placeholder="Ghi mô tả công việc"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <button type="submit" class="btn btn-success">Lưu Thông Tin</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script>
    document.getElementById('workID-select').addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const type = selected.getAttribute('data-type');
        document.getElementById('work-type-display').value = type || '';
    });
</script>
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
<script>
    $(document).ready(function () {
        $('#plantIDs').select2({
            placeholder: 'Chọn cây',
            allowClear: true
        });

        $('#plotID').on('change', function () {
            const plotID = $(this).val();
            if (plotID) {
                $.ajax({
                    url: `/plots/${plotID}/plants`,
                    type: 'GET',
                    success: function (data) {
                        $('#plantIDs').empty(); // Clear old options
                        data.forEach(function (plant) {
                            $('#plantIDs').append(`<option value="${plant.id}">${plant.plantCode}</option>`);
                        });
                        $('#plantIDs').trigger('change');
                    },
                    error: function () {
                        alert('Không thể tải danh sách cây');
                    }
                });
            } else {
                $('#plantIDs').empty();
            }
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
</script>
@endsection