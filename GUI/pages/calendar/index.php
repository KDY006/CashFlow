<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../autoload.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch tài chính - CashFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* CSS ĐỒNG BỘ MÀU SẮC */
        .cf-tab-btn { border: 2px solid transparent; border-radius: 15px; padding: 12px; background-color: #fff; transition: all 0.2s; text-align: left;}
        .cf-tab-btn.income-box { border-color: #198754; background-color: #d1e7dd; }
        .cf-tab-btn.expense-box { border-color: #dc3545; background-color: #f8d7da; }
        .cf-tab-btn.net-box { border-color: #0d6efd; background-color: #e7f1ff; }

        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; }
        .calendar-cell { 
            border: 1px solid #dee2e6; border-radius: 12px; min-height: 100px; padding: 8px; 
            background: #fff; cursor: pointer; transition: 0.2s; position: relative;
        }
        .calendar-cell:hover { border-color: #0d6efd; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .calendar-cell.active { border: 2px solid #0d6efd; background-color: #f0f7ff; }
        .calendar-cell.empty { background: #f8f9fa; border: none; opacity: 0.4; cursor: default; }
        
        .date-num { font-weight: 800; font-size: 1.1rem; color: #343a40; }
        .today .date-num { color: #0d6efd; text-decoration: underline; }
        
        /* Icon tờ giấy note */
        .note-indicator { position: absolute; top: 8px; right: 8px; color: #ffc107; font-size: 1.1rem; }
        
        .badge-money { font-size: 0.75rem; font-weight: bold; display: block; text-align: right; margin-top: 2px;}
        .badge-income { color: #198754; }
        .badge-expense { color: #dc3545; }

        .note-card { background: #fff9db; border-left: 5px solid #fab005; border-radius: 8px; }
    </style>
</head>
<body class="bg-light">
    <?php require_once __DIR__ . '/../../components/header.php'; ?>

    <main class="container py-4 mb-5">
        <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
            <div id="liveToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body fw-semibold" id="toastMessage">Thông báo!</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-calendar3 me-2 text-primary"></i>Lịch Tài Chính</h3>
            <div class="d-flex align-items-center bg-white p-1 rounded-pill shadow-sm border">
                <button class="btn btn-sm btn-light rounded-circle" onclick="changeMonth(-1)"><i class="bi bi-chevron-left"></i></button>
                <span id="currentMonthDisplay" class="fw-bold px-3" style="min-width: 130px; text-align:center;">Tháng...</span>
                <button class="btn btn-sm btn-light rounded-circle" onclick="changeMonth(1)"><i class="bi bi-chevron-right"></i></button>
                <input type="month" id="monthPicker" class="d-none" onchange="onMonthSelected()">
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="cf-tab-btn income-box shadow-sm">
                    <h6 class="text-success small fw-bold mb-1">THU NHẬP</h6>
                    <h5 class="fw-bold mb-0" id="monthIncome">0 đ</h5>
                </div>
            </div>
            <div class="col-4">
                <div class="cf-tab-btn expense-box shadow-sm">
                    <h6 class="text-danger small fw-bold mb-1">CHI TIÊU</h6>
                    <h5 class="fw-bold mb-0" id="monthExpense">0 đ</h5>
                </div>
            </div>
            <div class="col-4">
                <div class="cf-tab-btn net-box shadow-sm">
                    <h6 class="text-primary small fw-bold mb-1">CHÊNH LỆCH</h6>
                    <h5 class="fw-bold mb-0" id="monthNet">0 đ</h5>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 bg-white p-3">
                    <div class="calendar-grid mb-2">
                        <div class="text-center fw-bold text-muted small">T2</div><div class="text-center fw-bold text-muted small">T3</div>
                        <div class="text-center fw-bold text-muted small">T4</div><div class="text-center fw-bold text-muted small">T5</div>
                        <div class="text-center fw-bold text-muted small">T6</div><div class="text-center fw-bold text-primary small">T7</div>
                        <div class="text-center fw-bold text-danger small">CN</div>
                    </div>
                    <div id="calendarDays" class="calendar-grid"></div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card shadow-sm border-0 rounded-4 bg-white mb-4 overflow-hidden">
                    <div class="card-header bg-warning bg-opacity-10 border-0 p-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-warning-emphasis"><i class="bi bi-sticky-fill me-2"></i>Ghi chú ngày</h6>
                        <button class="btn btn-sm btn-warning rounded-pill fw-bold text-white px-3" onclick="openNoteModal()">Lưu/Sửa</button>
                    </div>
                    <div class="card-body p-3" id="dailyNoteContent">
                        <p class="text-muted fst-italic small mb-0">Không có ghi chú cho ngày này.</p>
                    </div>
                </div>

                <div id="dailyDetailList"></div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="noteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form id="noteForm" class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Ghi chú cho ngày <span id="noteDateLabel"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea name="content" id="noteTextarea" class="form-control border-0 bg-light" rows="5" placeholder="Nhập nội dung ghi chú... (Để trống để xóa)"></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2">Cập nhật ghi chú</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/app.js?v=<?= time() ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let calendarData = {};
        let dailyNotes = {}; // Chứa list ghi chú của tháng
        let currentMonthStr = ''; 
        let selectedDate = ''; 
        const noteModal = new bootstrap.Modal(document.getElementById('noteModal'));

        async function loadCalendarData() {
            currentMonthStr = document.getElementById('monthPicker').value;
            const [y, m] = currentMonthStr.split('-');
            document.getElementById('currentMonthDisplay').innerText = `Tháng ${m} / ${y}`;

            // Tải song song Giao dịch và Ghi chú
            const [resCal, resNotes] = await Promise.all([
                fetch(`../../controllers/AnalyticsController.php?action=get_calendar&month=${currentMonthStr}`),
                fetch(`../../controllers/DailyNoteController.php?action=get_all_month&month=${currentMonthStr}`)
            ]);

            const resultCal = await resCal.json();
            const resultNotes = await resNotes.json();

            if (resultCal.status) calendarData = resultCal.data;
            if (resultNotes.status) {
                dailyNotes = {};
                resultNotes.data.forEach(n => dailyNotes[n.note_date] = n.content);
            }

            renderCalendar(parseInt(y), parseInt(m));
            updateSummary();
            
            // Tự động chọn ngày hôm nay nếu mới load
            const today = new Date().toISOString().split('T')[0];
            if (!selectedDate || !selectedDate.startsWith(currentMonthStr)) {
                selectDay(calendarData[today] ? today : `${currentMonthStr}-01`);
            } else {
                selectDay(selectedDate);
            }
        }

        function renderCalendar(year, month) {
            const firstDay = new Date(year, month - 1, 1).getDay();
            const daysInMonth = new Date(year, month, 0).getDate();
            const startOffset = firstDay === 0 ? 6 : firstDay - 1; 
            const todayStr = new Date().toISOString().split('T')[0];
            let html = '';

            for (let i = 0; i < startOffset; i++) html += `<div class="calendar-cell empty"></div>`;

            for (let d = 1; d <= daysInMonth; d++) {
                const dateStr = `${year}-${String(month).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                const data = calendarData[dateStr] || { income: 0, expense: 0 };
                const hasNote = dailyNotes[dateStr];
                
                html += `
                <div class="calendar-cell ${dateStr === todayStr ? 'today' : ''} ${dateStr === selectedDate ? 'active' : ''}" 
                     id="cell_${dateStr}" onclick="selectDay('${dateStr}')">
                    <span class="date-num">${d}</span>
                    ${hasNote ? '<i class="bi bi-sticky-fill note-indicator"></i>' : ''}
                    <div class="mt-auto">
                        ${data.income > 0 ? `<span class="badge-money badge-income">+${(data.income/1000).toFixed(0)}K</span>` : ''}
                        ${data.expense > 0 ? `<span class="badge-money badge-expense">-${(data.expense/1000).toFixed(0)}K</span>` : ''}
                    </div>
                </div>`;
            }
            document.getElementById('calendarDays').innerHTML = html;
        }

        function selectDay(dateStr) {
            if(selectedDate) document.getElementById('cell_'+selectedDate)?.classList.remove('active');
            selectedDate = dateStr;
            document.getElementById('cell_'+dateStr)?.classList.add('active');

            // Hiển thị Ghi chú
            const noteArea = document.getElementById('dailyNoteContent');
            if (dailyNotes[dateStr]) {
                noteArea.innerHTML = `<div class="p-2 note-card small fw-semibold text-dark">${dailyNotes[dateStr]}</div>`;
            } else {
                noteArea.innerHTML = `<p class="text-muted fst-italic small mb-0">Không có ghi chú.</p>`;
            }

            fetchDailyTransactions(dateStr);
        }

        async function fetchDailyTransactions(dateStr) {
            const res = await fetch(`../../controllers/AnalyticsController.php?action=get_daily_transactions&date=${dateStr}`);
            const result = await res.json();
            const container = document.getElementById('dailyDetailList');
            
            if (result.status && result.data.length > 0) {
                let html = `<h6 class="fw-bold mb-3 mt-2">Giao dịch ngày ${dateStr.split('-').reverse().join('/')}</h6>`;
                result.data.forEach(t => {
                    const isInc = t.category_type === 'income';
                    const amountStr = window.formatNumberInput(t.amount.split('.')[0]);
                    const words = window.readVietnameseNumber ? window.readVietnameseNumber(t.amount.split('.')[0]) : '';
                    
                    html += `
                    <div class="card border-0 shadow-sm rounded-4 mb-2 p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-light p-2 text-primary"><i class="bi bi-tag-fill"></i></div>
                                <div>
                                    <div class="fw-bold text-dark">${t.category_name}</div>
                                    <div class="small text-muted">${t.note || '...'}</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold ${isInc ? 'text-success' : 'text-danger'}">${isInc ? '+' : '-'}${amountStr} đ</div>
                                <div class="text-muted fst-italic" style="font-size:0.65rem">${words}</div>
                            </div>
                        </div>
                    </div>`;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div class="text-center py-4 text-muted small">Không có giao dịch ngày này.</div>';
            }
        }

        function updateSummary() {
            let inc = 0, exp = 0;
            Object.values(calendarData).forEach(d => { inc += d.income; exp += d.expense; });
            document.getElementById('monthIncome').innerText = window.formatNumberInput(inc.toString()) + ' đ';
            document.getElementById('monthExpense').innerText = window.formatNumberInput(exp.toString()) + ' đ';
            const net = inc - exp;
            document.getElementById('monthNet').innerText = (net >= 0 ? '+' : '-') + window.formatNumberInput(Math.abs(net).toString()) + ' đ';
        }

        function openNoteModal() {
            document.getElementById('noteDateLabel').innerText = selectedDate;
            document.getElementById('noteTextarea').value = dailyNotes[selectedDate] || '';
            noteModal.show();
        }

        // Thêm biến khởi tạo Toast ở đầu phần script
        const toastEl = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastEl);
        function showToast(message, isSuccess = true) {
            document.getElementById('toastMessage').innerText = message;
            toastEl.className = isSuccess ? 'toast align-items-center text-bg-success border-0' : 'toast align-items-center text-bg-danger border-0';
            toast.show();
        }

        // Cập nhật sự kiện submit form ghi chú
        document.getElementById('noteForm').onsubmit = async (e) => {
            e.preventDefault();
            const content = document.getElementById('noteTextarea').value;
            const fd = new FormData();
            fd.append('action', 'save');
            fd.append('date', selectedDate);
            fd.append('content', content);

            try {
                const res = await fetch('../../controllers/DailyNoteController.php', { method: 'POST', body: fd });
                const result = await res.json();
                
                showToast(result.message, result.status); // Báo lên màn hình
                
                if (result.status) {
                    noteModal.hide();
                    loadCalendarData(); // Tải lại lịch để hiện/ẩn icon tờ giấy màu vàng
                }
            } catch (error) {
                showToast("Lỗi kết nối máy chủ!", false);
            }
        };

        function changeMonth(offset) {
            const [y, m] = document.getElementById('monthPicker').value.split('-');
            const d = new Date(y, parseInt(m) - 1 + offset, 1);
            document.getElementById('monthPicker').value = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
            loadCalendarData();
        }

        document.addEventListener('DOMContentLoaded', () => {
            const now = new Date();
            document.getElementById('monthPicker').value = `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2, '0')}`;
            loadCalendarData();
        });
    </script>
</body>
</html>