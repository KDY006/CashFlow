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
        /* CSS Giao diện chuẩn Bootstrap */
        .cf-tab-btn { border: 2px solid transparent; border-radius: 15px; padding: 12px; background-color: #f8f9fa; transition: all 0.2s; text-align: left; position: relative;}
        .cf-tab-btn.active-expense { border-color: #dc3545; background-color: #f8d7da; } 
        .cf-tab-btn.active-income { border-color: #198754; background-color: #d1e7dd; } 
        .cf-tab-btn h6 { font-size: 0.85rem; color: #6c757d; margin-bottom: 5px; }
        .cf-tab-btn.active-expense h6 { color: #dc3545; }
        .cf-tab-btn.active-income h6 { color: #198754; }
        
        .nav-pills-custom .nav-link { border-radius: 20px; color: #6c757d; font-weight: bold; background: transparent; }
        .nav-pills-custom .nav-link.active { background-color: #e7f1ff; color: #0d6efd; } 
        
        .list-item-category { border-bottom: 1px dashed #dee2e6; padding: 15px 0; }
        .list-item-category:last-child { border-bottom: none; }
        .time-picker-wrapper { background: #fff; border-radius: 20px; padding: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #dee2e6; }
    </style>
</head>
<body class="bg-light">

    <?php require_once __DIR__ . '/../../components/header.php'; ?>

    <main class="container py-4 mb-5">
        
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-house-door me-2 text-primary"></i>Tổng Quan</h3>
        </div>
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
            <div class="time-picker-wrapper d-flex align-items-center">
                <select id="filterType" class="form-select border-0 fw-bold shadow-none bg-transparent text-primary" style="width: 100px; cursor: pointer;" onchange="resetTimeValue()">
                    <option value="week">Tuần</option>
                    <option value="month" selected>Tháng</option>
                    <option value="year">Năm</option>
                </select>
                
                <div class="d-flex align-items-center border-start ps-2 ms-1">
                    <button class="btn btn-sm btn-light rounded-circle" onclick="changeTime(-1)"><i class="bi bi-chevron-left"></i></button>
                    <div id="timeDisplay" class="fw-bold text-center px-3 text-dark" style="min-width: 120px;">Đang tải...</div>
                    <button class="btn btn-sm btn-light rounded-circle" onclick="changeTime(1)"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-4">
                <button class="w-100 cf-tab-btn active-expense shadow-sm h-100" id="btnTabExpense" onclick="switchMainTab('expense')">
                    <h6><i class="bi bi-arrow-up-circle-fill me-1"></i>Chi tiêu</h6>
                    <h5 class="fw-bold mb-1 text-dark" id="txtTotalExpense">0 đ</h5>
                    <div id="txtTotalExpenseWords" class="text-danger fst-italic" style="font-size: 0.7rem; font-weight: normal; line-height: 1.2;">Không đồng</div>
                </button>
            </div>
            <div class="col-4">
                <button class="w-100 cf-tab-btn shadow-sm h-100" id="btnTabIncome" onclick="switchMainTab('income')">
                    <h6><i class="bi bi-arrow-down-circle-fill me-1"></i>Thu nhập</h6>
                    <h5 class="fw-bold mb-1 text-dark" id="txtTotalIncome">0 đ</h5>
                    <div id="txtTotalIncomeWords" class="text-success fst-italic" style="font-size: 0.7rem; font-weight: normal; line-height: 1.2;">Không đồng</div>
                </button>
            </div>
            <div class="col-4">
                <div class="w-100 cf-tab-btn shadow-sm border-0 bg-white d-flex flex-column justify-content-center h-100" style="cursor: default;">
                    <h6><i class="bi bi-calculator me-1"></i>Chênh lệch</h6>
                    <h5 class="fw-bold mb-1" id="txtNetCashflow">0 đ</h5>
                    <div id="txtNetCashflowWords" class="text-muted fst-italic" style="font-size: 0.7rem; font-weight: normal; line-height: 1.2;">Không đồng</div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4 mb-4 bg-white">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Biểu đồ so sánh Thu & Chi</h6>
                <div style="height: 250px;">
                    <canvas id="historyBarChart"></canvas>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-7">
                <div class="card shadow-sm border-0 rounded-4 bg-white h-100">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-4"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Cơ cấu phân bổ</h6>
                        
                        <div style="height: 250px; position: relative;" class="mb-4">
                            <canvas id="mainChart"></canvas>
                            <div id="chartEmptyState" class="position-absolute top-50 start-50 translate-middle text-muted text-center d-none">
                                <i class="bi bi-pie-chart fs-1 opacity-25"></i><br>Chưa có giao dịch
                            </div>
                        </div>

                        <ul class="nav nav-pills nav-fill nav-pills-custom mb-3 bg-light rounded-pill p-1 border" id="subTabWrapper">
                            <li class="nav-item">
                                <button class="nav-link active w-100 rounded-pill" id="btnTabChild" onclick="switchSubTab('child')">Danh mục con</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link w-100 rounded-pill" id="btnTabParent" onclick="switchSubTab('parent')">Danh mục cha</button>
                            </li>
                        </ul>

                        <div id="categoryListContainer"></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="card shadow-sm border-0 rounded-4 bg-white">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-safe2-fill text-warning me-2 fs-5"></i>Tình hình ngân sách</h6>
                            <a href="../budgets/index.php" class="text-decoration-none small text-primary fw-bold">Xem tất cả</a>
                        </div>
                        
                        <div id="budgetListContainer"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../../assets/js/app.js?v=<?= time() ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // --- QUẢN LÝ THỜI GIAN ĐỘC QUYỀN  ---
        let currentDate = new Date();
        let filterVal = ''; 
        let globalDashboardData = null;
        let currentMainTab = 'expense'; 
        let currentSubTab = 'child';    
        let chartInstance = null;
        let historyChartInstance = null;

        // Hàm tính Tuần thứ mấy của Tháng (Chính xác 100%)
        function getWeekOfMonth(date) {
            const d = date.getDate();
            const firstDay = new Date(date.getFullYear(), date.getMonth(), 1).getDay();
            const offset = (firstDay === 0 ? 6 : firstDay - 1); // 0=Thứ 2, 6=Chủ nhật
            return Math.ceil((d + offset) / 7);
        }

        function resetTimeValue() {
            currentDate = new Date();
            updateTimeDisplayAndFetch();
        }

        function changeTime(offset) {
            const type = document.getElementById('filterType').value;
            if (type === 'month') {
                currentDate.setMonth(currentDate.getMonth() + offset);
            } else if (type === 'year') {
                currentDate.setFullYear(currentDate.getFullYear() + offset);
            } else if (type === 'week') {
                // Thuật toán: Nhảy qua các tuần hợp lệ thay vì cộng 7 ngày
                let y = currentDate.getFullYear();
                let m = currentDate.getMonth();
                let newWeek = getWeekOfMonth(currentDate) + offset;
                
                // Tính số tuần tối đa của tháng hiện tại
                let maxWeeks = getWeekOfMonth(new Date(y, m + 1, 0));
                
                if (newWeek > maxWeeks) {
                    currentDate = new Date(y, m + 1, 1); // Tràn tuần -> Nhảy qua ngày 1 tháng sau (Tuần 1)
                } else if (newWeek < 1) {
                    currentDate = new Date(y, m, 0); // Âm tuần -> Lùi về ngày cuối tháng trước (Tuần Max)
                } else {
                    // Đang ở trong tháng -> Căn chỉnh ngày rơi đúng vào tuần tương ứng
                    const firstDay = new Date(y, m, 1).getDay();
                    const startOffset = (firstDay === 0 ? 6 : firstDay - 1);
                    let targetDay = (newWeek - 1) * 7 - startOffset + 1;
                    if (targetDay < 1) targetDay = 1;
                    currentDate = new Date(y, m, targetDay);
                }
            }
            updateTimeDisplayAndFetch();
        }

        function updateTimeDisplayAndFetch() {
            const type = document.getElementById('filterType').value;
            const display = document.getElementById('timeDisplay');
            const y = currentDate.getFullYear();
            const m = currentDate.getMonth() + 1;
            const padM = String(m).padStart(2, '0');

            if (type === 'month') {
                display.innerText = `Tháng ${padM}/${y}`;
                filterVal = `${y}-${padM}`;
            } else if (type === 'year') {
                display.innerText = `Năm ${y}`;
                filterVal = `${y}`;
            } else if (type === 'week') {
                const wOfMonth = getWeekOfMonth(currentDate);
                display.innerText = `Tuần ${wOfMonth} - Th${padM}/${y}`;
                filterVal = `${y}-${padM}-${wOfMonth}`; // Dữ liệu gửi xuống Backend: YYYY-MM-W
            }
            fetchDashboardData();
        }

        // --- FETCH & RENDER DỮ LIỆU ---
        async function fetchDashboardData() {
            const type = document.getElementById('filterType').value;
            try {
                const res = await fetch(`../../controllers/AnalyticsController.php?action=get_dashboard&filter_type=${type}&filter_val=${filterVal}`);
                const response = await res.json();
                
                if (response.status && response.data.overview) {
                    globalDashboardData = response.data;
                    
                    // Nạp 3 ô Tổng quát
                    document.getElementById('txtTotalExpense').innerText = globalDashboardData.overview.total_expense_fmt + ' đ';
                    document.getElementById('txtTotalIncome').innerText = globalDashboardData.overview.total_income_fmt + ' đ';
                    
                    const netElem = document.getElementById('txtNetCashflow');
                    const netValue = globalDashboardData.overview.net_cashflow;
                    const sign = netValue > 0 ? '+' : (netValue < 0 ? '-' : '');
                    netElem.innerText = sign + globalDashboardData.overview.net_cashflow_fmt + ' đ';
                    netElem.className = 'fw-bold mb-0 ' + (netValue > 0 ? 'text-success' : (netValue < 0 ? 'text-danger' : 'text-dark'));
                    
                    if (window.readVietnameseNumber) {
                        document.getElementById('txtTotalExpenseWords').innerText = window.readVietnameseNumber(globalDashboardData.overview.total_expense);
                        document.getElementById('txtTotalIncomeWords').innerText = window.readVietnameseNumber(globalDashboardData.overview.total_income);
                        // Dùng Math.abs để luôn lấy số dương khi đọc chữ cho ô Chênh lệch
                        document.getElementById('txtNetCashflowWords').innerText = window.readVietnameseNumber(Math.abs(netValue));
                    }
                    
                    // Vẽ các thành phần còn lại
                    renderHistoryChart(globalDashboardData.history_chart);
                    renderBudgets(globalDashboardData.budgets);
                    refreshUI();
                } else {
                    console.error("Dữ liệu API trả về không đúng cấu trúc V2", response);
                }
            } catch (error) { console.error("Lỗi tải dashboard", error); }
        }

        // VẼ BIỂU ĐỒ CỘT 12 KỲ
        function renderHistoryChart(history) {
            const ctx = document.getElementById('historyBarChart').getContext('2d');
            if (historyChartInstance) historyChartInstance.destroy();

            historyChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: history.map(h => h.label),
                    datasets: [
                        { label: 'Thu nhập', data: history.map(h => h.income), backgroundColor: '#198754', borderRadius: 4 },
                        { label: 'Chi tiêu', data: history.map(h => h.expense), backgroundColor: '#dc3545', borderRadius: 4 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { position: 'bottom', labels: { font: { family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif" } } } 
                    },
                    scales: { 
                        y: { 
                            beginAtZero: true, 
                            ticks: { 
                                callback: function(value) { 
                                    if(value === 0) return '0đ';
                                    if(value >= 1000000) return (value / 1000000).toFixed(1) + 'Tr';
                                    if(value >= 1000) return (value / 1000).toFixed(0) + 'K';
                                    return value + 'đ'; 
                                } 
                            } 
                        } 
                    }
                }
            });
        }

        function switchMainTab(tab) {
            currentMainTab = tab;
            document.getElementById('btnTabExpense').classList.remove('active-expense');
            document.getElementById('btnTabIncome').classList.remove('active-income');
            
            if (tab === 'expense') {
                document.getElementById('btnTabExpense').classList.add('active-expense');
                document.getElementById('subTabWrapper').style.display = 'flex';
            } else {
                document.getElementById('btnTabIncome').classList.add('active-income');
                document.getElementById('subTabWrapper').style.display = 'none';
            }
            refreshUI();
        }

        function switchSubTab(tab) {
            currentSubTab = tab;
            document.getElementById('btnTabChild').classList.remove('active');
            document.getElementById('btnTabParent').classList.remove('active');
            
            if (tab === 'child') document.getElementById('btnTabChild').classList.add('active');
            else document.getElementById('btnTabParent').classList.add('active');
            
            refreshUI();
        }

        function refreshUI() {
            if (!globalDashboardData) return;
            
            let sourceData = [];
            if (currentMainTab === 'income') {
                sourceData = globalDashboardData.income;
            } else {
                sourceData = currentSubTab === 'parent' ? globalDashboardData.expense.parent : globalDashboardData.expense.child;
            }

            const canvas = document.getElementById('mainChart');
            const emptyState = document.getElementById('chartEmptyState');
            if (chartInstance) chartInstance.destroy();

            if (sourceData.length === 0) {
                canvas.style.display = 'none';
                emptyState.classList.remove('d-none');
                document.getElementById('categoryListContainer').innerHTML = '<div class="text-center py-4 text-muted fst-italic">Không có giao dịch</div>';
                return;
            }

            canvas.style.display = 'block';
            emptyState.classList.add('d-none');

            const labels = sourceData.map(d => d.name);
            const dataPts = sourceData.map(d => d.amount);
            const colors = labels.map(name => window.getCategoryColor ? window.getCategoryColor(name) : '#888');

            chartInstance = new Chart(canvas, {
                type: 'doughnut',
                data: { labels: labels, datasets: [{ data: dataPts, backgroundColor: colors, borderWidth: 2 }] },
                options: { 
                    responsive: true, maintainAspectRatio: false, cutout: '75%', 
                    plugins: { 
                        legend: { 
                            display: true, position: 'right', labels: { font: { family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif", size: 12 } }
                        } 
                    }
                }
            });

            let listHtml = '';
            sourceData.forEach((item, index) => {
                listHtml += `
                <div class="list-item-category d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle shadow-sm" style="width: 16px; height: 16px; background-color: ${colors[index]};"></div>
                        <div>
                            <span class="fw-bold text-dark d-block">${item.name}</span>
                            <span class="small text-muted">${item.percent}%</span>
                        </div>
                    </div>
                    <div class="fw-bold ${currentMainTab === 'expense' ? 'text-danger' : 'text-success'}">
                        ${item.amount_fmt} đ
                    </div>
                </div>`;
            });
            document.getElementById('categoryListContainer').innerHTML = listHtml;
        }

        function renderBudgets(budgets) {
            const container = document.getElementById('budgetListContainer');
            if (!budgets || budgets.length === 0) {
                container.innerHTML = '<div class="text-center py-4 text-muted fst-italic">Bạn chưa lập ngân sách nào.</div>';
                return;
            }

            let html = '';
            budgets.forEach(b => {
                let pct = b.percent; let uiPct = pct > 100 ? 100 : pct;
                let barColor = 'bg-success'; let textColor = 'text-muted';
                
                if (pct >= 80 && pct <= 100) { barColor = 'bg-warning'; textColor = 'text-warning text-darken'; }
                if (pct > 100) { barColor = 'bg-danger'; textColor = 'text-danger'; }

                let limitVal = parseFloat(b.limit);
                let spentVal = parseFloat(b.spent);

                let remainText = limitVal >= spentVal 
                    ? `Còn lại ${b.remain_fmt} đ` 
                    : `Vượt mức ${b.remain_fmt} đ`;

                html += `
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-dark">${b.name}</span>
                        <span class="small fw-bold ${textColor}">${pct}%</span>
                    </div>
                    <div class="progress mb-1 bg-light" style="height: 8px;">
                        <div class="progress-bar ${barColor} rounded-pill" style="width: ${uiPct}%"></div>
                    </div>
                    <div class="text-end small text-muted">${remainText}</div>
                </div>`;
            });
            container.innerHTML = html;
        }

        document.addEventListener('DOMContentLoaded', updateTimeDisplayAndFetch);
    </script>
</body>
</html>