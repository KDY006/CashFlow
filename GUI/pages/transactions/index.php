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
                <!-- CHỈ GIỮ LẠI NÚT QUẢN LÝ DANH MỤC -->
                <button class="btn btn-outline-secondary rounded-pill px-3 shadow-sm" onclick="openCategoryModal()">
                    <i class="bi bi-tags"></i> <span class="d-none d-sm-inline ms-1">Danh mục</span>
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

    <!-- MODAL CHỈ DÀNH CHO SỬA GIAO DỊCH -->
    <div class="modal fade" id="editTransactionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Sửa Giao Dịch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="editTransactionForm">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="editTransactionId" value="">

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">SỐ TIỀN (VNĐ)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white text-success fw-bold">₫</span>
                                <input type="text" id="editAmountDisplay" class="form-control text-end fs-4 fw-bold text-success" placeholder="0" required>
                                <input type="hidden" name="amount" id="editAmount">
                            </div>
                            <div id="editAmountInWords" class="form-text text-end fst-italic text-success mt-1" style="min-height: 20px;"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">DANH MỤC</label>
                            <select name="category_id" id="editCategoryId" class="form-select form-select-lg" required>
                                <option value="" disabled selected>-- Đang tải danh mục --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">NGÀY</label>
                            <input type="date" name="transaction_date" id="editTransactionDate" class="form-control form-control-lg" required max="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted small">GHI CHÚ</label>
                            <textarea name="note" id="editNote" class="form-control" rows="2"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm fw-bold">Cập Nhật Giao Dịch</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL QUẢN LÝ DANH MỤC -->
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
        const editTransModal = new bootstrap.Modal(document.getElementById('editTransactionModal'));
        const catModal = new bootstrap.Modal(document.getElementById('categoryModal'));
        const toastEl = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastEl);
        
        function showToast(message, isSuccess = true) {
            document.getElementById('toastMessage').innerText = message;
            toastEl.className = isSuccess ? 'toast align-items-center text-bg-success border-0' : 'toast align-items-center text-bg-danger border-0';
            toast.show();
        }

        // --- XỬ LÝ NHẬP TIỀN CHO FORM SỬA (Gọi hàm từ Global) ---
        const editAmountInput = document.getElementById('editAmountDisplay');
        const editHiddenAmount = document.getElementById('editAmount');
        const editAmountWords = document.getElementById('editAmountInWords');

        editAmountInput.addEventListener('input', function(e) {
            let rawValue = e.target.value.replace(/\D/g, "");
            e.target.value = window.formatNumberInput(rawValue);
            editHiddenAmount.value = rawValue;
            editAmountWords.innerText = rawValue ? window.readVietnameseNumber(parseInt(rawValue)) : "";
        });

        // --- LOGIC GIAO DỊCH (TRANSACTIONS) ---
        async function fetchTransactions() {
            const res = await fetch('../../controllers/TransactionController.php?action=get_all');
            const result = await res.json();
            const list = document.getElementById('transactionList');
            
            // XỬ LÝ EMPTY STATE (TRẠNG THÁI RỖNG) CỰC XỊN
            if (result.data.length === 0) {
                list.innerHTML = `
                    <div class="text-center py-5 px-3">
                        <div class="mb-3">
                            <i class="bi bi-wallet2 text-success" style="font-size: 3rem; opacity: 0.5;"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Chưa có giao dịch nào</h5>
                        <p class="text-muted mb-4">Hãy ghi chép lại khoản thu chi đầu tiên của bạn để CashFlow giúp bạn quản lý nhé!</p>
                        <button class="btn btn-success rounded-pill px-4 py-2 shadow-sm fw-bold" onclick="openGlobalAddModal()">
                            <i class="bi bi-plus-lg me-1"></i> Tạo giao dịch mới
                        </button>
                    </div>`; 
                return;
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

        async function openEditModal(id) {
            const res = await fetch(`../../controllers/TransactionController.php?action=get&id=${id}`);
            const result = await res.json();
            if(result.status) {
                document.getElementById('editTransactionId').value = result.data.id;
                
                // Gọi hàm format từ Global
                const amountValue = result.data.amount.split('.')[0];
                editAmountInput.value = window.formatNumberInput(amountValue);
                editHiddenAmount.value = amountValue;
                editAmountWords.innerText = window.readVietnameseNumber(parseInt(amountValue));

                document.getElementById('editCategoryId').value = result.data.category_id;
                document.getElementById('editTransactionDate').value = result.data.transaction_date.split(' ')[0]; 
                document.getElementById('editNote').value = result.data.note;
                
                editTransModal.show();
            }
        }

        document.getElementById('editTransactionForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const res = await fetch('../../controllers/TransactionController.php', { method: 'POST', body: new FormData(e.target) });
            const result = await res.json();
            showToast(result.message, result.status);
            if (result.status) { editTransModal.hide(); fetchTransactions(); }
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
            const editSelect = document.getElementById('editCategoryId');
            
            list.innerHTML = '';
            editSelect.innerHTML = '<option value="" disabled selected>-- Chọn danh mục --</option>';

            result.data.forEach(c => {
                const typeText = c.type === 'income' ? 'Thu' : 'Chi';
                editSelect.innerHTML += `<option value="${c.id}">${c.name} (${typeText})</option>`;
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