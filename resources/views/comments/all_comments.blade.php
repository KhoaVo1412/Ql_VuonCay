@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold mb-0 title-header padding">Quản Lý Đánh Giá</h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Danh Sách Đánh Giá</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Add comments Modal -->
<form id="comments-form" action="{{ route('comments.save') }}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="modal fade" id="create-comments" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Tạo Đánh Giá</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="row gy-2">
                        <div class="form-row">

                            <div class="form-group">
                                <label for="name" class="form-label required">Tên Đánh Giá</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-input"
                                    placeholder="Tên đánh giá" required>
                            </div>
                        </div>
                        <div class="form-row">

                            <div class="form-group">
                                <label for="workerID" class="form-label required">Nhân Viên</label>
                                <select name="workerID" id="workerID" class="form-select" required>
                                    <option value="">Chọn nhân viên</option>
                                    @foreach($workers as $worker)
                                    <option value="{{ $worker->id }}" {{ old('workerID')==$worker->id ? 'selected' : ''
                                        }}>
                                        {{ $worker->name }}
                                    </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="deductionPoints" class="form-label required">Điểm Đánh Giá</label>
                                <input type="number" class="form-input" name="deductionPoints" id="deductionPoints"
                                    value="{{ old('deductionPoints', 0) }}" min="0" class="form-inputr" required>
                            </div>
                            <div class="form-group">
                                <label for="rating" class="form-label required">Xếp Hạng</label>
                                <input type="text" name="rating" class="form-input" id="rating"
                                    value="{{ old('rating') }}" class="form-inputr" required>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <label class="form-label">Ngày Đánh Giá</label>
                            <input type="date" class="form-control" name="date_comment"
                                value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-xl-6">
                            <label class="form-label">Số Lượng Công Việc</label>
                            <input type="number" class="form-control" id="countWork_display" value="0" readonly>
                            <input type="hidden" name="countWork" id="countWork" value="0" required>
                        </div>
                        <div class="col-xl-6">
                            <label class="form-label">Hoàn Thành Đúng Hạn</label>
                            <input type="number" class="form-control" id="countCofirm_display" value="0" readonly>
                            <input type="hidden" name="countCofirm" id="countCofirm" value="0" required>
                        </div>
                        <div class="col-xl-6">
                            <label class="form-label">Hoàn Thành Không Đúng Hạn</label>
                            <input type="number" class="form-control" id="countUn_display" value="0" readonly>
                            <input type="hidden" name="countUn" id="countUn" value="0" required>
                        </div>
                        <script>
                            document.getElementById('workerID').addEventListener('change', async function () {
                                const workerID = this.value;
                                if (!workerID) return;
                                try {
                                    const res = await fetch(`/workers/${workerID}/task-stats`, {
                                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                    });
                                    const data = await res.json();
                                    document.getElementById('countWork_display').value = data.countWork ?? 0;
                                    document.getElementById('countCofirm_display').value = data.countCofirm ?? 0;
                                    document.getElementById('countUn_display').value = data.countUn ?? 0;
                                    document.getElementById('countWork').value = data.countWork ?? 0;
                                    document.getElementById('countCofirm').value = data.countCofirm ?? 0;
                                    document.getElementById('countUn').value = data.countUn ?? 0;
                                } catch (e) {
                                    console.error(e);
                                    ['countWork', 'countCofirm', 'countUn'].forEach(k => {
                                        document.getElementById(k + '_display').value = 0;
                                        document.getElementById(k).value = 0;
                                    });
                                }
                            });
                        </script>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="note" class="form-label">Ghi Chú</label>
                                <textarea name="note" id="note" rows="4" class="form-input"
                                    placeholder="Ghi chú thêm...">{{ old('note') }}</textarea>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success" id="submit-btn-comments">Lưu</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- comments List -->
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between align-items-center" style="grid-gap: 3px">
            </div>
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="search-box">
                        <input type="text" class="search-inputs" placeholder="Tìm kiếm...">
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-content">
                    <div class="form-section">
                        <!-- First Filter Row -->
                        <div class="form-row">

                            <div class="form-group">
                                <label class="form-label">Tổ</label>
                                <select class="form-select" id="plotID" required>
                                    <option value="">Tất Cả</option>
                                    @foreach($plots as $p)
                                    <option value="{{ $p->id }}">{{ $p->plotName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Ngày Đánh Giá</label>
                                <input type="date" class="form-input" id="Datecomment">
                            </div>
                            <div class="form-group" style="display: flex; align-items: end;">
                                {{-- <button class="btn btn-success btn-w" onclick="filterTasks()">
                                    <i class="fa-light fa-filter-list"></i>
                                    Lọc
                                </button> --}}
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <div class="card-content">
                {{-- <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="task-filter-buttons" style="display: flex; gap: 0.5rem;">
                        <button class="task-filter-btn active" data-filter="all" onclick="setTaskFilter('all')">
                            <span>Danh Sách Công Nhân</span>
                            <span class="task-count" id="allTasksCount"></span>
                        </button>
                        <span class="filter-separator">|</span>
                        <button class="task-filter-btn" data-filter="pending" onclick="setTaskFilter('pending')">
                            <span>Chưa Hoàn Thành</span>
                            <span class="task-count" id="pendingTasksCount"></span>
                        </button>
                        <span class="filter-separator">|</span>
                        <button class="task-filter-btn" data-filter="completed" onclick="setTaskFilter('completed')">
                            <span>Đã Hoàn Thành</span>
                            <span class="task-count" id="completedTasksCount"></span>
                        </button>


                    </div>
                </div> --}}

                <div class="card-body">
                    <table id="comments-table" class="table table-bordered text-nowrap w-100">
                        <div id="buttons-container" class="d-flex justify-content-end gap-2">
                            <button id="edit-selected-btn" class="btn btn-warning"
                                style="border-radius: 30px; color: #FFFFFF; display: none">Không/Hoạt
                                Động</button>
                            <button id="delete-selected-btn" class="btn btn-danger"
                                style="border-radius: 30px; display: none;">
                                Xóa
                            </button>
                            @unlessrole('Công Nhân')
                            <button id="add-selected-btn" class="btn btn-success" style="border-radius: 7px;"
                                data-bs-toggle="modal" data-bs-target="#create-comments">
                                <i class="fa fa-plus"></i>Tạo Đánh Giá
                            </button>
                            @endunlessrole
                        </div>
                        <thead>
                            <tr>
                                <th></th>
                                <th>
                                    <input class="form-check-input check-all" type="checkbox" id="select-all-comments"
                                        value="" aria-label="...">
                                </th>
                                <th scope="col">Tên Đánh Giá</th>
                                <th scope="col">Tên Công Nhân</th>
                                <th scope="col">Ngày Đánh Giá</th>
                                <th scope="col">Điểm</th>
                                <th scope="col">Xếp Loại</th>
                                <th scope="col">Ý Kiến Công Nhân</th>
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
                                <th scope="col">Tên Đánh Giá</th>
                                <th scope="col">Tên Công Nhân</th>
                                <th scope="col">Ngày Đánh Giá</th>
                                <th scope="col">Điểm</th>
                                <th scope="col">Xếp Loại</th>
                                <th scope="col">Ý Kiến Công Nhân</th>
                                <th scope="col">Trạng Thái</th>
                                <th scope="col">Thao Tác</th>
                            </tr>
                        </tfoot>
                    </table>
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
                    Bạn chắc chắn muốn xóa Đánh Giá đã chọn?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-success">Xác Nhận</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
            var selectedRows = new Set();
            var dataTable = $('#comments-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json",
                    "emptyTable": "Không có dữ liệu",
                },
                dom: 'lrtip',
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
                    url: '{{ route('comments.index') }}',
                    type: 'GET',
                    data: function (d) {
                        d.start_date = $('#Datecomment').val();
                        d.plot_id   = $('#plotID').val();
                    }
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
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'workerID',
                        name: 'workerID'
                    },
                    {
                        data: 'date_comment',
                        name: 'date_comment'
                    },
                    {
                        data: 'deductionPoints',
                        name: 'deductionPoints'
                    },
                    {
                        data: 'rating',
                        name: 'rating'
                    },
                    {
                        data: 'opinion',
                        name: 'opinion'
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
            let t;
            $('.search-inputs').on('input', function () {
                clearTimeout(t);
                const v = this.value;
                t = setTimeout(() => dataTable.search(v).draw(), 250);
            });
            $('#Datecomment, #plotID').on('change', function () {
                dataTable.ajax.reload(null, true);
            });
            $('#select-all-comments').on('change', function() {
                var checked = $(this).prop('checked');
                $('#comments-table tbody .form-check-input').each(function() {
                    var commenID = $(this).data('id');
                    if (checked) {
                        selectedRows.add(commenID);
                    } else {
                        selectedRows.delete(commenID);
                    }
                    $(this).prop('checked', checked);
                });
                toggleButtons();

                console.log([...selectedRows]);
            });
            $('#comments-table tbody').on('change', '.form-check-input', function() {
                var commenID = $(this).data('id');
                toggleButtons();

                if ($(this).prop('checked')) {
                    selectedRows.add(commenID);
                } else {
                    selectedRows.delete(commenID);
                }
                console.log([...selectedRows]);
            });

            $('#comments-table').on('draw.dt', function() {
                $('#comments-table tbody .form-check-input').each(function() {
                    var commenID = $(this).data('id');
                    if (selectedRows.has(commenID)) {
                        $(this).prop('checked', true);
                    }
                });
            });
            $('#edit-selected-btn').on('click', function() {
                $('#confirmModal').modal('show');
                $('#confirmUpdateBtn').on('click', function() {
                    $.ajax({
                        url: '/comments/edit-multiple',
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
                console.log('Selected rows:', selectedRows);
                $('#deleteModal').modal('show');
                $('#confirmDeleteBtn').on('click', function() {
                    $.ajax({
                        url: '/comments/delete-multiple',
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
                var selected = $('#comments-table tbody .form-check-input:checked').length;
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

<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.5/dist/sweetalert2.min.css" rel="stylesheet">
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.5/dist/sweetalert2.min.js"></script>

<script>
    $(document).on('click', '.toggle-status', function(e) {
            e.preventDefault();

            let button = $(this);
            let id = button.data('id');

            Swal.fire({
                title: "Xác nhận thay đổi",
                text: "Bạn có chắc chắn muốn thay đổi trạng thái của Đánh Giá này?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Thay đổi",
                cancelButtonText: "Hủy"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('comments.status') }}',
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
                                    text: 'Trạng thái của Đánh Giá đã được cập nhật.',
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    timer: 3000
                                });
                            } else {
                                Swal.fire({
                                    text: response.message ||
                                        'Không thể thay đổi trạng thái của Đánh Giá.',
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
<script>
    function updateTaskCounts() {
            const total = tasks.length;
            const completed = tasks.filter(task => task.completed).length;
            const pending = total - completed;

            const allCountEl = document.getElementById('allTasksCount');
            const completedCountEl = document.getElementById('completedTasksCount');
            const pendingCountEl = document.getElementById('pendingTasksCount');
            
            if (allCountEl) allCountEl.textContent = total;
            if (completedCountEl) completedCountEl.textContent = completed;
            if (pendingCountEl) pendingCountEl.textContent = pending;
        }
        function setTaskFilter(filter) {
            currentTaskFilter = filter;
            
            // Update button states
            document.querySelectorAll('.task-filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-filter="${filter}"]`).classList.add('active');
            
            // Apply filter
            applyTaskFilter();
        }
        function applyTaskFilter() {
            switch(currentTaskFilter) {
                case 'pending':
                    filteredTasks = tasks.filter(task => !task.completed);
                    break;
                case 'completed':
                    filteredTasks = tasks.filter(task => task.completed);
                    break;
                case 'all':
                default:
                    filteredTasks = [...tasks];
                    break;
            }
            
            renderTasks();
        }
</script>

@endsection