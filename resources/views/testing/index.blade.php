@extends('layouts.app')


@section('content')
<div class="page-title my-3">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h5 class="padding">Lô Đã Kiểm Nghiệm</h5>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb padding">
                    <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="/testing">Lô Đã Kiểm Nghiệm</a></li>
                </ol>
            </nav>
        </div>
    </div>
    @include('layouts.alert')
</div>
<style>
    .dt-type-numeric {
        text-align: left !important;
    }
</style>
<form action="{{ url('/export-excel') }}" method="GET">
    <div id="filter" class="mb-4">
        <div class="card custom-card" style="padding: 10px">
            <div class="row align-items-end">


                <div class="col-md-3 mb-2">
                    <label class="text-dark fw-bold">Tháng Kiểm Nghiệm</label>
                    <div class="form-group mb-0">
                        <select class="form-select" name="month" id="month">
                            <option value="">-- Chọn Tháng --</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3 mb-2">
                    <label class="text-dark fw-bold">Năm Kiểm Nghiệm</label>
                    <div class="form-group mb-0">
                        <select class="form-select" name="year" id="year">
                            <option value="">-- Chọn Năm --</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3 mb-2">
                    <label class="text-dark fw-bold">Hạng</label>
                    <div class="form-group mb-0">
                        <select class="form-select" name="rank" id="rank">
                            <option value="">-- Chọn Hạng --</option>
                            @foreach ($ranks as $rank)
                            {{-- <option value="{{ $rank }}" {{ old('rank')==$rank ? 'selected' : '' }}>
                                {{ $rank }}
                            </option> --}}
                            <option value="{{ $rank }}" {{ old('rank')==$rank ? 'selected' : '' }}>
                                {{ strtoupper($rank) }}
                            </option>
                            @endforeach
                        </select>
                        <!-- <select class="form-select" name="rank" id="rank">
                                                                                                                        <option value="">Tất cả</option>
                                                                                                                        <option value="3L">3L</option>
                                                                                                                        <option value="RSS3">RSS3</option>
                                                                                                                    </select> -->
                    </div>
                </div>

                <div class="col-md-3 mb-2">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-between align-items-center cell-two">
                        <button type="button" class="btn btn-primary" id="filter-btn-t">Lọc</button>
                        <button id="export-btn" class="btn btn-success" type="submit">
                            <i class="fa fa-download me-2"></i>
                            Xuất Dữ Liệu
                        </button>

                        {{--
                        <div class="col-md-2 text-end">
                            <div class="form-group">
                                <button class="btn btn-dark" id="filter-btn-t">Tìm Kiếm</button>
                            </div>
                        </div> --}}
                    </div>
                </div>

                <!-- <div class="col-md-3">
                                                                                                                <label class="text-dark fw-bold">Ngày kiểm nghiệm</label>
                                                                                                                <div class="form-group">
                                                                                                                    <select class="form-select" name="day" id="day">
                                                                                                                        <option value="">Chọn ngày</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div> -->

                <!-- <div class="col-md-3">
                                                                                                                <label class="text-dark fw-bold">Nhà máy</label>
                                                                                                                <div class="form-group">
                                                                                                                    <select class="form-select" name="factory" id="factory">
                                                                                                                        <option value="">Tất cả</option>
                                                                                                                        <option value="Xí nghiệp cơ khí chế biến 30/4">Xí nghiệp cơ khí chế biến 30/4</option>
                                                                                                                        <option value="Xí nghiệp cơ khí chế biến Quản Lợi">Xí nghiệp cơ khí chế biến Quản Lợi
                                                                                                                        </option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div> -->



                <!-- <div class="col-md-4">
                                                                                                                <label class="text-dark fw-bold">Loại</label>
                                                                                                                <div class="form-group">
                                                                                                                    <select class="form-select" name="type" id="type">
                                                                                                                        <option value="">Tất cả</option>
                                                                                                                        <option value="svr">svr</option>
                                                                                                                        {{-- <option value="latex">latex</option> --}}
                                                                                                                        {{-- <option value="rss">rss</option> --}}
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div> -->



            </div>
        </div>
        <script>
            $(document).ready(function() {
                    // const $day = $("#day");
                    const $month = $("#month");
                    const $year = $("#year");

                    const currentDate = new Date();
                    const currentYear = currentDate.getFullYear();
                    const currentMonth = currentDate.getMonth() + 1;
                    // const futureYears = 50;

                    for (let month = 1; month <= 12; month++) {
                        const selected = month === currentMonth ? "selected" : "";
                        $month.append(`<option value="${month}" ${selected}>${month}</option>`);
                    }

                    for (let year = 2020; year <= currentYear; year++) {
                        const selected = year === currentYear ? "selected" : "";
                        $year.append(`<option value="${year}" ${selected}>${year}</option>`);
                    }

                    function updateDays() {
                        const month = parseInt($month.val());
                        const year = parseInt($year.val());

                        // $day.empty().append('<option value="">Chọn ngày</option>');

                        // if (month && year) {
                        //     const daysInMonth = new Date(year, month, 0).getDate();
                        //     for (let day = 1; day <= daysInMonth; day++) {
                        //         $day.append(`<option value="${day}">${day}</option>`);
                        //     }
                        // }
                    }

                    $month.on("change", updateDays);
                    $year.on("change", updateDays);

                    updateDays();
                });
        </script>


    </div>
</form>
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
                    <div id="hidden-columns_wrapper" class="dataTables_wrapper dt-bootstrap5">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="contract-table" class="table table-bordered w-100 dataTable hover"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>STT</th>
                                            <th>Mã Lô Hàng</th>
                                            <th>Loại mủ</th>
                                            <!-- <th>Nhà máy</th> -->
                                            <!-- <th>Ngày sản xuất</th> -->
                                            <th>Ngày Gửi Mẫu</th>
                                            <th>Ngày Kiểm Nghiệm</th>
                                            <th>Hạng</th>
                                            <th>Loại</th>
                                            <th>Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th>STT</th>
                                            <th>Mã Lô Hàng</th>
                                            <th>Loại mũ</th>
                                            <!-- <th>Nhà máy</th> -->
                                            <!-- <th>Ngày sản xuất</th> -->
                                            <th>Ngày Gửi Mẫu</th>
                                            <th>Ngày Kiểm Nghiệm</th>
                                            <th>Hạng</th>
                                            <th>Loại</th>
                                            <th>Thao Tác</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
            let table = $('#contract-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json",
                    emptyTable: "Không có dữ liệu phù hợp.",
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/testing-get-data',
                    type: 'GET',
                    data: function(d) {
                        // d.day = $("#day").val();
                        d.month = $("#month").val();
                        d.year = $("#year").val();
                        // d.factory = $("#factory").val();
                        d.rank = $("#rank").val();
                        // d.type = $("#type").val();
                        console.log(d);
                    },
                },
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
                        searchable: false
                    },
                    {
                        data: 'batch_code',
                        name: 'batch_code'
                    },
                    {
                        data: 'loai_mu',
                        name: 'loai_mu'
                    },
                    // {
                    //     data: 'factory',
                    //     name: 'factory'
                    // },
                    // {
                    //     data: 'date_sx',
                    //     name: 'date_sx'
                    // },
                    {
                        data: 'ngay_gui_mau',
                        name: 'ngay_gui_mau'
                    },
                    {
                        data: 'ngay_kiem_nghiem',
                        name: 'ngay_kiem_nghiem'
                    },
                    {
                        data: 'rank',
                        name: 'rank',
                        render: function(data, type, row) {
                            return data ? data.toUpperCase() : '';
                        }
                    },
                    {
                        data: 'type',
                        name: 'type',
                        render: function(data, type, row) {
                            return data ? data.toUpperCase() : '';
                        }
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return $('<div/>').html(data).text();
                        }
                    }
                ],
                // autoWidth: false,
                // scrollX: true
            });

            $("#filter-btn-t").on("click", function() {
                table.ajax.reload();
            });
        });
</script>
@endsection