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
                            <div class="form-group">
                                <label>Mã phân công</label>
                                <select class="form-control" name="taskID" required>
                                    @foreach($gentasks as $task)
                                    <option value="{{ $task->id }}" {{ $task->id == $proposal->taskID ? 'selected' : ''
                                        }}>
                                        {{ $task->code }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Tên phiếu</label>
                                <input type="text" class="form-control" name="proposaName"
                                    value="{{ $proposal->proposaName }}" required>
                            </div>

                            <div class="form-group">
                                <label>Ngày gửi</label>
                                <input type="date" class="form-control" name="proposalDate"
                                    value="{{ $proposal->proposalDate }}" required>
                            </div>

                            <div class="form-group">
                                <label>Ngày nhận</label>
                                <input type="date" class="form-control" name="approvalDate"
                                    value="{{ $proposal->approvalDate }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tình Trạng</label>
                                <select name="request_status" class="form-control" required>
                                    <option value="Đã duyệt" {{ $proposal->request_status=='Đã duyệt' ? 'selected' : ''
                                        }}>Đã
                                        duyệt</option>
                                    <option value="Chờ duyệt" {{ $proposal->request_status=='Chờ duyệt' ? 'selected' :
                                        '' }}>Chờ
                                        duyệt</option>
                                    <option value="Từ chối" {{ $proposal->request_status=='Từ chối' ? 'selected' : ''
                                        }}>Từ chối
                                    </option>
                                </select>
                            </div>
                        </div>

                        <hr>
                        <h5>Danh sách vật tư</h5>
                        <button type="button" class="btn-add-row" onclick="addMaterialRow()" title="Thêm dòng mới">
                            <i class="fas fa-plus"></i>
                        </button>
                        <table class="table table-bordered">
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
                                @foreach($proposal->proposalProducts as $index => $item)
                                <tr>
                                    <input type="hidden" name="proposalProducts[{{ $index }}][id]"
                                        value="{{ $item->id }}">

                                    <td>
                                        <select name="proposalProducts[{{ $index }}][productID]" class="form-control">
                                            @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ $product->id == $item->productID ?
                                                'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="proposalProducts[{{ $index }}][quantity]"
                                            class="form-control" value="{{ $item->materialQuantity }}" required>
                                    </td>
                                    <td>
                                        <select name="proposalProducts[{{ $index }}][job]" class="form-control">
                                            @foreach($works as $work)
                                            <option value="{{ $work->id }}" {{ $work->id == $item->job ? 'selected' : ''
                                                }}>
                                                {{ $work->workName }}
                                            </option>
                                            @endforeach
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
                                    <td>
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
                            <a href="{{ route('workps.index') }}" class="btn btn-secondary">Hủy</a>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</section>
<script>
    let rowIndex = 1;

    function addMaterialRow() {
        const tableBody = document.getElementById('materialTableBody');
        const newRow = document.createElement('tr');

        newRow.innerHTML = `
            <td>
                <select name="proposalProducts[${rowIndex}][productID]" class="form-control" required>
                    <option value="">Chọn vật tư</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="proposalProducts[${rowIndex}][quantity]" class="form-control" placeholder="Số lượng" min="1" required>
            </td>
            <td>
                <select name="proposalProducts[${rowIndex}][job]" class="form-control">
                    <option value="">Chọn công việc</option>
                    @foreach($works as $work)
                    <option value="{{ $work->id }}">{{ $work->workName }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="hidden" name="proposalProducts[${rowIndex}][created_by]" value="{{ auth()->id() }}">
                <input type="text" name="proposalProducts[${rowIndex}][proposer_name]" class="form-control" value="{{ auth()->user()->name }}" readonly>
            </td>
            <td>
                <input type="text" name="proposalProducts[${rowIndex}][note]" class="form-control" placeholder="Ghi chú">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm btn-remove-row" onclick="removeMaterialRow(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        tableBody.appendChild(newRow);
        rowIndex++;
        updateRemoveButtons();
    }

    function removeMaterialRow(button) {
        if (confirm('Bạn có chắc muốn xóa dòng này?')) {
            const row = button.closest('tr');
            row.remove();
            updateRemoveButtons();
        }
    }

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('#materialTableBody tr');
        rows.forEach((row, index) => {
            const removeBtn = row.querySelector('.btn-remove-row');
            if (removeBtn) {
                removeBtn.style.display = rows.length > 1 ? 'inline-block' : 'none';
            }
        });
    }

    function getCurrentMaterialData() {
        const materials = [];
        const rows = document.querySelectorAll('#materialTableBody tr');

        rows.forEach(row => {
            const productID = row.querySelector('select[name*="[productID]"]')?.value;
            const quantity = row.querySelector('input[name*="[quantity]"]')?.value;
            const job = row.querySelector('select[name*="[job]"]')?.value;
            const proposer = row.querySelector('input[name*="[proposer_name]"]')?.value;
            const note = row.querySelector('input[name*="[note]"]')?.value;

            if (productID || quantity) {
                materials.push({
                    productID,
                    quantity,
                    job,
                    proposer,
                    note
                });
            }
        });

        console.log(materials);
        return materials;
    }

    // Gọi ngay khi trang load
    document.addEventListener('DOMContentLoaded', updateRemoveButtons);
</script>

@endsection