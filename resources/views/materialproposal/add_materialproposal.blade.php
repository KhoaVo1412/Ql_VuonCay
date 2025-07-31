@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0">Tạo Phiếu Đề Xuất Vật Tư</h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('materialproposals.index') }}">Danh Sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm Đề Xuất</li>
                </ol>
            </nav>
        </div>
    </div>
    @include('layouts.alert')

    <div class="row">
        <div class="col-xl-12">
            <form id="form-materialproposal" action="{{ route('materialproposals.save') }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row modal-body gy-4">
                            <div class="form-section">
                                <!-- Request Info -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Mã phiếu</label>
                                        <input type="text" class="form-input" id="requestId" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Ngày tạo</label>
                                        <input type="date" class="form-input" id="requestDate" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Người đề xuất</label>
                                        <input type="text" class="form-input" id="requester"
                                            placeholder="Nhập tên người đề xuất" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Khu vực sử dụng</label>
                                        <select class="form-select" id="usageArea" required>
                                            <option value="">Chọn khu vực</option>
                                            <option value="Khu A">Khu A</option>
                                            <option value="Khu B">Khu B</option>
                                            <option value="Khu C">Khu C</option>
                                            <option value="Khu D">Khu D</option>
                                            <option value="Toàn vườn">Toàn vườn</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Mục đích sử dụng</label>
                                        <select class="form-select" id="purpose" required>
                                            <option value="">Chọn mục đích</option>
                                            <option value="Phòng trừ sâu bệnh">Phòng trừ sâu bệnh</option>
                                            <option value="Bón phân">Bón phân</option>
                                            <option value="Cắt tỉa">Cắt tỉa</option>
                                            <option value="Tưới nước">Tưới nước</option>
                                            <option value="Khác">Khác</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Mức độ ưu tiên</label>
                                        <select class="form-select" id="priority" required>
                                            <option value="">Chọn mức độ</option>
                                            <option value="Thấp">Thấp</option>
                                            <option value="Trung bình">Trung bình</option>
                                            <option value="Cao">Cao</option>
                                            <option value="Khẩn cấp">Khẩn cấp</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Material Items -->
                                <div class="material-items" id="materialItems">
                                    <div class="material-item">
                                        <div class="form-group">
                                            <label class="form-label">Tên vật tư</label>
                                            <select class="form-select" name="materialName" required>
                                                <option value="">Chọn vật tư</option>
                                                <option value="Thuốc trừ sâu Regent">Thuốc trừ sâu Regent</option>
                                                <option value="Thuốc diệt nấm Antracol">Thuốc diệt nấm Antracol</option>
                                                <option value="Phân bón NPK">Phân bón NPK</option>
                                                <option value="Phân hữu cơ">Phân hữu cơ</option>
                                                <option value="Dụng cụ cắt tỉa">Dụng cụ cắt tỉa</option>
                                                <option value="Bình xịt thuốc">Bình xịt thuốc</option>
                                                <option value="Ống tưới">Ống tưới</option>
                                                <option value="Khác">Khác</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Số lượng</label>
                                            <input type="number" class="form-input" name="quantity" min="1" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Đơn vị</label>
                                            <select class="form-select" name="unit" required>
                                                <option value="">Chọn đơn vị</option>
                                                <option value="Chai">Chai</option>
                                                <option value="Gói">Gói</option>
                                                <option value="Kg">Kg</option>
                                                <option value="Lít">Lít</option>
                                                <option value="Cái">Cái</option>
                                                <option value="Thùng">Thùng</option>
                                                <option value="Mét">Mét</option>
                                            </select>
                                        </div>
                                        <button type="button" class="remove-btn" onclick="removeMaterialItem(this)"
                                            style="display: none;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <div class="material-item">
                                        <div class="form-group">
                                            <label class="form-label">Tên vật tư</label>
                                            <select class="form-select" name="materialName">
                                                <option value="">Chọn vật tư</option>
                                                <option value="Thuốc trừ sâu Regent">Thuốc trừ sâu Regent</option>
                                                <option value="Thuốc diệt nấm Antracol">Thuốc diệt nấm Antracol</option>
                                                <option value="Phân bón NPK">Phân bón NPK</option>
                                                <option value="Phân hữu cơ">Phân hữu cơ</option>
                                                <option value="Dụng cụ cắt tỉa">Dụng cụ cắt tỉa</option>
                                                <option value="Bình xịt thuốc">Bình xịt thuốc</option>
                                                <option value="Ống tưới">Ống tưới</option>
                                                <option value="Khác">Khác</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Số lượng</label>
                                            <input type="number" class="form-input" name="quantity" min="1">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Đơn vị</label>
                                            <select class="form-select" name="unit">
                                                <option value="">Chọn đơn vị</option>
                                                <option value="Chai">Chai</option>
                                                <option value="Gói">Gói</option>
                                                <option value="Kg">Kg</option>
                                                <option value="Lít">Lít</option>
                                                <option value="Cái">Cái</option>
                                                <option value="Thùng">Thùng</option>
                                                <option value="Mét">Mét</option>
                                            </select>
                                        </div>
                                        <button type="button" class="remove-btn" onclick="removeMaterialItem(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="add-material" onclick="addMaterialItem()">
                                    <i class="fas fa-plus"></i> Thêm vật tư...
                                </div>

                                <!-- Additional Info -->
                                <div class="form-row" style="margin-top: 1.5rem;">
                                    <div class="form-group">
                                        <label class="form-label">Ngày nhận</label>
                                        <input type="date" class="form-input" id="receiveDate" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Người nhận</label>
                                        <input type="text" class="form-input" id="receiver"
                                            placeholder="Nhập tên người nhận" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group" style="flex: 1;">
                                        <label class="form-label">Ghi chú</label>
                                        <textarea class="form-input" id="notes" placeholder="Ghi chú thêm về yêu cầu..."
                                            style="min-height: 80px; resize: vertical;"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="prism-toggle d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-success" id="submit-btn-materialproposal">Lưu Thông
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

    .form-group.wide {
        flex: 2;
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
        /* max-width: 800px; */
        margin: 0 auto;
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

    .material-item .remove-btn {
        padding: 0.75rem;
        background-color: #ef4444;
        color: white;
        border: none;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .material-item .remove-btn:hover {
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
    function generateRequestId() {
            const now = new Date();
            const year = now.getFullYear().toString().substr(-2);
            const month = (now.getMonth() + 1).toString().padStart(2, '0');
            const day = now.getDate().toString().padStart(2, '0');
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            return `DX${year}${month}${day}${random}`;
        }
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('requestDate').value = today;
            
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            document.getElementById('receiveDate').value = tomorrow.toISOString().split('T')[0];
            document.getElementById('requestId').value = generateRequestId();
            initializeNavigation();
        });
        function initializeNavigation() {
            const navItems = document.querySelectorAll('.nav-item');
            const views = document.querySelectorAll('.view');

            navItems.forEach(item => {
                item.addEventListener('click', function() {
                    const targetView = this.getAttribute('data-view');
                    
                    navItems.forEach(nav => nav.classList.remove('active'));
                    this.classList.add('active');
                    
                    views.forEach(view => view.classList.add('hidden'));
                    
                    const targetElement = document.getElementById(targetView + '-view');
                    if (targetElement) {
                        targetElement.classList.remove('hidden');
                    }
                });
            });
        }

        // Add material item
        function addMaterialItem() {
            const container = document.getElementById('materialItems');
            const newItem = document.createElement('div');
            newItem.className = 'material-item';
            newItem.innerHTML = `
                <div class="form-group">
                    <label class="form-label">Tên vật tư</label>
                    <select class="form-select" name="materialName">
                        <option value="">Chọn vật tư</option>
                        <option value="Thuốc trừ sâu Regent">Thuốc trừ sâu Regent</option>
                        <option value="Thuốc diệt nấm Antracol">Thuốc diệt nấm Antracol</option>
                        <option value="Phân bón NPK">Phân bón NPK</option>
                        <option value="Phân hữu cơ">Phân hữu cơ</option>
                        <option value="Dụng cụ cắt tỉa">Dụng cụ cắt tỉa</option>
                        <option value="Bình xịt thuốc">Bình xịt thuốc</option>
                        <option value="Ống tưới">Ống tưới</option>
                        <option value="Khác">Khác</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Số lượng</label>
                    <input type="number" class="form-input" name="quantity" min="1">
                </div>
                <div class="form-group">
                    <label class="form-label">Đơn vị</label>
                    <select class="form-select" name="unit">
                        <option value="">Chọn đơn vị</option>
                        <option value="Chai">Chai</option>
                        <option value="Gói">Gói</option>
                        <option value="Kg">Kg</option>
                        <option value="Lít">Lít</option>
                        <option value="Cái">Cái</option>
                        <option value="Thùng">Thùng</option>
                        <option value="Mét">Mét</option>
                    </select>
                </div>
                <button type="button" class="remove-btn" onclick="removeMaterialItem(this)">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            
            const addButton = container.nextElementSibling;
            container.parentNode.insertBefore(newItem, addButton);
            
            updateRemoveButtons();
        }

        function removeMaterialItem(button) {
            button.closest('.material-item').remove();
            updateRemoveButtons();
        }

        function updateRemoveButtons() {
            const items = document.querySelectorAll('.material-item');
            items.forEach((item, index) => {
                const removeBtn = item.querySelector('.remove-btn');
                if (index === 0 && items.length === 1) {
                    removeBtn.style.display = 'none';
                } else {
                    removeBtn.style.display = 'block';
                }
            });
        }

        document.getElementById('materialproposal-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const materials = [];
            
            const materialItems = document.querySelectorAll('.material-item');
            materialItems.forEach(item => {
                const name = item.querySelector('[name="materialName"]').value;
                const quantity = item.querySelector('[name="quantity"]').value;
                const unit = item.querySelector('[name="unit"]').value;
                
                if (name && quantity && unit) {
                    materials.push({ name, quantity, unit });
                }
            });
            
            if (materials.length === 0) {
                alert('Vui lòng thêm ít nhất một vật tư!');
                return;
            }
            
            alert('Đã gửi phiếu đề xuất vật tư thành công!');
            
            this.reset();
            document.getElementById('requestId').value = generateRequestId();
            
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('requestDate').value = today;
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            document.getElementById('receiveDate').value = tomorrow.toISOString().split('T')[0];
        });
        updateRemoveButtons();
</script>
@endsection