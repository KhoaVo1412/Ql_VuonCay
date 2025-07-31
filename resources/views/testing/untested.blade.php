@extends('layouts.app')


@section('content')
<div class="page-title my-3">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h5 class="padding">Lô Chưa Kiểm Nghiệm</h5>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb padding">
                    <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="/untested">Lô Chưa Kiểm Nghiệm</a></li>
                </ol>
            </nav>
        </div>
    </div>

    @include('layouts.alert')

</div>
<style>
    .dt-type-numeric {
        text-align: left !important
    }
</style>
<div id="filter" class="my-3">
    <div class="card custom-card" style="padding: 10px">
        <div class="row align-items-end ">


            <div class="col-md-2 mb-2">
                <label class="text-dark fw-bold">Ngày Sản Xuất</label>
                <div class="form-group">
                    <select class="form-select" name="day" id="day">
                        <option value="">-- Chọn Ngày --</option>
                    </select>
                </div>
            </div>

            <div class="col-md-2 mb-2">
                <label class="text-dark fw-bold">Tháng Sản Xuất</label>
                <div class="form-group">
                    <select class="form-select" name="month" id="month">
                        <option value="">-- Chọn Tháng --</option>
                    </select>
                </div>
            </div>

            <div class="col-md-2 mb-2">
                <label class="text-dark fw-bold">Năm Sản Xuất</label>
                <div class="form-group">
                    <select class="form-select" name="year" id="year">
                        <option value="">-- Chọn Năm --</option>
                    </select>
                </div>
            </div>


            <div class="col-md-2 mb-2">
                <div class="form-group">
                    <button class="btn btn-primary" id="filter-btn">Lọc</button>
                </div>
            </div>

            {{-- <div class="col-md-3">
                <label class="text-dark fw-bold">Chọn nhà máy</label>
                <div class="form-group">
                    <select class="form-select" name="factory" id="factory">
                        <option value="">Tất cả</option>
                        <option value="Xí nghiệp cơ khí chế biến 30/4">Xí nghiệp cơ khí chế biến 30/4</option>
                        <option value="Xí nghiệp cơ khí chế biến Quản Lợi">Xí nghiệp cơ khí chế biến Quản Lợi</option>
                    </select>
                </div>
            </div> --}}

        </div>
    </div>
    <script>
        $(document).ready(function() {
                const $day = $("#day");
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

                    $day.empty().append('<option value="">Chọn ngày</option>');

                    if (month && year) {
                        const daysInMonth = new Date(year, month, 0).getDate();
                        for (let day = 1; day <= daysInMonth; day++) {
                            $day.append(`<option value="${day}">${day}</option>`);
                        }
                    }
                }

                $month.on("change", updateDays);
                $year.on("change", updateDays);
                $("#filter-btn").on("click", function() {
                    const selectedDay = $day.val();
                    const selectedMonth = $month.val();
                    const selectedYear = $year.val();
                    console.log("Ngày: ", selectedDay, "Tháng: ", selectedMonth, "Năm: ", selectedYear);
                    localStorage.setItem('selectedDay', selectedDay);
                    localStorage.setItem('selectedMonth', selectedMonth);
                    localStorage.setItem('selectedYear', selectedYear);
                });
                const savedDay = localStorage.getItem('selectedDay');
                const savedMonth = localStorage.getItem('selectedMonth');
                const savedYear = localStorage.getItem('selectedYear');

                if (savedMonth) {
                    $month.val(savedMonth).trigger("change"); // Cập nhật lại tháng
                }
                if (savedYear) {
                    $year.val(savedYear).trigger("change"); // Cập nhật lại năm
                }
                if (savedDay) {
                    $day.val(savedDay); // Cập nhật lại ngày
                }
                updateDays();
            });
    </script>
</div>
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
                                            <th scope="col">STT</th>
                                            <th scope="col">Mã Lô Hàng</th>
                                            <th scope="col">Loại mủ</th>
                                            {{-- <th scope="col">Nhà máy</th> --}}
                                            <th scope="col">Ngày Sản Xuất</th>
                                            <th scope="col">Trạng Thái</th>
                                            <th scope="col">Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th scope="col">STT</th>
                                            <th scope="col">Mã Lô Hàng</th>
                                            <th scope="col">Loại mũ</th>
                                            {{-- <th scope="col">Nhà máy</th> --}}
                                            <th scope="col">Ngày Sản Xuất</th>
                                            <th scope="col">Trạng Thái</th>
                                            <th scope="col">Thao Tác</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @include('layouts.page_svr')
                </div>
            </div>
        </div>
    </div>
</div>
<style>

</style>

<script>
    $(document).ready(function() {
            let table = $('#contract-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json",
                    emptyTable: "Không có dữ liệu phù hợp.",
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
                    url: '/untested-get-data',
                    type: 'GET',
                    data: function(d) {
                        d.day = $("#day").val();
                        d.month = $("#month").val();
                        d.year = $("#year").val();
                        // d.factory = $("#factory").val();
                    },
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
                    // { data: 'factory', name: 'factory' },
                    {
                        data: 'date_sx',
                        name: 'date_sx'
                    },
                    {
                        data: 'checked',
                        name: 'checked'
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


            $("#filter-btn").on("click", function() {
                table.ajax.reload();
            });
        });
</script>
@endsection