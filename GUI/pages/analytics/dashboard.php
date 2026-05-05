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
    <style>
        /* CSS cho Lịch Giao Dịch */
        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; margin-top: 15px;}
        .calendar-header { text-align: center; font-weight: bold; font-size: 0.85rem; padding: 5px; color: #6c757d; }
        .calendar-cell { border: 1px solid #f0f2f5; border-radius: 8px; min-height: 80px; padding: 5px; background: #fff; transition: transform 0.2s; cursor: pointer;}
        .calendar-cell:hover { border-color: #0d6efd; box-shadow: 0 4px 10px rgba(0,0,0,0.05); z-index: 1; transform: scale(1.05);}
        .calendar-cell.empty { background: transparent; border: none; cursor: default; box-shadow: none;}
        .calendar-cell.empty:hover { transform: none; }
        .date-num { font-weight: 800; color: #343a40; font-size: 0.9rem; margin-bottom: 2px; }
        .date-today { background-color: #0d6efd; color: white; border-radius: 50%; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; }
        .cal-money { font-size: 0.75rem; font-weight: 600; line-height: 1.2; text-align: right; margin-top: 2px;}
        .anomaly-icon { position: absolute; top: 4px; right: 4px; font-size: 0.8rem; color: #dc3545; animation: pulse 1.5s infinite; }
        
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.2); } 100% { transform: scale(1); } }
        
        /* Chỉnh lại trên mobile */
        @media (max-width: 768px) {
            .calendar-grid { gap: 4px; }
            .calendar-cell { min-height: 60px; padding: 2px; }
            .cal-money { font-size: 0.65rem; }
        }
    </style>
</head>
<body class="bg-light">

    <?php require_once __DIR__ . '/../../components/header.php'; ?>

    <main class="container py-4 mb-5">
        <?php require_once __DIR__ . '/../../components/alert.php'; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark mb-0">Tổng quan</h3>
            <input type="month" id="monthPicker" class="form-control fw-bold border-0 bg-white shadow-sm text-primary" style="width: 160px; cursor: pointer;" onchange="loadDashboardData()">
        </div>

        <!-- THẺ TÓM TẮT -->
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

        <!-- TÍNH NĂNG MỚI 1: LỊCH GIAO DỊCH -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold mb-0"><i class="bi bi-calendar3 me-2 text-primary"></i>Lịch Giao Dịch</h5>
                    <small class="text-muted"><i class="bi bi-lightning-fill text-danger"></i> Chi tiêu cao bất thường</small>
                </div>
                <div id="calendarContainer">
                    <div class="text-center py-4 text-muted"><div class="spinner-border text-primary spinner-border-sm me-2"></div>Đang vẽ lịch...</div>
                </div>
            </div>
        </div>

        <!-- TÍNH NĂNG MỚI 2: SỨC KHỎE HŨ NGÂN SÁCH -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 rounded-4 h-100 border-start border-4 border-danger">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-danger mb-3"><i class="bi bi-exclamation-triangle-fill me-2"></i>Hũ báo động (Sắp cạn)</h6>
                        <div id="dangerBudgets">
                            <div class="text-muted small fst-italic">Đang kiểm tra...</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 rounded-4 h-100 border-start border-4 border-success">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-success mb-3"><i class="bi bi-shield-check me-2"></i>Hũ an toàn (Còn dư dả)</h6>
                        <div id="safeBudgets">
                            <div class="text-muted small fst-italic">Đang kiểm tra...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KHU VỰC BIỂU ĐỒ (Giữ nguyên) -->
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

    <?php require_once __DIR__ . '/../../components/bottom-nav.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let pieChartInstance = null;
        let barChartInstance = null;

        // Hàm format số tiền
        function formatMoney(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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

                    // Vẽ Biểu đồ
                    renderPieChart(data.pie_chart);
                    renderBarChart(data.bar_chart);
                    
                    // Vẽ Lịch và Sức khỏe Hũ (Dữ liệu mới)
                    renderCalendar(parseInt(year), parseInt(month), data.calendar_data || {});
                    renderBudgetHealth(data.budget_health || []);
                }
            } catch (error) {
                console.error(error);
            }
        }

        function renderCalendar(year, month, calendarData) {
            const daysInMonth = new Date(year, month, 0).getDate();
            // Lùi ngày để Thứ 2 là ngày đầu tuần (theo chuẩn VN)
            let firstDay = new Date(year, month - 1, 1).getDay(); 
            let startDayIndex = firstDay === 0 ? 6 : firstDay - 1; 

            const today = new Date();
            const isCurrentMonth = today.getFullYear() === year && (today.getMonth() + 1) === month;
            const currentDay = today.getDate();

            let html = '<div class="calendar-grid">';
            const days = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
            days.forEach(d => html += `<div class="calendar-header">${d}</div>`);

            // Ô trống đầu tháng
            for(let i = 0; i < startDayIndex; i++) {
                html += `<div class="calendar-cell empty"></div>`;
            }

            // Vẽ các ngày
            for(let i = 1; i <= daysInMonth; i++) {
                // Key định dạng YYYY-MM-DD
                let dateStr = `${year}-${String(month).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                let data = calendarData[dateStr] || { income: 0, expense: 0, is_anomaly: false };

                let dayClass = (isCurrentMonth && i === currentDay) ? 'date-today' : '';
                let content = `<div class="date-num ${dayClass}">${i}</div>`;
                
                if (data.is_anomaly) content += `<i class="bi bi-lightning-fill anomaly-icon" title="Chi tiêu cao bất thường"></i>`;
                
                // Hiển thị tiền nếu có
                let moneyHtml = '<div class="mt-1">';
                if (data.income > 0) moneyHtml += `<div class="cal-money text-success">+${formatMoney(data.income)}</div>`;
                if (data.expense > 0) moneyHtml += `<div class="cal-money text-danger">-${formatMoney(data.expense)}</div>`;
                moneyHtml += '</div>';

                html += `<div class="calendar-cell position-relative" onclick="alert('Chi tiết ngày ${i}/${month}')">${content}${moneyHtml}</div>`;
            }
            html += '</div>';
            document.getElementById('calendarContainer').innerHTML = html;
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

                    if (pct >= 80) dangerHtml += itemHtml; // Báo động (>= 80%)
                    else if (pct < 50) safeHtml += itemHtml; // An toàn (< 50%)
                });
            }

            document.getElementById('dangerBudgets').innerHTML = dangerHtml || '<div class="text-muted small fst-italic"><i class="bi bi-emoji-smile me-1"></i>Tuyệt vời, không có hũ nào báo động!</div>';
            document.getElementById('safeBudgets').innerHTML = safeHtml || '<div class="text-muted small fst-italic">Các hũ đang được sử dụng mức trung bình.</div>';
        }

        // --- CÁC HÀM VẼ BIỂU ĐỒ (Giữ nguyên logic cũ của bạn) ---
        function renderPieChart(pieData) {
            const canvas = document.getElementById('pieChart');
            const emptyState = document.getElementById('pieEmpty');
            if (pieChartInstance) pieChartInstance.destroy();
            if (!pieData.data || pieData.data.length === 0) { canvas.style.display = 'none'; emptyState.classList.remove('d-none'); return; }
            canvas.style.display = 'block'; emptyState.classList.add('d-none');
            const colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#F06292', '#AED581'];
            pieChartInstance = new Chart(canvas, {
                type: 'doughnut',
                data: { labels: pieData.labels, datasets: [{ data: pieData.data, backgroundColor: colors }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
            });
        }

        function renderBarChart(barData) {
            const canvas = document.getElementById('barChart');
            if (barChartInstance) barChartInstance.destroy();
            barChartInstance = new Chart(canvas, {
                type: 'bar',
                data: { labels: barData.labels, datasets: [{ label: 'Thu', data: barData.income, backgroundColor: '#198754' }, { label: 'Chi', data: barData.expense, backgroundColor: '#dc3545' }] },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date();
            let mm = today.getMonth() + 1;
            document.getElementById('monthPicker').value = `${today.getFullYear()}-${mm < 10 ? '0'+mm : mm}`;
            loadDashboardData();
        });
    </script>
</body>
</html>