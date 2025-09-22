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
                        <h2>Tổng quan vườn cây</h2>
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
                        <h2>Tổng Quan Sản Lượng</h2>
                        <p>Theo dõi quá trình thu mua và khai thác</p>
                    </div>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-header">
                                <i class="fa-regular fa-warehouse-full" style="color: #059669;padding-right: 5px;"></i>
                                <span class="stat-title">Tổng Sản Lượng Mủ Thu Mua</span>
                            </div>
                            <div class="card-body">
                                <canvas id="rubberDonut">
                                </canvas>
                            </div>
                        </div>
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                            const ctx = document.getElementById('rubberDonut').getContext('2d');
                            const labels = @json($byType->pluck('product_name'));
                            const data   = (@json($byType->pluck('total_qty'))).map(v => Number(v) || 0);
                            const totalRubber = data.reduce((a, b) => a + b, 0);
                            // Plugin: 2 dòng text ở giữa
                            const centerText = {
                                id: 'centerText',
                                afterDraw(chart) {
                                const {ctx, chartArea: {left, top, width, height}} = chart;
                                const cx = left + width / 2;
                                const cy = top + height / 2;

                                ctx.save();
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'middle';
                                // Dòng 1: tiêu đề
                                ctx.fillStyle = '#6b7280';
                                ctx.font = `${Math.min(width, height) / 5}px sans-serif`;
                                // ctx.fillText('Tổng', cx, cy - 5);
                                // Dòng 2: giá trị
                                ctx.fillStyle = '#111827';
                                ctx.font = `${Math.min(width, height) / 9}px sans-serif`;
                                const formatted = new Intl.NumberFormat('vi-VN').format(totalRubber) + ' Kg';
                                ctx.fillText(formatted, cx, cy + 9);
                                ctx.restore();
                                }
                            };
                            new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                labels,
                                datasets: [{
                                    data,
                                    backgroundColor: ['#059669','#22c55e','#eab308','#ef4444','#3b82f6','#8b5cf6','#ec4899','#14b8a6'],
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
                                        label: function (context) {
                                        const value = Number(context.raw) || 0;
                                        const percent = totalRubber > 0 ? (value / totalRubber * 100).toFixed(1) : 0;
                                        return `${context.label}: ${new Intl.NumberFormat('vi-VN').format(value)} (${percent}%)`;
                                        }
                                    }
                                    }
                                }
                                },
                                plugins: [centerText]
                            });
                            });
                        </script>

                        <div class="stat-card">
                            <div class="stat-header">
                                <i class="fas fa-basket-shopping stat-icon"
                                    style="color:#059669;padding-right:5px;"></i>
                                <span class="stat-title">Tổng Sản Lượng Khai Thác</span>
                            </div>
                            <div class="card-body">
                                <canvas id="harvestDonut"></canvas>
                            </div>
                        </div>
                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                const ctx = document.getElementById('harvestDonut').getContext('2d');
                                const labels = @json($harvestByProduct->pluck('product_name'));
                                const data   = (@json($harvestByProduct->pluck('total_qty'))).map(v => Number(v) || 0);
                                const totalHarvest = data.reduce((a, b) => a + b, 0);
                                const centerText = {
                                    id: 'centerText',
                                    afterDraw(chart) {
                                        const {ctx, chartArea: {left, top, width, height}} = chart;
                                        const cx = left + width / 2;
                                        const cy = top + height / 2;
                                        ctx.save();
                                        ctx.textAlign = 'center';
                                        ctx.textBaseline = 'middle';
                                        // dòng 1
                                        ctx.fillStyle = '#6b7280';
                                        ctx.font = `${Math.min(width, height) / 5}px sans-serif`;
                                        // ctx.fillText('Tổng', cx, cy - 5);
                                        // dòng 2
                                        ctx.fillStyle = '#111827';
                                        ctx.font = `${Math.min(width, height) / 9}px sans-serif`;
                                        const formatted = new Intl.NumberFormat('vi-VN').format(totalHarvest) + ' Kg';
                                        ctx.fillText(formatted, cx, cy + 9);
                                        ctx.restore();
                                    }
                                };
                                new Chart(ctx, {
                                    type: 'doughnut',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            data: data,
                                            backgroundColor: [
                                                '#059669','#22c55e','#eab308','#ef4444',
                                                '#3b82f6','#8b5cf6','#ec4899','#14b8a6'
                                            ],
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
                                                    label: function (context) {
                                                        const value = Number(context.raw) || 0;
                                                        const percent = totalHarvest > 0 ? (value / totalHarvest * 100).toFixed(1) : 0;
                                                        return `${context.label}: ${new Intl.NumberFormat('vi-VN').format(value)} (${percent}%)`;
                                                    }
                                                }
                                            }
                                        }
                                    },
                                    plugins: [centerText]
                                });
                            });
                        </script>
                    </div>
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

{{-- <script>
    document.addEventListener("DOMContentLoaded", function() {
            fetch("/farm-vehicle-statistics")
                .then(response => response.json())
                .then(data => {
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
                            paddingTop: 0 
                        }
                    };
                   
                    // === Biểu đồ Lô Hàng ===
                    const validBatches = data.batches.filter(item => item.month !== null);

                    const batchMonths = validBatches.map(item => item.month).sort((a, b) => a - b);

                    const totalBatches = batchMonths.map(month => {
                        const item = data.batches.find(b => b.month == month);
                        return item ? item.total_batches : 0;
                    });

                    const completedBatches = batchMonths.map(month => {
                        const item = data.batches.find(b => b.month == month);
                        return item ? item.completed_batches : 0;
                    });

                    const pendingBatches = batchMonths.map(month => {
                        const item = data.batches.find(b => b.month == month);
                        return item ? item.pending_batches : 0;
                    });
                    const isSingleBar = batchMonths.length === 1;

                    const ctxBatch = document.getElementById("batchChart");
                    if (ctxBatch) {
                        new Chart(ctxBatch, {
                            type: "bar",
                            data: {
                                labels: batchMonths.map(m => `Tháng ${m}`),
                                datasets: [{
                                        label: "Đã kiểm nghiệm",
                                        data: completedBatches,
                                        backgroundColor: "rgba(75, 192, 75, 0.6)",
                                        borderColor: "rgba(75, 192, 75, 1)",
                                        borderWidth: 1,
                                        barPercentage: isSingleBar ? 0.4 : 0.7,
                                        categoryPercentage: isSingleBar ? 0.5 : 0.8,
                                    },
                                    {
                                        label: "Chưa kiểm nghiệm",
                                        data: pendingBatches,
                                        backgroundColor: "rgba(178, 191, 199, 100)",
                                        borderColor: "rgba(178, 191, 199, 1)",
                                        borderWidth: 1,
                                        barPercentage: isSingleBar ? 0.4 : 0.7,
                                        categoryPercentage: isSingleBar ? 0.5 : 0.8,
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    x: {

                                        stacked: true, // ✅ gộp cột theo chiều ngang
                                        ticks: {
                                            font: {
                                                size: 16
                                            }
                                        }
                                    },
                                    y: {

                                        stacked: true, // ✅ gộp cột theo chiều dọc
                                        beginAtZero: true
                                    }
                                },
                                plugins: {
                                    legendMargin: { // <-- Set option of custom plugin
                                        paddingTop: 20 // <---- override the default value
                                    },
                                    legend: {
                                        onClick: (event) => {
                                            event.preventDefault();
                                        },
                                    },
                                    datalabels: {
                                        color: 'white',
                                        anchor: 'center',
                                        align: 'center',
                                        font: (context) => {
                                            return {
                                                size: window.innerWidth <= 768 ? 10 : 14
                                            };
                                        },
                                        offset: -2,
                                        formatter: (value) => {
                                            return Number(value) === 0 ? null : value;
                                        }
                                    }
                                },
                            },
                            // plugins: [generateTextLabelPlugin(14, '#fff')]
                            plugins: [legendMargin, ChartDataLabels]
                        });
                    }


                    // Hợp đồng - khách hàng
                    const contractCount = data.countContractWithCustomers.contracts;

                    const customerCount = data.countContractWithCustomers.customers;
                    const contractCustomer = document.getElementById("barChartContractCustomer");
                    if (contractCustomer) {
                        new Chart(contractCustomer, {
                            type: "bar",
                            data: {
                                labels: ["", ""], // 🔹 Gán labels rỗng để không hiển thị trên trục X
                                datasets: [{
                                        label: "Hợp Đồng",
                                        data: [contractCount, 0], // Chỉ có dữ liệu cho Hợp Đồng
                                        backgroundColor: "rgba(75, 192, 75, 0.6)",
                                        borderColor: "rgba(75, 192, 75, 1)",
                                        // borderWidth: 1,
                                        barPercentage: 5, // Điều chỉnh độ rộng cột (giá trị từ 0 đến 1)
                                        categoryPercentage: 0.1 // Điều chỉnh khoảng cách giữa các cột
                                    },
                                    {
                                        label: "Khách Hàng",
                                        data: [0, customerCount], // Chỉ có dữ liệu cho Khách Hàng
                                        backgroundColor: "rgba(255, 99, 132, 0.6)", // Đỏ hồng
                                        borderColor: "rgba(255, 99, 132, 1)",
                                        // borderWidth: 1,
                                        barPercentage: 5, // Điều chỉnh độ rộng cột (giá trị từ 0 đến 1)
                                        categoryPercentage: 0.1 // Điều chỉnh khoảng cách giữa các cột
                                    },
                                ],
                            },
                            options: {
                                responsive: true,
                                scales: {

                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            autoSkip: true, // Tự động bỏ bớt nếu có quá nhiều giá trị
                                        },
                                    }
                                },
                                plugins: {
                                    legendMargin: { // <-- Set option of custom plugin
                                        paddingTop: 20 // <---- override the default value
                                    },
                                    legend: {
                                        onClick: (event) => {
                                            event.preventDefault(); // Chặn event click
                                        },
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

                                    }
                                },
                            },
                            // plugins: [generateTextLabelPlugin(14, '#fff'), ]
                            plugins: [legendMargin, ChartDataLabels]
                        });
                    }

                    // === Biểu đồ số chuyến theo loại mủ từ từng nông trường ===
                    const results = data.countTripByTypeOfPusFromPlantation.results;

                    // Gom dữ liệu: { [farm_name]: { [name_pus]: total_trip } }
                    const groupedData = {};
                    results.forEach(item => {
                        const farm = item.farm_code;
                        const pus = item.name_pus;
                        const total = item.total_pus;

                        if (!groupedData[farm]) {
                            groupedData[farm] = {};
                        }
                        groupedData[farm][pus] = total;
                    });
                    // Lấy danh sách nông trường
                    const farmNames = Object.keys(groupedData);
                    // Hàm tạo màu ngẫu nhiên dạng rgba
                    function getColorMap(pusNames, opacity = 0.7) {
                        const baseColors = [
                            `rgba(75, 192, 75, ${opacity})`,
                            `rgba(255, 159, 64, ${opacity})`,
                            `rgba(255, 99, 132, ${opacity})`,
                            `rgba(54, 162, 235, ${opacity})`,
                            `rgba(255, 205, 86, ${opacity})`,
                            `rgba(153, 102, 255, ${opacity})`,
                            `rgba(100, 200, 100, ${opacity})`,
                            `rgba(201, 203, 207, ${opacity})`,
                        ];

                        const map = {};
                        let colorIndex = 0;
                        pusNames.forEach(pus => {
                            map[pus] = baseColors[colorIndex] || getRandomColor(opacity);
                            colorIndex++;
                        });

                        return map;
                    }


                    function getRandomColor(opacity = 0.7) {
                        const min = 60;
                        const max = 200;
                        const r = Math.floor(Math.random() * (max - min + 1)) + min;
                        const g = Math.floor(Math.random() * (max - min + 1)) + min;
                        const b = Math.floor(Math.random() * (max - min + 1)) + min;
                        return `rgba(${r}, ${g}, ${b}, ${opacity})`;
                    }
                    // Lấy danh sách tất cả loại mủ (để đảm bảo đúng thứ tự cột)
                    const pusNames = [...new Set(results.map(item => item.name_pus))];
                    const colorMap = getColorMap(pusNames);
                    // Xóa cache giữ cố định 1 màu cho các loại mủ
                    localStorage.removeItem('pusColorMap');

                    // Tạo datasets cho từng loại mủ
                    const datasets = pusNames.map(pus => {
                        return {
                            label: pus,
                            data: farmNames.map(farm => groupedData[farm][pus] || 0),
                            backgroundColor: colorMap[pus] || 'rgba(201, 203, 207, 0.7)',

                        };
                    });

                    let chartInstance = null;


                    function createChart() {
                        const ctxTrips = document.getElementById("tripTypeOfPusPlantationChart");

                        if (ctxTrips) {
                            const isMobile = window.innerWidth <= 768; // Kiểm tra kích thước màn hình
                            const isSingleFarm = farmNames.length === 1;

                            if (isMobile) {
                                ctxTrips.height = isSingleFarm ? 150 : farmNames.length * 80;
                            } else {
                                ctxTrips.height = isSingleFarm ? 200 : 200;
                            }

                            const indexAxis = isMobile ? 'y' : 'x';

                            const scales = {
                                x: {
                                    stacked: true,
                                    ...(indexAxis === 'x' && {
                                        title: {
                                            display: true,
                                            text: 'Nông Trường - Đơn vị',
                                            font: {
                                                weight: 'bold',
                                                size: isMobile ? 10 : 14
                                            }
                                        }
                                    })
                                },
                                y: {
                                    stacked: true,
                                    beginAtZero: true,
                                    ...(indexAxis === 'y' && {
                                        title: {
                                            display: true,
                                            text: 'Nông Trường - Đơn vị',
                                            font: {
                                                weight: 'bold',
                                                size: isMobile ? 10 : 14
                                            }
                                        }
                                    })
                                }
                            };
                            if (chartInstance) {
                                chartInstance.destroy();
                            }
                            chartInstance = new Chart(ctxTrips, {
                                type: "bar",
                                data: {
                                    labels: farmNames,
                                    datasets: datasets,
                                },
                                options: {
                                    responsive: true,
                                    scales: scales,
                                    indexAxis: indexAxis,
                                    elements: {
                                        bar: {
                                            maxBarThickness: isSingleFarm ? 30 : 60
                                        }
                                    },
                                    plugins: {
                                        legendMargin: { // <-- Set option of custom plugin
                                            paddingTop: 20 // <---- override the default value
                                        },
                                        legend: {
                                            onClick: (event) => {
                                                event.preventDefault();
                                            },
                                        },
                                        datalabels: {
                                            color: 'white',
                                            anchor: 'center',
                                            align: 'center',
                                            font: (context) => {
                                                return {
                                                    size: window.innerWidth <= 768 ? 10 : 12
                                                };
                                            },

                                            offset: -2,
                                            formatter: (value) => {
                                                return Number(value) === 0 ? null : value;
                                            }
                                        },
                                        tooltip: {
                                            mode: 'index',
                                            // intersect: false,

                                        },
                                    },
                                    categoryPercentage: isSingleFarm ? 0.4 : 0.7,
                                    barPercentage: isSingleFarm ? 0.5 : 0.8,
                                },
                                // plugins: [generateTextLabelPlugin(14, '#fff')]
                                plugins: [legendMargin, ChartDataLabels]
                            });
                            // Kiểm tra nếu biểu đồ đã tồn tại, hủy bỏ nó trước khi tạo mới

                        }
                    }

                    const chartContainer = document.getElementById("tripTypeOfPusPlantationChart");

                    // Hàm debounce
                    function debounce(func, wait = 200) {
                        let timeout;
                        return function(...args) {
                            clearTimeout(timeout);
                            timeout = setTimeout(() => func.apply(this, args), wait);
                        };
                    }

                    // Tạo biểu đồ ban đầu
                    createChart();

                    // Debounce resize window, tránh gọi liên tục
                    const debouncedCreateChart = debounce(createChart, 300);
                    window.addEventListener('resize', debouncedCreateChart);

                    // === Biểu đồ danh sách lô hàng ===
                    const totalBatche = data.countBatchesCreateConnect.totalBatches;
                    const linkedBatches = data.countBatchesCreateConnect.linkedBatches;
                    const createConnectBatches = document.getElementById("listbatchChart");
                    if (createConnectBatches) {
                        new Chart(createConnectBatches, {
                            type: "bar",
                            data: {
                                labels: ["", ""], // 🔹 Gán labels rỗng để không hiển thị trên trục X
                                datasets: [{
                                        label: "Đã tạo",
                                        data: [totalBatche, 0], // Chỉ có dữ liệu cho Hợp Đồng
                                        backgroundColor: "rgba(75, 192, 75, 0.6)",
                                        borderColor: "rgba(75, 192, 75, 1)",
                                        // borderWidth: 1,
                                        barPercentage: 5, // Điều chỉnh độ rộng cột (giá trị từ 0 đến 1)
                                        categoryPercentage: 0.1 // Điều chỉnh khoảng cách giữa các cột
                                    },
                                    {
                                        label: "Đã liên kết",
                                        data: [0,
                                            linkedBatches
                                        ], // Chỉ có dữ liệu cho Khách Hàng
                                        backgroundColor: "rgba(255, 99, 132, 0.6)", // Đỏ hồng
                                        borderColor: "rgba(255, 99, 132, 1)",
                                        // borderWidth: 1,
                                        barPercentage: 5, // Điều chỉnh độ rộng cột (giá trị từ 0 đến 1)
                                        categoryPercentage: 0.1 // Điều chỉnh khoảng cách giữa các cột
                                    },
                                ],
                            },
                            options: {
                                responsive: true,
                                scales: {

                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            autoSkip: true, // Tự động bỏ bớt nếu có quá nhiều giá trị
                                        },
                                    }
                                },
                                plugins: {
                                    legendMargin: { // <-- Set option of custom plugin
                                        paddingTop: 20 // <---- override the default value
                                    },
                                    legend: {
                                        onClick: (event) => {
                                            event.preventDefault(); // Chặn event click
                                        },
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

                                    }
                                },
                            },
                            // plugins: [generateTextLabelPlugin(14, '#fff'), ]
                            plugins: [legendMargin, ChartDataLabels]
                        });
                    }

                });

        });
</script> --}}
{{-- @hasanyrole('Admin|Nông Trường|Xem Nông Trường')
<div class="card">
    <div class="card-header">
        <div class="card-body">
            <h4 style="text-align: center">Biểu đồ Xe và Nông Trường</h4>
            <canvas id="farmVehicleChart"></canvas>
        </div>
    </div>
</div>
@endhasanyrole --}}
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
        display: flex;
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
</style>
@endsection