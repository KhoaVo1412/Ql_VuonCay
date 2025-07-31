@extends('layouts.app')
@section('content')
    <div class="page-title my-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                {{-- <h4>Báo cáo</h4> --}}
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb padding">
                        <li class="breadcrumb-item"><a href="/">Trang Chủ</a></li>
                        <li class="breadcrumb-item">Báo Cáo</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ url('/report') }}">
        {{-- <div class="container"> --}}
        <div class="card">
            <div class="card-header text-start">
                <div class="row d-flex align-items-end">
                    <h5>Báo cáo</h5>
                    <p class="col-md-3 text-dark">Chọn Thời Điểm Báo Cáo</p>
                    <div class="col-md-3">
                        <label class="text-dark fw-bold">Tháng</label>
                        <div class="form-group">
                            <select class="form-select" name="month" id="month">
                                <option value="" {{ $month == '' ? 'selected' : '' }}>Tới thời điểm hiện tại
                                </option>
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ str_pad($m, 1, '0', STR_PAD_LEFT) }}"
                                        {{ str_pad($m, 1, '0', STR_PAD_LEFT) == $month ? 'selected' : '' }}>
                                        {{ str_pad($m, 1, '0', STR_PAD_LEFT) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="text-dark fw-bold">Năm</label>
                        <div class="form-group">
                            <select class="form-select" name="year" id="year">
                                <option value="" {{ $year == '' ? 'selected' : '' }}>Tất cả
                                </option>
                                {{-- @for ($y = date('Y') - 5; $y <= date('Y') + 5; $y++) <option value="{{ $y }}" {{
                                    $y==$year ? 'selected' : '' }}>
                            {{ $y }}
                            </option>
                            @endfor --}}
                                @for ($y = 2024; $y <= date('Y'); $y++)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group d-grid">
                            <button type="submit" class="btn btn-success" id="filter-btn">Chọn</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div style="card-body">
                    <h4 class="fs-4 fs-md-3 fs-lg-2 text-center">QUẢN LÝ CHẤT LƯỢNG</h4>
                    <div class="container my-4">
                        <p class="fs-6 fs-md-5 fs-lg-4 text-center"><b>Tổng lô
                                ({{ $totalBatch[0] }})</b>
                        <div style="position: relative; height: 300px;">
                            <div id="chartData" data-values='@json($totalBatch)'>
                                <canvas id="pieChart1"></canvas>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div style="card-body">
                    <h4 class="fs-4 fs-md-3 fs-lg-2 text-center">DANH SÁCH LÔ HÀNG</h4>
                    <div class="container my-4">
                        <div style="position: relative; height: 300px;">
                            <div id="barData" data-values='@json($totalIngredient)'>
                                <canvas id="barChart1" height="320px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--
        </div> --}}
    </form>

    <script>
        //Plugin hiển thị phần trăm trên biểu đồ
        function generatePercentLabelPlugin(fontSize, fontColor, total) {
            return {
                id: 'percentLabelPlugin',
                afterDatasetsDraw(chart) {
                    const {
                        ctx,
                        data
                    } = chart;
                    const dataset = data.datasets[0];

                    chart.getDatasetMeta(0).data.forEach((element, index) => {
                        const value = dataset.data[index];

                        // Tính phần trăm
                        const percentage = (value / total) * 100;

                        // Nếu phần trăm là 0% thì không hiển thị
                        if (percentage === 0) {
                            return;
                        }

                        const text = `${percentage.toFixed(1)}%`;
                        const {
                            x,
                            y
                        } = element.tooltipPosition();

                        ctx.save();
                        ctx.fillStyle = fontColor || '#fff';
                        ctx.font = `${fontSize}px sans-serif`;
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(text, x, y);
                        ctx.restore();
                    });
                }
            };
        }

        //Plugin hiển thị text trên biểu đồ

        // function generateTextLabelPlugin(fontSize, fontColor) {
        //     return {
        //         id: 'percentLabelPlugin',
        //         afterDatasetsDraw(chart) {
        //             const {
        //                 ctx,
        //                 data
        //             } = chart;
        //             const dataset = data.datasets[0];


        //             chart.getDatasetMeta(0).data.forEach((element, index) => {
        //                 const value = dataset.data[index];
        //                 const text = value;
        //                 const {
        //                     x,
        //                     y
        //                 } = element.getCenterPoint();

        //                 ctx.save();
        //                 ctx.fillStyle = fontColor || '#fff';
        //                 ctx.font = `${fontSize}px sans-serif`;
        //                 ctx.textAlign = 'center';
        //                 ctx.textBaseline = 'middle';
        //                 ctx.fillText(text, x, y);
        //                 ctx.restore();
        //             });
        //         }
        //     };
        // }

        // Hàm tạo biểu đồ Pie
        function createPieChart(canvasId, labels, values) {
            const total = values[0];
            const ctx = document.getElementById(canvasId).getContext('2d');
            const isMobile = window.innerWidth <= 768;
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: [values[1], values[2]],
                        backgroundColor: ['red', 'blue'],
                    }]
                },
                options: {
                    cutout: (context) => {
                        return window.innerWidth <= 768 ? '50' :
                            '';
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            onClick: (event) => {
                                event.preventDefault(); // Chặn event click
                            },
                            labels: {
                                font: {
                                    size: 14 // Đổi kích thước chữ
                                }
                            },
                        },
                        tooltip: {
                            callbacks: {

                                label: (context) => {
                                    const label = context.label;
                                    const value = context.raw;
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        },
                        rotation: Math.PI / 4, // Quay góc của miếng bánh
                        circumference: Math.PI * 1.5, // Giảm bớt độ rộng mỗi miếng bánh
                        datalabels: {
                            color: 'white',
                            anchor: 'center',
                            align: 'center',

                            font: {
                                size: 12
                            },
                            padding: 10, // Thêm padding cho nhãn
                            textPadding: 10, // Thêm khoảng cách giữa số liệu và viền
                            formatter: (value, context) => {
                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return Number(value) === 0 ? null : ` ${percentage}% `;
                            }

                        },
                    }
                },
                // plugins: [generatePercentLabelPlugin(14, '#fff', total)]
                plugins: [ChartDataLabels]
            });
        }
        const legendMargin = {
            id: 'legendMargin',
            beforeInit(chart, legend, options) {
                let fitValue = chart.legend.fit;
                chart.legend.fit = function fit() {
                    fitValue.bind(chart.legend)();
                    return this.height += options.paddingTop;
                }
            },
            defaults: {
                paddingTop: 0 // <-- default padding
            }
        };

        function createBarChart(canvasId, labels, values) {
            // const total = values[0];
            const ctx = document.getElementById(canvasId).getContext('2d');

            new Chart(ctx, {
                type: 'bar', // Thay đổi từ 'pie' sang 'bar'
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: ['red', 'blue'],
                        barPercentage: 0.5, // Điều chỉnh độ rộng cột (giá trị từ 0 đến 1)
                        categoryPercentage: 0.5 // Điều chỉnh khoảng cách giữa các cột
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            ticks: {
                                font: {
                                    size: 14,
                                },

                            }
                        },
                        y: {
                            beginAtZero: true, // Bắt đầu từ 0
                            ticks: {
                                autoSkip: true, // Tự động bỏ bớt nếu có quá nhiều giá trị
                            },

                        }
                    },
                    // scales: {
                    //     y: {
                    //         ticks: {
                    //             display: false // Ẩn số trên trục Y
                    //         },
                    //         grid: {
                    //             drawTicks: false // Ẩn dấu gạch nhỏ bên trục Y (nếu có)
                    //         }
                    //     }
                    // },
                    plugins: {
                        legendMargin: { // <-- Set option of custom plugin
                            paddingTop: 20 // <---- override the default value
                        },
                        legend: {
                            display: false,
                        },
                        datalabels: {
                            color: (context) => {
                                return window.innerWidth <= 768 ? 'black' :
                                    'white';
                            },
                            anchor: (context) => {
                                // Dynamically set anchor based on screen width
                                return window.innerWidth <= 768 ? 'end' :
                                    'center'; // center on mobile
                            },
                            align: (context) => {
                                // Dynamically set align based on screen width
                                return window.innerWidth <= 768 ? 'top' :
                                    'center'; // center on mobile
                            },
                            font: {
                                size: 14
                            },
                            offset: -2,
                            formatter: (value) => {
                                return Number(value) === 0 ? null : value;
                            }

                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                    const label = context.label;
                                    const value = context.raw;
                                    // const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value}`;
                                }
                            }
                        }
                    }
                },
                // plugins: [generateTextLabelPlugin(14, '#fff')]
                plugins: [legendMargin, ChartDataLabels]
            });

        }


        var totalBatch = JSON.parse(document.getElementById('chartData').dataset.values);
        var totalIngredient = JSON.parse(document.getElementById('barData').dataset.values);

        // // Dữ liệu từ Laravel (biểu đồ lô đã kiểm nghiệm)

        createPieChart('pieChart1', ['Chưa kiểm nghiệm', 'Đã kiểm nghiệm'], totalBatch);
        createBarChart('barChart1', ['Đã tạo', 'Đã liên kết'], totalIngredient);
    </script>
@endsection
