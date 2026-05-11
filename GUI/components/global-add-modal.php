<style>
    /* CSS Tùy chỉnh riêng cho Modal Thêm Giao Dịch */
    .modal-add-trans .nav-pills .nav-link { transition: all 0.2s ease; color: #6c757d; }
    .modal-add-trans .nav-pills .nav-link.active { background-color: #0d6efd; color: #fff !important; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2); }
    
    .modal-add-trans .input-group-amount { border: 2px solid #e9ecef; transition: all 0.2s; background: #fff; }
    .modal-add-trans .input-group-amount:focus-within { border-color: #198754; box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.1); }
    
    .modal-add-trans .form-control-custom { border: 1px solid #dee2e6; background-color: #f8f9fa; transition: all 0.2s; }
    .modal-add-trans .form-control-custom:focus { background-color: #fff; border-color: #0d6efd; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); }

    .ai-textarea { border: 2px solid #cfe2ff; border-radius: 20px; resize: none; transition: 0.3s; }
    .ai-textarea:focus { border-color: #0d6efd; box-shadow: 0 4px 15px rgba(13,110,253,0.1); background-color: #fff !important; }
    
    .ai-icon-wrapper { width: 80px; height: 80px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #e7f1ff, #cfe2ff); border-radius: 50%; box-shadow: 0 8px 20px rgba(13,110,253,0.15); margin-bottom: 15px; }
</style>

<div class="modal fade modal-add-trans" id="globalTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success p-2 rounded-circle me-2 d-inline-flex"><i class="bi bi-wallet2"></i></div>
                    Ghi chép giao dịch
                </h5>
                <button type="button" class="btn-close bg-light rounded-circle p-2" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-4 pt-3">
                
                <ul class="nav nav-pills nav-fill bg-light border rounded-pill p-1 mb-4" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill fw-bold" data-bs-toggle="tab" data-bs-target="#manualTab">
                            <i class="bi bi-pencil-square me-1"></i>Nhập thủ công
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill fw-bold" data-bs-toggle="tab" data-bs-target="#aiTab">
                            <i class="bi bi-stars text-warning me-1"></i>AI Phân tích
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    
                    <div class="tab-pane fade show active" id="manualTab">
                        <form id="globalTransactionForm">
                            <input type="hidden" name="action" value="add">

                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted small mb-1">SỐ TIỀN (VNĐ)</label>
                                <div class="input-group input-group-lg input-group-amount rounded-4 overflow-hidden">
                                    <span class="input-group-text bg-transparent border-0 text-success fw-bold fs-4">₫</span>
                                    <input type="text" id="globalAmountDisplay" class="form-control border-0 text-end fs-3 fw-bold text-success shadow-none p-3" placeholder="0" required>
                                    <input type="hidden" name="amount" id="globalAmount">
                                </div>
                                <div id="globalAmountInWords" class="text-end fst-italic text-success mt-1 fw-semibold" style="min-height: 20px; font-size: 0.75rem;"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted small mb-1">DANH MỤC GIAO DỊCH</label>
                                <select name="category_id" id="globalCategoryId" class="form-select form-control-custom rounded-4 py-3" required>
                                    <option value="" disabled selected>-- Đang tải danh mục --</option>
                                </select>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="form-label fw-bold text-muted small mb-1">THỜI GIAN</label>
                                    <input type="datetime-local" name="transaction_date" id="globalTransactionDate" class="form-control form-control-custom rounded-4 py-3" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-muted small mb-1">GHI CHÚ THÊM</label>
                                    <textarea name="note" id="globalNote" class="form-control form-control-custom rounded-4 py-3" rows="2" placeholder="Chi tiết giao dịch (không bắt buộc)..."></textarea>
                                </div>
                            </div>

                            <button type="submit" id="btnSubmitTrans" class="btn btn-success btn-lg w-100 rounded-pill shadow-sm fw-bold">
                                <i class="bi bi-check2-circle me-2"></i>Lưu Giao Dịch
                            </button>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="aiTab">
                        <div class="text-center mb-4 mt-2">
                            <div class="ai-icon-wrapper">
                                <i class="bi bi-robot text-primary" style="font-size: 2.8rem;"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Tự động nhận diện giao dịch</h5>
                            <p class="text-muted small px-2 mb-0">Viết tự do điều bạn vừa chi tiêu, AI sẽ tự động hiểu và ghi chép thay bạn.</p>
                        </div>
                        
                        <div class="mb-4 position-relative">
                            <textarea id="aiTransactionInput" class="form-control form-control-lg ai-textarea bg-light p-3" rows="3" placeholder="Ví dụ: Đổ xăng 60k, ăn sáng phở cuốn 45k..."></textarea>
                        </div>
                        
                        <button type="button" id="btnAiParse" onclick="parseAiTransaction()" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm fw-bold">
                            <i class="bi bi-magic me-2"></i>Thêm giao dịch
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <div id="globalLiveToast" class="toast align-items-center border-0 rounded-3 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-semibold px-3 py-3" id="globalToastMessage">Thông báo!</div>
            <button type="button" class="btn-close btn-close-white me-3 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
    let globalTransModal = null;

    // HÀM HIỂN THỊ THÔNG BÁO CHUẨN GIAO DIỆN (Thay thế hoàn toàn alert)
    function showGlobalToast(message, isSuccess = true) {
        // Ưu tiên dùng hàm showToast của trang gốc nếu có
        if (typeof window.showToast === 'function') {
            window.showToast(message, isSuccess);
            return;
        }
        
        // Nếu trang gốc không có, dùng Toast độc quyền của Modal
        const toastEl = document.getElementById('globalLiveToast');
        const msgEl = document.getElementById('globalToastMessage');
        if (toastEl && msgEl) {
            msgEl.innerText = message;
            toastEl.className = isSuccess 
                ? 'toast align-items-center text-bg-success border-0 rounded-3 shadow-lg' 
                : 'toast align-items-center text-bg-danger border-0 rounded-3 shadow-lg';
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
    }

    if (typeof formatNumberInput === 'undefined') {
        window.formatNumberInput = function(value) { value = value.replace(/\D/g, ""); return value.replace(/\B(?=(\d{3})+(?!\d))/g, "."); };
    }

    if (typeof readVietnameseNumber === 'undefined') {
        window.readVietnameseNumber = function(number) {
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
            do { let block = temp % 1000; if (block > 0) { let s = readThreeDigits(block, temp < 1000); str = s + levels[i] + " " + str; } temp = Math.floor(temp / 1000); i++; } while (temp > 0);
            return str.trim().charAt(0).toUpperCase() + str.trim().slice(1) + " đồng";
        };
    }

    const gAmountInput = document.getElementById('globalAmountDisplay');
    const gHiddenAmount = document.getElementById('globalAmount');
    const gAmountWords = document.getElementById('globalAmountInWords');

    gAmountInput.addEventListener('input', function(e) {
        let rawValue = e.target.value.replace(/\D/g, "");
        e.target.value = formatNumberInput(rawValue);
        gHiddenAmount.value = rawValue;
        gAmountWords.innerText = rawValue ? readVietnameseNumber(parseInt(rawValue)) : "";
    });

    async function openGlobalAddModal() {
        if (!globalTransModal) globalTransModal = new bootstrap.Modal(document.getElementById('globalTransactionModal'));
        document.getElementById('globalTransactionForm').reset();
        document.getElementById('aiTransactionInput').value = '';

        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('globalTransactionDate').value = now.toISOString().slice(0, 16);
        gAmountWords.innerText = ''; gHiddenAmount.value = '';

        const select = document.getElementById('globalCategoryId');
        select.innerHTML = '<option value="" disabled selected>-- Chọn danh mục --</option>';

        try {
            const res = await fetch('../../controllers/CategoryController.php?action=get_tree');
            const result = await res.json();
            if (result.status) {
                result.data.forEach(parent => {
                    if (parent.type === 'income') {
                        select.innerHTML += `<option value="${parent.id}">🟢 ${parent.name} (Thu)</option>`;
                    } else {
                        let optgroup = `<optgroup label="🔴 ${parent.name}">`;
                        if (parent.children && parent.children.length > 0) {
                            parent.children.forEach(child => { optgroup += `<option value="${child.id}">${child.name}</option>`; });
                        }
                        optgroup += `</optgroup>`;
                        select.innerHTML += optgroup;
                    }
                });
            }
        } catch (error) { console.error(error); }

        globalTransModal.show();
    }

    // XỬ LÝ AI BÓC TÁCH VÀ TỰ ĐỘNG LƯU
    async function parseAiTransaction() {
        const text = document.getElementById('aiTransactionInput').value;
        if (!text) return;

        const btn = document.getElementById('btnAiParse');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
        btn.disabled = true;

        const fd = new FormData();
        fd.append('action', 'parse_text');
        fd.append('text', text);

        try {
            const res = await fetch('../../controllers/AiController.php', { method: 'POST', body: fd });
            const result = await res.json();

            if (result.status && result.data.amount) {
                // Đổ dữ liệu vào Form Thủ Công
                gAmountInput.value = formatNumberInput(result.data.amount.toString());
                gHiddenAmount.value = result.data.amount;
                if (result.data.category_id) document.getElementById('globalCategoryId').value = result.data.category_id;
                if (result.data.note) document.getElementById('globalNote').value = result.data.note;
                
                // Tự động Gửi Form (Kích hoạt lưu Database)
                document.getElementById('btnSubmitTrans').click();
                
            } else {
                // Đã thay thế alert bằng Toast
                showGlobalToast("AI không hiểu dữ liệu. Vui lòng nhập rõ hơn!", false);
            }
        } catch (error) {
            showGlobalToast('Lỗi kết nối máy chủ AI!', false);
        } finally {
            btn.innerHTML = '<i class="bi bi-magic me-2"></i>Trích xuất & Thêm nhanh';
            btn.disabled = false;
        }
    }

    document.getElementById('globalTransactionForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const res = await fetch('../../controllers/TransactionController.php', { method: 'POST', body: new FormData(e.target) });
        const result = await res.json();
        
        // Đã thay thế alert bằng Toast
        showGlobalToast(result.message, result.status);

        if (result.status) {
            if (globalTransModal) globalTransModal.hide();
            // Tải lại các giao diện nếu đang đứng ở trang tương ứng
            if (typeof fetchTransactions === 'function') fetchTransactions();
            if (typeof fetchBudgets === 'function') fetchBudgets();
            if (typeof loadDashboardData === 'function') loadDashboardData();
            if (typeof loadCalendarData === 'function') loadCalendarData();
        }
    });
</script>