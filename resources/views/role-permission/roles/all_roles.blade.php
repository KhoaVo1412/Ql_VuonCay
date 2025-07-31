@extends('layouts.app')
@section('content')
<div class="container-fluid1">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0"></h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Trang Chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Danh Sách Vai Trò</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Delete Confirmation Modal -->

    <div>
        <!-- Start:add task modal -->
        <form action="{{ route('store.roles') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal fade" id="add-role" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title">Tạo Vai Trò</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-4">
                            <div class="row gy-2">
                                <div class="col-xl-12">
                                    <label for="account-id" class="form-label">Tên Vai Trò</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}"
                                        placeholder="" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary" id="submt-btn">Thêm Vai Trò</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                {{-- <div class="card-header d-flex" style="justify-content: space-between;">
                    <div class="card-title">
                    </div>
                    @include('role-permission.nav_permission')

                    <button class="btn btn-sm btn-primary btn-wave waves-light" data-bs-toggle="modal"
                        data-bs-target="#add-role">
                        <i class="ri-add-line fw-semibold align-middle me-1"></i> Tạo Vai Trò
                    </button>
                </div> --}}
                <div class="card-header" style="justify-content: space-between; text-align: center;">
                    <div class="card-title">
                    </div>
                    @include('role-permission.nav_permission')
                </div>
                <div class="card-header" style="justify-content: space-between; text-align: right;">
                    <div class="card-title" style="justify-content: space-between;display: flex;">
                        <h5>Danh Sách Vai Trò</h5>
                        <button class="btn btn-sm btn-primary btn-wave waves-light" data-bs-toggle="modal"
                            data-bs-target="#add-role">
                            <i class="ri-add-line fw-semibold align-middle me-1"></i> Tạo Vai Trò
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    {{-- <div class="table-responsive"> --}}
                        <div id="hidden-columns_wrapper" class="dataTables_wrapper dt-bootstrap5">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="data-table" class="table table-bordered text-nowrap w-100 dataTable"
                                        aria-describedby="hidden-columns_info" {{-- style="width: 963px;" --}}
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                {{-- <th>
                                                    <input class="form-check-input check-all" type="checkbox"
                                                        id="all-tasks" value="" aria-label="...">
                                                </th> --}}
                                                <th scope="col">STT</th>
                                                <th scope="col">Tên Vai Trò</th>
                                                <th scope="col">Thao Tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                {{-- <th>
                                                </th> --}}
                                                <th scope="col">STT</th>
                                                <th scope="col">Tên Vai Trò</th>
                                                <th scope="col">Thao Tác</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <script>
                                        $(document).ready(function() {
                                            var dataTable = $('#data-table').DataTable({
                                                "language": {
                                                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json"
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
                                                    url: "{{ route('all.roles') }}",
                                                },
                                                columns: [
                                                    // { data: 'check', name: 'check'},
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
                                                        data: 'stt',
                                                        name: 'stt'
                                                    },
                                                    {
                                                        data: 'name',
                                                        name: 'name'
                                                    },
                                                    {
                                                        data: 'Action',
                                                        name: 'Action'
                                                    }
                                                ]
                                            });
                                            // $('#form-sub-human').on('submit', function(e) {
                                            //     e.preventDefault();
                                            //     dataTable.ajax.reload(); // Tải lại dữ liệu DataTables sau khi thay đổi lọc
                                            // });
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                        {{--
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection