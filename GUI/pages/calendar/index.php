<?php
// Tệp: GUI/pages/calendar/index.php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../autoload.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch giao dịch - CashFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* CSS CHUYÊN BIỆT CHO LỊCH */
        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; margin-top: 15px;}
        .calendar-header { text-align: center; font-weight: bold; font-size: 0.85rem; padding: 5px; color: #6c757d; }
        .calendar-cell { border: 1px solid #f0f2f5; border-radius: 8px; min-height: 85px; padding: 5px; background: #fff; transition: transform 0.2s; cursor: pointer;}
        .calendar-cell:hover { border-color: #0d6efd; box-shadow: 0 4px 10px rgba(0,0,0,0.05); z-index: 1; transform: scale(1.05);}
        .calendar-cell.empty { background: transparent; border: none; cursor: default; box-shadow: none;}
        .calendar-cell.empty:hover { transform: none; }
        .date-num { font-weight: 800; color: #343a40; font-size: 0.9rem; margin-bottom: 2px; }
        .date-today { background-color: #0d6efd; color: white; border-radius: 50%; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; }
        .cal-money { font-size: 0.75rem; font-weight: 600; line-height: 1.2; text-align: right; margin-top: 2px;}
        .anomaly-icon { position: absolute; top: 4px; right: 4px; font-size: 0.8rem; color: #dc3545; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.2); } 100% { transform: scale(1); } }
        @media (max-width: 768px) {
            .calendar-grid { gap: 4px; }
            .calendar-cell { min-height: 65px; padding: 2px; }
            .cal-money { font-size: 0.65rem; }
        }
    </style>
</head>
<body class="bg-light">

    <?php require_once __DIR__ . '/../../components/header.php'; ?>

    <main class="container py-4 mb-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
            <h3 class="fw-bold text-dark mb-0">Lịch giao dịch</h3>
            <div class="d-flex align-items-center bg-white rounded-pill shadow-sm p-1 border">
                <button class="btn btn-light rounded-circle btn-sm d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" onclick="changeMonth(-1)"><i class="bi bi-chevron-left fw-bold"></i></button>
                <div class="mx-3 text-center" style="min-width: 120px;"><span id="currentMonthDisplay" class="fw-bold text-primary fs-6">...</span></div>
                <button class="btn btn-light rounded-circle btn-sm d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" onclick="changeMonth(1)"><i class="bi bi-chevron-right fw-bold"></i></button>
            </div>
            <input type="month" id="monthPicker" class="d-none" onchange="loadCalendarData()">
        </div>

        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted"><i class="bi bi-lightning-fill text-danger"></i> Chi tiêu cao bất thường</small>
                </div>
                <div id="calendarContainer">
                    <div class="text-center py-4 text-muted"><div class="spinner-border text-primary spinner-border-sm me-2"></div>Đang vẽ lịch...</div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4 bg-white" id="dailyTransactionsCard" style="display: none;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3" id="selectedDateTitle">Chi tiết ngày...</h6>
                <div id="dailyTransactionsList" class="text-muted small">Tính năng đang được phát triển...</div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="../../assets/js/app.js?v=<?= time() ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function formatMoney(num) { return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); }

        function changeMonth(offset) {
            const picker = document.getElementById('monthPicker');
            let [year, month] = picker.value.split('-').map(Number);
            month += offset;
            if (month < 1) { month = 12; year--; } else if (month > 12) { month = 1; year++; }
            picker.value = `${year}-${String(month).padStart(2, '0')}`;
            document.getElementById('currentMonthDisplay').innerText = `Tháng ${String(month).padStart(2, '0')}, ${year}`;
            loadCalendarData();
        }

        async function loadCalendarData() {
            try {
                const monthVal = document.getElementById('monthPicker').value; 
                const [year, month] = monthVal.split('-');
                
                // Gọi API chuyên dụng thay vì gọi toàn bộ Dashboard
                const res = await fetch(`../../controllers/AnalyticsController.php?action=get_calendar&month=${monthVal}`);
                const response = await res.json();
                
                if (response.status) {
                    // Dữ liệu trả về giờ là mảng phẳng calendar_data
                    renderCalendar(parseInt(year), parseInt(month), response.data || {});
                }
            } catch (error) { 
                console.error("Lỗi tải lịch:", error); 
            }
        }

        function renderCalendar(year, month, calendarData) {
            const daysInMonth = new Date(year, month, 0).getDate();
            let firstDay = new Date(year, month - 1, 1).getDay(); 
            let startDayIndex = firstDay === 0 ? 6 : firstDay - 1; 
            const today = new Date();
            const isCurrentMonth = today.getFullYear() === year && (today.getMonth() + 1) === month;
            const currentDay = today.getDate();

            let html = '<div class="calendar-grid">';
            const days = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
            days.forEach(d => html += `<div class="calendar-header">${d}</div>`);

            for(let i = 0; i < startDayIndex; i++) html += `<div class="calendar-cell empty"></div>`;

            for(let i = 1; i <= daysInMonth; i++) {
                let dateStr = `${year}-${String(month).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                let data = calendarData[dateStr] || { income: 0, expense: 0, is_anomaly: false };
                let dayClass = (isCurrentMonth && i === currentDay) ? 'date-today' : '';
                let content = `<div class="date-num ${dayClass}">${i}</div>`;
                if (data.is_anomaly) content += `<i class="bi bi-lightning-fill anomaly-icon" title="Chi tiêu cao bất thường"></i>`;
                
                let moneyHtml = '<div class="mt-1">';
                if (data.income > 0) moneyHtml += `<div class="cal-money text-success">+${formatMoney(data.income)}</div>`;
                if (data.expense > 0) moneyHtml += `<div class="cal-money text-danger">-${formatMoney(data.expense)}</div>`;
                moneyHtml += '</div>';

                html += `<div class="calendar-cell position-relative" onclick="showDaily(${i}, ${month})">${content}${moneyHtml}</div>`;
            }
            html += '</div>';
            document.getElementById('calendarContainer').innerHTML = html;
        }

        async function showDaily(day, month) {
            // Hiện panel và cuộn mượt xuống
            const card = document.getElementById('dailyTransactionsCard');
            card.style.display = 'block';
            document.getElementById('selectedDateTitle').innerText = `Giao dịch ngày ${String(day).padStart(2, '0')}/${String(month).padStart(2, '0')}`;
            card.scrollIntoView({ behavior: 'smooth' });

            const listContainer = document.getElementById('dailyTransactionsList');
            listContainer.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary spinner-border-sm me-2"></div>Đang tải dữ liệu...</div>';

            // Ghép chuỗi YYYY-MM-DD để gửi lên API
            const pickerValue = document.getElementById('monthPicker').value; 
            const [year] = pickerValue.split('-');
            const dateStr = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

            try {
                const res = await fetch(`../../controllers/AnalyticsController.php?action=get_daily_transactions&date=${dateStr}`);
                const response = await res.json();

                if (response.status && response.data.length > 0) {
                    let html = '';
                    response.data.forEach(t => {
                        const isIncome = t.category_type === 'income';
                        const sign = isIncome ? '+' : '-';
                        const colorClass = isIncome ? 'text-success' : 'text-danger';
                        const icon = isIncome ? 'bi-arrow-down-circle-fill' : 'bi-arrow-up-circle-fill';
                        const noteText = t.note ? t.note : '<span class="opacity-50">Không có ghi chú</span>';
                        
                        // Gọi hàm lấy màu ngẫu nhiên từ app.js của bạn
                        const iconColor = typeof getCategoryColor === 'function' ? getCategoryColor(t.category_name) : '#6c757d';

                        html += `
                        <div class="d-flex justify-content-between align-items-center mb-3 p-3 border rounded-4 bg-white shadow-sm transition-hover">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 45px; height: 45px; background-color: ${iconColor}">
                                    <i class="bi ${icon} fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark fs-6">${t.category_name}</div>
                                    <div class="small text-muted">${noteText} • <i class="bi bi-clock"></i> ${t.time_val || '00:00'}</div>
                                </div>
                            </div>
                            <div class="fw-bold fs-5 ${colorClass}">
                                ${sign}${formatMoney(t.amount)} đ
                            </div>
                        </div>`;
                    });
                    listContainer.innerHTML = html;
                } else {
                    listContainer.innerHTML = `
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary opacity-25"></i>
                            <div class="fst-italic">Không có giao dịch nào phát sinh trong ngày này.</div>
                        </div>`;
                }
            } catch (error) {
                console.error(error);
                listContainer.innerHTML = '<div class="text-danger text-center py-3"><i class="bi bi-x-circle me-2"></i>Lỗi tải dữ liệu.</div>';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date();
            const yyyy = today.getFullYear(); let mm = today.getMonth() + 1;
            document.getElementById('monthPicker').value = `${yyyy}-${mm < 10 ? '0'+mm : mm}`;
            document.getElementById('currentMonthDisplay').innerText = `Tháng ${mm < 10 ? '0'+mm : mm}, ${yyyy}`;
            loadCalendarData();
        });
    </script>
</body>
</html>