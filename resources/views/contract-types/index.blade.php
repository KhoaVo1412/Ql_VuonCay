@extends("layouts.app")


@section('content')

<div class="page-title my-3">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h4></h4>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb padding">
                    <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="/contract-types">Loại Hợp Đồng</a></li>
                </ol>
            </nav>
        </div>
    </div>

    @include("layouts.alert")

</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between align-items-center" style="grid-gap: 3px;">
                <h5>Loại Hợp Đồng</h5>
                <a href="{{route('contract-types.create')}}">
                    <button class="btn btn-primary me-1 mb-1">Thêm Loại Hợp Đồng</button>
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
                <table id="contract-types" class="table table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th></th>
                            <th>
                                <input class="form-check-input check-all" type="checkbox" id="select-all-contractT"
                                    value="" aria-label="...">
                            </th>
                            <!-- <th>STT</th> -->
                            <th>Loại Hợp Đồng</th>
                            <th>Mã Loại Hợp Đồng</th>
                            <th>Tên Loại Hợp Đồng</th>
                            <!-- <th>Thao Tác</th> -->
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>

                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <!-- <th>STT</th> -->
                            <th>Loại Hợp Đồng</th>
                            <th>Mã Loại Hợp Đồng</th>
                            <th>Tên Loại Hợp Đồng</th>
                            <!-- <th>Thao Tác</th> -->
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
    $(document).ready(function () {
        var selectedRows = new Set();
        var dataTable = $('#contract-types').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json"
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
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col, i) {
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
                url: 'contract-get-data',
                type: 'GET',
            },

            columns: [{
                data: null,
                name: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
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
                data: 'contract_type_type',
                name: 'contract_type_type'
            },
            {
                data: 'contract_type_code',
                name: 'contract_type_code'
            },
            {
                data: 'contract_type_name',
                name: 'contract_type_name'
            },
                // {
                //     data: 'actions',
                //     name: 'actions',
                //     orderable: false,
                //     searchable: false,
                // }
            ],
            rowCallback: function (row, data) {
                $(row).attr('data-id', data.id);
            }
        });
        $('#select-all-contractT').on('change', function () {
            var checked = $(this).prop('checked');
            $('#contract-types tbody .form-check-input').each(function () {
                var contId = $(this).data('id');
                if (checked) {
                    selectedRows.add(contId);
                } else {
                    selectedRows.delete(contId);
                }
                $(this).prop('checked', checked);
            });
            toggleButtons();

            console.log([...selectedRows]);
        });

        $('#contract-types tbody').on('change', '.form-check-input', function () {
            var contId = $(this).data('id');
            toggleButtons();

            if ($(this).prop('checked')) {
                selectedRows.add(contId);
            } else {
                selectedRows.delete(contId);
            }
            console.log([...selectedRows]);
        });
        $('#contract-types').on('draw.dt', function () {
            $('#contract-types tbody .form-check-input').each(function () {
                var contId = $(this).data('id');
                if (selectedRows.has(contId)) {
                    $(this).prop('checked', true);
                }
            });
        });

        // function toggleButtons() {
        //     var selectedRows = $('#contract-types tbody .form-check-input:checked');
        //     if (selectedRows.length > 0) {
        //         $('#delete-selected-btn').show();
        //     } else {
        //         $('#delete-selected-btn').hide();
        //     }
        // }
        function toggleButtons() {
            var selected = $('#contract-types tbody .form-check-input:checked').length;
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
        $('#delete-selected-btn').on('click', function () {
            $('#deleteModal').modal('show');
            $('#confirmDeleteBtn').on('click', function () {
                $.ajax({
                    url: '/contracttype/delete-multiple',
                    type: 'POST',
                    data: {
                        ids: [...selectedRows]
                    },
                    success: function (response) {
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
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Có lỗi xảy ra!',
                            text: 'Đã xảy ra lỗi trong quá trình xóa.',
                            confirmButtonText: 'OK'
                        });
                        console.error('Error:', xhr);
                    }
                });
            });

            $('#deleteModal').on('hidden.bs.modal', function () {
                $('#confirmDeleteBtn').off('click');
            });
        });
    });
</script>
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.js"></script>
@endsection