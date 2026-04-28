<div class="modal fade" id="globalTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Thêm Giao Dịch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="globalTransactionForm">
                    <input type="hidden" name="action" value="add">

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">SỐ TIỀN (VNĐ)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white text-success fw-bold">₫</span>
                            <input type="text" id="globalAmountDisplay" class="form-control text-end fs-4 fw-bold text-success" placeholder="0" required>
                            <input type="hidden" name="amount" id="globalAmount">
                        </div>
                        <div id="globalAmountInWords" class="form-text text-end fst-italic text-success mt-1" style="min-height: 20px;"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">DANH MỤC</label>
                        <select name="category_id" id="globalCategoryId" class="form-select form-select-lg" required>
                            <option value="" disabled selected>-- Đang tải danh mục --</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">NGÀY</label>
                        <input type="date" name="transaction_date" id="globalTransactionDate" class="form-control form-control-lg" required max="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted small">GHI CHÚ</label>
                        <textarea name="note" class="form-control" rows="2"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill shadow-sm fw-bold">Lưu Giao Dịch</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // KHÔNG khởi tạo Modal ngay lúc load trang nữa
    let globalTransModal = null;

    // --- CÁC HÀM DÙNG CHUNG ---
    if (typeof formatNumberInput === 'undefined') {
        window.formatNumberInput = function(value) {
            value = value.replace(/\D/g, ""); 
            return value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        };
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

    // --- XỬ LÝ NHẬP TIỀN ---
    const gAmountInput = document.getElementById('globalAmountDisplay');
    const gHiddenAmount = document.getElementById('globalAmount');
    const gAmountWords = document.getElementById('globalAmountInWords');

    gAmountInput.addEventListener('input', function(e) {
        let rawValue = e.target.value.replace(/\D/g, "");
        e.target.value = formatNumberInput(rawValue);
        gHiddenAmount.value = rawValue;
        gAmountWords.innerText = rawValue ? readVietnameseNumber(parseInt(rawValue)) : "";
    });

    // --- MỞ MODAL VÀ NẠP DANH MỤC ---
    async function openGlobalAddModal() {
        // Lúc này mới khởi tạo Modal (đảm bảo Bootstrap đã load xong)
        if (!globalTransModal) {
            globalTransModal = new bootstrap.Modal(document.getElementById('globalTransactionModal'));
        }

        document.getElementById('globalTransactionForm').reset();
        document.getElementById('globalTransactionDate').valueAsDate = new Date();
        gAmountWords.innerText = '';
        gHiddenAmount.value = '';

        const res = await fetch('../../controllers/CategoryController.php?action=get_all');
        const result = await res.json();
        const select = document.getElementById('globalCategoryId');
        select.innerHTML = '<option value="" disabled selected>-- Chọn danh mục --</option>';
        result.data.forEach(c => {
            const typeText = c.type === 'income' ? 'Thu' : 'Chi';
            select.innerHTML += `<option value="${c.id}">${c.name} (${typeText})</option>`;
        });

        globalTransModal.show();
    }

    // --- SUBMIT FORM BẰNG AJAX ---
    document.getElementById('globalTransactionForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const res = await fetch('../../controllers/TransactionController.php', { method: 'POST', body: new FormData(e.target) });
        const result = await res.json();
        
        if (typeof showToast === 'function') {
            showToast(result.message, result.status);
        } else {
            alert(result.message);
        }

        if (result.status) {
            if (globalTransModal) globalTransModal.hide();
            if (typeof fetchTransactions === 'function') fetchTransactions();
            if (typeof fetchBudgets === 'function') fetchBudgets();
        }
    });
</script>