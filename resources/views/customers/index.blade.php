@extends("layouts.app")


@section('content')

<div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0"></h5>

        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="/customers">Khách Hàng</a></li>
                </ol>
            </nav>
        </div>
    </div>

    @include("layouts.alert")

</div>

<!-- <div class="buttons d-flex justify-content-end align-items-center my-3">

    <a href="{{route('customers.create')}}">
        <button class="btn btn-success me-1 mb-1">Thêm Khách Hàng</button>
    </a>
</div> -->
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between align-items-center" style="grid-gap: 3px">
                <h5>Khách Hàng</h5>
                <a href="{{route('customers.create')}}">
                    <button class="btn btn-primary me-1 mb-1">Thêm Khách Hàng</button>
                </a>
            </div>
            <div class="card-body">
                <div id="buttons-container" class="d-flex justify-content-end gap-2">
                    <button id="delete-selected-btn" class="btn btn-danger" {{--
                        style="display: none; border-radius: 30px;" --}} style="border-radius: 30px;">
                        Xóa
                    </button>
                </div>
                <!-- <div style="position: relative;">
                    <div class="table-responsive"> -->
                <table id="customer-tables" class="table table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th></th>
                            <th>
                                <input class="form-check-input check-all" type="checkbox" id="select-all-customers"
                                    value="" aria-label="...">
                            </th>
                            <!-- <th>STT</th> -->
                            <th>Tên Công Ty</th>
                            <th>Loại Khách Hàng</th>
                            <th>Điện Thoại</th>
                            <th>Email</th>
                            <th>Địa Chỉ</th>
                            <!-- <th>Mô Tả</th> -->
                            <th>Thao Tác</th>

                        </tr>
                    </thead>
                    <tbody>

                    </tbody>

                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <!-- <th>STT</th> -->
                            <th>Tên Công Ty</th>
                            <th>Loại Khách Hàng</th>
                            <th>Điện Thoại</th>
                            <th>Email</th>
                            <th>Địa Chỉ</th>
                            <!-- <th>Mô Tả</th> -->
                            <th>Thao Tác</th>
                        </tr>
                    </tfoot>
                </table>

                <!-- </div>
                </div> -->
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
                    Bạn chắc chắn muốn dữ liệu đã chọn ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-primary">Xác
                        Nhận</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        var selectedRows = new Set();

        $dataTable = $('#customer-tables').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json",
                "emptyTable": "Không có dữ liệu",
            },
            processing: true,
            serverSide: true,
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
                url: 'customer-get-data',
                type: 'GET',
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
                // {
                //     data: 'DT_RowIndex',
                //     name: 'DT_RowIndex',
                //     orderable: false,
                //     searchable: false
                // },
                {
                    data: 'company_name',
                    name: 'company_name'
                },
                {
                    data: 'customer_type',
                    name: 'customer_type'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'address',
                    name: 'address',
                },
                // {
                //     data: 'description',
                //     name: 'description'
                // },
                {
                    data: 'actions',
                    orderable: false,
                    searchable: false,
                }
            ],
            rowCallback: function(row, data) {
                $(row).attr('data-id', data.id);
            }
        });
        $('#select-all-customers').on('change', function() {
            var checked = $(this).prop('checked');
            $('#customer-tables tbody .form-check-input').each(function() {
                var customerId = $(this).data('id');
                if (checked) {
                    selectedRows.add(customerId);
                } else {
                    selectedRows.delete(customerId);
                }
                $(this).prop('checked', checked);
            });
            toggleButtons();

            console.log([...selectedRows]);
        });

        $('#customer-tables tbody').on('change', '.form-check-input', function() {
            var customerId = $(this).data('id');
            toggleButtons();

            if ($(this).prop('checked')) {
                selectedRows.add(customerId);
            } else {
                selectedRows.delete(customerId);
            }
            console.log([...selectedRows]);
        });
        $('#customer-tables').on('draw.dt', function() {
            $('#customer-tables tbody .form-check-input').each(function() {
                var customerId = $(this).data('id');
                if (selectedRows.has(customerId)) {
                    $(this).prop('checked', true);
                }
            });
        });

        // function toggleButtons() {
        //     var selectedRows = $('#customer-tables tbody .form-check-input:checked');
        //     if (selectedRows.length > 0) {
        //         $('#delete-selected-btn').show();
        //     } else {
        //         $('#delete-selected-btn').hide();
        //     }
        // }

        function toggleButtons() {
            var selected = $('#customer-tables tbody .form-check-input:checked').length;
            if (selected > 0) {
                $('#buttons-container').css('visibility', 'visible');
            } else {
                $('#buttons-container').css('visibility', 'hidden');
            }
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var selectedIds = [];
        $('#delete-selected-btn').on('click', function() {
            $('#deleteModal').modal('show');
            $('#confirmDeleteBtn').show();

            $('#confirmDeleteBtn').on('click', function() {
                $.ajax({
                    url: '/customers/delete-multiple', // Adjust the URL to match your route
                    type: 'POST',
                    data: {
                        ids: [...selectedRows],
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Có lỗi xảy ra!',
                                html: response.message,
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: response.message || 'Dữ liệu đã được xóa thành công.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                selectedRows.clear();
                                $('#deleteModal').modal('hide');
                                $('#buttons-container').hide();
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = xhr.responseJSON && xhr.responseJSON.message ?
                            xhr.responseJSON.message :
                            'Có lỗi xảy ra khi xóa dữ liệu!';
                        $('#confirmDeleteBtn').hide();
                        $('#deleteModal').modal('hide');
                        Swal.fire({
                            icon: 'error',
                            title: 'Có lỗi xảy ra!',
                            html: errorMessage,
                            confirmButtonText: 'OK'
                        });
                        console.error('Error:', xhr.responseJSON || xhr);
                    }
                });
            });

            $('#deleteModal').on('hidden.bs.modal', function() {
                $('#confirmDeleteBtn').off('click');
                $('#confirmDeleteBtn').show();
            });
        });
    });
</script>
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.js"></script>

@endsection