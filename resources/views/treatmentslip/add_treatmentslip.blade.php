@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0">Tạo Phiếu Cây Bệnh</h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('treatmentslips.index') }}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm Phiếu Trị</li>
                </ol>
            </nav>
        </div>
    </div>
    @include('layouts.alert')

    <div class="row">
        <div class="col-xl-12">
            <form id="form-treatmentslip" action="{{ route('treatmentslips.save') }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row modal-body gy-4">
                            <div class="form-section">


                                <!-- Second Row -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Ngày ghi nhận</label>
                                        <input type="date" class="form-input" id="recordDate" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Tên bệnh</label>
                                        <select class="form-select" id="diseaseName" required>
                                            <option value="">Chọn loại bệnh</option>
                                            <option value="Nấm lá">Nấm lá</option>
                                            <option value="Sâu đục thân">Sâu đục thân</option>
                                            <option value="Bệnh héo xanh">Bệnh héo xanh</option>
                                            <option value="Thiếu dinh dưỡng">Thiếu dinh dưỡng</option>
                                            <option value="Sâu ăn lá">Sâu ăn lá</option>
                                            <option value="Bệnh khác">Bệnh khác</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Số lượng</label>
                                        <input type="number" class="form-input" id="quantity" placeholder="Số cây bệnh"
                                            min="1">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Tên tổ</label>
                                        <input type="text" class="form-input" id="teamName" placeholder="Nhập tên tổ">
                                    </div>
                                </div>

                                <!-- Third Row -->
                                <div class="form-row">
                                    <div class="form-group" style="flex: 2;">
                                        <label class="form-label">Biểu hiện bệnh</label>
                                        <textarea class="form-input" id="symptoms"
                                            placeholder="Mô tả chi tiết biểu hiện bệnh..."
                                            style="min-height: 80px; resize: vertical;" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Mức độ lây lan</label>
                                        <select class="form-select" id="spreadLevel" required>
                                            <option value="">Chọn mức độ</option>
                                            <option value="Thấp">Thấp (&lt; 10%)</option>
                                            <option value="Trung bình">Trung bình (10-30%)</option>
                                            <option value="Cao">Cao (30-60%)</option>
                                            <option value="Nghiêm trọng">Nghiêm trọng (&gt; 60%)</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Fourth Row -->
                                <div class="form-row">
                                    <div class="form-group" style="flex: 1;">
                                        <label class="form-label">Nguyên nhân</label>
                                        <textarea class="form-input" id="cause"
                                            placeholder="Phân tích nguyên nhân gây bệnh..."
                                            style="min-height: 80px; resize: vertical;" required></textarea>
                                    </div>
                                </div>

                                <!-- Treatment Plan Section -->
                                <div style="border-top: 1px solid #e5e7eb; padding-top: 1.5rem; margin-top: 1.5rem;">
                                    <h3
                                        style="color: #374151; font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem;">
                                        Kế
                                        hoạch điều trị</h3>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Ngày bắt đầu</label>
                                            <input type="date" class="form-input" id="startDate">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Ngày kết thúc (dự kiến)</label>
                                            <input type="date" class="form-input" id="endDate">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Người phụ trách</label>
                                            <input type="text" class="form-input" id="assignedTo"
                                                placeholder="Tên người phụ trách">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Mức độ ưu tiên</label>
                                            <select class="form-select" id="priority">
                                                <option value="">Chọn mức độ</option>
                                                <option value="Thấp">Thấp</option>
                                                <option value="Trung bình">Trung bình</option>
                                                <option value="Cao">Cao</option>
                                                <option value="Khẩn cấp">Khẩn cấp</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Treatment Steps -->
                                    <div id="treatmentSteps" style="margin-top: 1rem;">
                                        <div class="treatment-step"
                                            style="display: flex; gap: 1rem; margin-bottom: 1rem; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background-color: #f9fafb;">
                                            <div
                                                style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; background-color: #3b82f6; color: white; border-radius: 50%; font-weight: bold;">
                                                1</div>
                                            <div style="flex: 1;">
                                                <div class="form-row" style="margin-bottom: 0.5rem;">
                                                    <div class="form-group">
                                                        <label class="form-label">Tên thuốc/Vật tư</label>
                                                        <select class="form-select" name="medicine">
                                                            <option value="">Chọn thuốc/vật tư</option>
                                                            <option value="Thuốc trừ sâu Regent">Thuốc trừ sâu Regent
                                                            </option>
                                                            <option value="Thuốc diệt nấm Antracol">Thuốc diệt nấm
                                                                Antracol
                                                            </option>
                                                            <option value="Phân bón NPK">Phân bón NPK</option>
                                                            <option value="Thuốc kháng sinh thực vật">Thuốc kháng sinh
                                                                thực vật
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Liều lượng</label>
                                                        <input type="text" class="form-input" name="dosage"
                                                            placeholder="VD: 20ml/lít nước">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Ngày thực hiện</label>
                                                        <input type="date" class="form-input" name="treatmentDate">
                                                    </div>
                                                </div>
                                                <div class="form-row" style="margin-bottom: 0;">
                                                    <div class="form-group" style="flex: 1;">
                                                        <label class="form-label">Hướng dẫn</label>
                                                        <textarea class="form-input" name="instructions"
                                                            style="min-height: 60px;"
                                                            placeholder="Hướng dẫn chi tiết cách sử dụng..."></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="remove-btn btn-danger"
                                                onclick="removeTreatmentStep(this)" style="align-self: flex-start;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="add-material" onclick="addTreatmentStep()" style="margin-top: 1rem;">
                                        <i class="fas fa-plus"></i> Thêm bước điều trị
                                    </div>
                                </div>

                                <!-- Results Section -->
                                <div style="border-top: 1px solid #e5e7eb; padding-top: 1.5rem; margin-top: 1.5rem;">
                                    <div class="form-row">
                                        <div class="form-group" style="flex: 1;">
                                            <label class="form-label">Kết quả điều trị</label>
                                            <textarea class="form-input" id="results"
                                                placeholder="Ghi chú kết quả sau khi điều trị..."
                                                style="min-height: 80px; resize: vertical;"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="prism-toggle d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-success" id="submit-btn-treatmentslip">Lưu Thông
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
    $(document).ready(function () {
        $('#farm_id').select2({
            placeholder: "Chọn Nông Trường",
            allowClear: true,
            minimumResultsForSearch: 0,
            width: '100%',
        });
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
<script>
    let treatmentStepCounter = 1;

    function addTreatmentStep() {
        treatmentStepCounter++;
        const container = document.getElementById('treatmentSteps');
        const newStep = document.createElement('div');
        newStep.className = 'treatment-step';
        newStep.style.cssText = 'display: flex; gap: 1rem; margin-bottom: 1rem; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background-color: #f9fafb;';
        
        newStep.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; background-color: #3b82f6; color: white; border-radius: 50%; font-weight: bold;">${treatmentStepCounter}</div>
            <div style="flex: 1;">
                <div class="form-row" style="margin-bottom: 0.5rem;">
                    <div class="form-group">
                        <label class="form-label">Tên thuốc/Vật tư</label>
                        <select class="form-select" name="medicine">
                            <option value="">Chọn thuốc/vật tư</option>
                            <option value="Thuốc trừ sâu Regent">Thuốc trừ sâu Regent</option>
                            <option value="Thuốc diệt nấm Antracol">Thuốc diệt nấm Antracol</option>
                            <option value="Phân bón NPK">Phân bón NPK</option>
                            <option value="Thuốc kháng sinh thực vật">Thuốc kháng sinh thực vật</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Liều lượng</label>
                        <input type="text" class="form-input" name="dosage" placeholder="VD: 20ml/lít nước">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ngày thực hiện</label>
                        <input type="date" class="form-input" name="treatmentDate">
                    </div>
                </div>
                <div class="form-row" style="margin-bottom: 0;">
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Hướng dẫn</label>
                        <textarea class="form-input" name="instructions" style="min-height: 60px;" placeholder="Hướng dẫn chi tiết cách sử dụng..."></textarea>
                    </div>
                </div>
            </div>
            <button type="button" class="remove-btn" onclick="removeTreatmentStep(this)" style="align-self: flex-start;">
                <i class="fas fa-trash"></i>
            </button>
        `;
        
        container.appendChild(newStep);
    }

    function removeTreatmentStep(button) {
        button.closest('.treatment-step').remove();
        updateTreatmentStepNumbers();
    }

    function updateTreatmentStepNumbers() {
        const steps = document.querySelectorAll('.treatment-step');
        steps.forEach((step, index) => {
            const numberElement = step.querySelector('div:first-child');
            numberElement.textContent = index + 1;
        });
        treatmentStepCounter = steps.length;
    }

    // Initialize treatment form
    function initializeTreatmentForm() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('recordDate').value = today;
        document.getElementById('startDate').value = today;
        
        // Set end date default to 7 days later
        const nextWeek = new Date();
        nextWeek.setDate(nextWeek.getDate() + 7);
        document.getElementById('endDate').value = nextWeek.toISOString().split('T')[0];
    }

    // Treatment form submission
    document.addEventListener('DOMContentLoaded', function() {
        const treatmentForm = document.getElementById('treatmentForm');
        if (treatmentForm) {
            treatmentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                alert('Đã tạo phiếu điều trị thành công!');
                this.reset();
                initializeTreatmentForm();
            });
        }
        
        // Initialize treatment form when page loads
        setTimeout(initializeTreatmentForm, 100);
    });
</script>
@endsection