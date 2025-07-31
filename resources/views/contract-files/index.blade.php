@extends('layouts.app')
@section('content')
<style>
    /* .dataTable,
                                        .nested-table {
                                            border-collapse: collapse !important;
                                            width: 100%;
                                        } */

    /* Loại bỏ margin và padding thừa */
    /* .table-container {
                                        margin: 0 !important;
                                        padding: 0 !important;
                                        width: 100%;

                                    } */

    /* Định dạng bảng con */
    /* .nested-table th,
                                .nested-table td {
                                    border: 0.5px solid #dee2e6 !important;
                                    padding: 8px;
                                    text-align: left;

                                } */

    /* Xóa border thừa giữa bảng con và bảng cha */
    /* .nested-table {
                                    border-left: none !important;
                                    border-right: none !important;
                                } */

    /* Kiểm soát chiều rộng bảng con để không bị chồng lên */
    /* .nested-table tbody tr {
                                display: table-row;
                                width: 100%;
                            } */

    /* Cố định chiều rộng 2 cột "Tên" và "Tên file" trong bảng con */
    .nested-table th:nth-child(1),
    .nested-table td:nth-child(1) {
        width: 50%;

    }

    .nested-table th:nth-child(2),
    .nested-table td:nth-child(2) {
        width: 50%;

    }

    /* table.dataTable>tbody>tr.child ul.dtr-details>li:first-child {
                                                padding-top: 0;
                                                white-space: normal;
                                                word-break: break-all;
                                                overflow-wrap: break-word;
                                            } */

    /* Cột Tên - giữ gọn, không xuống dòng lung tung */
    .nested-table td.file-name {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Cột File - giữ khả năng xuống dòng */
    .nested-table td.file-link {
        white-space: normal;
        word-break: break-word;
        overflow-wrap: break-word;
    }


    /* table.dataTable>tbody>tr:first-child>* {
                            word-break: break-all;
                        } */

    #files-table td.dt-type-numeric,
    #files-table th.dt-type-numeric {
        text-align: center !important;
    }

    .table-container {
        overflow-x: auto;
        min-width: 220px;

    }
</style>
<div class="page-title my-3">

    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
        <h4 class="page-title fw-semibold fs-18 mb-0"> </h4>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb padding">
                    <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                    <li class="breadcrumb-item">Danh Sách</li>
                </ol>
            </nav>
        </div>
    </div>

    @include('layouts.alert')
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div
                class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h5 class="mb-2 mb-md-0 w-100 w-md-50 flex-grow-1">Danh Sách Quản Lý Mã Lệnh Xuất Hàng</h5>
                <div class="d-flex w-100 w-md-auto justify-content-end">
                    <a href="{{ route('create-file.index') }}" class="btn btn-primary">
                        Gắn File Vào Lệnh Xuất Hàng
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- <div class="table-responsive"> -->
                <table class="table table-bordered" id="files-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>STT</th>
                            <th>Mã Lệnh Xuất Hàng</th>
                            <th>Files</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>

                    <tfoot>
                        <tr>
                            <th></th>
                            <th>STT</th>
                            <th>Mã Lệnh Xuất Hàng</th>
                            <th>Files</th>
                            <th>Thao tác</th>
                        </tr>
                    </tfoot>
                </table>
                <!-- </div> -->

            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
            let table = $('#files-table').DataTable({
                processing: true,
                serverSide: true,
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
                ajax: "{{ route('contract-files.data') }}",
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json",
                    "emptyTable": "Không có dữ liệu trong bảng",
                },
                order: [],
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
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'code',
                        name: 'code',
                        className: 'text-center'
                    },
                    {
                        data: 'files',
                        name: 'files',
                        render: function(data, type, row) {
                            if (!data || data.length === 0) {
                                return ''; // Trả về chuỗi rỗng nếu không có dữ liệu
                            }

                            let tableContent = data.map((file, index) => {
                                let fileUrl = `/contracts/${row.id}/${file.file_name}`;
                                return `
            <tr>
                <td class="file-name">${file.name}</td>
                <td class="file-link"><a href="${fileUrl}" target="_blank">${file.file_name}</a></td>
            </tr>`;
                            }).join('');

                            return `
            <div class="table-container">
                <table class="nested-table table table-sm table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Tên</th>
                            <th>File</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${tableContent}
                    </tbody>
                </table>
            </div>`;
                        },
                        className: 'nested-table-container',
                        orderable: false,

                    },

                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                ],
                columnDefs: [{
                        className: 'dtr-control text-center',
                        orderable: false,
                        targets: 0,
                        width: "5px",

                    },
                    {
                        width: "5px",
                        targets: 1,
                        className: 'text-center',
                    },
                    {
                        width: "200px",
                        targets: 2,
                        className: 'text-start',
                    },
                    {
                        width: "5px",
                        targets: 4,
                        className: 'text-start',
                    },

                ],

            });

        });
</script>
@endsection