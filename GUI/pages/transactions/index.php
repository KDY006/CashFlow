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
    <style>
        /* CSS tuỳ chỉnh cho giao diện 2 cột */
        .transaction-card { transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; }
        .transaction-card:hover { transform: translateY(-3px); box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important; }
        .col-income .card-header { background-color: #e8f5e9; color: #198754; }
        .col-expense .card-header { background-color: #fce4e4; color: #dc3545; }

        /* CSS cho chức năng Gom nhóm (Accordion) */
        .category-group-header { cursor: pointer; transition: background-color 0.2s; }
        .category-group-header:hover { background-color: #f8f9fa; }
        .category-items-container { display: none; border-top: 1px solid #f0f2f5; margin-top: 10px; padding-top: 10px;}
        .category-items-container.active { display: block; animation: slideDown 0.3s ease-out forwards; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        
        .mini-transaction-item { transition: background-color 0.2s; cursor: pointer; border-bottom: 1px dashed #dee2e6; padding: 12px 10px; border-radius: 8px;}
        .mini-transaction-item:last-child { border-bottom: none; }
        .mini-transaction-item:hover { background-color: #e9ecef; }
        
        .chevron-icon { transition: transform 0.3s; }
        .rotate-180 { transform: rotate(180deg); }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../../components/header.php'; ?>

    <main class="container py-4">
        <!-- TIÊU ĐỀ & NÚT -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-journals me-2 text-primary"></i>Sổ Giao Dịch</h3>
            <button class="btn btn-outline-secondary rounded-pill px-4 shadow-sm fw-bold" onclick="openCategoryModal()">
                <i class="bi bi-tags-fill me-1 text-primary"></i> Quản lý danh mục
            </button>
        </div>

        <!-- THANH CÔNG CỤ (TOOLBAR) -->
        <div class="card shadow-sm border-0 rounded-4 mb-4 bg-white">
            <div class="card-body p-3">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text" id="searchInput" class="form-control border-0 bg-light" placeholder="Tìm tên danh mục, ghi chú..." onkeyup="applyFilters()">
                        </div>
                    </div>
                    
                    <!-- Dropdown Chế độ xem -->
                    <div class="col-6 col-md-2">
                        <select id="viewMode" class="form-select border-0 bg-primary bg-opacity-10 text-primary fw-bold" onchange="applyFilters()">
                            <option value="list">Dạng danh sách</option>
                            <option value="group">Gom theo danh mục</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <select id="filterType" class="form-select border-0 bg-light" onchange="applyFilters()">
                            <option value="all">Tất cả (Thu/Chi)</option>
                            <option value="income">Chỉ Khoản Thu</option>
                            <option value="expense">Chỉ Khoản Chi</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <select id="sortOrder" class="form-select border-0 bg-light" onchange="applyFilters()">
                            <option value="date_desc">Mới nhất</option>
                            <option value="date_asc">Cũ nhất</option>
                            <option value="amount_desc">Tiền cao nhất</option>
                            <option value="amount_asc">Tiền thấp nhất</option>
                        </select>
                    </div>
                    
                    <div class="col-6 col-md-2 text-end">
                        <span class="badge bg-secondary rounded-pill px-3 py-2 w-100" id="totalCountBadge">0 giao dịch</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- KHU VỰC HIỂN THỊ DỮ LIỆU (CHIA 2 CỘT) -->
        <div id="loadingSpinner" class="text-center py-5 text-muted">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Đang đồng bộ dữ liệu...</p>
        </div>

        <div id="emptyState" class="text-center py-5 px-3 d-none">
            <i class="bi bi-folder-x text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
            <h5 class="fw-bold text-dark mt-3">Không có dữ liệu</h5>
            <p class="text-muted mb-0">Chưa có giao dịch nào khớp với bộ lọc của bạn.</p>
        </div>

        <div class="row g-4 d-none" id="dataContainer">
            <!-- Cột Khoản Thu -->
            <div class="col-12 col-lg-6 col-income">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-transparent">
                    <div class="card-header border-0 py-3 d-flex justify-content-between align-items-center rounded-4 mb-3 shadow-sm">
                        <h6 class="fw-bold mb-0"><i class="bi bi-arrow-down-circle-fill me-2"></i>KHOẢN THU</h6>
                        <h6 class="fw-bold mb-0" id="totalIncomeText">+0 ₫</h6>
                    </div>
                    <div id="incomeList"></div>
                </div>
            </div>
            
            <!-- Cột Khoản Chi -->
            <div class="col-12 col-lg-6 col-expense">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-transparent">
                    <div class="card-header border-0 py-3 d-flex justify-content-between align-items-center rounded-4 mb-3 shadow-sm">
                        <h6 class="fw-bold mb-0"><i class="bi bi-arrow-up-circle-fill me-2"></i>KHOẢN CHI</h6>
                        <h6 class="fw-bold mb-0" id="totalExpenseText">-0 ₫</h6>
                    </div>
                    <div id="expenseList"></div>
                </div>
            </div>
        </div>
    </main>

    <!-- [CÁC MODAL THÊM/SỬA/DANH MỤC ĐƯỢC GIỮ NGUYÊN] -->
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
                                <input type="text" id="editAmountDisplay" class="form-control text-end fs-4 fw-bold text-success" required>
                                <input type="hidden" name="amount" id="editAmount">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">DANH MỤC</label>
                            <select name="category_id" id="editCategoryId" class="form-select form-select-lg" required></select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">NGÀY</label>
                            <input type="date" name="transaction_date" id="editTransactionDate" class="form-control form-control-lg" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted small">GHI CHÚ</label>
                            <textarea name="note" id="editNote" class="form-control" rows="2"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm fw-bold">Cập Nhật</button>
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
                            <div class="col-12"><input type="text" name="name" id="catName" class="form-control" placeholder="Tên danh mục mới..." required></div>
                            <div class="col-8">
                                <select name="type" id="catType" class="form-select">
                                    <option value="expense">Khoản Chi (Đỏ)</option>
                                    <option value="income">Khoản Thu (Xanh)</option>
                                </select>
                            </div>
                            <div class="col-4"><button type="submit" class="btn btn-dark w-100 fw-bold">Thêm</button></div>
                        </div>
                    </form>
                    <hr class="text-muted">
                    <div id="categoryList" class="list-group list-group-flush border rounded-3"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
        <div id="liveToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex"><div class="toast-body fw-semibold" id="toastMessage">Thông báo!</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../../components/bottom-nav.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const editTransModal = new bootstrap.Modal(document.getElementById('editTransactionModal'));
        const catModal = new bootstrap.Modal(document.getElementById('categoryModal'));
        const toastEl = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastEl);
        let allData = [];

        function showToast(message, isSuccess = true) {
            document.getElementById('toastMessage').innerText = message;
            toastEl.className = isSuccess ? 'toast align-items-center text-bg-success border-0' : 'toast align-items-center text-bg-danger border-0';
            toast.show();
        }

        const editAmountInput = document.getElementById('editAmountDisplay');
        const editHiddenAmount = document.getElementById('editAmount');
        editAmountInput.addEventListener('input', function(e) {
            let rawValue = e.target.value.replace(/\D/g, "");
            e.target.value = window.formatNumberInput(rawValue);
            editHiddenAmount.value = rawValue;
        });

        async function fetchCategories() {
            const res = await fetch('../../controllers/CategoryController.php?action=get_all');
            const result = await res.json();
            const list = document.getElementById('categoryList');
            const editSelect = document.getElementById('editCategoryId');
            
            list.innerHTML = ''; editSelect.innerHTML = '<option value="" disabled selected>-- Chọn danh mục --</option>';

            result.data.forEach(c => {
                const typeText = c.type === 'income' ? 'Thu' : 'Chi';
                
                // GỌI HÀM TỪ APP.JS
                const catColor = window.getCategoryColor(c.name);

                editSelect.innerHTML += `<option value="${c.id}">${c.name} (${typeText})</option>`;
                list.innerHTML += `
                    <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                        <div class="d-flex align-items-center">
                            <span class="rounded-circle me-3" style="width: 16px; height: 16px; background-color: ${catColor}; display: inline-block;"></span>
                            <span class="fw-semibold">${c.name}</span>
                            <small class="ms-2 text-muted">(${typeText})</small>
                        </div>
                        <button onclick="deleteCategory(${c.id})" class="btn btn-sm text-danger"><i class="bi bi-x-circle"></i></button>
                    </div>`;
            });
        }

        function applyFilters() {
            let filteredData = [...allData];

            const keyword = document.getElementById('searchInput').value.toLowerCase();
            if (keyword) {
                filteredData = filteredData.filter(item => 
                    item.category_name.toLowerCase().includes(keyword) || 
                    (item.note && item.note.toLowerCase().includes(keyword))
                );
            }

            const typeFilter = document.getElementById('filterType').value;
            if (typeFilter !== 'all') {
                filteredData = filteredData.filter(item => item.category_type === typeFilter);
            }

            const sortOrder = document.getElementById('sortOrder').value;
            filteredData.sort((a, b) => {
                if (sortOrder === 'date_desc') return b.rawDate - a.rawDate;
                if (sortOrder === 'date_asc') return a.rawDate - b.rawDate;
                if (sortOrder === 'amount_desc') return b.rawAmount - a.rawAmount;
                if (sortOrder === 'amount_asc') return a.rawAmount - b.rawAmount;
            });

            const viewMode = document.getElementById('viewMode').value;
            if (viewMode === 'group') renderGroupedUI(filteredData);
            else renderListUI(filteredData);
        }

        function renderListUI(data) {
            const container = document.getElementById('dataContainer');
            const emptyState = document.getElementById('emptyState');
            document.getElementById('totalCountBadge').innerText = `${data.length} giao dịch`;

            if (data.length === 0) { container.classList.add('d-none'); emptyState.classList.remove('d-none'); return; }
            container.classList.remove('d-none'); emptyState.classList.add('d-none');

            let htmlInc = ''; let htmlExp = ''; let totalInc = 0; let totalExp = 0;

            data.forEach(t => {
                const isInc = t.category_type === 'income';
                const sign = isInc ? '+' : '-';
                const moneyClass = isInc ? 'text-success' : 'text-danger';
                const icon = isInc ? 'bi-arrow-down-left-circle' : 'bi-arrow-up-right-circle';
                
                // GỌI HÀM TỪ APP.JS
                const catColor = window.getCategoryColor(t.category_name); 
                
                if (isInc) totalInc += t.rawAmount; else totalExp += t.rawAmount;

                const cardHtml = `
                    <div class="card transaction-card border-0 shadow-sm rounded-4 mb-3 bg-white" onclick="openEditModal(${t.id})">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: ${catColor}26; color: ${catColor};">
                                        <i class="bi ${icon} fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark">${t.category_name}</h6>
                                        <small class="text-muted d-block">${t.formatted_date}</small>
                                        ${t.note ? `<small class="text-secondary fst-italic"><i class="bi bi-chat-left-text me-1"></i>${t.note}</small>` : ''}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-2 fw-bold ${moneyClass}">${sign}${t.formatted_amount} ₫</h6>
                                    <button onclick="event.stopPropagation(); deleteTransaction(${t.id})" class="btn btn-sm btn-outline-danger border-0 rounded-circle"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>`;

                if (isInc) htmlInc += cardHtml; else htmlExp += cardHtml;
            });

            document.getElementById('incomeList').innerHTML = htmlInc || '<div class="text-center py-4 text-muted small">Không có khoản thu nào.</div>';
            document.getElementById('expenseList').innerHTML = htmlExp || '<div class="text-center py-4 text-muted small">Không có khoản chi nào.</div>';
            document.getElementById('totalIncomeText').innerText = '+' + window.formatNumberInput(totalInc.toString()) + ' ₫';
            document.getElementById('totalExpenseText').innerText = '-' + window.formatNumberInput(totalExp.toString()) + ' ₫';
        }

        function renderGroupedUI(data) {
            const container = document.getElementById('dataContainer');
            const emptyState = document.getElementById('emptyState');
            document.getElementById('totalCountBadge').innerText = `${data.length} giao dịch`;

            if (data.length === 0) { container.classList.add('d-none'); emptyState.classList.remove('d-none'); return; }
            container.classList.remove('d-none'); emptyState.classList.add('d-none');

            const grouped = { income: {}, expense: {} };
            let totalInc = 0; let totalExp = 0;

            data.forEach(t => {
                const type = t.category_type; const catName = t.category_name;
                if (!grouped[type][catName]) grouped[type][catName] = { name: catName, total: 0, items: [] };
                grouped[type][catName].total += t.rawAmount;
                grouped[type][catName].items.push(t);
                if (type === 'income') totalInc += t.rawAmount; else totalExp += t.rawAmount;
            });

            const generateGroupHtml = (groupData, isInc) => {
                let html = ''; const sign = isInc ? '+' : '-';
                const sortedGroups = Object.values(groupData).sort((a, b) => b.total - a.total);

                sortedGroups.forEach((grp, idx) => {
                    const groupId = `grp_${isInc ? 'inc' : 'exp'}_${idx}`;
                    const moneyClass = isInc ? 'text-success' : 'text-danger';
                    
                    // GỌI HÀM TỪ APP.JS
                    const catColor = window.getCategoryColor(grp.name);
                    
                    let itemsHtml = '';
                    grp.items.forEach(t => {
                        itemsHtml += `
                            <div class="mini-transaction-item d-flex justify-content-between align-items-center" onclick="openEditModal(${t.id})">
                                <div>
                                    <small class="fw-semibold d-block text-dark">${t.formatted_date}</small>
                                    ${t.note ? `<small class="text-muted fst-italic"><i class="bi bi-arrow-return-right me-1"></i>${t.note}</small>` : ''}
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold ${moneyClass} small">${sign}${t.formatted_amount} ₫</span>
                                    <button onclick="event.stopPropagation(); deleteTransaction(${t.id})" class="btn btn-sm text-danger ms-2 p-0"><i class="bi bi-x-circle"></i></button>
                                </div>
                            </div>`;
                    });

                    html += `
                        <div class="card border-0 shadow-sm mb-3 rounded-4 overflow-hidden bg-white">
                            <div class="category-group-header d-flex justify-content-between align-items-center p-3" onclick="toggleAccordion('${groupId}')">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: ${catColor}26; color: ${catColor};">
                                        <i class="bi bi-folder2-open fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark">${grp.name}</h6>
                                        <small class="text-muted">${grp.items.length} khoản</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <h6 class="mb-0 fw-bold ${moneyClass}">${sign}${window.formatNumberInput(grp.total.toString())} ₫</h6>
                                    <i class="bi bi-chevron-down text-muted chevron-icon fs-5" id="icon_${groupId}"></i>
                                </div>
                            </div>
                            <div class="category-items-container px-3 pb-2" id="${groupId}">${itemsHtml}</div>
                        </div>`;
                });
                return html;
            };

            document.getElementById('incomeList').innerHTML = generateGroupHtml(grouped.income, true) || '<div class="text-center py-4 text-muted small">Không có khoản thu nào.</div>';
            document.getElementById('expenseList').innerHTML = generateGroupHtml(grouped.expense, false) || '<div class="text-center py-4 text-muted small">Không có khoản chi nào.</div>';
            document.getElementById('totalIncomeText').innerText = '+' + window.formatNumberInput(totalInc.toString()) + ' ₫';
            document.getElementById('totalExpenseText').innerText = '-' + window.formatNumberInput(totalExp.toString()) + ' ₫';
        }

        window.toggleAccordion = function(groupId) {
            const container = document.getElementById(groupId);
            const icon = document.getElementById('icon_' + groupId);
            if (container.classList.contains('active')) { container.classList.remove('active'); icon.classList.remove('rotate-180'); } 
            else { container.classList.add('active'); icon.classList.add('rotate-180'); }
        };

        async function fetchTransactions() {
            document.getElementById('loadingSpinner').classList.remove('d-none');
            document.getElementById('dataContainer').classList.add('d-none');
            document.getElementById('emptyState').classList.add('d-none');

            const res = await fetch('../../controllers/TransactionController.php?action=get_all');
            const result = await res.json();
            
            if(result.status) {
                allData = result.data.map(item => ({
                    ...item,
                    rawAmount: parseFloat(item.amount),
                    rawDate: new Date(item.formatted_date.split('/').reverse().join('-')) 
                }));
                applyFilters(); 
            }
            document.getElementById('loadingSpinner').classList.add('d-none');
        }

        async function openEditModal(id) {
            const res = await fetch(`../../controllers/TransactionController.php?action=get&id=${id}`);
            const result = await res.json();
            if(result.status) {
                document.getElementById('editTransactionId').value = result.data.id;
                const amountValue = result.data.amount.split('.')[0];
                editAmountInput.value = window.formatNumberInput(amountValue);
                editHiddenAmount.value = amountValue;
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

        function openCategoryModal() { catModal.show(); }

        document.addEventListener('DOMContentLoaded', () => { fetchCategories(); fetchTransactions(); });
    </script>
</body>
</html>