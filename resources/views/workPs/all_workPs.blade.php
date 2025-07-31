@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0 padding">Đề Xuất Vật Tư</h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Danh Sách Đề Xuất</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- workps List -->
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="containers mt-2">
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="search-box">
                            <input type="text" class="search-inputs" placeholder="Tìm kiếm...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-content">
                    <div class="form-section">
                        <!-- First Filter Row -->
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Từ Ngày</label>
                                <input type="date" class="form-input" id="StartDate">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Đến Ngày</label>
                                <input type="date" class="form-input" id="EndDate">
                            </div>
                            <div class="form-group" style="display: flex; align-items: end;">
                                <button class="btn btn-success btn-w" onclick="filterTasks()">
                                    <i class="fa-light fa-filter-list"></i>
                                    Lọc
                                </button>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <!-- Task Filter Buttons -->
                        <div class="task-filter-buttons" style="display: flex;">
                            <button class="task-filter-btn active" data-filter="all" onclick="setTaskFilter('all')">
                                <span>Danh Sách Đề Xuất</span>
                                <span class="task-count" id="allTasksCount"></span>
                            </button>
                            <button class="task-filter-btn" data-filter="pending" onclick="setTaskFilter('pending')">
                                <span>Chưa Duyệt</span>
                                <span class="task-count" id="pendingTasksCount"></span>
                            </button>

                            <button class="task-filter-btn" data-filter="completed"
                                onclick="setTaskFilter('completed')">
                                <span>Đã Duyệt</span>
                                <span class="task-count" id="completedTasksCount"></span>
                            </button>
                            <button class="task-filter-btn" data-filter="completed"
                                onclick="setTaskFilter('completed')">
                                <span>Từ Chối</span>
                                <span class="task-count" id="completedTasksCount"></span>
                            </button>


                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                {{-- <div style="position: relative;"> --}}
                    {{-- <div class="table-responsive"> --}}
                        <table id="workps-table" class="table table-bordered text-nowrap w-100">
                            <div id="buttons-container" class="d-flex justify-content-end gap-3">
                                <button id="edit-selected-btn" class="btn btn-warning"
                                    style="border-radius: 7px; color: #FFFFFF; display: none">Không/Hoạt
                                    Động</button>
                                <button id="delete-selected-btn" class="btn btn-danger"
                                    style="border-radius: 7px; display: none;">
                                    Xóa
                                </button>
                                <button id="add-selected-btn" class="btn btn-success" style="border-radius: 7px;">
                                    <a href="{{route('workps.add')}}" class="text-white"><i class="fa fa-plus"></i>Tạo
                                        Đề Xuất</a>
                                </button>
                            </div>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>
                                        <input class="form-check-input check-all" type="checkbox" id="select-all-workps"
                                            value="" aria-label="...">
                                    </th>
                                    {{-- <th scope="col">STT</th> --}}
                                    <th scope="col">Tên Phiếu Vật Tư</th>
                                    <th scope="col">Ngày Đề Xuất</th>
                                    <th scope="col">Ngày Nhận</th>
                                    <th scope="col">Người Tạo</th>
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
                                    <th scope="col">Tên Phiếu Vật Tư</th>
                                    <th scope="col">Ngày Đề Xuất</th>
                                    <th scope="col">Ngày Nhận</th>
                                    <th scope="col">Người Tạo</th>
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
                <button type="button" id="confirmUpdateBtn" class="btn btn-primary">Xác Nhận</button>
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
                Bạn chắc chắn muốn xóa vườn cây đã chọn?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-primary">Xác Nhận</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
            var selectedRows = new Set();
            var dataTable = $('#workps-table').DataTable({
                "language": {
                    // "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json",
                    "emptyTable": "Không có dữ liệu",
                },
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
                    url: '{{ route('workps.index') }}',
                    type: 'GET'
                },
                columns: [
                    {
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
                        data: 'proposaName',
                        name: 'proposaName'
                    },
                    {
                        data: 'proposalDate',
                        name: 'proposalDate'
                    },
                    {
                        data: 'approvalDate',
                        name: 'approvalDate'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
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
            $('#select-all-workps').on('change', function() {
                var checked = $(this).prop('checked');
                $('#workps-table tbody .form-check-input').each(function() {
                    var farmId = $(this).data('id');
                    if (checked) {
                        selectedRows.add(farmId);
                    } else {
                        selectedRows.delete(farmId);
                    }
                    $(this).prop('checked', checked);
                });
                toggleButtons();

                console.log([...selectedRows]);
            });
            $('#workps-table tbody').on('change', '.form-check-input', function() {
                var farmId = $(this).data('id');
                toggleButtons();

                if ($(this).prop('checked')) {
                    selectedRows.add(farmId);
                } else {
                    selectedRows.delete(farmId);
                }
                console.log([...selectedRows]);
            });

            $('#workps-table').on('draw.dt', function() {
                $('#workps-table tbody .form-check-input').each(function() {
                    var farmId = $(this).data('id');
                    if (selectedRows.has(farmId)) {
                        $(this).prop('checked', true);
                    }
                });
            });
            $('#edit-selected-btn').on('click', function() {
                $('#confirmModal').modal('show');
                $('#confirmUpdateBtn').on('click', function() {
                    $.ajax({
                        url: '/workps/edit-multiple',
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
                        url: '/workps/delete-multiple',
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

            // function toggleButtons() {
            //     var selectedRows = $('#workps-table tbody .form-check-input:checked');

            //     if (selectedRows.length > 0) {
            //         $('#edit-selected-btn').show();
            //         $('#delete-selected-btn').show();
            //     } else {
            //         $('#edit-selected-btn').hide();
            //         $('#delete-selected-btn').hide();
            //     }
            // }

            function toggleButtons() {
                var selected = $('#workps-table tbody .form-check-input:checked').length;
                // if (selected > 0) {
                //     $('#buttons-container').css('visibility', 'visible');
                // } else {
                //     $('#buttons-container').css('visibility', 'hidden');
                // }
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
                text: "Bạn có chắc chắn muốn thay đổi trạng thái của vườn cây này?",
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
                                    text: 'Trạng thái của vườn cây đã được cập nhật.',
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    timer: 3000
                                });
                            } else {
                                Swal.fire({
                                    text: response.message ||
                                        'Không thể thay đổi trạng thái của vườn cây.',
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
<style>
    @media (min-width: 1400px) {

        .container {
            max-width: 900px !important;
        }
    }
</style>
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
@endsection