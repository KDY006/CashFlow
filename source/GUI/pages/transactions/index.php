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
    <title>Sổ Giao Dịch - CashFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* CSS đồng bộ với trang Tổng quan */
        .cf-tab-btn { border: 2px solid transparent; border-radius: 15px; padding: 12px; background-color: #f8f9fa; transition: all 0.2s; text-align: left; position: relative;}
        .cf-tab-btn.active-expense { border-color: #dc3545; background-color: #f8d7da; }
        .cf-tab-btn.active-income { border-color: #198754; background-color: #d1e7dd; }
        .cf-tab-btn h6 { font-size: 0.85rem; color: #6c757d; margin-bottom: 5px; }
        .cf-tab-btn.active-expense h6 { color: #dc3545; }
        .cf-tab-btn.active-income h6 { color: #198754; }

        /* Nút Lọc Danh mục Cha */
        .pill-filters { display: flex; overflow-x: auto; gap: 10px; padding-bottom: 5px; scrollbar-width: none; }
        .pill-filters::-webkit-scrollbar { display: none; }
        .filter-btn { border-radius: 25px; padding: 8px 20px; font-weight: 600; font-size: 0.9rem; transition: all 0.2s; white-space: nowrap; border: 1px solid transparent; background: #fff; color: #6c757d; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .filter-btn:hover { background: #f8f9fa; }
        .filter-btn.active { background: #0d6efd; color: white; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2); }

        /* Card Giao dịch */
        .transaction-card { transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; border: 1px solid #f0f2f5;}
        .transaction-card:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.05) !important; border-color: #e9ecef; }
        
        /* Đồng bộ Modal Sửa */
        .input-group-custom { border: 1px solid #dee2e6; border-radius: 12px; overflow: hidden; background-color: #f8f9fa; transition: all 0.2s; }
        .input-group-custom:focus-within { border-color: #0d6efd; background-color: #fff; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); }
        .input-group-custom .input-group-text { background: transparent; border: none; padding-left: 1.25rem; color: #6c757d; }
        .input-group-custom .form-control, .input-group-custom .form-select { background: transparent; border: none; box-shadow: none; padding-left: 0.5rem; }
    </style>
</head>
<body class="bg-light">
    <?php require_once __DIR__ . '/../../components/header.php'; ?>

    <main class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-journals me-2 text-primary"></i>Sổ Giao Dịch</h3>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-6">
                <button class="w-100 cf-tab-btn active-expense shadow-sm" id="btnTabExpense" onclick="switchMainTab('expense')">
                    <h6><i class="bi bi-arrow-up-circle-fill me-1"></i>Chi tiêu</h6>
                    <h5 class="fw-bold mb-0 text-dark" id="totalExpenseText">0 đ</h5>
                </button>
            </div>
            <div class="col-6">
                <button class="w-100 cf-tab-btn shadow-sm" id="btnTabIncome" onclick="switchMainTab('income')">
                    <h6><i class="bi bi-arrow-down-circle-fill me-1"></i>Thu nhập</h6>
                    <h5 class="fw-bold mb-0 text-dark" id="totalIncomeText">0 đ</h5>
                </button>
            </div>
        </div>

        <div id="parentFiltersWrapper" class="mb-4">
            <div class="pill-filters" id="pillContainer">
                </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4 mb-4 bg-white">
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-12 col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="searchInput" class="form-control border-0 bg-light" placeholder="Tìm theo tên danh mục, ghi chú..." onkeyup="renderTransactions()">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <select id="sortOrder" class="form-select border-0 bg-light fw-semibold" onchange="renderTransactions()">
                            <option value="date_desc">Mới nhất trước</option>
                            <option value="date_asc">Cũ nhất trước</option>
                            <option value="amount_desc">Số tiền cao nhất</option>
                            <option value="amount_asc">Số tiền thấp nhất</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div id="loadingSpinner" class="text-center py-5 text-muted">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Đang tải dữ liệu...</p>
        </div>

        <div id="transactionList" class="pb-5"></div>

    </main>

    <div class="modal fade" id="editTransactionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Chỉnh sửa Giao dịch</h5>
                    <button type="button" class="btn-close bg-light rounded-circle p-2" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 pt-3">
                    <form id="editTransactionForm">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="editTransactionId">

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small mb-1">SỐ TIỀN (VNĐ)</label>
                            <div class="input-group input-group-lg border rounded-4 overflow-hidden" style="transition: 0.2s;">
                                <span class="input-group-text bg-white border-0 text-primary fw-bold fs-4">₫</span>
                                <input type="text" id="editAmountDisplay" class="form-control border-0 text-end fs-3 fw-bold text-primary shadow-none p-3" required>
                                <input type="hidden" name="amount" id="editAmount">
                            </div>
                            <div id="editAmountInWords" class="text-end fst-italic text-primary mt-1 fw-semibold" style="min-height: 20px; font-size: 0.75rem;"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small mb-1">DANH MỤC GIAO DỊCH</label>
                            <div class="input-group input-group-lg input-group-custom">
                                <span class="input-group-text"><i class="bi bi-tags-fill"></i></span>
                                <select name="category_id" id="editCategoryId" class="form-select fw-semibold" required></select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small mb-1">THỜI GIAN</label>
                            <div class="input-group input-group-lg input-group-custom">
                                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                <input type="datetime-local" name="transaction_date" id="editTransactionDate" class="form-control fw-semibold" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small mb-1">GHI CHÚ THÊM</label>
                            <div class="input-group input-group-custom p-2">
                                <textarea name="note" id="editNote" class="form-control border-0" rows="2" placeholder="Chi tiết giao dịch..."></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm fw-bold">
                            <i class="bi bi-save2 me-2"></i>Lưu Thay Đổi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
        <div id="liveToast" class="toast align-items-center border-0 rounded-3 shadow-lg" role="alert">
            <div class="d-flex"><div class="toast-body fw-semibold px-3 py-3" id="toastMessage">Thông báo!</div><button type="button" class="btn-close btn-close-white me-3 m-auto" data-bs-dismiss="toast"></button></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/app.js?v=<?= time() ?>"></script>
    
    <script>
        const editTransModal = new bootstrap.Modal(document.getElementById('editTransactionModal'));
        const toastEl = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastEl);
        
        let allData = [];
        let categoryTree = [];
        let currentMainTab = 'expense'; 
        let currentParentFilter = 'all'; 

        function showToast(message, isSuccess = true) {
            document.getElementById('toastMessage').innerText = message;
            toastEl.className = isSuccess ? 'toast align-items-center text-bg-success border-0 rounded-3 shadow-lg' : 'toast align-items-center text-bg-danger border-0 rounded-3 shadow-lg';
            toast.show();
        }

        const editAmountInput = document.getElementById('editAmountDisplay');
        const editHiddenAmount = document.getElementById('editAmount');
        const editAmountWords = document.getElementById('editAmountInWords');

        editAmountInput.addEventListener('input', function(e) {
            let rawValue = e.target.value.replace(/\D/g, "");
            e.target.value = window.formatNumberInput(rawValue);
            editHiddenAmount.value = rawValue;
            editAmountWords.innerText = rawValue ? window.readVietnameseNumber(parseInt(rawValue)) : "";
        });

        // ĐÃ FIX LỖI HIỂN THỊ NGÀY THÁNG LÚC MỞ MODAL SỬA
        async function openEditModal(id) {
            const res = await fetch(`../../controllers/TransactionController.php?action=get&id=${id}`);
            const result = await res.json();
            if(result.status) {
                document.getElementById('editTransactionId').value = result.data.id;
                
                const amountValue = result.data.amount.split('.')[0];
                editAmountInput.value = window.formatNumberInput(amountValue);
                editHiddenAmount.value = amountValue;
                editAmountWords.innerText = window.readVietnameseNumber(parseInt(amountValue));
                
                document.getElementById('editCategoryId').value = result.data.category_id;
                
                // Format lại ngày tháng chuẩn cho thẻ input datetime-local
                let dbDate = result.data.transaction_date; // Dạng: 2026-05-01 08:30:00
                if(dbDate) {
                    document.getElementById('editTransactionDate').value = dbDate.replace(' ', 'T').substring(0, 16);
                }
                
                document.getElementById('editNote').value = result.data.note;
                editTransModal.show();
            }
        }

        async function fetchCategories() {
            try {
                const res = await fetch('../../controllers/CategoryController.php?action=get_tree');
                const result = await res.json();
                
                if (result.status) {
                    categoryTree = result.data;
                    renderPillFilters();
                    
                    const editSelect = document.getElementById('editCategoryId');
                    editSelect.innerHTML = '<option value="" disabled selected>-- Chọn danh mục --</option>';
                    categoryTree.forEach(parent => {
                        if (parent.type === 'income') {
                            editSelect.innerHTML += `<option value="${parent.id}">🟢 ${parent.name} (Thu)</option>`;
                        } else {
                            let optgroup = `<optgroup label="🔴 ${parent.name}">`;
                            if(parent.children) {
                                parent.children.forEach(child => { optgroup += `<option value="${child.id}">${child.name}</option>`; });
                            }
                            optgroup += `</optgroup>`;
                            editSelect.innerHTML += optgroup;
                        }
                    });
                }
            } catch (e) { console.error(e); }
        }

        function switchMainTab(tab) {
            currentMainTab = tab;
            document.getElementById('btnTabExpense').classList.remove('active-expense');
            document.getElementById('btnTabIncome').classList.remove('active-income');
            
            if (tab === 'expense') {
                document.getElementById('btnTabExpense').classList.add('active-expense');
                document.getElementById('parentFiltersWrapper').style.display = 'block';
            } else {
                document.getElementById('btnTabIncome').classList.add('active-income');
                document.getElementById('parentFiltersWrapper').style.display = 'none';
            }
            currentParentFilter = 'all'; 
            renderPillFilters();
            renderTransactions();
        }

        function renderPillFilters() {
            const container = document.getElementById('pillContainer');
            const expenseParents = categoryTree.filter(c => c.type === 'expense');
            
            let html = `<button class="filter-btn ${currentParentFilter === 'all' ? 'active' : ''}" onclick="setParentFilter('all')">Tất cả chi tiêu</button>`;
            expenseParents.forEach(p => {
                const isActive = currentParentFilter == p.id;
                html += `<button class="filter-btn ${isActive ? 'active' : ''}" onclick="setParentFilter(${p.id})">${p.name}</button>`;
            });
            container.innerHTML = html;
        }

        function setParentFilter(id) {
            currentParentFilter = id;
            renderPillFilters();
            renderTransactions();
        }

        async function fetchTransactions() {
            document.getElementById('loadingSpinner').classList.remove('d-none');
            document.getElementById('transactionList').innerHTML = '';

            const res = await fetch('../../controllers/TransactionController.php?action=get_all');
            const result = await res.json();
            
            if(result.status) {
                allData = result.data.map(item => ({
                    ...item,
                    rawAmount: parseFloat(item.amount),
                    rawDateObj: new Date(item.raw_date) 
                }));
                
                let sumInc = 0; let sumExp = 0;
                allData.forEach(t => { if(t.category_type === 'income') sumInc += t.rawAmount; else sumExp += t.rawAmount; });
                document.getElementById('totalIncomeText').innerText = '+' + window.formatNumberInput(sumInc.toString()) + ' đ';
                document.getElementById('totalExpenseText').innerText = '-' + window.formatNumberInput(sumExp.toString()) + ' đ';

                renderTransactions(); 
            }
            document.getElementById('loadingSpinner').classList.add('d-none');
        }

        function renderTransactions() {
            let filtered = allData.filter(t => t.category_type === currentMainTab);

            const keyword = document.getElementById('searchInput').value.toLowerCase();
            if (keyword) {
                filtered = filtered.filter(t => t.category_name.toLowerCase().includes(keyword) || (t.note && t.note.toLowerCase().includes(keyword)));
            }

            if (currentMainTab === 'expense' && currentParentFilter !== 'all') {
                filtered = filtered.filter(t => t.parent_id == currentParentFilter);
            }

            const sortOrder = document.getElementById('sortOrder').value;
            filtered.sort((a, b) => {
                if (sortOrder === 'date_desc') return b.rawDateObj - a.rawDateObj;
                if (sortOrder === 'date_asc') return a.rawDateObj - b.rawDateObj;
                if (sortOrder === 'amount_desc') return b.rawAmount - a.rawAmount;
                if (sortOrder === 'amount_asc') return a.rawAmount - b.rawAmount;
            });

            const container = document.getElementById('transactionList');
            if (filtered.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-folder-x text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        <h6 class="fw-bold text-dark mt-3">Không có giao dịch</h6>
                        <p class="text-muted small">Thử thay đổi bộ lọc hoặc thêm giao dịch mới.</p>
                    </div>`;
                return;
            }

            const grouped = {};
            filtered.forEach(t => {
                let groupName = '';
                if (currentMainTab === 'income') {
                    groupName = 'Khoản thu'; 
                } else {
                    if (currentParentFilter === 'all') {
                        groupName = t.parent_name; 
                    } else {
                        groupName = t.category_name; 
                    }
                }
                if(!grouped[groupName]) grouped[groupName] = { total: 0, items: [] };
                grouped[groupName].items.push(t);
                grouped[groupName].total += t.rawAmount;
            });

            let html = '';
            for(const [gName, gData] of Object.entries(grouped)) {
                const isInc = currentMainTab === 'income';
                const moneyClass = isInc ? 'text-success' : 'text-danger';
                
                html += `
                <div class="d-flex justify-content-between align-items-end mt-4 mb-3 px-1 border-bottom pb-2">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi ${isInc ? 'bi-box-arrow-in-down-left' : 'bi-tags-fill'} text-secondary me-2"></i>${gName}</h6>
                    <span class="small fw-bold ${moneyClass}">${isInc ? '+' : '-'}${window.formatNumberInput(gData.total.toString())} đ</span>
                </div>`;

                gData.items.forEach(t => {
                    const icon = isInc ? 'bi-arrow-down-left-circle' : 'bi-arrow-up-right-circle';
                    const catColor = window.getCategoryColor ? window.getCategoryColor(t.category_name) : '#0d6efd';
                    
                    html += `
                    <div class="card transaction-card rounded-4 bg-white mb-2" onclick="openEditModal(${t.id})">
                        <div class="card-body p-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: ${catColor}15; color: ${catColor}; border: 1px solid ${catColor}30;">
                                    <i class="bi ${icon} fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">${t.category_name}</h6>
                                    <small class="text-muted d-block"><i class="bi bi-clock me-1"></i>${t.raw_date.replace('T', ' ')}</small>
                                    ${t.note ? `<small class="text-secondary fst-italic"><i class="bi bi-chat-left-text me-1"></i> ${t.note}</small>` : ''}
                                </div>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-1 fw-bold ${moneyClass}">${isInc ? '+' : '-'}${t.formatted_amount} đ</h6>
                                
                                <div class="text-muted fst-italic mb-2" style="font-size: 0.75rem; max-width: 180px; text-align: right; margin-left: auto; line-height: 1.2;">
                                    ${window.readVietnameseNumber ? window.readVietnameseNumber(t.rawAmount) : ''}
                                </div>
                                
                                <button onclick="event.stopPropagation(); deleteTransaction(${t.id})" class="btn btn-sm text-danger opacity-50 border-0 p-0 hover-opacity-100" title="Xóa giao dịch"><i class="bi bi-trash fs-5"></i></button>
                            </div>
                        </div>
                    </div>`;
                });
            }
            container.innerHTML = html;
        }

        document.getElementById('editTransactionForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const res = await fetch('../../controllers/TransactionController.php', { method: 'POST', body: new FormData(e.target) });
            const result = await res.json();
            showToast(result.message, result.status);
            if (result.status) { editTransModal.hide(); fetchTransactions(); } 
        });

        async function deleteTransaction(id) {
            if(confirm('Bạn có chắc muốn xóa giao dịch này? Hệ thống sẽ cập nhật lại toàn bộ báo cáo và ngân sách.')) {
                const fd = new FormData(); fd.append('action', 'delete'); fd.append('id', id);
                const res = await fetch('../../controllers/TransactionController.php', { method: 'POST', body: fd });
                const result = await res.json();
                showToast(result.message, result.status);
                if(result.status) fetchTransactions(); 
            }
        }

        document.addEventListener('DOMContentLoaded', () => { 
            fetchCategories(); 
            fetchTransactions(); 
        });
    </script>
</body>
</html>