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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

    <?php require_once __DIR__ . '/../../components/header.php'; ?>

    <main class="container py-4 mb-5">
        <?php require_once __DIR__ . '/../../components/alert.php'; ?>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
            <h3 class="fw-bold text-dark mb-0">Tổng quan</h3>
            <div class="d-flex align-items-center bg-white rounded-pill shadow-sm p-1 border">
                <button class="btn btn-light rounded-circle btn-sm d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" onclick="changeMonth(-1)"><i class="bi bi-chevron-left fw-bold"></i></button>
                <div class="mx-3 text-center" style="min-width: 120px;"><span id="currentMonthDisplay" class="fw-bold text-primary fs-6">Tháng 05, 2026</span></div>
                <button class="btn btn-light rounded-circle btn-sm d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" onclick="changeMonth(1)"><i class="bi bi-chevron-right fw-bold"></i></button>
            </div>
            <input type="month" id="monthPicker" class="d-none" onchange="loadDashboardData()">
        </div>

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

        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 rounded-4 h-100 border-start border-4 border-danger">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-danger mb-3"><i class="bi bi-exclamation-triangle-fill me-2"></i>Hũ báo động</h6>
                        <div id="dangerBudgets"><div class="text-muted small fst-italic">Đang kiểm tra...</div></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 rounded-4 h-100 border-start border-4 border-success">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-success mb-3"><i class="bi bi-shield-check me-2"></i>Hũ an toàn</h6>
                        <div id="safeBudgets"><div class="text-muted small fst-italic">Đang kiểm tra...</div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-5">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4">Cơ cấu chi tiêu</h6>
                        <div style="height: 250px; position: relative;">
                            <canvas id="pieChart"></canvas>
                            <div id="pieEmpty" class="position-absolute top-50 start-50 translate-middle text-muted d-none text-center">
                                <i class="bi bi-pie-chart fs-1"></i><br>Chưa có dữ liệu
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-7">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4">Thu/Chi năm <span id="chartYear"></span></h6>
                        <div style="height: 250px;"><canvas id="barChart"></canvas></div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="../../assets/js/app.js?v=<?= time() ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let pieChartInstance = null; let barChartInstance = null;
        function formatMoney(num) { return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); }

        function changeMonth(offset) {
            const picker = document.getElementById('monthPicker');
            let [year, month] = picker.value.split('-').map(Number);
            month += offset;
            if (month < 1) { month = 12; year--; } else if (month > 12) { month = 1; year++; }
            picker.value = `${year}-${String(month).padStart(2, '0')}`;
            document.getElementById('currentMonthDisplay').innerText = `Tháng ${String(month).padStart(2, '0')}, ${year}`;
            loadDashboardData();
        }

        async function loadDashboardData() {
            try {
                const monthVal = document.getElementById('monthPicker').value; 
                const [year, month] = monthVal.split('-');
                document.getElementById('chartYear').innerText = year;

                const res = await fetch(`../../controllers/AnalyticsController.php?action=get_dashboard&month=${monthVal}`);
                const response = await res.json();
                
                if (response.status) {
                    const data = response.data;
                    const stats = data.stats;
                    
                    let balColor = 'text-dark'; let balSign = '';
                    if (stats.balance_status === 'positive') { balColor = 'text-primary'; balSign = '+'; }
                    else if (stats.balance_status === 'negative') { balColor = 'text-danger'; }

                    document.getElementById('summaryBalance').innerHTML = `<span class="${balColor}">${balSign}${stats.formatted.net_balance} đ</span>`;
                    document.getElementById('summaryIncome').innerText = `+${stats.formatted.total_income} đ`;
                    document.getElementById('summaryExpense').innerText = `-${stats.formatted.total_expense} đ`;

                    renderPieChart(data.pie_chart);
                    renderBarChart(data.bar_chart);
                    renderBudgetHealth(data.budget_health || []);
                }
            } catch (error) { console.error(error); }
        }

        function renderBudgetHealth(budgets) {
            let dangerHtml = ''; let safeHtml = '';
            if (!budgets || budgets.length === 0) {
                dangerHtml = safeHtml = '<div class="text-muted small fst-italic">Chưa lập ngân sách cho tháng này.</div>';
            } else {
                budgets.forEach(b => {
                    let pct = b.progress_percentage;
                    let remain = b.amount_limit - b.total_spent;
                    let remainText = remain >= 0 ? `Còn ${formatMoney(remain)}đ` : `Vượt ${formatMoney(Math.abs(remain))}đ`;
                    let itemHtml = `
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small text-dark">${b.category_name}</span>
                                <span class="small fw-semibold ${pct >= 100 ? 'text-danger' : 'text-muted'}">${pct}% (${remainText})</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar ${pct >= 80 ? 'bg-danger' : 'bg-success'}" style="width: ${pct > 100 ? 100 : pct}%"></div>
                            </div>
                        </div>`;
                    if (pct >= 80) dangerHtml += itemHtml; else if (pct < 50) safeHtml += itemHtml;
                });
            }
            document.getElementById('dangerBudgets').innerHTML = dangerHtml || '<div class="text-muted small fst-italic"><i class="bi bi-emoji-smile me-1"></i>Không có hũ nào báo động!</div>';
            document.getElementById('safeBudgets').innerHTML = safeHtml || '<div class="text-muted small fst-italic">Các hũ đang mức trung bình.</div>';
        }

        function renderPieChart(pieData) {
            const canvas = document.getElementById('pieChart'); const emptyState = document.getElementById('pieEmpty');
            if (pieChartInstance) pieChartInstance.destroy();
            if (!pieData.data || pieData.data.length === 0) { canvas.style.display = 'none'; emptyState.classList.remove('d-none'); return; }
            canvas.style.display = 'block'; emptyState.classList.add('d-none');
            pieChartInstance = new Chart(canvas, { type: 'doughnut', data: { labels: pieData.labels, datasets: [{ data: pieData.data, backgroundColor: ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#F06292', '#AED581'] }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } } });
        }

        function renderBarChart(barData) {
            const canvas = document.getElementById('barChart');
            if (barChartInstance) barChartInstance.destroy();
            barChartInstance = new Chart(canvas, { type: 'bar', data: { labels: barData.labels, datasets: [{ label: 'Thu', data: barData.income, backgroundColor: '#198754' }, { label: 'Chi', data: barData.expense, backgroundColor: '#dc3545' }] }, options: { responsive: true, maintainAspectRatio: false } });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date(); const yyyy = today.getFullYear(); let mm = today.getMonth() + 1;
            document.getElementById('monthPicker').value = `${yyyy}-${mm < 10 ? '0'+mm : mm}`;
            document.getElementById('currentMonthDisplay').innerText = `Tháng ${mm < 10 ? '0'+mm : mm}, ${yyyy}`;
            loadDashboardData();
        });
    </script>
</body>
</html>