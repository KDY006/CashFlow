<?php
// Tệp: GUI/pages/transactions/index.php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../autoload.php';

$userId = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Sử Giao Dịch - CashFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php require_once __DIR__ . '/../../components/header.php'; ?>

    <main class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark mb-0">Giao dịch</h3>
            <div>
                <button class="btn btn-outline-secondary rounded-pill px-3 shadow-sm me-2" onclick="openCategoryModal()">
                    <i class="bi bi-tags"></i> <span class="d-none d-sm-inline ms-1">Danh mục</span>
                </button>
                <button class="btn btn-success rounded-pill px-3 shadow-sm" onclick="openAddModal()">
                    <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline ms-1">Thêm</span>
                </button>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0" id="transactionList">
                <div class="text-center py-5 text-muted">
                    <div class="spinner-border text-success" role="status"></div>
                    <p class="mt-2">Đang tải dữ liệu...</p>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="transactionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">Thêm Giao Dịch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="transactionForm">
                        <input type="hidden" name="action" id="formAction" value="add">
                        <input type="hidden" name="id" id="transactionId" value="">

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">SỐ TIỀN (VNĐ)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white text-success fw-bold">₫</span>
                                <input type="text" id="amountDisplay" class="form-control text-end fs-4 fw-bold text-success" placeholder="0" required>
                                <input type="hidden" name="amount" id="amount">
                            </div>
                            <div id="amountInWords" class="form-text text-end fst-italic text-success mt-1" style="min-height: 20px;"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">DANH MỤC</label>
                            <select name="category_id" id="categoryId" class="form-select form-select-lg" required>
                                <option value="" disabled selected>-- Đang tải danh mục --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">NGÀY</label>
                            <input type="date" name="transaction_date" id="transactionDate" class="form-control form-control-lg" required max="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted small">GHI CHÚ</label>
                            <textarea name="note" id="note" class="form-control" rows="2"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill shadow-sm fw-bold">Lưu Giao Dịch</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title fw-bold">Quản lý Danh mục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="categoryForm" class="mb-4">
                        <div class="row g-2">
                            <div class="col-12">
                                <input type="text" name="name" id="catName" class="form-control" placeholder="Tên danh mục mới..." required>
                            </div>
                            <div class="col-8">
                                <select name="type" id="catType" class="form-select">
                                    <option value="expense">Khoản Chi (Đỏ)</option>
                                    <option value="income">Khoản Thu (Xanh)</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <button type="submit" class="btn btn-dark w-100 fw-bold">Thêm</button>
                            </div>
                        </div>
                    </form>
                    
                    <hr class="text-muted">

                    <h6 class="fw-bold mb-3">Danh sách của bạn</h6>
                    <div id="categoryList" class="list-group list-group-flush border rounded-3">
                        <div class="text-center p-3"><span class="spinner-border spinner-border-sm text-secondary"></span></div>
                    </div>
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
        const transModal = new bootstrap.Modal(document.getElementById('transactionModal'));
        const catModal = new bootstrap.Modal(document.getElementById('categoryModal'));
        const toastEl = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastEl);
        
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
                let hundred = Math.floor(num / 100);
                let ten = Math.floor((num % 100) / 10);
                let unit = num % 10;
                let res = "";

                if (hundred > 0 || !isFirst) res += units[hundred] + " trăm ";
                if (ten > 1) {
                    res += units[ten] + " mươi ";
                    if (unit == 1) res += "mốt ";
                    else if (unit == 5) res += "lăm ";
                    else if (unit > 0) res += units[unit] + " ";
                } else if (ten == 1) {
                    res += "mười ";
                    if (unit == 5) res += "lăm ";
                    else if (unit > 0) res += units[unit] + " ";
                } else if (unit > 0) {
                    if (!isFirst || hundred > 0) res += "linh " + units[unit] + " ";
                    else res += units[unit] + " ";
                }
                return res;
            }

            let str = "";
            let i = 0;
            let temp = number;
            do {
                let block = temp % 1000;
                if (block > 0) {
                    let s = readThreeDigits(block, temp < 1000);
                    str = s + levels[i] + " " + str;
                }
                temp = Math.floor(temp / 1000);
                i++;
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

        // --- LOGIC GIAO DỊCH (TRANSACTIONS) ---
        async function fetchTransactions() {
            const res = await fetch('../../controllers/TransactionController.php?action=get_all');
            const result = await res.json();
            const list = document.getElementById('transactionList');
            
            if (result.data.length === 0) {
                list.innerHTML = `<div class="text-center py-5 text-muted"><i class="bi bi-receipt fs-1 mb-2"></i><p>Bạn chưa có giao dịch nào.</p></div>`; return;
            }
            
            let html = '<div class="list-group list-group-flush">';
            result.data.forEach(t => {
                const typeClass = t.category_type === 'income' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger';
                const sign = t.category_type === 'income' ? '+' : '-';
                const cClass = t.category_type === 'income' ? 'text-success' : 'text-danger';
                
                html += `
                    <div class="list-group-item p-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-2 me-3 ${typeClass}"><i class="bi bi-wallet2"></i></div>
                            <div>
                                <h6 class="mb-0 fw-bold">${t.category_name}</h6>
                                <small class="text-muted">${t.formatted_date} ${t.note ? '• '+t.note : ''}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <h6 class="mb-1 fw-bold ${cClass}">${sign}${t.formatted_amount}</h6>
                            <button onclick="openEditModal(${t.id})" class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></button>
                            <button onclick="deleteTransaction(${t.id})" class="btn btn-sm btn-light border text-danger"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>`;
            });
            list.innerHTML = html + '</div>';
        }

        function openAddModal() {
            document.getElementById('modalTitle').innerText = 'Thêm Giao Dịch';
            document.getElementById('formAction').value = 'add';
            document.getElementById('transactionForm').reset();
            document.getElementById('transactionDate').valueAsDate = new Date();
            document.getElementById('amountInWords').innerText = '';
            document.getElementById('amount').value = '';
            transModal.show();
        }

        async function openEditModal(id) {
            const res = await fetch(`../../controllers/TransactionController.php?action=get&id=${id}`);
            const result = await res.json();
            if(result.status) {
                document.getElementById('modalTitle').innerText = 'Sửa Giao Dịch';
                document.getElementById('formAction').value = 'edit';
                document.getElementById('transactionId').value = result.data.id;
                
                const amountValue = result.data.amount.split('.')[0];
                amountInput.value = formatNumberInput(amountValue);
                hiddenAmount.value = amountValue;
                amountWords.innerText = readVietnameseNumber(parseInt(amountValue));

                document.getElementById('categoryId').value = result.data.category_id;
                document.getElementById('transactionDate').value = result.data.transaction_date.split(' ')[0]; 
                document.getElementById('note').value = result.data.note;
                transModal.show();
            }
        }

        document.getElementById('transactionForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const res = await fetch('../../controllers/TransactionController.php', { method: 'POST', body: new FormData(e.target) });
            const result = await res.json();
            showToast(result.message, result.status);
            if (result.status) { transModal.hide(); fetchTransactions(); }
        });

        async function deleteTransaction(id) {
            if(confirm('Bạn có chắc muốn xóa giao dịch này?')) {
                const fd = new FormData(); fd.append('action', 'delete'); fd.append('id', id);
                const res = await fetch('../../controllers/TransactionController.php', { method: 'POST', body: fd });
                const result = await res.json();
                showToast(result.message, result.status);
                if(result.status) fetchTransactions();
            }
        }

        // --- LOGIC DANH MỤC (CATEGORIES) ---
        async function fetchCategories() {
            const res = await fetch('../../controllers/CategoryController.php?action=get_all');
            const result = await res.json();
            const list = document.getElementById('categoryList');
            const select = document.getElementById('categoryId');
            
            list.innerHTML = '';
            select.innerHTML = '<option value="" disabled selected>-- Chọn danh mục --</option>';

            result.data.forEach(c => {
                const typeText = c.type === 'income' ? 'Thu' : 'Chi';
                select.innerHTML += `<option value="${c.id}">${c.name} (${typeText})</option>`;
                list.innerHTML += `
                    <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                        <div class="d-flex align-items-center">
                            <span class="badge ${c.color_class} me-2 rounded-pill">&nbsp;&nbsp;</span>
                            <span class="fw-semibold">${c.name}</span>
                            <small class="ms-2 text-muted">(${typeText})</small>
                        </div>
                        <button onclick="deleteCategory(${c.id})" class="btn btn-sm text-danger"><i class="bi bi-x-circle"></i></button>
                    </div>`;
            });
        }

        function openCategoryModal() { catModal.show(); }

        document.getElementById('categoryForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd = new FormData(e.target); fd.append('action', 'add');
            const res = await fetch('../../controllers/CategoryController.php', { method: 'POST', body: fd });
            const result = await res.json();
            showToast(result.message, result.status);
            if (result.status) { document.getElementById('catName').value = ''; fetchCategories(); }
        });

        async function deleteCategory(id) {
            if(confirm('Bạn có chắc muốn xóa danh mục này?')) {
                const fd = new FormData(); fd.append('action', 'delete'); fd.append('id', id);
                const res = await fetch('../../controllers/CategoryController.php', { method: 'POST', body: fd });
                const result = await res.json();
                showToast(result.message, result.status);
                if(result.status) fetchCategories();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            fetchCategories(); 
            fetchTransactions();
        });
    </script>
</body>
</html>