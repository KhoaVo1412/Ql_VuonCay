@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0">Lịch Sử Hoạt Động</h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 padding">
                    <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Lịch Sử Hoạt Động</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between align-items-center" style="grid-gap: 3px">
                {{-- <h5>Lịch Sử Hoạt Động</h5> --}}
            </div>
            <div class="mb-3 d-flex gap-2" style="padding-left: 25px">
                <label for="filter-date" class="form-lable" style="font-weight: bold; padding-top: 5px;">Lọc
                    Ngày</label>
                <input type="date" id="filter-date" class="form-control" value="{{ now()->toDateString() }}"
                    style="max-width: 200px;">
                <label for="filter-model" class="form-lable" style="font-weight: bold; padding-top: 5px;">Lọc
                    Phương Thức</label>
                <select id="filter-model" class="form-control" style="max-width: 200px;">
                    <option value="">Tất cả</option>
                    @foreach($modelTypes as $modelType)
                    <option value="{{ $modelType }}">{{ $modelType }}</option>
                    @endforeach
                </select>
            </div>
            <div class="card-body">
                <table id="check-action-table" class="table table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th></th>

                            <th scope="col">STT</th>
                            <th scope="col">Tên Tài Khoản</th>
                            <th scope="col">Hàng Động</th>
                            <th scope="col">Phương Thức</th>
                            <th scope="col">Chi Tiết Thay Đổi</th>
                            <th scope="col">Ngày</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate this section -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th scope="col">STT</th>
                            <th scope="col">Tên Tài Khoản</th>
                            <th scope="col">Hàng Động</th>
                            <th scope="col">Phương Thức</th>
                            <th scope="col">Chi Tiết Thay Đổi</th>
                            <th scope="col">Ngày</th>

                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
            var selectedRows = new Set();
            var dataTable = $('#check-action-table').DataTable({
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
                    url: '{{ route("checkAction.index") }}',
                    type: 'GET',
                    data: function(d) {
                        d.date = $('#filter-date').val();
                        d.model_type = $('#filter-model').val();
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
                        data: 'stt',
                        name: 'stt'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'action_type',
                        name: 'action_type'
                    },
                    {
                        data: 'model_type',
                        name: 'model_type'
                    },
                    {
                        data: 'details',
                        name: 'details'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    
                ],
            });
            $('#filter-date').on('change', function () {
                dataTable.draw();
            });
            $('#filter-model').on('change', function () {
                dataTable.draw();
            });
        });
</script>
<style>
    @media (max-width: 480px) {
        .mb-3.d-flex {
            flex-direction: column;
            /* Stack the elements vertically */
            padding-left: 0;
            /* Remove the left padding */
            align-items: flex-start;
            /* Align items to the start */
        }

        .form-lable {
            font-weight: bold;
            padding-top: 5px;
        }

        .form-control {
            max-width: 100% !important;
            /* Make inputs take full width on small screens */
        }

        #filter-date,
        #filter-model {
            max-width: 100% !important;
            /* Ensure both inputs take full width */
        }

        .d-flex gap-2 {
            gap: 10px;
            /* Adjust gap between elements */
        }
    }
</style>
@endsection