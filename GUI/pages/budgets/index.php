<?php
// Tệp: GUI/pages/budgets/index.php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../autoload.php';

$userId = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ngân Sách - CashFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php require_once __DIR__ . '/../../components/header.php'; ?>

    <main class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-calendar-check fs-3 me-2 text-primary"></i>
                <input type="month" id="monthPicker" class="form-control fw-bold border-0 bg-light shadow-sm text-primary" style="width: 160px; cursor: pointer;" onchange="fetchBudgets()">
            </div>
            <button class="btn btn-success rounded-pill px-3 shadow-sm" onclick="openAddModal()">
                <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline ms-1">Lập ngân sách</span>
            </button>
        </div>

        <div id="budgetList" class="row g-3">
            <div class="text-center py-5 text-muted col-12">
                <div class="spinner-border text-success" role="status"></div>
                <p class="mt-2">Đang tải ngân sách...</p>
            </div>
        </div>
    </main>

    <div class="modal fade" id="budgetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">Lập Ngân Sách</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="budgetForm">
                        <input type="hidden" name="action" id="formAction" value="add">
                        <input type="hidden" name="id" id="budgetId" value="">
                        <input type="hidden" name="month" id="hiddenMonth">
                        <input type="hidden" name="year" id="hiddenYear">

                        <div class="mb-3" id="categoryGroup">
                            <label class="form-label fw-semibold text-muted small">DANH MỤC CHI TIÊU</label>
                            <select name="category_id" id="categoryId" class="form-select form-select-lg" required>
                                <option value="" disabled selected>-- Đang tải danh mục --</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted small">GIỚI HẠN CHI (VNĐ)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white text-success fw-bold">₫</span>
                                <input type="text" id="amountDisplay" class="form-control text-end fs-4 fw-bold text-success" placeholder="0" required>
                                <input type="hidden" name="amount_limit" id="amount">
                            </div>
                            <div id="amountInWords" class="form-text text-end fst-italic text-success mt-1" style="min-height: 20px;"></div>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill shadow-sm fw-bold">Lưu Ngân Sách</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
        <div id="liveToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-semibold" id="toastMessage">Thông báo!</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../../components/bottom-nav.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const bdgModal = new bootstrap.Modal(document.getElementById('budgetModal'));
        const toastEl = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastEl);

        // --- THUẬT TOÁN ĐỒNG BỘ MÀU SẮC ---
        function getCategoryColor(name) {
            const colors = [
                '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#F06292', 
                '#AED581', '#7986CB', '#4DB6AC', '#FFB74D', '#A1887F', 
                '#90A4AE', '#BA68C8', '#FF8A65', '#4DD0E1', '#81C784'
            ];
            let hash = 0;
            for (let i = 0; i < name.length; i++) { hash = name.charCodeAt(i) + ((hash << 5) - hash); }
            return colors[Math.abs(hash) % colors.length];
        }
        
        function showToast(message, isSuccess = true) {
            document.getElementById('toastMessage').innerText = message;
            toastEl.className = isSuccess ? 'toast align-items-center text-bg-success border-0' : 'toast align-items-center text-bg-danger border-0';
            toast.show();
        }

        // --- ĐỊNH DẠNG VÀ ĐỌC SỐ TIỀN ---
        function formatNumberInput(value) {
            value = value.replace(/\D/g, ""); 
            return value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function readVietnameseNumber(number) {
            if (!number || number == 0) return "";
            const units = ["", "một", "hai", "ba", "bốn", "năm", "sáu", "bảy", "tám", "chín"];
            const levels = ["", "nghìn", "triệu", "tỷ", "nghìn tỷ", "triệu tỷ"];
            function readThreeDigits(num, isFirst) {
                let hundred = Math.floor(num / 100); let ten = Math.floor((num % 100) / 10); let unit = num % 10; let res = "";
                if (hundred > 0 || !isFirst) res += units[hundred] + " trăm ";
                if (ten > 1) { res += units[ten] + " mươi "; if (unit == 1) res += "mốt "; else if (unit == 5) res += "lăm "; else if (unit > 0) res += units[unit] + " "; } 
                else if (ten == 1) { res += "mười "; if (unit == 5) res += "lăm "; else if (unit > 0) res += units[unit] + " "; } 
                else if (unit > 0) { if (!isFirst || hundred > 0) res += "linh " + units[unit] + " "; else res += units[unit] + " "; }
                return res;
            }
            let str = ""; let i = 0; let temp = number;
            do {
                let block = temp % 1000;
                if (block > 0) { let s = readThreeDigits(block, temp < 1000); str = s + levels[i] + " " + str; }
                temp = Math.floor(temp / 1000); i++;
            } while (temp > 0);
            return str.trim().charAt(0).toUpperCase() + str.trim().slice(1) + " đồng";
        }

        const amountInput = document.getElementById('amountDisplay');
        const hiddenAmount = document.getElementById('amount');
        const amountWords = document.getElementById('amountInWords');

        amountInput.addEventListener('input', function(e) {
            let rawValue = e.target.value.replace(/\D/g, "");
            e.target.value = formatNumberInput(rawValue);
            hiddenAmount.value = rawValue;
            amountWords.innerText = rawValue ? readVietnameseNumber(parseInt(rawValue)) : "";
        });

        async function fetchCategories() {
            const res = await fetch('../../controllers/CategoryController.php?action=get_all');
            const result = await res.json();
            const select = document.getElementById('categoryId');
            select.innerHTML = '<option value="" disabled selected>-- Chọn danh mục --</option>';
            result.data.filter(c => c.type === 'expense').forEach(c => {
                select.innerHTML += `<option value="${c.id}">${c.name}</option>`;
            });
        }

        async function fetchBudgets() {
            const pickerVal = document.getElementById('monthPicker').value;
            const [year, month] = pickerVal.split('-');
            
            const res = await fetch(`../../controllers/BudgetController.php?action=get_by_month&month=${month}&year=${year}`);
            const result = await res.json();
            const list = document.getElementById('budgetList');
            
            if (result.data.length === 0) {
                list.innerHTML = `<div class="col-12 text-center py-5 text-muted"><i class="bi bi-safe2 fs-1 mb-2"></i><p>Tháng này bạn chưa thiết lập hũ ngân sách nào.</p></div>`; return;
            }
            
            let html = '';
            result.data.forEach(b => {
                let pct = b.progress_percentage;
                let barColor = 'bg-success';
                if (pct >= 80 && pct <= 100) barColor = 'bg-warning';
                if (pct > 100) barColor = 'bg-danger';

                let uiPct = pct > 100 ? 100 : pct; 
                let remainText = b.remain_amount >= 0 
                    ? `<span class="text-success small fw-semibold">Còn lại: ${b.formatted_remain}</span>`
                    : `<span class="text-danger small fw-bold">Ghi nợ: ${b.formatted_remain}</span>`;

                // --- GỌI HÀM SINH MÀU TỪ TÊN DANH MỤC ---
                const catColor = getCategoryColor(b.category_name);

                html += `
                <div class="col-12 col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <!-- ÁP DỤNG MÀU LÊN CHẤM TRÒN (BADGE) -->
                                    <span class="rounded-circle me-2" style="width: 14px; height: 14px; background-color: ${catColor}; display: inline-block;"></span>
                                    <h6 class="mb-0 fw-bold">${b.category_name}</h6>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li><a class="dropdown-item" href="#" onclick="openEditModal(${b.id}, ${b.amount_limit})"><i class="bi bi-pencil me-2"></i>Sửa giới hạn</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteBudget(${b.id})"><i class="bi bi-trash me-2"></i>Xóa hũ</a></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">${b.formatted_spent} / ${b.formatted_limit}</small>
                                <small class="text-muted fw-bold">${pct}%</small>
                            </div>
                            
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar ${barColor}" role="progressbar" style="width: ${uiPct}%"></div>
                            </div>
                            
                            <div class="text-end">
                                ${remainText}
                            </div>
                        </div>
                    </div>
                </div>`;
            });
            list.innerHTML = html;
        }

        function openAddModal() {
            document.getElementById('modalTitle').innerText = 'Lập Hũ Ngân Sách';
            document.getElementById('formAction').value = 'add';
            document.getElementById('budgetForm').reset();
            const pickerVal = document.getElementById('monthPicker').value;
            const [year, month] = pickerVal.split('-');
            document.getElementById('hiddenMonth').value = month;
            document.getElementById('hiddenYear').value = year;
            document.getElementById('categoryGroup').style.display = 'block'; 
            document.getElementById('amountInWords').innerText = '';
            document.getElementById('amount').value = '';
            bdgModal.show();
        }

        function openEditModal(id, currentLimit) {
            document.getElementById('modalTitle').innerText = 'Sửa Giới Hạn Chi';
            document.getElementById('formAction').value = 'update';
            document.getElementById('budgetId').value = id;
            document.getElementById('categoryGroup').style.display = 'none'; 
            amountInput.value = formatNumberInput(currentLimit.toString());
            hiddenAmount.value = currentLimit;
            amountWords.innerText = readVietnameseNumber(parseInt(currentLimit));
            bdgModal.show();
        }

        document.getElementById('budgetForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const res = await fetch('../../controllers/BudgetController.php', { method: 'POST', body: new FormData(e.target) });
            const result = await res.json();
            showToast(result.message, result.status);
            if (result.status) { bdgModal.hide(); fetchBudgets(); }
        });

        async function deleteBudget(id) {
            if(confirm('Xóa hũ ngân sách này? Các giao dịch cũ vẫn được giữ nguyên, chỉ xóa giới hạn.')) {
                const fd = new FormData(); fd.append('action', 'delete'); fd.append('id', id);
                const res = await fetch('../../controllers/BudgetController.php', { method: 'POST', body: fd });
                const result = await res.json();
                showToast(result.message, result.status);
                if(result.status) fetchBudgets();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date();
            const yyyy = today.getFullYear();
            let mm = today.getMonth() + 1;
            if (mm < 10) mm = '0' + mm;
            document.getElementById('monthPicker').value = `${yyyy}-${mm}`;
            fetchCategories(); 
            fetchBudgets();
        });
    </script>
</body>
</html>