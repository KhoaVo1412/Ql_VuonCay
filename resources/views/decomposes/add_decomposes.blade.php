@extends('layouts.app')
@section('content')
<section>
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0">Tạo Phiếu Phân Rã</h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('plantingareas.index') }}">Danh sách</a></li>
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
                                        <input type="text" id="medicalRecordId" class="form-input"
                                            placeholder="Nhập mã phiếu" required>
                                    </div>
                                    <div class="form-group wide">
                                        <label for="medicalRecordName" class="form-label required">Tên Phiếu</label>
                                        <input type="text" id="medicalRecordName" class="form-input"
                                            placeholder="Nhập tên phiếu" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="" class="form-label required">Kho</label>
                                        <input type="text" id="" class="form-input" required>
                                    </div>
                                    <div class="form-group wide">
                                        <label for="diseaseName" class="form-label required">Người Tạo</label>
                                        <input type="text" id="diseaseName" class="form-input" placeholder="" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="quantity" class="form-label">Ngày Tạo</label>
                                        <input type="date" id="quantity" class="form-input" placeholder="">
                                    </div>

                                </div>

                                <!-- Fourth Row -->
                                <div class="form-row">
                                    <div class="form-group" style="flex: 1;">
                                        <label class="form-label">Ghi Chú</label>
                                        <textarea class="form-input" id="cause" placeholder="Ghi chú..."
                                            style="min-height: 80px; resize: vertical;" required></textarea>
                                    </div>
                                </div>

                                <div class="material-section">
                                    <div class="section-title">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <i class="fas fa-exchange-alt"></i>
                                            Phân Rã Vật Tư
                                        </div>
                                        <button type="button" class="btn-add-new-material"
                                            onclick="addNewMaterialContainer()">
                                            <i class="fas fa-plus"></i>
                                            Thêm
                                        </button>
                                    </div>

                                    <div id="materialContainers">
                                        <!-- Initial Material Container -->
                                        <div class="material-replacement-container" data-container-id="1">
                                            <div class="container-number">1</div>
                                            <button type="button" class="btn-remove-container"
                                                onclick="removeMaterialContainer(1)" style="display: none;">
                                                <i class="fas fa-times"></i>
                                            </button>

                                            <!-- Original Materials to Replace -->
                                            <div class="replacement-section">
                                                <div class="replacement-title">
                                                    {{-- <i class="fas fa-arrow-up"></i> --}}
                                                    Nguyên Liệu Cần Đổi
                                                </div>

                                                <div class="material-row">
                                                    <div class="form-group">
                                                        <label class="form-label">Mã vật tư</label>
                                                        <select class="material-select">
                                                            <option value="">Chọn mã vật tư</option>
                                                            <option value="VT001">VT001 - Thuốc trừ sâu Regent</option>
                                                            <option value="VT002">VT002 - Phân bón NPK</option>
                                                            <option value="VT003">VT003 - Dụng cụ cắt tỉa</option>
                                                            <option value="VT004">VT004 - Bình xịt thuốc</option>
                                                            <option value="VT005">VT005 - Ống tưới nước</option>
                                                            <option value="VT006">VT006 - Thuốc diệt nấm</option>
                                                            <option value="VT007">VT007 - Phân hữu cơ</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Tên vật tư</label>
                                                        <input type="text" class="material-input"
                                                            placeholder="Nhập tên vật tư">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Số lượng</label>
                                                        <input type="number" class="material-input" placeholder="0"
                                                            min="0">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Đơn vị</label>
                                                        <select class="material-select">
                                                            <option value="">Chọn đơn vị</option>
                                                            <option value="kg">Kg</option>
                                                            <option value="lit">Lít</option>
                                                            <option value="chai">Chai</option>
                                                            <option value="hop">Hộp</option>
                                                            <option value="cai">Cái</option>
                                                            <option value="met">Mét</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="button" class="btn-add-material"
                                                            onclick="addNewMaterialContainer()" title="Thêm bảng mới">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Section Divider -->
                                            <div class="section-divider"></div>

                                            <!-- New Materials (Replacement) -->
                                            <div class="replacement-section">
                                                <div class="replacement-title">
                                                    {{-- <i class="fas fa-arrow-down"></i> --}}
                                                    Vật Tư Mới (Thay Thế)
                                                </div>

                                                <div class="material-row">
                                                    <div class="form-group">
                                                        <label class="form-label">Mã vật tư</label>
                                                        <select class="material-select">
                                                            <option value="">Chọn mã vật tư</option>
                                                            <option value="VT001">VT001 - Thuốc trừ sâu Regent</option>
                                                            <option value="VT002">VT002 - Phân bón NPK</option>
                                                            <option value="VT003">VT003 - Dụng cụ cắt tỉa</option>
                                                            <option value="VT004">VT004 - Bình xịt thuốc</option>
                                                            <option value="VT005">VT005 - Ống tưới nước</option>
                                                            <option value="VT006">VT006 - Thuốc diệt nấm</option>
                                                            <option value="VT007">VT007 - Phân hữu cơ</option>
                                                            <option value="VT008">VT008 - Thuốc trừ sâu sinh học
                                                            </option>
                                                            <option value="VT009">VT009 - Phân bón hữu cơ</option>
                                                            <option value="VT010">VT010 - Dụng cụ cắt tỉa cao cấp
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Tên vật tư</label>
                                                        <input type="text" class="material-input"
                                                            placeholder="Nhập tên vật tư">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Số lượng</label>
                                                        <input type="number" class="material-input" placeholder="0"
                                                            min="0">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">Đơn vị</label>
                                                        <select class="material-select">
                                                            <option value="">Chọn đơn vị</option>
                                                            <option value="kg">Kg</option>
                                                            <option value="lit">Lít</option>
                                                            <option value="chai">Chai</option>
                                                            <option value="hop">Hộp</option>
                                                            <option value="cai">Cái</option>
                                                            <option value="met">Mét</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="button" class="btn-add-material"
                                                            onclick="addNewMaterialContainer()" title="Thêm bảng mới">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Reset Button -->
                                            <div style="text-align: center; margin-top: 15px;">
                                                <button type="button" class="btn-reset-materials"
                                                    onclick="resetMaterialContainer(1)" title="Làm mới bảng này">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            </div>
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
    $(document).ready(function () {
        $('#farm_id').select2({
            placeholder: "Chọn Nông Trường",
            allowClear: true,
            minimumResultsForSearch: 0,
            width: '100%',
        });
    });
</script>

<script>
    let containerCounter = 1;

        // Add new material container (complete table with both sections)
        function addNewMaterialContainer() {
            containerCounter++;
            const containersDiv = document.getElementById('materialContainers');
            const newContainer = document.createElement('div');
            newContainer.className = 'material-replacement-container';
            newContainer.setAttribute('data-container-id', containerCounter);
            
            newContainer.innerHTML = `
                <div class="container-number">${containerCounter}</div>
                <button type="button" class="btn-remove-container" onclick="removeMaterialContainer(${containerCounter})">
                    <i class="fas fa-times"></i>
                </button>

                <!-- Original Materials to Replace -->
                <div class="replacement-section">
                    <div class="replacement-title">
                        <i class="fas fa-arrow-up"></i>
                        Nguyên Liệu Cần Đổi
                    </div>
                    
                    <div class="material-row">
                        <div class="form-group">
                            <label class="form-label">Mã vật tư</label>
                            <select class="material-select">
                                <option value="">Chọn mã vật tư</option>
                                <option value="VT001">VT001 - Thuốc trừ sâu Regent</option>
                                <option value="VT002">VT002 - Phân bón NPK</option>
                                <option value="VT003">VT003 - Dụng cụ cắt tỉa</option>
                                <option value="VT004">VT004 - Bình xịt thuốc</option>
                                <option value="VT005">VT005 - Ống tưới nước</option>
                                <option value="VT006">VT006 - Thuốc diệt nấm</option>
                                <option value="VT007">VT007 - Phân hữu cơ</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tên vật tư</label>
                            <input type="text" class="material-input" placeholder="Nhập tên vật tư">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Số lượng</label>
                            <input type="number" class="material-input" placeholder="0" min="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Đơn vị</label>
                            <select class="material-select">
                                <option value="">Chọn đơn vị</option>
                                <option value="kg">Kg</option>
                                <option value="lit">Lít</option>
                                <option value="chai">Chai</option>
                                <option value="hop">Hộp</option>
                                <option value="cai">Cái</option>
                                <option value="met">Mét</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn-add-material" onclick="addNewMaterialContainer()" title="Thêm bảng mới">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Section Divider -->
                <div class="section-divider"></div>

                <!-- New Materials (Replacement) -->
                <div class="replacement-section">
                    <div class="replacement-title">
                        <i class="fas fa-arrow-down"></i>
                        Vật Tư Mới (Thay Thế)
                    </div>
                    
                    <div class="material-row">
                        <div class="form-group">
                            <label class="form-label">Mã vật tư</label>
                            <select class="material-select">
                                <option value="">Chọn mã vật tư</option>
                                <option value="VT001">VT001 - Thuốc trừ sâu Regent</option>
                                <option value="VT002">VT002 - Phân bón NPK</option>
                                <option value="VT003">VT003 - Dụng cụ cắt tỉa</option>
                                <option value="VT004">VT004 - Bình xịt thuốc</option>
                                <option value="VT005">VT005 - Ống tưới nước</option>
                                <option value="VT006">VT006 - Thuốc diệt nấm</option>
                                <option value="VT007">VT007 - Phân hữu cơ</option>
                                <option value="VT008">VT008 - Thuốc trừ sâu sinh học</option>
                                <option value="VT009">VT009 - Phân bón hữu cơ</option>
                                <option value="VT010">VT010 - Dụng cụ cắt tỉa cao cấp</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tên vật tư</label>
                            <input type="text" class="material-input" placeholder="Nhập tên vật tư">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Số lượng</label>
                            <input type="number" class="material-input" placeholder="0" min="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Đơn vị</label>
                            <select class="material-select">
                                <option value="">Chọn đơn vị</option>
                                <option value="kg">Kg</option>
                                <option value="lit">Lít</option>
                                <option value="chai">Chai</option>
                                <option value="hop">Hộp</option>
                                <option value="cai">Cái</option>
                                <option value="met">Mét</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn-add-material" onclick="addNewMaterialContainer()" title="Thêm bảng mới">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Reset Button -->
                <div style="text-align: center; margin-top: 15px;">
                    <button type="button" class="btn-reset-materials" onclick="resetMaterialContainer(${containerCounter})" title="Làm mới bảng này">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
            `;
            
            containersDiv.appendChild(newContainer);
            updateRemoveButtons();
            
            // Focus on the first input of the new container
            const firstInput = newContainer.querySelector('.material-select');
            if (firstInput) {
                firstInput.focus();
            }
            
            showMessage('success', `Đã thêm bảng vật tư mới #${containerCounter}!`);
        }

        // Remove material container
        function removeMaterialContainer(containerId) {
            const containers = document.querySelectorAll('.material-replacement-container');
            
            if (containers.length <= 1) {
                showMessage('error', 'Phải có ít nhất một bảng vật tư!');
                return;
            }
            
            if (confirm('Bạn có chắc muốn xóa bảng vật tư này?')) {
                const container = document.querySelector(`[data-container-id="${containerId}"]`);
                if (container) {
                    container.remove();
                    updateContainerNumbers();
                    updateRemoveButtons();
                    showMessage('success', 'Đã xóa bảng vật tư!');
                }
            }
        }

        // Reset specific material container
        function resetMaterialContainer(containerId) {
            if (confirm('Bạn có chắc muốn làm mới bảng vật tư này?')) {
                const container = document.querySelector(`[data-container-id="${containerId}"]`);
                if (container) {
                    // Reset all inputs in this container
                    const inputs = container.querySelectorAll('.material-input');
                    const selects = container.querySelectorAll('.material-select');
                    
                    inputs.forEach(input => input.value = '');
                    selects.forEach(select => select.selectedIndex = 0);
                    
                    showMessage('success', 'Đã làm mới bảng vật tư!');
                }
            }
        }

        // Update container numbers after removal
        function updateContainerNumbers() {
            const containers = document.querySelectorAll('.material-replacement-container');
            containers.forEach((container, index) => {
                const numberElement = container.querySelector('.container-number');
                const newNumber = index + 1;
                numberElement.textContent = newNumber;
                container.setAttribute('data-container-id', newNumber);
                
                // Update remove button onclick
                const removeBtn = container.querySelector('.btn-remove-container');
                if (removeBtn) {
                    removeBtn.setAttribute('onclick', `removeMaterialContainer(${newNumber})`);
                }
                
                // Update reset button onclick
                const resetBtn = container.querySelector('.btn-reset-materials');
                if (resetBtn) {
                    resetBtn.setAttribute('onclick', `resetMaterialContainer(${newNumber})`);
                }
            });
            
            containerCounter = containers.length;
        }

        // Update remove buttons visibility
        function updateRemoveButtons() {
            const containers = document.querySelectorAll('.material-replacement-container');
            containers.forEach((container, index) => {
                const removeBtn = container.querySelector('.btn-remove-container');
                if (removeBtn) {
                    // Hide remove button on first container, show on others
                    removeBtn.style.display = index === 0 ? 'none' : 'flex';
                }
            });
        }

        // Auto-fill material name when code is selected
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('material-select') && e.target.closest('.form-group').querySelector('.form-label').textContent === 'Mã vật tư') {
                const selectedOption = e.target.options[e.target.selectedIndex];
                const materialName = selectedOption.text.split(' - ')[1];
                const nameInput = e.target.closest('.material-row').querySelector('input[type="text"]');
                if (materialName && nameInput) {
                    nameInput.value = materialName;
                }
            }
        });

        // Approve request
        function approveRequest() {
            const allMaterials = getAllMaterialsData();
            
            if (allMaterials.length === 0) {
                showMessage('error', 'Vui lòng thêm ít nhất một vật tư!');
                return;
            }
            
            if (confirm('Bạn có chắc muốn duyệt đề xuất này?')) {
                // Update status
                const statusBadge = document.getElementById('statusBadge');
                statusBadge.textContent = 'Đã duyệt';
                statusBadge.className = 'status-badge status-approved';
                
                showMessage('success', 'Đã duyệt đề xuất vật tư thành công!');
                
                // Disable form
                disableForm();
            }
        }

        // Reject request
        function rejectRequest() {
            const reason = document.getElementById('rejectReason').value.trim();
            if (!reason) {
                showMessage('error', 'Vui lòng nhập lý do từ chối!');
                document.getElementById('rejectReason').focus();
                return;
            }
            
            if (confirm('Bạn có chắc muốn từ chối đề xuất này?')) {
                // Update status
                const statusBadge = document.getElementById('statusBadge');
                statusBadge.textContent = 'Từ chối';
                statusBadge.className = 'status-badge status-rejected';
                
                showMessage('success', 'Đã từ chối đề xuất vật tư!');
                
                // Disable form
                disableForm();
            }
        }

        // Get all materials data from all containers
        function getAllMaterialsData() {
            const materials = [];
            const containers = document.querySelectorAll('.material-replacement-container');
            
            containers.forEach((container, containerIndex) => {
                const originalRow = container.querySelector('.replacement-section:first-child .material-row');
                const newRow = container.querySelector('.replacement-section:last-child .material-row');
                
                // Get original material data
                const originalCode = originalRow.querySelector('.material-select').value;
                const originalName = originalRow.querySelector('input[type="text"]').value;
                const originalQuantity = originalRow.querySelector('input[type="number"]').value;
                const originalUnit = originalRow.querySelectorAll('.material-select')[1].value;
                
                // Get new material data
                const newCode = newRow.querySelector('.material-select').value;
                const newName = newRow.querySelector('input[type="text"]').value;
                const newQuantity = newRow.querySelector('input[type="number"]').value;
                const newUnit = newRow.querySelectorAll('.material-select')[1].value;
                
                if (originalCode && originalName && originalQuantity && originalUnit &&
                    newCode && newName && newQuantity && newUnit) {
                    materials.push({
                        containerId: containerIndex + 1,
                        original: { code: originalCode, name: originalName, quantity: originalQuantity, unit: originalUnit },
                        replacement: { code: newCode, name: newName, quantity: newQuantity, unit: newUnit }
                    });
                }
            });
            
            return materials;
        }

        // Disable form after approval/rejection
        function disableForm() {
            const inputs = document.querySelectorAll('.material-input, .material-select, .reason-textarea');
            const buttons = document.querySelectorAll('.btn-add-material, .btn-remove-container, .btn-reset-materials, .btn-add-new-material, .btn-approve, .btn-reject');
            
            inputs.forEach(input => input.disabled = true);
            buttons.forEach(button => button.disabled = true);
        }

        // Show message
        function showMessage(type, text) {
            const messageEl = document.getElementById(type + 'Message');
            const textEl = document.getElementById(type + 'Text');
            
            textEl.textContent = text;
            messageEl.classList.add('show');
            
            setTimeout(() => {
                messageEl.classList.remove('show');
            }, 4000);
        }

        // Auto-resize textarea
        document.addEventListener('input', function(e) {
            if (e.target.matches('.reason-textarea')) {
                e.target.style.height = 'auto';
                e.target.style.height = e.target.scrollHeight + 'px';
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                approveRequest();
            }
            
            if (e.key === 'Escape') {
                // Clear current focus
                document.activeElement.blur();
            }
        });

        // Initialize form
        document.addEventListener('DOMContentLoaded', function() {
            updateRemoveButtons();
            showMessage('success', 'Form đã sẵn sàng để sử dụng!');
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