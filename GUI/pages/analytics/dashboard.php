<?php
// Tệp: GUI/pages/analytics/dashboard.php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../autoload.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tổng quan - CashFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <!-- Nạp thư viện Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <?php require_once __DIR__ . '/../../components/header.php'; ?>

    <main class="container py-4">
        
        <?php require_once __DIR__ . '/../../components/alert.php'; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark mb-0">Bảng điều khiển</h3>
            <!-- Bộ chọn tháng YYYY-MM gửi thẳng xuống API -->
            <input type="month" id="monthPicker" class="form-control fw-bold border-0 bg-white shadow-sm text-success" style="width: 160px; cursor: pointer;" onchange="loadDashboardData()">
        </div>

        <!-- THẺ TÓM TẮT (SUMMARY CARDS) -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0 rounded-4 h-100 bg-primary bg-opacity-10">
                    <div class="card-body">
                        <div class="text-muted fw-semibold small mb-1">SỐ DƯ THÁNG NÀY</div>
                        <h4 class="fw-bold mb-0" id="summaryBalance">₫ 0</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card shadow-sm border-0 rounded-4 h-100 bg-success bg-opacity-10">
                    <div class="card-body">
                        <div class="text-success fw-semibold small mb-1">TỔNG THU</div>
                        <h5 class="fw-bold text-success mb-0" id="summaryIncome">₫ 0</h5>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card shadow-sm border-0 rounded-4 h-100 bg-danger bg-opacity-10">
                    <div class="card-body">
                        <div class="text-danger fw-semibold small mb-1">TỔNG CHI</div>
                        <h5 class="fw-bold text-danger mb-0" id="summaryExpense">₫ 0</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- KHU VỰC BIỂU ĐỒ -->
        <div class="row g-4">
            <!-- Biểu đồ cơ cấu chi tiêu (Pie Chart) -->
            <div class="col-12 col-lg-5">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4">Cơ cấu chi tiêu tháng</h6>
                        <div style="height: 250px; position: relative;">
                            <canvas id="pieChart"></canvas>
                            <div id="pieEmpty" class="position-absolute top-50 start-50 translate-middle text-muted d-none text-center">
                                <i class="bi bi-pie-chart fs-1"></i><br>Chưa có dữ liệu
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Biểu đồ thu chi 12 tháng (Bar Chart) -->
            <div class="col-12 col-lg-7">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4">Thu/Chi 12 tháng năm <span id="chartYear"></span></h6>
                        <div style="height: 250px;">
                            <canvas id="barChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../../components/footer.php'; ?>
    <?php require_once __DIR__ . '/../../components/bottom-nav.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let pieChartInstance = null;
        let barChartInstance = null;

        async function loadDashboardData() {
            try {
                const monthVal = document.getElementById('monthPicker').value; 
                document.getElementById('chartYear').innerText = monthVal.split('-')[0];

                const res = await fetch(`../../controllers/AnalyticsController.php?action=get_dashboard&month=${monthVal}`);
                
                // Nếu PHP báo lỗi 500, fetch sẽ ném lỗi tại đây
                if (!res.ok) throw new Error("Lỗi máy chủ " + res.status);
                
                const response = await res.json();
                
                if (response.status) {
                    const data = response.data;
                    const stats = data.stats;
                    
                    let balColor = 'text-muted'; let balSign = '';
                    if (stats.balance_status === 'positive') { balColor = 'text-primary'; balSign = '+'; }
                    else if (stats.balance_status === 'negative') { balColor = 'text-danger'; }

                    document.getElementById('summaryBalance').innerHTML = `<span class="${balColor}">${balSign}${stats.formatted.net_balance} đ</span>`;
                    document.getElementById('summaryIncome').innerText = `+${stats.formatted.total_income} đ`;
                    document.getElementById('summaryExpense').innerText = `-${stats.formatted.total_expense} đ`;

                    renderPieChart(data.pie_chart);
                    renderBarChart(data.bar_chart);
                } else {
                    console.error("Lỗi từ API:", response.message);
                }
            } catch (error) {
                console.error("Lỗi nghiêm trọng khi tải Dashboard:", error);
                alert("Không thể tải dữ liệu thống kê. Vui lòng nhấn F12 để xem chi tiết lỗi!");
            }
        }

        function renderPieChart(pieData) {
            const canvas = document.getElementById('pieChart');
            const emptyState = document.getElementById('pieEmpty');
            
            if (pieChartInstance) pieChartInstance.destroy();

            // Xử lý Empty State nếu không có data
            if (!pieData.data || pieData.data.length === 0) {
                canvas.style.display = 'none';
                emptyState.classList.remove('d-none');
                return;
            }

            canvas.style.display = 'block';
            emptyState.classList.add('d-none');

            pieChartInstance = new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: pieData.labels,
                    datasets: [{
                        data: pieData.data,
                        backgroundColor: pieData.colors,
                        borderWidth: 2,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { position: 'right' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    // Hiển thị % thay vì số tiền thô
                                    let percent = pieData.percentages[context.dataIndex];
                                    return ` ${context.label}: ${pieData.formatted[context.dataIndex]} (${percent}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function renderBarChart(barData) {
            const canvas = document.getElementById('barChart');
            if (barChartInstance) barChartInstance.destroy();

            barChartInstance = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: barData.labels,
                    datasets: [
                        { label: 'Thu', data: barData.income, backgroundColor: '#198754', borderRadius: 4 },
                        { label: 'Chi', data: barData.expense, backgroundColor: '#dc3545', borderRadius: 4 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            ticks: { callback: function(value) { return value.toLocaleString('vi-VN'); } } 
                        }
                    }
                }
            });
        }

        // Khởi tạo tháng hiện tại và tải dữ liệu ngay khi mở trang
        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date();
            const yyyy = today.getFullYear();
            let mm = today.getMonth() + 1;
            if (mm < 10) mm = '0' + mm;
            
            document.getElementById('monthPicker').value = `${yyyy}-${mm}`;
            loadDashboardData();
        });
    </script>
</body>
</html>