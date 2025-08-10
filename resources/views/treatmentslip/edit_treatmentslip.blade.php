@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0"> Phiếu Trị</h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('treatmentslips.index') }}">Danh sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm Phiếu Bệnh</li>
                </ol>
            </nav>
        </div>
    </div>
    @include('layouts.alert')

    <div class="row">
        <div class="col-xl-12">
            <form id="form-account" action="{{ route('treatmentslips.update', $treatmentslips->id) }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row modal-body gy-4">

                            <div class="treatment-plan">
                                <h2 class="treatment-plan-title">Kế Hoạch Điều Trị</h2>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="startDate" class="form-label">Ngày Bắt Đầu</label>
                                        <input type="date" name="sessionStart" class="form-input"
                                            value="{{ old('sessionStart', $treatmentslips->sessionStart) }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="sessionEnd" class="form-label">Ngày Kết Thúc (Dự Kiến)</label>
                                        <input type="date" name="sessionEnd" class="form-input"
                                            value="{{ old('sessionEnd', $treatmentslips->sessionEnd) }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="assignedTo" class="form-label">Người Phụ Trách</label>
                                        <select name="assigned_to" class="form-select" required>
                                            <option value="">Chọn người phụ trách</option>
                                            @foreach($workers as $worker)
                                            <option value="{{ $worker->id }}" {{ old('assigned_to', $treatmentslips->
                                                assigned_to) == $worker->id ? 'selected' : '' }}>{{ $worker->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="priority" class="form-label">Mức Độ Ưu Tiên</label>
                                        <select name="priority" class="form-select">
                                            <option value="">Chọn Mức Độ</option>
                                            <option value="Thấp" {{ old('priority', $treatmentslips->priority) ==
                                                'Thấp' ? 'selected' : '' }}>Thấp</option>
                                            <option value="Trung Bình" {{ old('priority', $treatmentslips->priority)
                                                == 'Trung Bình' ? 'selected' : '' }}>Trung Bình</option>
                                            <option value="Cao" {{ old('priority', $treatmentslips->priority) == 'Cao'
                                                ? 'selected' : '' }}>Cao</option>
                                            <option value="Khẩn Cấp" {{ old('priority', $treatmentslips->priority) ==
                                                'Khẩn Cấp' ? 'selected' : '' }}>Khẩn Cấp</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Các bước điều trị -->
                                <div class="treatment-steps">
                                    @foreach($treatmentslips->treatmentSteps as $index => $step)
                                    <div class="treatment-step" id="step-{{ $index }}">
                                        <div class="step-number">{{ $loop->iteration }}</div>
                                        <div class="step-content">
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label">Tên Thuốc/Vật Tư</label>
                                                    <select name="steps[{{ $index }}][productID]" class="form-select"
                                                        required>
                                                        @foreach($products as $product)
                                                        <option value="{{ $product->id }}" {{ old('steps.' . $index
                                                            . '.productID' , $step->productID) == $product->id ?
                                                            'selected'
                                                            : '' }}>{{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Liều Lượng</label>
                                                    <input type="text" name="steps[{{ $index }}][dose]"
                                                        class="form-input" placeholder="Nhập liều lượng"
                                                        value="{{ old('steps.' . $index . '.dose', $step->dose) }}">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Ngày Thực Hiện</label>
                                                    <input type="date" name="steps[{{ $index }}][execution_date]"
                                                        class="form-input"
                                                        value="{{ old('steps.' . $index . '.execution_date', $step->execution_date) }}">
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label">Hướng Dẫn</label>
                                                    <textarea name="steps[{{ $index }}][instructions]"
                                                        class="form-textarea"
                                                        placeholder="Nhập hướng dẫn chi tiết...">{{ old('steps.' . $index . '.instructions', $step->instructions) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="step-actions">
                                            <button type="button" class="btn btn-outline"
                                                onclick="removeTreatmentStep(this)" style="color: #ef4444;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="add-step-btn" onclick="addStep()">
                                    <i class="fas fa-plus" style="margin-right: 0.5rem;"></i>
                                    Thêm bước điều trị
                                </div>
                            </div>

                            <div class="form-row" style="margin-top: 1.5rem;">
                                <div class="form-group" style="flex: 1;">
                                    <label for="results" class="form-label">Kết Quả Điều Trị</label>
                                    <textarea id="results" class="form-textarea"
                                        placeholder="Ghi chú kết quả sau khi điều trị...">{{ old('results') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="prism-toggle d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-success">Cập Nhật Thông Tin</button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</section>
<script>
    let stepCount = document.querySelectorAll('.treatment-step').length; 

    function addStep() {
        const container = document.querySelector('.treatment-steps');

        const newStepId = `step-${stepCount}`;

        const newStepHTML = `
            <div class="treatment-steps">
                <div class="treatment-step" id="${newStepId}">
                <div class="step-number">${stepCount + 1}</div>
                    <div class="step-content">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Tên Thuốc/Vật Tư</label>
                                <select name="steps[${stepCount}][productID]" class="form-select" required>
                                    <option value="">Chọn sản phẩm</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Liều Lượng</label>
                                <input type="text" name="steps[${stepCount}][dose]" class="form-input" placeholder="Nhập liều lượng" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Ngày Thực Hiện</label>
                                <input type="date" name="steps[${stepCount}][execution_date]" class="form-input" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Hướng Dẫn</label>
                                <textarea name="steps[${stepCount}][instructions]" class="form-textarea" placeholder="Nhập hướng dẫn chi tiết..." required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="step-actions">
                        <button type="button" onclick="removeTreatmentStep(this)" class="btn btn-outline" style="color: #ef4444;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newStepHTML);
        stepCount++;

    }
     function removeTreatmentStep(button) {
        button.closest('.treatment-step').remove();
    }

</script>
<script>
    function autoResize(textarea) {
        textarea.style.height = 'auto'; 
        textarea.style.height = textarea.scrollHeight + 'px';
    }
</script>
<style>
    .form-label {
        font-weight: bold;
    }

    .select2-container--default .select2-selection--single {
        height: 37px;
    }

    .modal-dialog {
        max-width: 90% !important;
        margin: 1.75rem auto;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-title {
        color: #059669;
        font-size: 1rem;
        font-weight: bold;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
    }

    /* Form Styles */
    .treatment-form {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        padding: 2rem;
    }

    .form-group.wide {
        flex: 2;
    }


    .form-label.required::after {
        content: " *";
        color: #ef4444;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        transition: border-color 0.2s;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }



    /* Treatment Plan Section */
    .treatment-plan {
        margin-top: 2rem;
        border-top: 1px solid #e5e7eb;
        padding-top: 1.5rem;
    }

    .treatment-plan-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #374151;
    }

    .treatment-steps {
        margin-top: 1rem;
    }

    .treatment-step {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        padding: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        background-color: #f9fafb;
    }

    .step-number {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        background-color: #3b82f6;
        color: white;
        border-radius: 50%;
        font-weight: bold;
    }

    .step-content {
        flex: 1;
    }

    .step-actions {
        display: flex;
        gap: 0.5rem;
    }

    .add-step-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem;
        border: 2px dashed #d1d5db;
        border-radius: 0.5rem;
        color: #6b7280;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 1rem;
    }

    .add-step-btn:hover {
        border-color: #3b82f6;
        color: #3b82f6;
        background-color: #f0f9ff;
    }

    /* Form Footer */
    .form-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e5e7eb;
    }

    .form-status {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-draft {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    .status-pending {
        background-color: #fef3c7;
        color: #d97706;
    }

    .status-approved {
        background-color: #dcfce7;
        color: #166534;
    }

    .status-completed {
        background-color: #dbeafe;
        color: #1e40af;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
            gap: 1rem;
        }

        .form-group,
        .form-group.wide {
            flex: none;
        }
    }
</style>
@endsection