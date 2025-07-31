@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold mb-0 title-header padding">Quản Lý Công Việc</h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Danh Sách Công Việc</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<form id="works-form" action="{{ route('works.save') }}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="modal fade" id="create-works" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Tạo Công Việc</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="row gy-2">
                        <div class="form-row">
                            <!-- Form chọn công việc -->
                            <div class="form-group">
                                {{-- <input type="text" name="code" class="form-control mt-2" placeholder=""> --}}

                                <label>Tên Việc</label>

                                <select name="workID" class="form-select" id="workID-select" required>
                                    <option value="">Chọn công việc</option>
                                    @foreach ($works as $work)
                                    <option value="{{ $work->id }}" data-type="{{ $work->workType }}">{{ $work->workName
                                        }}
                                    </option>
                                    @endforeach
                                </select>
                                <input type="text" id="work-type-display" class="form-control mt-2" readonly
                                    placeholder="Loại công việc sẽ hiển thị ở đây">
                            </div>
                            <!-- Tự động hiển thị Loại -->

                            <div class="form-group">
                                <label>Người phụ trách</label>
                                <select name="workerID" class="form-select">
                                    @foreach ($workers as $worker)
                                    <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row">

                            <div class="form-group">
                                <label>Ngày bắt đầu</label>
                                <input type="date" name="workDate" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Lô</label>
                                <select name="plotID" class="form-select">
                                    @foreach ($plots as $plot)
                                    <option value="{{ $plot->id }}">{{ $plot->plotName }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row">

                            <div class="form-group">
                                <label>Loại nhiệm vụ</label>
                                <select name="type" class="form-select">
                                    <option value="Khai thác">Khai thác</option>
                                    <option value="Chăm Sóc">Chăm Sóc</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Mức độ ưu tiên</label>
                                <select name="priority" class="form-select">
                                    <option value="Thấp">Thấp</option>
                                    <option value="Trung bình">Trung bình</option>
                                    <option value="Cao">Cao</option>
                                    <option value="Khẩn cấp">Khẩn cấp</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Mô tả công việc</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success" id="submit-btn-works">Lưu</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- works List -->
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
                            {{-- <div class="form-group">
                                <label class="form-label">Tìm Kiếm</label>
                                <input type="text" class="form-input" id="taskKeyword"
                                    placeholder="Tìm kiếm công việc...">
                            </div> --}}
                            <div class="form-group">
                                <label class="form-label">Loại công việc</label>
                                <select class="form-select" id="taskType">
                                    <option value="">Tất cả</option>
                                    <option value="Tưới nước">Tưới nước</option>
                                    <option value="Bón phân">Bón phân</option>
                                    <option value="Cắt tỉa">Cắt tỉa</option>
                                    <option value="Phun thuốc">Phun thuốc</option>
                                    <option value="Thu hoạch">Thu hoạch</option>
                                    <option value="Làm cỏ">Làm cỏ</option>
                                    <option value="Kiểm tra">Kiểm tra</option>
                                </select>
                            </div>
                            {{-- <div class="form-group">
                                <label class="form-label">Vườn</label>
                                <select class="form-select" id="taskGarden">
                                    <option value="">Tất cả vườn</option>
                                    <option value="Khu A">Khu A</option>
                                    <option value="Khu B">Khu B</option>
                                    <option value="Khu C">Khu C</option>
                                    <option value="Khu D">Khu D</option>
                                </select>
                            </div> --}}
                            <div class="form-group">
                                <label class="form-label">Lô</label>
                                <select class="form-select" id="taskLot">
                                    <option value="">Tất cả lô</option>
                                    <option value="Lô 1">Lô 1</option>
                                    <option value="Lô 2">Lô 2</option>
                                    <option value="Lô 3">Lô 3</option>
                                    <option value="Lô 4">Lô 4</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Mức độ ưu tiên</label>
                                <select class="form-select" id="taskPriority">
                                    <option value="">Tất cả</option>
                                    <option value="Thấp">Thấp</option>
                                    <option value="Trung bình">Trung bình</option>
                                    <option value="Cao">Cao</option>
                                    <option value="Khẩn cấp">Khẩn cấp</option>
                                </select>
                            </div>
                        </div>

                        <!-- Second Filter Row -->
                        <div class="form-row">

                            <div class="form-group">
                                <label class="form-label">Thời gian bắt đầu</label>
                                <input type="date" class="form-input" id="taskStartDate">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Thời gian kết thúc</label>
                                <input type="date" class="form-input" id="taskEndDate">
                            </div>
                            <div class="form-group" style="display: flex; align-items: end;">
                                <button class="btn btn-success btn-w" onclick="filterTasks()">
                                    <i class="fa-light fa-filter-list"></i>
                                    Lọc
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-content">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <!-- Task Filter Buttons -->
                    <div class="task-filter-buttons" style="display: flex; gap: 0.5rem;">
                        <button class="task-filter-btn active" data-filter="all" onclick="setTaskFilter('all')">
                            <span>Danh Sách Công Việc</span>
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
                    <button class="btn btn-success">
                        <a href="{{route('works.add')}}" class="text-white">
                            <i class="fas fa-plus"></i>
                            Thêm Công Việc</a>
                    </button>
                    {{-- <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#create-works">
                        <i class="fas fa-plus"></i>
                        Thêm Công Việc
                    </button> --}}
                </div>

                <div class="card-body">
                    <table id="works-table" class="table table-bordered text-nowrap w-100">
                        <div id="buttons-container" class="d-flex justify-content-end gap-2">
                            <button id="edit-selected-btn" class="btn btn-warning"
                                style="border-radius: 30px; color: #FFFFFF">Không/Hoạt
                                Động</button>
                            <button id="delete-selected-btn" class="btn btn-danger" style="border-radius: 30px;">
                                Xóa
                            </button>
                        </div>
                        <thead>
                            <tr>
                                <th></th>
                                <th>
                                    <input class="form-check-input check-all" type="checkbox" id="select-all-works"
                                        value="" aria-label="...">
                                </th>
                                {{-- <th scope="col">STT</th> --}}
                                <th scope="col">Mã</th>
                                <th scope="col">Tên Công Việc</th>
                                <th scope="col">Loại Công Việc</th>
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
                                <th scope="col">Tên Công Việc</th>
                                <th scope="col">Loại Công Việc</th>
                                <th scope="col">Ngày Làm</th>
                                <th scope="col">Thực Hiện</th>
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
                    Bạn chắc chắn muốn xóa Công Việc đã chọn?
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
            var dataTable = $('#works-table').DataTable({
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
                    url: '{{ route('works.index') }}',
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
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'workName',
                        name: 'workName'
                    },
                    {
                        data: 'workType',
                        name: 'workType'
                    },
                    {
                        data: 'workDate',
                        name: 'workDate'
                    },
                    {
                        data: 'workerID',
                        name: 'workerID'
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
            $('#select-all-works').on('change', function() {
                var checked = $(this).prop('checked');
                $('#works-table tbody .form-check-input').each(function() {
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
            $('#works-table tbody').on('change', '.form-check-input', function() {
                var workId = $(this).data('id');
                toggleButtons();

                if ($(this).prop('checked')) {
                    selectedRows.add(workId);
                } else {
                    selectedRows.delete(workId);
                }
                console.log([...selectedRows]);
            });

            $('#works-table').on('draw.dt', function() {
                $('#works-table tbody .form-check-input').each(function() {
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
                        url: '/works/edit-multiple',
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
                                $('#buttons-container').hide();
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
                        url: '/works/delete-multiple',
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
                                $('#buttons-container').hide();
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
                var selected = $('#works-table tbody .form-check-input:checked').length;
                if (selected > 0) {
                    $('#buttons-container').css('visibility', 'visible');
                } else {
                    $('#buttons-container').css('visibility', 'hidden');
                }
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
                text: "Bạn có chắc chắn muốn thay đổi trạng thái của Công Việc này?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Thay đổi",
                cancelButtonText: "Hủy"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('works.status') }}',
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
                                    text: 'Trạng thái của Công Việc đã được cập nhật.',
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    timer: 3000
                                });
                            } else {
                                Swal.fire({
                                    text: response.message ||
                                        'Không thể thay đổi trạng thái của Công Việc.',
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
    // JS: Gán loại công việc tương ứng
    document.getElementById('workID-select').addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const type = selected.getAttribute('data-type');
        document.getElementById('work-type-display').value = type || '';
    });

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
        function filterTasks() {
            const keywordEl = document.getElementById('taskKeyword');
            const typeEl = document.getElementById('taskType');
            const gardenEl = document.getElementById('taskGarden');
            const lotEl = document.getElementById('taskLot');
            const priorityEl = document.getElementById('taskPriority');
            const startDateEl = document.getElementById('taskStartDate');
            const endDateEl = document.getElementById('taskEndDate');
            
            const keyword = keywordEl ? keywordEl.value.toLowerCase() : '';
            const type = typeEl ? typeEl.value : '';
            const garden = gardenEl ? gardenEl.value : '';
            const lot = lotEl ? lotEl.value : '';
            const priority = priorityEl ? priorityEl.value : '';
            const startDate = startDateEl ? startDateEl.value : '';
            const endDate = endDateEl ? endDateEl.value : '';

            // First apply search filters
            let searchFiltered = tasks.filter(task => {
                if (keyword && !task.name.toLowerCase().includes(keyword)) return false;
                if (type && task.type !== type) return false;
                if (garden && task.garden !== garden) return false;
                if (lot && task.lot !== lot) return false;
                if (priority && task.priority !== priority) return false;
                if (startDate && task.startDate < startDate) return false;
                if (endDate && task.startDate > endDate) return false;
                return true;
            });

            // Then apply status filter
            switch(currentTaskFilter) {
                case 'pending':
                    filteredTasks = searchFiltered.filter(task => !task.completed);
                    break;
                case 'completed':
                    filteredTasks = searchFiltered.filter(task => task.completed);
                    break;
                case 'all':
                default:
                    filteredTasks = searchFiltered;
                    break;
            }

            renderTasks();
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
<style>
    #buttons-container {
        visibility: hidden;
    }
</style>
@endsection