@extends('layouts.app')


@section('content')
<section class="section mt-3">
    <div class="card">
        <div class="card-body">
            {{-- {{ $posts->desc }} --}}
            <main class="main-content">
                <!-- Dashboard View -->
                <div id="dashboard-view" class="view">
                    <div class="page-header">
                        <h3>Tổng Quan Vườn Cây</h3>
                        <p>Theo dõi tình trạng tổng thể của vườn cây</p>
                    </div>

                    <!-- Stats Cards -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-header">
                                <i class="fas fa-tree stat-icon" style="color: #059669;padding-right: 5px;"></i>
                                <span class="stat-title">Tổng số cây</span>
                            </div>
                            <div class="stat-value">{{ number_format($total) }}</div>
                            <div class="stat-change">+12 cây mới</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <i class="fas fa-heart stat-icon" style="color: #22c55e;padding-right: 5px;"></i>
                                <span class="stat-title">Cây khỏe mạnh</span>
                            </div>
                            <div class="stat-value">{{ number_format($healthy) }}</div>
                            <div class="stat-change">{{ $healthyPercent }}%</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <i class="fas fa-exclamation-triangle stat-icon"
                                    style="color: #eab308;padding-right: 5px;"></i>
                                <span class="stat-title">Cây bệnh</span>
                            </div>
                            <div class="stat-value">{{ number_format($sick) }}</div>
                            <div class="stat-change">+3 từ tuần trước</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <i class="fas fa-times-circle stat-icon" style="color: #ef4444;padding-right: 5px;"></i>
                                <span class="stat-title">Cây đổ/chết</span>
                            </div>
                            <div class="stat-value">{{ number_format($dead) }}</div>
                            <div class="stat-change">+1 từ tuần trước</div>
                        </div>
                    </div>

                    <div class="content-grid">
                        <!-- Health Status -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Tình trạng sức khỏe</h3>
                                <p class="card-description">Phân bố tình trạng cây trong vườn</p>
                            </div>
                            <div class="card-content">
                                <div class="progress-item">
                                    <div class="progress-header">
                                        <span>Khỏe mạnh</span>
                                        <span>92.7%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 92.7%"></div>
                                    </div>
                                </div>

                                <div class="progress-item">
                                    <div class="progress-header">
                                        <span>Bệnh nhẹ</span>
                                        <span>4.2%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill yellow" style="width: 4.2%"></div>
                                    </div>
                                </div>

                                <div class="progress-item">
                                    <div class="progress-header">
                                        <span>Bệnh nặng</span>
                                        <span>1.9%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill red" style="width: 1.9%"></div>
                                    </div>
                                </div>

                                <div class="progress-item">
                                    <div class="progress-header">
                                        <span>Đổ/Chết</span>
                                        <span>1.2%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill gray" style="width: 1.2%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Alerts -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Cảnh báo gần đây</h3>
                                <p class="card-description">Các vấn đề cần xử lý khẩn cấp</p>
                            </div>
                            <div class="card-content">
                                <div class="alert-item">
                                    <div class="alert-left">
                                        <i class="fas fa-exclamation-triangle alert-icon high"></i>
                                        <div class="alert-info-h">
                                            <h4>Cây số #A127</h4>
                                            <p>Bệnh</p>
                                        </div>
                                    </div>
                                    <div class="alert-right">
                                        <span class="badge high">Cao</span>
                                        <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">2 giờ trước
                                        </p>
                                    </div>
                                </div>

                                <div class="alert-item">
                                    <div class="alert-left">
                                        <i class="fas fa-exclamation-triangle alert-icon critical"></i>
                                        <div class="alert-info-h">
                                            <h4>Cây số #B045</h4>
                                            <p>Đổ</p>
                                        </div>
                                    </div>
                                    <div class="alert-right">
                                        <span class="badge critical">Khẩn cấp</span>
                                        <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">5 giờ trước
                                        </p>
                                    </div>
                                </div>

                                <div class="alert-item">
                                    <div class="alert-left">
                                        <i class="fas fa-exclamation-triangle alert-icon medium"></i>
                                        <div class="alert-info-h">
                                            <h4>Khu vực C</h4>
                                            <p>Sâu bệnh</p>
                                        </div>
                                    </div>
                                    <div class="alert-right">
                                        <span class="badge medium">Trung bình</span>
                                        <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">1 ngày trước
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="page-header">
                        <h3>Tổng Quan Sản Lượng</h3>
                        <p>Theo dõi quá trình thu mua và khai thác</p>
                    </div>
                    <div class="stats-grid">
                        {{-- CARD: TỔNG SẢN LƯỢNG KHAI THÁC --}}
                        <div class="stat-card">
                            <div class="stat-header">
                                <i class="fas fa-basket-shopping stat-icon"
                                    style="color:#059669;padding-right:5px;"></i>
                                <span class="stat-title">Tổng Sản Lượng Khai Thác</span>
                            </div>
                            <div class="card-body">
                                <div class="mini-filter" style="display:flex;gap:.5rem;align-items:center;">
                                    <div class="form-control">
                                        <label class="form-label" for="purchaseFrom">Từ Ngày</label>
                                        <input type="date" id="harvestFrom"
                                            value="{{ optional($fromDate)->toDateString() }}" class="form-control">
                                    </div>
                                    <div class="form-control">
                                        <label class="form-label" for="purchaseFrom">Đến Ngày</label>
                                        <input type="date" id="harvestTo"
                                            value="{{ optional($toDate)->toDateString() }}" class="form-control">
                                    </div>
                                    <button id="harvestApply" class="btn btn-sm btn-primary">Lọc</button>
                                </div>
                                <canvas id="harvestDonut"></canvas>
                            </div>
                        </div>
                        {{-- CARD: TỔNG SẢN LƯỢNG MỦ THU MUA --}}
                        <div class="stat-card">
                            <div class="stat-header">
                                <i class="fa-regular fa-warehouse-full" style="color:#059669;padding-right:5px;"></i>
                                <span class="stat-title">Tổng Sản Lượng Mủ Thu Mua</span>
                            </div>
                            <div class="mini-filter" style="display:flex;gap:.5rem;align-items:center;">
                                <div class="form-control">
                                    <label class="form-label" for="purchaseFrom">Từ Ngày</label>
                                    <input type="date" id="purchaseFrom"
                                        value="{{ optional($fromDate)->toDateString() }}" class="form-control">
                                </div>
                                <div class="form-control">
                                    <label class="form-label" for="purchaseTo">Đến Ngày</label>
                                    <input type="date" id="purchaseTo" value="{{ optional($toDate)->toDateString() }}"
                                        class="form-control">
                                </div>
                                <button id="purchaseApply" class="btn btn-sm btn-primary">Lọc</button>
                            </div>
                            <div class="card-body">
                                <canvas id="rubberDonut"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Chỉ include 1 lần Chart.js trong trang --}}
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                        // ===== Tham số endpoint JSON =====
                        const purchaseUrl = @json(route('dashboard.purchaseSummary'));
                        const harvestUrl  = @json(route('dashboard.harvestSummary'));

                        // ===== Plugin text tổng ở giữa (tự tính theo dataset hiện tại) =====
                        const centerTotalText = {
                            id: 'centerTotalText',
                            afterDraw(chart) {
                            const { ctx, chartArea } = chart;
                            if (!chartArea) return;
                            const { left, top, width, height } = chartArea;
                            const sum = (chart.data.datasets?.[0]?.data || [])
                                .reduce((a, b) => a + (Number(b) || 0), 0);
                            const cx = left + width / 2;
                            const cy = top + height / 2;

                            ctx.save();
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillStyle = '#111827';
                            ctx.font = `${Math.min(width, height) / 9}px sans-serif`;
                            ctx.fillText(new Intl.NumberFormat('vi-VN').format(sum) + ' Kg', cx, cy + 9);
                            ctx.restore();
                            }
                        };

                        // ===== Bảng màu dùng chung =====
                        const COLORS = ['#059669','#22c55e','#eab308','#ef4444','#3b82f6','#8b5cf6','#ec4899','#14b8a6'];

                        //  1) Donut Thu Mua (khởi tạo với dữ liệu ban đầu từ server) 
                        const purchaseCtx = document.getElementById('rubberDonut').getContext('2d');
                        const purchaseChart = new Chart(purchaseCtx, {
                            type: 'doughnut',
                            data: {
                            labels: @json($byType->pluck('product_name')),
                            datasets: [{
                                data: (@json($byType->pluck('total_qty'))).map(v => Number(v) || 0),
                                backgroundColor: COLORS,
                                borderWidth: 1
                            }]
                            },
                            options: {
                            responsive: true,
                            cutout: '70%',
                            plugins: {
                                legend: { position: 'right' },
                                tooltip: {
                                callbacks: {
                                    label: function (ctx) {
                                    const value = Number(ctx.raw) || 0;
                                    const total = (ctx.chart.data.datasets[0].data || [])
                                        .reduce((a, b) => a + (Number(b)||0), 0);
                                    const percent = total > 0 ? (value/total*100).toFixed(1) : 0;
                                    return `${ctx.label}: ${new Intl.NumberFormat('vi-VN').format(value)} (${percent}%)`;
                                    }
                                }
                                }
                            }
                            },
                            plugins: [centerTotalText]
                        });

                        async function reloadPurchase() {
                            const from = document.getElementById('purchaseFrom').value;
                            const to   = document.getElementById('purchaseTo').value;

                            const url = new URL(purchaseUrl, window.location.origin);
                            if (from) url.searchParams.set('from', from);
                            if (to)   url.searchParams.set('to', to);

                            const res = await fetch(url.toString());
                            const json = await res.json();
                            if (!json.ok) return;

                            purchaseChart.data.labels = json.labels || [];
                            purchaseChart.data.datasets[0].data = (json.data || []).map(v => Number(v) || 0);
                            purchaseChart.update();
                        }

                        document.getElementById('purchaseApply').addEventListener('click', reloadPurchase);
                        ['purchaseFrom','purchaseTo'].forEach(id => {
                            document.getElementById(id).addEventListener('keyup', e => { if (e.key === 'Enter') reloadPurchase(); });
                        });

                        //  2) Donut Khai Thác (khởi tạo với dữ liệu ban đầu từ server) 
                        const harvestCtx = document.getElementById('harvestDonut').getContext('2d');
                        const harvestChart = new Chart(harvestCtx, {
                            type: 'doughnut',
                            data: {
                            labels: @json($harvestByProduct->pluck('product_name')),
                            datasets: [{
                                data: (@json($harvestByProduct->pluck('total_qty'))).map(v => Number(v) || 0),
                                backgroundColor: COLORS,
                                borderWidth: 1
                            }]
                            },
                            options: {
                            responsive: true,
                            cutout: '70%',
                            plugins: {
                                legend: { position: 'right' },
                                tooltip: {
                                callbacks: {
                                    label: function (ctx) {
                                    const value = Number(ctx.raw) || 0;
                                    const total = (ctx.chart.data.datasets[0].data || [])
                                        .reduce((a, b) => a + (Number(b)||0), 0);
                                    const percent = total > 0 ? (value/total*100).toFixed(1) : 0;
                                    return `${ctx.label}: ${new Intl.NumberFormat('vi-VN').format(value)} (${percent}%)`;
                                    }
                                }
                                }
                            }
                            },
                            plugins: [centerTotalText]
                        });

                        async function reloadHarvest() {
                            const from = document.getElementById('harvestFrom').value;
                            const to   = document.getElementById('harvestTo').value;

                            const url = new URL(harvestUrl, window.location.origin);
                            if (from) url.searchParams.set('from', from);
                            if (to)   url.searchParams.set('to', to);

                            const res = await fetch(url.toString());
                            const json = await res.json();
                            if (!json.ok) return;

                            harvestChart.data.labels = json.labels || [];
                            harvestChart.data.datasets[0].data = (json.data || []).map(v => Number(v) || 0);
                            harvestChart.update();
                        }

                        document.getElementById('harvestApply').addEventListener('click', reloadHarvest);
                        ['harvestFrom','harvestTo'].forEach(id => {
                            document.getElementById(id).addEventListener('keyup', e => { if (e.key === 'Enter') reloadHarvest(); });
                        });
                        });
                    </script>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-header">
                                <i class="fa-regular fa-warehouse-full" style="color: #059669;padding-right: 5px;"></i>
                                <span class="stat-title">Xu hướng sản lượng khai thác theo tháng (12 tháng gần
                                    nhất)</span>
                            </div>
                            <div class="card-body">
                                <canvas id="harvestMonthlyLine" height="260"></canvas>
                            </div>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                            const ctx = document.getElementById('harvestMonthlyLine').getContext('2d');

                            const labels = @json($labels);                         // ['10/2024','11/2024',...]
                            const data   = (@json($dataMonthlyHarvest)).map(v => Number(v) || 0);

                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                labels,
                                datasets: [{
                                    label: 'Khai thác',
                                    data,
                                    fill: false,
                                    tension: 0.3,          // đường mượt
                                    pointRadius: 3,
                                    borderWidth: 2         // (không set màu -> để Chart.js tự chọn, đúng guideline)
                                }]
                                },
                                options: {
                                responsive: true,
                                plugins: {
                                    legend: { display: true },
                                    tooltip: {
                                    callbacks: {
                                        label: (ctx) => {
                                        const v = Number(ctx.parsed.y) || 0;
                                        return ' ' + new Intl.NumberFormat('vi-VN').format(v);
                                        }
                                    }
                                    }
                                },
                                scales: {
                                    y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: (value) => new Intl.NumberFormat('vi-VN').format(value)
                                    }
                                    }
                                }
                                }
                            });
                            });
                        </script>
                        <div class="stat-card">
                            <div class="stat-header">
                                <i class="fa-regular fa-warehouse-full" style="color: #059669;padding-right: 5px;"></i>
                                <span class="stat-title">Bảng thu mua theo tháng (12 tháng gần nhất)</span>
                            </div>
                            <div class="card-body">
                                <canvas id="purchaseMonthlyLine" height="260"></canvas>
                            </div>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                            const ctx = document.getElementById('purchaseMonthlyLine').getContext('2d');
                            const labels = @json($labelsPurchase);
                            const purchase = (@json($dataMonthlyPurchase)).map(v => Number(v) || 0);

                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                labels,
                                datasets: [{
                                    label: 'Thu mua',
                                    data: purchase,
                                    fill: false,
                                    tension: 0.3,
                                    pointRadius: 3,
                                    borderWidth: 2
                                    // không set màu → để Chart.js tự chọn
                                }]
                                },
                                options: {
                                responsive: true,
                                plugins: {
                                    legend: { display: true },
                                    tooltip: {
                                    callbacks: {
                                        label: (ctx) => ' ' + new Intl.NumberFormat('vi-VN').format(Number(ctx.parsed.y)||0)
                                    }
                                    }
                                },
                                scales: {
                                    y: {
                                    beginAtZero: true,
                                    ticks: { callback: v => new Intl.NumberFormat('vi-VN').format(v) }
                                    }
                                }
                                }
                            });
                            });
                        </script>
                    </div>
                </div>

                <!-- Map View -->
                <div id="map-view" class="view hidden">
                    <div class="page-header">
                        <h2>Bản đồ vườn cây</h2>
                        <p>Xem tổng quan vị trí và tình trạng các khu vực</p>
                    </div>

                    <div class="content-grid">
                        <div class="card" style="grid-column: 1 / -1;">
                            <div class="card-header">
                                <h3 class="card-title">Sơ đồ vườn</h3>
                                <p class="card-description">Click vào từng khu để xem chi tiết</p>
                            </div>
                            <div class="card-content">
                                <div class="map-container">
                                    <div class="zone healthy" style="left: 20%; top: 20%;">
                                        <h3>Khu A</h3>
                                        <p style="font-size: 0.875rem;">156 cây</p>
                                        <div class="zone-indicators">
                                            <div class="indicator green"></div>
                                            <div class="indicator yellow"></div>
                                            <div class="indicator red"></div>
                                        </div>
                                    </div>

                                    <div class="zone healthy" style="left: 60%; top: 20%;">
                                        <h3>Khu B</h3>
                                        <p style="font-size: 0.875rem;">234 cây</p>
                                        <div class="zone-indicators">
                                            <div class="indicator green"></div>
                                            <div class="indicator yellow"></div>
                                            <div class="indicator red"></div>
                                        </div>
                                    </div>

                                    <div class="zone warning" style="left: 20%; top: 60%;">
                                        <h3>Khu C</h3>
                                        <p style="font-size: 0.875rem;">189 cây</p>
                                        <div class="zone-indicators">
                                            <div class="indicator green"></div>
                                            <div class="indicator yellow"></div>
                                            <div class="indicator red"></div>
                                        </div>
                                    </div>

                                    <div class="zone healthy" style="left: 60%; top: 60%;">
                                        <h3>Khu D</h3>
                                        <p style="font-size: 0.875rem;">298 cây</p>
                                        <div class="zone-indicators">
                                            <div class="indicator green"></div>
                                            <div class="indicator yellow"></div>
                                            <div class="indicator red"></div>
                                        </div>
                                    </div>

                                    <div class="legend">
                                        <h4>Chú thích</h4>
                                        <div class="legend-item">
                                            <div class="legend-color healthy" style="border-color: #22c55e;"></div>
                                            <span>Khu khỏe mạnh (&lt;5% bệnh)</span>
                                        </div>
                                        <div class="legend-item">
                                            <div class="legend-color warning" style="border-color: #eab308;"></div>
                                            <span>Khu cảnh báo (5-10% bệnh)</span>
                                        </div>
                                        <div class="legend-item">
                                            <div class="legend-color danger" style="border-color: #ef4444;"></div>
                                            <span>Khu nguy hiểm (&gt;10% bệnh)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trees Management View -->
                <div id="trees-view" class="view hidden">
                    <div class="page-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h2>Quản lý cây</h2>
                                <p>Theo dõi và quản lý từng cây trong vườn</p>
                            </div>
                            <button class="btn btn-primary">
                                <i class="fas fa-plus" style="margin-right: 0.5rem;"></i>
                                Thêm cây mới
                            </button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-content">
                            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                                <input type="text" class="search-input"
                                    placeholder="Tìm kiếm theo mã cây, loại cây, vị trí..." style="flex: 1;">
                                <select class="search-input" style="width: 200px;">
                                    <option value="all">Tất cả</option>
                                    <option value="healthy">Khỏe mạnh</option>
                                    <option value="sick-light">Bệnh nhẹ</option>
                                    <option value="sick-heavy">Bệnh nặng</option>
                                    <option value="fallen">Đổ</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Danh sách cây (4)</h3>
                        </div>
                        <div class="card-content">
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Mã cây</th>
                                            <th>Loại cây</th>
                                            <th>Tuổi</th>
                                            <th>Vị trí</th>
                                            <th>Chiều cao</th>
                                            <th>Tình trạng</th>
                                            <th>Ghi chú</th>
                                            <th>Kiểm tra cuối</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>A001</strong></td>
                                            <td>Xoài</td>
                                            <td>5 năm</td>
                                            <td>Khu A - Hàng 1</td>
                                            <td>3.2m</td>
                                            <td><span class="status-badge status-healthy">Khỏe mạnh</span></td>
                                            <td>Tốt</td>
                                            <td>15/12/2024</td>
                                            <td>
                                                <button class="btn btn-outline" style="margin-right: 0.25rem;"><i
                                                        class="fas fa-eye"></i></button>
                                                <button class="btn btn-outline" style="margin-right: 0.25rem;"><i
                                                        class="fas fa-edit"></i></button>
                                                <button class="btn btn-outline" style="color: #ef4444;"><i
                                                        class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>A002</strong></td>
                                            <td>Cam</td>
                                            <td>3 năm</td>
                                            <td>Khu A - Hàng 1</td>
                                            <td>2.1m</td>
                                            <td><span class="status-badge status-sick-light">Bệnh nhẹ</span></td>
                                            <td>Lá vàng</td>
                                            <td>14/12/2024</td>
                                            <td>
                                                <button class="btn btn-outline" style="margin-right: 0.25rem;"><i
                                                        class="fas fa-eye"></i></button>
                                                <button class="btn btn-outline" style="margin-right: 0.25rem;"><i
                                                        class="fas fa-edit"></i></button>
                                                <button class="btn btn-outline" style="color: #ef4444;"><i
                                                        class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>B015</strong></td>
                                            <td>Bưởi</td>
                                            <td>7 năm</td>
                                            <td>Khu B - Hàng 3</td>
                                            <td>4.1m</td>
                                            <td><span class="status-badge status-sick-heavy">Bệnh nặng</span></td>
                                            <td>Sâu đục thân</td>
                                            <td>13/12/2024</td>
                                            <td>
                                                <button class="btn btn-outline" style="margin-right: 0.25rem;"><i
                                                        class="fas fa-eye"></i></button>
                                                <button class="btn btn-outline" style="margin-right: 0.25rem;"><i
                                                        class="fas fa-edit"></i></button>
                                                <button class="btn btn-outline" style="color: #ef4444;"><i
                                                        class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>C032</strong></td>
                                            <td>Nhãn</td>
                                            <td>4 năm</td>
                                            <td>Khu C - Hàng 2</td>
                                            <td>0m</td>
                                            <td><span class="status-badge status-fallen">Đổ</span></td>
                                            <td>Gãy gốc</td>
                                            <td>12/12/2024</td>
                                            <td>
                                                <button class="btn btn-outline" style="margin-right: 0.25rem;"><i
                                                        class="fas fa-eye"></i></button>
                                                <button class="btn btn-outline" style="margin-right: 0.25rem;"><i
                                                        class="fas fa-edit"></i></button>
                                                <button class="btn btn-outline" style="color: #ef4444;"><i
                                                        class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Other Views (Placeholder) -->
                <div id="alerts-view" class="view hidden">
                    <div class="page-header">
                        <h2>Cảnh báo</h2>
                        <p>Trang cảnh báo đang phát triển...</p>
                    </div>
                </div>

                <div id="health-view" class="view hidden">
                    <div class="page-header">
                        <h2>Tình trạng sức khỏe</h2>
                        <p>Trang tình trạng sức khỏe đang phát triển...</p>
                    </div>
                </div>

                <div id="schedule-view" class="view hidden">
                    <div class="page-header">
                        <h2>Lịch chăm sóc</h2>
                        <p>Trang lịch chăm sóc đang phát triển...</p>
                    </div>
                </div>

                <div id="settings-view" class="view hidden">
                    <div class="page-header">
                        <h2>Cài đặt</h2>
                        <p>Trang cài đặt đang phát triển...</p>
                    </div>
                </div>
            </main>
        </div>
    </div>
</section>

@hasanyrole('Admin|Nông Trường|Xem Nông Trường')
<div class="card">
    <div class="card-header">
        <div class="card-body">
            {{-- <h4 style="text-align: center">Biểu Đồ Loại Mủ Theo Từng Nông Trường</h4> --}}
            <h4 style="text-align: center">Biểu Đồ</h4>
            <canvas id="tripTypeOfPusPlantationChart"></canvas>
        </div>
    </div>
</div>
@endhasanyrole

@hasanyrole('Admin|Xem Hợp Đồng|Quản Lý Hợp Đồng')
<div class="card">
    <div class="card-header">
        <div class="card-body">
            <h4 style="text-align: center">Biểu đồ</h4>
            <canvas id="barChartContractCustomer"></canvas>
        </div>
    </div>
</div>
@endhasanyrole
<style>
    .main-content {
        flex: 1;
        overflow-y: auto;
        padding: 2rem;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-header h2 {
        font-size: 1.875rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .page-header p {
        color: #64748b;
    }

    /* Cards */
    .card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    }

    .card-header {
        padding: 1.5rem 1.5rem 0;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .card-description {
        color: #64748b;
        font-size: 0.875rem;
    }

    .card-content {
        padding: 1.5rem;
    }

    /* Stats Grid */
    .stats-grid {
        text-align: center;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }

    .stat-header {
        /* display: flex; */
        justify-content: between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .stat-title {
        font-size: 0.875rem;
        color: #64748b;
    }

    .stat-icon {
        font-size: 1rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.25rem;
    }

    .stat-change {
        font-size: 0.75rem;
        color: #64748b;
    }

    /* Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    /* Progress Bar */
    .progress-item {
        margin-bottom: 1rem;
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .progress-bar {
        width: 100%;
        height: 0.5rem;
        background-color: #e5e7eb;
        border-radius: 0.25rem;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background-color: #10b981;
        transition: width 0.3s ease;
    }

    .progress-fill.yellow {
        background-color: #f59e0b;
    }

    .progress-fill.red {
        background-color: #ef4444;
    }

    .progress-fill.gray {
        background-color: #6b7280;
    }

    /* Alerts */
    .alert-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }

    .alert-left {
        display: flex;
        align-items: center;
    }

    .alert-icon {
        margin-right: 0.75rem;
        font-size: 1rem;
    }

    .alert-icon.high {
        color: #f59e0b;
    }

    .alert-icon.critical {
        color: #ef4444;
    }

    .alert-icon.medium {
        color: #eab308;
    }

    .alert-info-h h4 {
        font-weight: 500;
        font-size: 0.875rem;
    }

    .alert-info-h p {
        font-size: 0.75rem;
        color: #64748b;
    }

    .alert-right {
        text-align: right;
    }

    .badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .badge.critical {
        background-color: #fef2f2;
        color: #dc2626;
    }

    .badge.high {
        background-color: #fef3c7;
        color: #d97706;
    }

    .badge.medium {
        background-color: #fefce8;
        color: #ca8a04;
    }

    /* Table */
    .table-container {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 0.75rem;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
    }

    th {
        font-weight: 600;
        color: #374151;
        background-color: #f9fafb;
    }

    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-healthy {
        background-color: #dcfce7;
        color: #166534;
    }

    .status-sick-light {
        background-color: #fefce8;
        color: #854d0e;
    }

    .status-sick-heavy {
        background-color: #fed7aa;
        color: #9a3412;
    }

    .status-fallen {
        background-color: #fecaca;
        color: #991b1b;
    }

    /* Buttons */
    .btn {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 0.375rem;
        cursor: pointer;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .btn-primary {
        background-color: #3b82f6;
        color: white;
    }

    .btn-primary:hover {
        background-color: #2563eb;
    }

    .btn-outline {
        background-color: transparent;
        border: 1px solid #d1d5db;
        color: #374151;
    }

    .btn-outline:hover {
        background-color: #f9fafb;
    }

    /* Map */
    .map-container {
        position: relative;
        background-color: #f0fdf4;
        border: 2px dashed #bbf7d0;
        border-radius: 0.5rem;
        height: 400px;
        padding: 1rem;
    }

    .zone {
        position: absolute;
        width: 120px;
        height: 80px;
        border-radius: 0.5rem;
        border: 2px solid;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 0.5rem;
    }

    .zone:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .zone.healthy {
        background-color: #dcfce7;
        border-color: #22c55e;
    }

    .zone.warning {
        background-color: #fefce8;
        border-color: #eab308;
    }

    .zone.danger {
        background-color: #fecaca;
        border-color: #ef4444;
    }

    .zone h3 {
        font-weight: bold;
        margin-bottom: 0.25rem;
    }

    .zone-indicators {
        display: flex;
        gap: 0.25rem;
        margin-top: 0.25rem;
    }

    .indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .indicator.green {
        background-color: #22c55e;
    }

    .indicator.yellow {
        background-color: #eab308;
    }

    .indicator.red {
        background-color: #ef4444;
    }

    /* Legend */
    .legend {
        position: absolute;
        bottom: 1rem;
        left: 1rem;
        background: white;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .legend h4 {
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.25rem;
        font-size: 0.75rem;
    }

    .legend-color {
        width: 1rem;
        height: 1rem;
        border-radius: 0.25rem;
        margin-right: 0.5rem;
        border: 1px solid;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container {
            flex-direction: column;
        }

        .sidebar {
            width: 100%;
            height: auto;
        }

        .content-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Hidden class for view switching */
    .hidden {
        display: none;
    }

    @media(max-width: 500px) {
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .mini-filter {
            display: block !important;
        }

        .form-control {
            margin-top: 4px;
        }

        #harvestApply {
            width: 100%;
            margin-top: 3px;
        }

        #purchaseApply {
            width: 100%;
            margin-top: 3px;
        }

    }
</style>
@endsection