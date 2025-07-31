@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0"></h4>
        <div class="ms-md-1 ms-0">

            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Danh Sách Lệnh Xuất Hàng</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<style>
    .dt-type-numeric {
        text-align: left !important;
    }
</style>
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <!-- <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center"
                                                                style="grid-gap: 3px;">
                                                                <h5>Danh Sách Lệnh Xuất</h5>
                                                                <a href="{{ route('add-orderbatchs') }}" class="btn btn-sm btn-primary btn-wave waves-light">
                                                                    <i class="ri-add-line fw-semibold align-middle me-1"></i> Gắn Mã Lô Vào Lệnh Xuất
                                                                </a>
                                                            </div> -->
            <div
                class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h5 class="mb-2 mb-md-0 w-100 w-md-50 flex-grow-1">Danh Sách Lệnh Xuất Hàng</h5>
                <div class="d-flex w-100 w-md-auto justify-content-end">
                    <a href="{{ route('add-orderbatchs') }}" class="btn btn-sm btn-primary btn-wave waves-light">
                        <i class="ri-add-line fw-semibold align-middle me-1"></i> Gắn Mã Lô Vào Lệnh Xuất Hàng
                    </a>
                </div>
            </div>



            <div class="card-body">
                {{-- <div style="position: relative;">
                    <div class="table-responsive"> --}}
                        <div id="buttons-container" class="d-flex justify-content-end gap-2">
                            <button id="delete-selected-btn" class="btn btn-danger" {{--
                                style="display: none; border-radius: 30px;" --}} style="border-radius: 30px;">
                                Xóa
                            </button>
                        </div>
                        <table id="table-human" class="table table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>
                                        <input class="form-check-input check-all" type="checkbox" id="select-all-orderB"
                                            value="" aria-label="...">
                                    </th>
                                    <!-- <th scope="col">STT</th> -->
                                    <th scope="col">Mã Lệnh Xuất Hàng</th>
                                    <th scope="col">Mã Lô Hàng</th>
                                    <th scope="col">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th scope="col"></th>
                                    <!-- <th scope="col">STT</th> -->
                                    <th scope="col">Mã Lệnh Xuất Hàng</th>
                                    <th scope="col">Mã Mô Hàng</th>
                                    <th scope="col">Thao Tác</th>
                                </tr>
                            </tfoot>
                        </table>
                        <script>
                            $(document).ready(function() {
                        var selectedRows = new Set();
                        var dataTable = $('#table-human').DataTable({
                            "language": {
                                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json",
                                "emptyTable": "Không có dữ liệu trong bảng",
                            },
                            processing: true,
                            serverSide: true,
                            // repornSive: true,
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
                                url: "{{ route('orderbatchs.index') }}",
                                data: function(d) {},
                                rowCallback: function(row, data) {
                                    $(row).attr('data-id', data.id);
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
                                // {
                                //     data: 'stt',
                                //     name: 'stt'
                                // },
                                {
                                    data: 'code',
                                    name: 'code'
                                },
                                {
                                    data: 'batch_code',
                                    name: 'batch_code'
                                },
                                {
                                    data: 'action',
                                    name: 'action'
                                },
                            ]
                        });
                        $('#select-all-orderB').on('change', function() {
                            var checked = $(this).prop('checked');
                            $('#table-human tbody .form-check-input').each(function() {
                                var orderId = $(this).data('id');
                                if (checked) {
                                    selectedRows.add(orderId);
                                } else {
                                    selectedRows.delete(orderId);
                                }
                                $(this).prop('checked', checked);
                            });
                            toggleButtons();

                            console.log([...selectedRows]); // Kiểm tra danh sách đã chọn
                        });
                        $('#table-human tbody').on('change', '.form-check-input', function() {
                            var orderId = $(this).data('id');
                            toggleButtons();

                            if ($(this).prop('checked')) {
                                selectedRows.add(orderId);
                            } else {
                                selectedRows.delete(orderId);
                            }
                            console.log([...selectedRows]); // Kiểm tra  danh sách chọn
                        });

                        $('#table-human').on('draw.dt', function() {
                            $('#table-human tbody .form-check-input').each(function() {
                                var orderId = $(this).data('id');
                                if (selectedRows.has(orderId)) {
                                    $(this).prop('checked', true);
                                }
                            });
                        });
                        $('#delete-selected-btn').on('click', function() {
                            $('#deleteModal').modal('show');
                            $('#confirmDeleteBtn').one('click', function() {
                                $('#deleteModal').modal('hide');

                                $.ajax({
                                    url: '/orderbatchs/delete-multiple',
                                    type: 'POST',
                                    data: {
                                        ids: [...selectedRows]
                                    },
                                    success: function(response) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Thành công!',
                                            text: 'Xóa mã lệnh thành công.',
                                            confirmButtonText: 'OK'
                                        }).then(() => {
                                            selectedRows.clear();
                                            $('#deleteModal').modal(
                                                'hide');
                                            $('#buttons-container')
                                                .hide();
                                            location.reload();
                                        });
                                    },
                                    error: function(xhr, status, error) {
                                        var errorMessage = 'Đã xảy ra lỗi trong quá trình xóa.';
                                        try {
                                            var response = JSON.parse(xhr.responseText);
                                            if (response.message) {
                                                errorMessage = response.message;
                                            }
                                        } catch (e) {
                                            console.error('Error parsing response:', e);
                                        }
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Lỗi!',
                                            // text: 'Có lỗi khi xóa dữ liệu.',
                                            text: errorMessage,
                                            confirmButtonText: 'OK'
                                        });
                                        console.error('Error:', error);
                                        console.error('XHR:', xhr);
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
                        //     var selectedRows = $('#table-human tbody .form-check-input:checked');

                        //     if (selectedRows.length > 0) {
                        //         $('#edit-selected-btn').show();
                        //         $('#delete-selected-btn').show();
                        //     } else {
                        //         $('#edit-selected-btn').hide();
                        //         $('#delete-selected-btn').hide();
                        //     }
                        // }
                        function toggleButtons() {
                            var selected = $('#table-human tbody .form-check-input:checked').length;
                            if (selected > 0) {
                                $('#buttons-container').css('visibility', 'visible');
                            } else {
                                $('#buttons-container').css('visibility', 'hidden');
                            }
                        }
                        $('#form-sub-orderbatch').on('submit', function(e) {
                            e.preventDefault();
                            dataTable.ajax.reload();
                        });
                    });
                        </script>

                        {{--
                    </div>
                </div> --}}
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
                Bạn chắc chắn muốn mã lệnh đã chọn?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-primary">Xác Nhận</button>
            </div>
        </div>
    </div>
</div>
<script>
    function confirmDelete() {
        console.log("Deleting...");
        $('.modal-header, .modal-footer, .modal-body').addClass('hide');
        $('.confirm-delete').removeClass('hide');
        //$('#myModal').modal('hide');
    }

    function openModal() {
        $('.confirm-delete').addClass('hide');
        $('#myModal .modal-header, .modal-footer, .modal-body').removeClass('hide');
        $('#myModal').modal('show');
    }
</script>
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.5/dist/sweetalert2.min.css" rel="stylesheet">
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.5/dist/sweetalert2.min.js"></script>
@endsection