@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0">Danh Sách Vật Tư</h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Danh Sách Vật Tư</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Add products Modal -->
<form id="products-form" action="{{ route('products.save') }}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="modal fade" id="create-products" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Tạo Vật Tư</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="row gy-2">
                        <!-- Tên sản phẩm -->
                        <div class="col-12">
                            <label for="name" class="form-label">Tên Sản Phẩm</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Tên" required>
                        </div>

                        <!-- Danh mục -->
                        <div class="col-12">
                            <label for="categoryID" class="form-label">Danh Mục</label>
                            <select name="categoryID" id="categoryID" class="form-select" required>
                                <option value="">-- Chọn danh mục --</option>
                                @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="warehouseID" class="form-label">Thuộc Kho</label>
                            <select name="warehouseID" id="warehouseID" class="form-select" required>
                                <option value="">-- Chọn kho --</option>
                                @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="unitID" class="form-label">Đơn Vị</label>
                            <select name="unitID" id="unitID" class="form-select" required>
                                <option value="">-- Chọn đơn vị --</option>
                                @foreach ($units as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- <div class="col-12">
                            <label for="parentID" class="form-label">Danh Mục Cha (nếu có)</label>
                            <select name="parentID" id="parentID" class="form-select">
                                <option value="">-- Không có --</option>
                                @foreach ($categories as $cate)
                                <option value="{{ $cate->id }}">{{ $cate->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="submit-btn-products">Lưu</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- products List -->
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between align-items-center" style="grid-gap: 3px">

            </div>
            <div class="container mt-2">
                <div class="row g-3">
                    <div class="col-md-10">
                        <div class="search-box">
                            <input type="text" class="search-inputs" placeholder="Tìm Kiếm...">
                        </div>
                    </div>
                </div>
            </div>
            {{-- <style>
                #buttons-container {
                    position: absolute;
                    top: -10%;
                    left: 0%;
                    /* z-index: 10; */
                    display: flex;
                    gap: 10px;
                }

                .table-responsive {
                    margin-top: 30px;
                }
            </style> --}}
            <div class="card-body">
                <table id="products-table" class="table table-bordered text-nowrap w-100">
                    <div id="buttons-container" class="d-flex justify-content-end gap-2">
                        <button id="edit-selected-btn" class="btn btn-warning"
                            style="border-radius: 7px; color: #FFFFFF; display: none">Không/Hoạt
                            Động</button>
                        <button id="delete-selected-btn" class="btn btn-danger"
                            style="border-radius: 7px; display: none;">
                            Xóa
                        </button>
                        <button id="add-selected-btn" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target="#create-products" style="border-radius: 30px;">
                            <i class="fa fa-plus"></i>Tạo Vật Tư
                        </button>
                    </div>
                    <thead>
                        <tr>
                            <th></th>
                            <th>
                                <input class="form-check-input check-all" type="checkbox" id="select-all-products"
                                    value="" aria-label="...">
                            </th>

                            {{-- <th scope="col">STT</th> --}}
                            <th scope="col">Tên</th>
                            <th scope="col">Danh mục</th>
                            <th scope="col">Đơn vị</th>
                            <th scope="col">Trạng Thái</th>
                            <th scope="col">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate this section -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">
                            </th>
                            {{-- <th scope="col">STT</th> --}}
                            <th scope="col">Tên</th>
                            <th scope="col">Danh mục</th>
                            <th scope="col">Đơn vị</th>
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
                Bạn chắc chắn muốn xóa đơn vị đã chọn?
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
            var dataTable = $('#products-table').DataTable({
                "language": {
                    // "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json"
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
                    url: '{{ route('products.index') }}',
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
                        data: 'name',
                        name: 'name'
                    },
                  
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
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
            $('#select-all-products').on('change', function() {
                var checked = $(this).prop('checked');
                $('#products-table tbody .form-check-input').each(function() {
                    var unitId = $(this).data('id');
                    if (checked) {
                        selectedRows.add(unitId);
                    } else {
                        selectedRows.delete(unitId);
                    }
                    $(this).prop('checked', checked);
                });
                toggleButtons();

                console.log([...selectedRows]); // Kiểm tra danh sách đã chọn
            });
            $('#products-table tbody').on('change', '.form-check-input', function() {
                var unitId = $(this).data('id');
                toggleButtons();

                if ($(this).prop('checked')) {
                    selectedRows.add(unitId);
                } else {
                    selectedRows.delete(unitId);
                }
                console.log([...selectedRows]); // Kiểm tra  danh sách chọn
            });

            $('#products-table').on('draw.dt', function() {
                $('#products-table tbody .form-check-input').each(function() {
                    var unitId = $(this).data('id');
                    if (selectedRows.has(unitId)) {
                        $(this).prop('checked', true);
                    }
                });
            });
            $('#edit-selected-btn').on('click', function() {
                $('#confirmModal').modal('show');
                $('#confirmUpdateBtn').on('click', function() {
                    $.ajax({
                        url: '/products/edit-multiple',
                        type: 'POST',
                        data: {
                            ids: [...selectedRows], // Chuyển Set thành mảng
                            status: 'Không hoạt động'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: 'Trạng thái đã được cập nhật.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                selectedRows.clear(); // Reset danh sách đã chọn
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
                        url: '/products/delete-multiple',
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
                var selected = $('#products-table tbody .form-check-input:checked').length;
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
                text: "Bạn có chắc chắn muốn thay đổi trạng thái của đơn vị này?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Thay đổi",
                cancelButtonText: "Hủy"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('products.status') }}',
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
                                    text: 'Trạng thái của đơn vị đã được cập nhật.',
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    timer: 3000
                                });
                            } else {
                                Swal.fire({
                                    text: response.message ||
                                        'Không thể thay đổi trạng thái của đơn vị.',
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