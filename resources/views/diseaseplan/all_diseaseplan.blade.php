@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0">Phiếu Cây Bệnh</h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Danh Sách Phiếu Cây Bệnh</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Add diseaseplan Modal -->
<form id="diseaseplan-form" action="{{ route('diseaseplans.save') }}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="modal fade" id="create-diseaseplan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Tạo mới</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <!-- Basic Information -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="medicalRecordId" class="form-label required">Mã Bệnh án</label>
                            <input type="text" id="medicalRecordId" class="form-input" placeholder="Nhập mã bệnh án"
                                required>
                        </div>
                        <div class="form-group wide">
                            <label for="medicalRecordName" class="form-label required">Tên Bệnh án</label>
                            <input type="text" id="medicalRecordName" class="form-input" placeholder="Nhập tên bệnh án"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="gardenId" class="form-label">Mã Vườn</label>
                            <input type="text" id="gardenId" class="form-input" placeholder="Nhập mã vườn">
                        </div>
                        <div class="form-group">
                            <label for="lotId" class="form-label">Mã Lô</label>
                            <input type="text" id="lotId" class="form-input" placeholder="Nhập mã lô">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="recordDate" class="form-label required">Ngày Ghi Nhận</label>
                            <input type="date" id="recordDate" class="form-input" required>
                        </div>
                        <div class="form-group wide">
                            <label for="diseaseName" class="form-label required">Tên Bệnh</label>
                            <input type="text" id="diseaseName" class="form-input" placeholder="Nhập tên bệnh" required>
                        </div>
                        <div class="form-group">
                            <label for="quantity" class="form-label">Số Lượng</label>
                            <input type="number" id="quantity" class="form-input" placeholder="Nhập số lượng">
                        </div>
                        <div class="form-group">
                            <label for="teamName" class="form-label">Tên Tổ</label>
                            <input type="text" id="teamName" class="form-input" placeholder="Nhập tên tổ">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group wide">
                            <label for="symptoms" class="form-label required">Biểu Hiện Bệnh</label>
                            <textarea id="symptoms" class="form-textarea" placeholder="Mô tả chi tiết biểu hiện bệnh..."
                                required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="spreadLevel" class="form-label required">Mức Độ Lây Lan</label>
                            <select id="spreadLevel" class="form-select" required>
                                <option value="">Chọn mức độ</option>
                                <option value="low">Thấp</option>
                                <option value="medium">Trung bình</option>
                                <option value="high">Cao</option>
                                <option value="critical">Nghiêm trọng</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group" style="flex: 1;">
                            <label for="cause" class="form-label required">Nguyên Nhân</label>
                            <textarea id="cause" class="form-textarea" placeholder="Mô tả nguyên nhân gây bệnh..."
                                required></textarea>
                        </div>
                    </div>

                    <!-- Treatment Plan Section -->
                    <div class="treatment-plan">
                        <h2 class="treatment-plan-title">Kế Hoạch Điều Trị</h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="startDate" class="form-label">Ngày Bắt Đầu</label>
                                <input type="date" id="startDate" class="form-input">
                            </div>
                            <div class="form-group">
                                <label for="endDate" class="form-label">Ngày Kết Thúc (Dự Kiến)</label>
                                <input type="date" id="endDate" class="form-input">
                            </div>
                            <div class="form-group">
                                <label for="assignedTo" class="form-label">Người Phụ Trách</label>
                                <input type="text" id="assignedTo" class="form-input"
                                    placeholder="Nhập tên người phụ trách">
                            </div>
                            <div class="form-group">
                                <label for="priority" class="form-label">Mức Độ Ưu Tiên</label>
                                <select id="priority" class="form-select">
                                    <option value="">Chọn Mức Độ</option>
                                    <option value="low">Thấp</option>
                                    <option value="medium">Trung Bình</option>
                                    <option value="high">Cao</option>
                                    <option value="urgent">Khẩn Cấp</option>
                                </select>
                            </div>
                        </div>

                        <div class="treatment-steps">
                            <div class="treatment-step">
                                <div class="step-number">1</div>
                                <div class="step-content">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Tên Thuốc/Vật Tư</label>
                                            <input type="text" class="form-input"
                                                placeholder="Nhập tên thuốc hoặc vật tư">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Liều Lượng</label>
                                            <input type="text" class="form-input" placeholder="Nhập liều lượng">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Ngày Thực Hiện</label>
                                            <input type="date" class="form-input">
                                        </div>
                                    </div>
                                    <div class="form-row" style="margin-bottom: 0;">
                                        <div class="form-group" style="flex: 1;">
                                            <label class="form-label">Hướng Dẫn</label>
                                            <textarea class="form-textarea" style="min-height: 60px;"
                                                placeholder="Nhập hướng dẫn chi tiết..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="step-actions">
                                    <button type="button" class="btn btn-outline" style="color: #ef4444;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="treatment-step">
                                <div class="step-number">2</div>
                                <div class="step-content">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Tên Thuốc/Vật Tư</label>
                                            <input type="text" class="form-input"
                                                placeholder="Nhập tên thuốc hoặc vật tư">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Liều Lượng</label>
                                            <input type="text" class="form-input" placeholder="Nhập liều lượng">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Ngày Thực Hiện</label>
                                            <input type="date" class="form-input">
                                        </div>
                                    </div>
                                    <div class="form-row" style="margin-bottom: 0;">
                                        <div class="form-group" style="flex: 1;">
                                            <label class="form-label">Hướng Dẫn</label>
                                            <textarea class="form-textarea" style="min-height: 60px;"
                                                placeholder="Nhập hướng dẫn chi tiết..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="step-actions">
                                    <button type="button" class="btn btn-outline" style="color: #ef4444;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="add-step-btn" onclick="alert('Thêm bước điều trị mới')">
                                <i class="fas fa-plus" style="margin-right: 0.5rem;"></i>
                                Thêm bước điều trị
                            </div>
                        </div>
                    </div>

                    <!-- Results and Notes -->
                    <div class="form-row" style="margin-top: 1.5rem;">
                        <div class="form-group" style="flex: 1;">
                            <label for="results" class="form-label">Kết Quả Điều Trị</label>
                            <textarea id="results" class="form-textarea"
                                placeholder="Ghi chú kết quả sau khi điều trị..."></textarea>
                        </div>
                    </div>

                    <!-- Form Footer -->
                    <div class="form-footer">
                        <div class="form-status">
                            <span>Trạng thái:</span>
                            <span class="status-badge status-draft">Bản Nháp</span>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-outline">
                                <i class="fas fa-times"></i>
                                Hủy
                            </button>
                            <button type="button" class="btn btn-secondary">
                                <i class="fas fa-save"></i>
                                Lưu Nháp
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i>
                                Hoàn Thành
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success" id="submit-btn-diseaseplan">Lưu</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- diseaseplan List -->
<div class="row">

    <div class="col-xl-12">
        <div class="card custom-card">
            {{-- <div class="container">
                <div class="page-header">
                    <h1 class="page-title">Phiếu điều trị</h1>
                    <div class="form-actions">
                        <button class="btn btn-outline">
                            <i class="fas fa-print"></i>
                            In phiếu
                        </button>
                        <button class="btn btn-success">
                            <i class="fas fa-save"></i>
                            Lưu phiếu
                        </button>
                    </div>
                </div>

                <form class="treatment-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="medicalRecordId" class="form-label required">Mã Bệnh án</label>
                            <input type="text" id="medicalRecordId" class="form-input" placeholder="Nhập mã bệnh án"
                                required>
                        </div>
                        <div class="form-group wide">
                            <label for="medicalRecordName" class="form-label required">Tên Bệnh án</label>
                            <input type="text" id="medicalRecordName" class="form-input" placeholder="Nhập tên bệnh án"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="gardenId" class="form-label">Mã Vườn</label>
                            <input type="text" id="gardenId" class="form-input" placeholder="Nhập mã vườn">
                        </div>
                        <div class="form-group">
                            <label for="lotId" class="form-label">Mã Lô</label>
                            <input type="text" id="lotId" class="form-input" placeholder="Nhập mã lô">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="recordDate" class="form-label required">Ngày Ghi Nhận</label>
                            <input type="date" id="recordDate" class="form-input" required>
                        </div>
                        <div class="form-group wide">
                            <label for="diseaseName" class="form-label required">Tên Bệnh</label>
                            <input type="text" id="diseaseName" class="form-input" placeholder="Nhập tên bệnh" required>
                        </div>
                        <div class="form-group">
                            <label for="quantity" class="form-label">Số Lượng</label>
                            <input type="number" id="quantity" class="form-input" placeholder="Nhập số lượng">
                        </div>
                        <div class="form-group">
                            <label for="teamName" class="form-label">Tên Tổ</label>
                            <input type="text" id="teamName" class="form-input" placeholder="Nhập tên tổ">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group wide">
                            <label for="symptoms" class="form-label required">Biểu Hiện Bệnh</label>
                            <textarea id="symptoms" class="form-textarea" placeholder="Mô tả chi tiết biểu hiện bệnh..."
                                required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="spreadLevel" class="form-label required">Mức Độ Lây Lan</label>
                            <select id="spreadLevel" class="form-select" required>
                                <option value="">Chọn mức độ</option>
                                <option value="low">Thấp</option>
                                <option value="medium">Trung bình</option>
                                <option value="high">Cao</option>
                                <option value="critical">Nghiêm trọng</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group" style="flex: 1;">
                            <label for="cause" class="form-label required">Nguyên Nhân</label>
                            <textarea id="cause" class="form-textarea" placeholder="Mô tả nguyên nhân gây bệnh..."
                                required></textarea>
                        </div>
                    </div>

                    <div class="treatment-plan">
                        <h2 class="treatment-plan-title">Kế Hoạch Điều Trị</h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="startDate" class="form-label">Ngày Bắt Đầu</label>
                                <input type="date" id="startDate" class="form-input">
                            </div>
                            <div class="form-group">
                                <label for="endDate" class="form-label">Ngày Kết Thúc (Dự Kiến)</label>
                                <input type="date" id="endDate" class="form-input">
                            </div>
                            <div class="form-group">
                                <label for="assignedTo" class="form-label">Người Phụ Trách</label>
                                <input type="text" id="assignedTo" class="form-input"
                                    placeholder="Nhập tên người phụ trách">
                            </div>
                            <div class="form-group">
                                <label for="priority" class="form-label">Mức Độ Ưu Tiên</label>
                                <select id="priority" class="form-select">
                                    <option value="">Chọn Mức Độ</option>
                                    <option value="low">Thấp</option>
                                    <option value="medium">Trung Bình</option>
                                    <option value="high">Cao</option>
                                    <option value="urgent">Khẩn Cấp</option>
                                </select>
                            </div>
                        </div>

                        <div class="treatment-steps">
                            <div class="treatment-step">
                                <div class="step-number">1</div>
                                <div class="step-content">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Tên Thuốc/Vật Tư</label>
                                            <input type="text" class="form-input"
                                                placeholder="Nhập tên thuốc hoặc vật tư">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Liều Lượng</label>
                                            <input type="text" class="form-input" placeholder="Nhập liều lượng">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Ngày Thực Hiện</label>
                                            <input type="date" class="form-input">
                                        </div>
                                    </div>
                                    <div class="form-row" style="margin-bottom: 0;">
                                        <div class="form-group" style="flex: 1;">
                                            <label class="form-label">Hướng Dẫn</label>
                                            <textarea class="form-textarea" style="min-height: 60px;"
                                                placeholder="Nhập hướng dẫn chi tiết..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="step-actions">
                                    <button type="button" class="btn btn-outline" style="color: #ef4444;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="treatment-step">
                                <div class="step-number">2</div>
                                <div class="step-content">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Tên Thuốc/Vật Tư</label>
                                            <input type="text" class="form-input"
                                                placeholder="Nhập tên thuốc hoặc vật tư">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Liều Lượng</label>
                                            <input type="text" class="form-input" placeholder="Nhập liều lượng">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Ngày Thực Hiện</label>
                                            <input type="date" class="form-input">
                                        </div>
                                    </div>
                                    <div class="form-row" style="margin-bottom: 0;">
                                        <div class="form-group" style="flex: 1;">
                                            <label class="form-label">Hướng Dẫn</label>
                                            <textarea class="form-textarea" style="min-height: 60px;"
                                                placeholder="Nhập hướng dẫn chi tiết..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="step-actions">
                                    <button type="button" class="btn btn-outline" style="color: #ef4444;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="add-step-btn" onclick="alert('Thêm bước điều trị mới')">
                                <i class="fas fa-plus" style="margin-right: 0.5rem;"></i>
                                Thêm bước điều trị
                            </div>
                        </div>
                    </div>

                    <div class="form-row" style="margin-top: 1.5rem;">
                        <div class="form-group" style="flex: 1;">
                            <label for="results" class="form-label">Kết Quả Điều Trị</label>
                            <textarea id="results" class="form-textarea"
                                placeholder="Ghi chú kết quả sau khi điều trị..."></textarea>
                        </div>
                    </div>
                    <div class="form-footer">
                        <div class="form-status">
                            <span>Trạng thái:</span>
                            <span class="status-badge status-draft">Bản Nháp</span>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-outline">
                                <i class="fas fa-times"></i>
                                Hủy
                            </button>
                            <button type="button" class="btn btn-secondary">
                                <i class="fas fa-save"></i>
                                Lưu Nháp
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i>
                                Hoàn Thành
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const today = new Date().toISOString().split('T')[0];
                    document.getElementById('recordDate').value = today;
                    document.getElementById('startDate').value = today;
                    
                    const nextWeek = new Date();
                    nextWeek.setDate(nextWeek.getDate() + 7);
                    document.getElementById('endDate').value = nextWeek.toISOString().split('T')[0];
                });
            </script> --}}
            <div class="card-header d-flex justify-content-between align-items-center" style="grid-gap: 3px;">
                {{-- <h5>Danh Sách Phiếu Cây Bệnh</h5> --}}
                {{-- <button class="btn btn-sm btn-success btn-wave waves-light" data-bs-toggle="modal"
                    data-bs-target="#create-diseaseplan">
                    <i class="fa fa-plus"></i> Tạo Phiếu Cây Bệnh
                </button> --}}
                {{-- <button class="btn btn-sm btn-success">
                    <i class="fa fa-plus"></i><a href="{{ route('diseaseplans.add') }}" class="text-white"> Tạo Phiếu
                        Cây Bệnh</a>
                </button> --}}
            </div>
            <div class="container mt-2">
                <div class="row g-3">
                    <div class="col-md-2">
                        <input type="text" class="form-control" placeholder="Mã bệnh án" id="maBenhAn">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" id="ngayGhiNhan">
                    </div>
                    <div class="col-md-1">
                        <input type="text" class="form-control" placeholder="Vườn" id="vuon">
                    </div>
                    <div class="col-md-1">
                        <input type="text" class="form-control" placeholder="Lô" id="lo">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" placeholder="Tên bệnh" id="tenBenh">
                    </div>
                    <div class="col-md-3 d-flex align-items-center">
                        <button class="btn btn-success btn-w" onclick="locBenhAn()"><i
                                class="fa-light fa-filter-list"></i>
                            Lọc
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                {{-- <div style="position: relative;"> --}}
                    {{-- <div class="table-responsive"> --}}
                        <table id="diseaseplan-table" class="table table-bordered text-nowrap w-100">
                            <div id="buttons-container" class="d-flex justify-content-end gap-2">
                                <button id="edit-selected-btn" class="btn btn-warning"
                                    style="border-radius: 7px; color: #FFFFFF; display: none">Không/Hoạt
                                    Động</button>
                                <button id="delete-selected-btn" class="btn btn-danger"
                                    style="border-radius: 7px; display: none;">
                                    Xóa
                                </button>
                                <button id="add-selected-btn" class="btn btn-success" style="border-radius: 7px;">
                                    <i class="fa fa-plus"></i><a href="{{ route('diseaseplans.add') }}"
                                        class="text-white"> Tạo Phiếu
                                        Cây Bệnh</a>
                                </button>
                            </div>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>
                                        <input class="form-check-input check-all" type="checkbox"
                                            id="select-all-diseaseplan" value="" aria-label="...">
                                    </th>
                                    {{-- <th scope="col">STT</th> --}}
                                    <th scope="col">Mã</th>
                                    <th scope="col">Tên Phiếu Cây Bệnh</th>
                                    <th scope="col">Loại Phiếu Cây Bệnh</th>
                                    <th scope="col">Ngày Làm</th>
                                    <th scope="col">Thực Hiện</th>
                                    <th scope="col">Trạng Thái</th>
                                    <th scope="col">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this section -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    {{-- <th scope="col">STT</th> --}}
                                    <th scope="col">Mã</th>
                                    <th scope="col">Tên Phiếu Cây Bệnh</th>
                                    <th scope="col">Loại Phiếu Cây Bệnh</th>
                                    <th scope="col">Ngày Làm</th>
                                    <th scope="col">Thực Hiện</th>
                                    <th scope="col">Trạng Thái</th>
                                    <th scope="col">Thao Tác</th>
                                </tr>
                            </tfoot>
                        </table>
                        {{--
                    </div> --}}
                    {{-- </div> --}}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Xác Nhận Cập Nhật</h5>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn chắc chắn muốn cập nhật trạng thái đã chọn?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" id="confirmUpdateBtn" class="btn btn-success">Xác Nhận</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Xoa -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác Nhận Xóa</h5>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn chắc chắn muốn xóa Phiếu Cây Bệnh đã chọn?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-success">Xác Nhận</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
            var selectedRows = new Set();
            var dataTable = $('#diseaseplan-table').DataTable({
                // "language": {
                //     "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json",
                //     "emptyTable": "Không có dữ liệu",
                // },
                processing: true,
                serverSide: true,
                // responsive: true,
                columnDefs: [{
                    className: 'dtr-control',
                    orderable: false,
                    targets: 0,
                }],
                order: [1, 'asc'],
                responsive: {
                    details: {
                        type: 'column',
                        renderer: function(api, rowIdx, columns) {
                            var data = $.map(columns, function(col, i) {
                                return col.hidden ?
                                    '<li data-dtr-index="' + i + '" data-dt-row="' + rowIdx +
                                    '" data-dt-column="' + col.columnIndex + '">' +
                                    '<span class="dtr-title">' + col.title + ':</span> ' +
                                    '<span class="dtr-data">' + col.data + '</span>' +
                                    '</li>' :
                                    '';
                            }).join('');

                            return data ? $('<ul data-dtr-index="' + rowIdx + '" class="dtr-details"/>')
                                .append(data) : false;
                        }
                    }
                },
                ajax: {
                    url: "{{ route('diseaseplans.index') }}",
                    type: 'GET'
                },
                columns: [{
                        data: null,
                        name: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '';
                        }

                    },
                    {
                        data: 'check',
                        name: 'check',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'diseaseName',
                        name: 'diseaseName'
                    },
                    {
                        data: 'plantCode',
                        name: 'plantCode'
                    },
                    {
                        data: 'detectionDate',
                        name: 'detectionDate'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                rowCallback: function(row, data) {
                    $(row).attr('data-id', data.id);
                }
            });
            $('#select-all-diseaseplan').on('change', function() {
                var checked = $(this).prop('checked');
                $('#diseaseplan-table tbody .form-check-input').each(function() {
                    var workId = $(this).data('id');
                    if (checked) {
                        selectedRows.add(workId);
                    } else {
                        selectedRows.delete(workId);
                    }
                    $(this).prop('checked', checked);
                });
                toggleButtons();

                console.log([...selectedRows]);
            });
            $('#diseaseplan-table tbody').on('change', '.form-check-input', function() {
                var workId = $(this).data('id');
                toggleButtons();

                if ($(this).prop('checked')) {
                    selectedRows.add(workId);
                } else {
                    selectedRows.delete(workId);
                }
                console.log([...selectedRows]);
            });

            $('#diseaseplan-table').on('draw.dt', function() {
                $('#diseaseplan-table tbody .form-check-input').each(function() {
                    var workId = $(this).data('id');
                    if (selectedRows.has(workId)) {
                        $(this).prop('checked', true);
                    }
                });
            });
            $('#edit-selected-btn').on('click', function() {
                $('#confirmModal').modal('show');
                $('#confirmUpdateBtn').on('click', function() {
                    $.ajax({
                        url: '/diseaseplans/edit-multiple',
                        type: 'POST',
                        data: {
                            ids: [...selectedRows],
                            status: 'Không hoạt động'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: 'Trạng thái đã được cập nhật.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                selectedRows.clear();
                                $('#confirmModal').modal('hide');
                                // $('#buttons-container').hide();
                                $('#edit-selected-btn').hide();
                                $('#delete-selected-btn').hide();
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: 'Có lỗi khi cập nhật trạng thái.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });
                $('#confirmModal').on('hidden.bs.modal', function() {
                    $('#confirmUpdateBtn').off('click');
                });
            });
            $('#delete-selected-btn').on('click', function() {
                $('#deleteModal').modal('show');
                $('#confirmDeleteBtn').on('click', function() {
                    $.ajax({
                        url: '/diseaseplans/delete-multiple',
                        type: 'POST',
                        data: {
                            ids: [...selectedRows]
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: 'Dữ liệu đã được xóa thành công.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                selectedRows.clear();
                                $('#deleteModal').modal('hide');
                                // $('#buttons-container').hide();
                                $('#edit-selected-btn').hide();
                                $('#delete-selected-btn').hide();
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: 'Có lỗi khi xóa dữ liệu.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });

                $('#deleteModal').on('hidden.bs.modal', function() {
                    $('#confirmDeleteBtn').off('click');
                });
            });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function toggleButtons() {
                var selected = $('#diseaseplan-table tbody .form-check-input:checked').length;
                if (selected > 0) {
                    $('#edit-selected-btn').show();
                    $('#delete-selected-btn').show();
                } else {
                    $('#edit-selected-btn').hide();
                    $('#delete-selected-btn').hide();
                }
                $('#add-selected-btn').show();
            }

        });
</script>

<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.5/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.5/dist/sweetalert2.min.js"></script>

<script>
    $(document).on('click', '.toggle-status', function(e) {
            e.preventDefault();

            let button = $(this);
            let id = button.data('id');

            Swal.fire({
                title: "Xác nhận thay đổi",
                text: "Bạn có chắc chắn muốn thay đổi trạng thái của Phiếu Cây Bệnh này?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Thay đổi",
                cancelButtonText: "Hủy"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('farm.status') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                if (response.status === 'Hoạt động') {
                                    button.removeClass('bg-danger').addClass('bg-success').text(
                                        'Hoạt động');
                                } else {
                                    button.removeClass('bg-success').addClass('bg-danger').text(
                                        'Không hoạt động');
                                }

                                Swal.fire({
                                    text: 'Trạng thái của Phiếu Cây Bệnh đã được cập nhật.',
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    timer: 3000
                                });
                            } else {
                                Swal.fire({
                                    text: response.message ||
                                        'Không thể thay đổi trạng thái của Phiếu Cây Bệnh.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    timer: 3000
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                text: 'Không thể thay đổi trạng thái, vui lòng thử lại.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                timer: 3000
                            });
                        }
                    });
                }
            });
        });
</script>

@endsection