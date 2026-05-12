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
    <title>Danh Mục và Ngân Sách - CashFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* CSS GIAO DIỆN ĐỒNG BỘ */
        .nav-pills-custom .nav-link { border-radius: 25px; color: #6c757d; font-weight: 600; background: #fff; border: 1px solid #dee2e6; margin: 0 5px; padding: 10px 20px; transition: all 0.2s;}
        .nav-pills-custom .nav-link:hover { background-color: #f8f9fa; }
        .nav-pills-custom .nav-link.active { background-color: #e7f1ff; color: #0d6efd; border-color: #0d6efd; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.15);}
        
        .category-item { transition: all 0.2s; border: 1px solid #f0f2f5; }
        .category-item:hover { border-color: #dee2e6; background-color: #f8f9fa; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
        
        .border-dashed { border: 2px dashed #dee2e6 !important; }
        .border-dashed:hover { border-color: #dc3545 !important; background-color: #fff5f5; }
        .border-dashed-success:hover { border-color: #198754 !important; background-color: #f0fdf4; }

        .budget-card { transition: all 0.2s; border: 1px solid transparent; }
        .budget-card:hover { border-color: #e9ecef; transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important; }
        
        /* Loại bỏ mũi tên mặc định của input month */
        input[type="month"]::-webkit-calendar-picker-indicator { cursor: pointer; opacity: 0.6; transition: 0.2s; }
        input[type="month"]::-webkit-calendar-picker-indicator:hover { opacity: 1; }
    </style>
</head>
<body class="bg-light">
    <?php require_once __DIR__ . '/../../components/header.php'; ?>

    <main class="container py-4 mb-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-grid-1x2-fill me-2 text-primary"></i>Danh Mục & Ngân Sách</h3>
        </div>

        <ul class="nav nav-pills nav-pills-custom justify-content-center mb-4" id="budgetTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories-pane" type="button" role="tab">
                    <i class="bi bi-tags-fill me-1"></i> Quản lý Danh mục
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="budgets-tab" data-bs-toggle="tab" data-bs-target="#budgets-pane" type="button" role="tab">
                    <i class="bi bi-safe2-fill me-1"></i> Lập Ngân sách
                </button>
            </li>
        </ul>

        <div class="tab-content" id="budgetTabsContent">
            
            <div class="tab-pane fade show active" id="categories-pane" role="tabpanel">
                <div class="d-flex justify-content-end mb-4">
                    <button class="btn btn-primary rounded-pill fw-bold shadow-sm px-4 py-2" onclick="openAddCategoryModal()">
                        <i class="bi bi-plus-lg me-2"></i>Tạo danh mục mới
                    </button>
                </div>
                
                <div id="categoryTreeContainer">
                    <div class="text-center py-5 text-muted"><div class="spinner-border text-primary"></div><p class="mt-2">Đang tải danh mục...</p></div>
                </div>
            </div>

            <div class="tab-pane fade" id="budgets-pane" role="tabpanel">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                    <div class="d-flex align-items-center bg-white p-1 rounded-pill shadow-sm border" style="width: fit-content;">
                        <button class="btn btn-sm btn-light rounded-circle" onclick="changeMonth(-1)"><i class="bi bi-chevron-left"></i></button>
                        <input type="month" id="monthPicker" class="form-control border-0 bg-transparent fw-bold text-center text-primary px-2 shadow-none" style="width: 140px; cursor: pointer;" onchange="fetchBudgets()">
                        <button class="btn btn-sm btn-light rounded-circle" onclick="changeMonth(1)"><i class="bi bi-chevron-right"></i></button>
                    </div>

                    <button class="btn btn-success rounded-pill fw-bold shadow-sm px-4 py-2" onclick="openAddModal()">
                        <i class="bi bi-plus-lg me-2"></i>Lập Hũ Ngân Sách
                    </button>
                </div>

                <div class="row g-4" id="budgetList">
                    <div class="col-12 text-center py-5 text-muted"><div class="spinner-border text-success"></div><p class="mt-2">Đang tải hũ ngân sách...</p></div>
                </div>
            </div>

        </div>
    </main>

    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content border-0 shadow rounded-4" id="addCategoryForm">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tạo danh mục mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">LOẠI DANH MỤC</label>
                        <select name="type" id="modalCatType" class="form-select form-select-lg" onchange="handleTypeChange()">
                            <option value="expense">Chi tiêu (Đỏ)</option>
                            <option value="income">Thu nhập (Xanh)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="parentSelectBox">
                        <label class="form-label small fw-bold text-muted">THUỘC NHÓM CHA</label>
                        <select name="parent_id" id="modalParentId" class="form-select form-select-lg">
                            <option value="">-- Đang tải nhóm cha --</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">TÊN DANH MỤC CHI TIẾT</label>
                        <input type="text" name="name" class="form-control form-control-lg" placeholder="Ví dụ: Tiền điện, Netflix, Lương..." required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-3 shadow-sm">Lưu danh mục</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="budgetModal" tabindex="-1">
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
                            <label class="form-label fw-semibold text-muted small">DANH MỤC CẦN QUẢN LÝ</label>
                            <select name="category_id" id="budgetCategoryId" class="form-select form-select-lg" required>
                                <option value="" disabled selected>-- Đang tải danh mục --</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted small">GIỚI HẠN CHI (VNĐ)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white text-success fw-bold">₫</span>
                                <input type="text" id="amountDisplay" class="form-control text-end fs-3 fw-bold text-success" placeholder="0" required>
                                <input type="hidden" name="amount_limit" id="amount">
                            </div>
                            <div id="amountInWords" class="form-text text-end fst-italic text-success mt-1" style="font-size: 0.8rem;"></div>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill shadow-sm fw-bold">Lưu Ngân Sách</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
        <div id="liveToast" class="toast align-items-center text-bg-success border-0" role="alert">
            <div class="d-flex"><div class="toast-body fw-semibold" id="toastMessage">Thông báo!</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/app.js?v=<?= time() ?>"></script>
    
    <script>
        const addCatModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
        const bdgModal = new bootstrap.Modal(document.getElementById('budgetModal'));
        const toastEl = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastEl);

        function showToast(message, isSuccess = true) {
            document.getElementById('toastMessage').innerText = message;
            toastEl.className = isSuccess ? 'toast align-items-center text-bg-success border-0' : 'toast align-items-center text-bg-danger border-0';
            toast.show();
        }
        
        async function loadCategoryTree() {
            const res = await fetch('../../controllers/CategoryController.php?action=get_tree');
            const result = await res.json();
            
            const treeContainer = document.getElementById('categoryTreeContainer');
            const modalParentSelect = document.getElementById('modalParentId');
            const budgetCatSelect = document.getElementById('budgetCategoryId');

            let parentOptions = '<option value="" disabled selected>-- Chọn nhóm cha (Bắt buộc) --</option>';
            let budgetOptions = '<option value="" disabled selected>-- Chọn danh mục cần lập ngân sách --</option>';

            if(result.status && result.data.length > 0) {
                const expenses = result.data.filter(c => c.type === 'expense');
                const incomes = result.data.filter(c => c.type === 'income');

                // 1. CỘT CHI TIÊU
                let expenseHtml = '';
                expenses.forEach(parent => {
                    parentOptions += `<option value="${parent.id}">${parent.name}</option>`;
                    let optgroup = `<optgroup label="🔴 ${parent.name}">`;
                    parent.children.forEach(child => { optgroup += `<option value="${child.id}">${child.name}</option>`; });
                    optgroup += `</optgroup>`;
                    budgetOptions += optgroup;

                    expenseHtml += `
                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold text-danger mb-0"><i class="bi bi-folder2-open me-2 fs-5"></i>${parent.name}</h6>
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">${parent.children.length} mục</span>
                        </div>
                        <div class="card-body pt-2 pb-4">
                            <div class="d-flex flex-column gap-2">
                                ${parent.children.map(child => `
                                    <div class="category-item d-flex justify-content-between align-items-center p-3 rounded-4 bg-light">
                                        <span class="fw-semibold text-dark"><i class="bi bi-arrow-return-right me-3 text-muted opacity-50"></i>${child.name}</span>
                                        <button class="btn btn-sm text-danger opacity-50 border-0 p-0 hover-opacity-100" onclick="deleteCategory(${child.id})" title="Xóa danh mục"><i class="bi bi-trash fs-5"></i></button>
                                    </div>
                                `).join('')}
                                <div class="mt-2">
                                    <button class="btn btn-light text-danger opacity-75 w-100 rounded-4 py-2 border-dashed fw-bold" onclick="openQuickAddChild(${parent.id})">
                                        <i class="bi bi-plus-circle me-1"></i> Thêm mục con
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });

                // 2. CỘT THU NHẬP
                let incomeHtml = `
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden sticky-top" style="top: 90px; z-index: 1;">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold text-success mb-0"><i class="bi bi-wallet2 me-2 fs-5"></i>Nguồn Thu Nhập</h6>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">${incomes.length} mục</span>
                    </div>
                    <div class="card-body pt-2 pb-4">
                        <div class="d-flex flex-column gap-2">
                            ${incomes.map(inc => `
                                <div class="category-item d-flex justify-content-between align-items-center p-3 rounded-4 bg-light">
                                    <span class="fw-semibold text-dark"><i class="bi bi-check-circle-fill text-success me-3 opacity-50"></i>${inc.name}</span>
                                    <button class="btn btn-sm text-danger opacity-50 border-0 p-0 hover-opacity-100" onclick="deleteCategory(${inc.id})" title="Xóa danh mục"><i class="bi bi-trash fs-5"></i></button>
                                </div>
                            `).join('')}
                            <div class="mt-2">
                                <button class="btn btn-light text-success opacity-75 w-100 rounded-4 py-2 border-dashed border-dashed-success fw-bold" onclick="openQuickAddIncome()">
                                    <i class="bi bi-plus-circle me-1"></i> Thêm nguồn thu
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;

                treeContainer.innerHTML = `
                <div class="row g-4">
                    <div class="col-12 col-lg-7">${expenseHtml}</div>
                    <div class="col-12 col-lg-5">${incomeHtml}</div>
                </div>`;

                modalParentSelect.innerHTML = parentOptions;
                budgetCatSelect.innerHTML = budgetOptions;

            } else {
                treeContainer.innerHTML = '<div class="text-center py-5 text-muted bg-white rounded-4 shadow-sm border-0"><i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i><p>Chưa có danh mục nào.</p></div>';
            }
        }

        function handleTypeChange() {
            const type = document.getElementById('modalCatType').value;
            const parentBox = document.getElementById('parentSelectBox');
            parentBox.style.display = (type === 'income') ? 'none' : 'block';
            document.getElementById('modalParentId').required = (type === 'expense');
        }

        function openAddCategoryModal() {
            document.getElementById('addCategoryForm').reset();
            handleTypeChange();
            addCatModal.show();
        }

        function openQuickAddChild(parentId) {
            document.getElementById('addCategoryForm').reset();
            document.getElementById('modalCatType').value = 'expense';
            handleTypeChange();
            document.getElementById('modalParentId').value = parentId;
            addCatModal.show();
        }

        function openQuickAddIncome() {
            document.getElementById('addCategoryForm').reset();
            document.getElementById('modalCatType').value = 'income';
            handleTypeChange(); 
            addCatModal.show();
        }

        document.getElementById('addCategoryForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd = new FormData(e.target);
            fd.append('action', 'add');
            const res = await fetch('../../controllers/CategoryController.php', { method: 'POST', body: fd });
            const result = await res.json();
            showToast(result.message, result.status);
            if (result.status) { addCatModal.hide(); loadCategoryTree(); }
        });

        async function deleteCategory(id) {
            if(confirm('Xóa danh mục này? Không thể xóa nếu đã có giao dịch phát sinh.')) {
                const fd = new FormData(); fd.append('action', 'delete'); fd.append('id', id);
                const res = await fetch('../../controllers/CategoryController.php', { method: 'POST', body: fd });
                const result = await res.json();
                showToast(result.message, result.status);
                if(result.status) loadCategoryTree();
            }
        }

        // ==========================================
        // QUẢN LÝ NGÂN SÁCH (ĐỒNG BỘ VỚI DASHBOARD)
        // ==========================================
        const amountInput = document.getElementById('amountDisplay');
        const hiddenAmount = document.getElementById('amount');
        const amountWords = document.getElementById('amountInWords');

        amountInput.addEventListener('input', function(e) {
            let rawValue = e.target.value.replace(/\D/g, "");
            e.target.value = window.formatNumberInput(rawValue);
            hiddenAmount.value = rawValue;
            amountWords.innerText = rawValue ? window.readVietnameseNumber(parseInt(rawValue)) : "";
        });

        function changeMonth(offset) {
            const picker = document.getElementById('monthPicker');
            let [y, m] = picker.value.split('-');
            let date = new Date(y, parseInt(m) - 1 + offset, 1);
            picker.value = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
            fetchBudgets();
        }

        async function fetchBudgets() {
            const pickerVal = document.getElementById('monthPicker').value;
            const [year, month] = pickerVal.split('-');
            
            const res = await fetch(`../../controllers/BudgetController.php?action=get_by_month&month=${month}&year=${year}`);
            const result = await res.json();
            const list = document.getElementById('budgetList');
            
            if (result.data.length === 0) {
                list.innerHTML = `
                <div class="col-12 text-center py-5 text-muted bg-white rounded-4 shadow-sm border-0">
                    <i class="bi bi-safe2 fs-1 mb-3 d-block opacity-25"></i>
                    <p class="mb-4">Tháng này bạn chưa thiết lập hũ ngân sách nào.</p>
                    <button class="btn btn-outline-success rounded-pill fw-bold shadow-sm px-4" onclick="clonePreviousBudgets()">
                        <i class="bi bi-copy me-2"></i>Thừa kế ngân sách tháng trước
                    </button>
                </div>`; 
                return;
            }
            
            let html = '';
            result.data.forEach(b => {
                let pct = b.progress_percentage;
                let barColor = 'bg-success'; let textColor = 'text-success';
                if (pct >= 80 && pct <= 100) { barColor = 'bg-warning'; textColor = 'text-warning text-darken'; }
                if (pct > 100) { barColor = 'bg-danger'; textColor = 'text-danger'; }

                let uiPct = pct > 100 ? 100 : pct; 
                let remainText = b.remain_amount >= 0 
                    ? `<span class="text-muted small fw-semibold">Còn lại:</span> <span class="text-success fw-bold">${b.formatted_remain} đ</span>`
                    : `<span class="text-danger small fw-semibold">Ghi nợ:</span> <span class="text-danger fw-bold">${b.formatted_remain} đ</span>`;

                const catColor = window.getCategoryColor ? window.getCategoryColor(b.category_name) : '#0d6efd';

                html += `
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card shadow-sm border-0 rounded-4 h-100 bg-white budget-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" style="width: 40px; height: 40px; background-color: ${catColor};">
                                        <i class="bi bi-tags-fill"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold text-dark fs-5">${b.category_name}</h6>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light rounded-circle border-0 text-muted" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                        <li><a class="dropdown-item py-2 fw-semibold" href="#" onclick="openEditModal(${b.id}, ${b.amount_limit})"><i class="bi bi-pencil me-2 text-primary"></i>Sửa giới hạn</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item py-2 text-danger fw-semibold" href="#" onclick="deleteBudget(${b.id})"><i class="bi bi-trash me-2"></i>Xóa hũ</a></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <div>
                                    <div class="text-muted small mb-1">Đã chi / Giới hạn</div>
                                    <h6 class="fw-bold mb-0 text-dark">${b.formatted_spent} <span class="text-muted fw-normal small">/ ${b.formatted_limit}</span></h6>
                                </div>
                                <h5 class="fw-bold mb-0 ${textColor}">${pct}%</h5>
                            </div>
                            
                            <div class="progress mb-3 bg-light" style="height: 10px; border-radius: 10px;">
                                <div class="progress-bar ${barColor}" role="progressbar" style="width: ${uiPct}%"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center bg-light rounded-3 px-3 py-2">
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
            document.getElementById('budgetCategoryId').required = true;
            document.getElementById('amountInWords').innerText = '';
            document.getElementById('amount').value = '';
            bdgModal.show();
        }

        function openEditModal(id, currentLimit) {
            document.getElementById('modalTitle').innerText = 'Sửa Giới Hạn Chi';
            document.getElementById('formAction').value = 'update';
            document.getElementById('budgetId').value = id;
            document.getElementById('categoryGroup').style.display = 'none'; 
            document.getElementById('budgetCategoryId').required = false;
            amountInput.value = window.formatNumberInput(currentLimit.toString());
            hiddenAmount.value = currentLimit;
            amountWords.innerText = window.readVietnameseNumber(parseInt(currentLimit));
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
            if(confirm('Xóa hũ ngân sách này? Giao dịch cũ vẫn giữ nguyên, chỉ xóa giới hạn.')) {
                const fd = new FormData(); fd.append('action', 'delete'); fd.append('id', id);
                const res = await fetch('../../controllers/BudgetController.php', { method: 'POST', body: fd });
                const result = await res.json();
                showToast(result.message, result.status);
                if(result.status) fetchBudgets();
            }
        }

        async function clonePreviousBudgets() {
            const pickerVal = document.getElementById('monthPicker').value;
            const [year, month] = pickerVal.split('-');
            const fd = new FormData(); 
            fd.append('action', 'clone_previous'); 
            fd.append('month', parseInt(month));
            fd.append('year', parseInt(year));

            try {
                const res = await fetch('../../controllers/BudgetController.php', { method: 'POST', body: fd });
                const result = await res.json();
                showToast(result.message, result.status);
                if (result.status) fetchBudgets();
            } catch (error) { showToast('Lỗi kết nối máy chủ!', false); }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date();
            const yyyy = today.getFullYear();
            let mm = today.getMonth() + 1;
            document.getElementById('monthPicker').value = `${yyyy}-${mm < 10 ? '0'+mm : mm}`;
            
            loadCategoryTree(); 
            fetchBudgets();
        });
    </script>
</body>
</html>