@extends("layouts.app")

@section('content')
<div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h5 class="page-title fw-semibold fs-18 mb-0"></h5>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="#">Chứng Chỉ</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h4>Chứng Chỉ</h4>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="#">Chứng Chỉ</a></li>
                </ol>
            </nav>
        </div>
    </div> -->
</div>

<!-- <div class="d-flex flex-wrap justify-content-end">
    <div class="">
        <a href="{{route('certi.create')}}">
            <button class="btn icon icon-left text-white" style="background-color: #08839d;"><i
                    class="fa-solid fa-plus"></i> Thêm Chứng Chỉ</button>
        </a>
    </div>
</div> -->
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between align-items-center" style="grid-gap: 3px;">
                <h5>Chứng Chỉ</h5>

                <a href="{{route('certi.create')}}" class="btn btn-sm btn-primary btn-wave waves-light">
                    <i class="fa-solid fa-plus"></i> Thêm Chứng Chỉ
                </a>
            </div>
            <div class="card-body">
                <!-- <div class="table-responsive"> -->
                <div id="hidden-columns_wrapper" class="dataTables_wrapper dt-bootstrap5">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="certificates-table" class="table table-bordered w-100 dataTable">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>STT</th>
                                        <th>Tên</th>
                                        <th>Tập Tin</th>
                                        <th>Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- </div> -->
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        var cerTable = $("#certificates-table").DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json",
                "emptyTable": "Không có dữ liệu trong bảng",
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
                url: "/get-certificates/data",
                method: "GET",
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
                data: "stt",
                name: "stt",
                width: "5%"
            },
            {
                data: "name",
                name: "name"
            },
            {
                data: "file_name",
                name: "file_name"
            },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            },
            ],
        });
    });
</script>

@endsection