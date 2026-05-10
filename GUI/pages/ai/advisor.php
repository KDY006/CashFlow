<?php
// Tệp: GUI/pages/ai/advisor.php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../autoload.php';

$aiBus = new AiAdvisorBUS();
$userId = $_SESSION['user_id'];

// Lấy lịch sử chat
$chatHistory = $aiBus->getUserInsights($userId);

// Kiểm tra đếm ngược 24h
$cooldown = $aiBus->checkCooldown($userId);
$canChat = $cooldown['can_consult'];
$hoursLeft = $cooldown['hours_left'] ?? 0;

// Xử lý gom nhóm tin nhắn theo ngày
$groupedHistory = [];
$typeToUserMsg = [
    'anomaly' => 'Tìm điểm lãng phí & Cảnh báo',
    'warning' => 'Tìm điểm lãng phí & Cảnh báo',
    'advice'  => 'Cho tôi lời khuyên tiết kiệm',
    'forecast'=> 'Dự báo tài chính cuối tháng',
    'summary' => 'Tóm tắt tình hình tháng này'
];

foreach (array_reverse($chatHistory) as $msg) {
    $dateStr = date('Y-m-d', strtotime($msg['created_at']));
    $groupedHistory[$dateStr][] = $msg;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cố vấn Chatbot AI - CashFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* Tùy chỉnh Full màn hình cho trang Chatbot */
        html, body { height: 100%; margin: 0; overflow: hidden; }
        body { display: flex; flex-direction: column; background-color: #f0f2f5; }
        
        .main-chat-wrapper { flex-grow: 1; display: flex; flex-direction: column; overflow: hidden; }
        
        /* Header và Footer của Chat */
        .chat-header { background-color: #fff; padding: 15px 25px; border-bottom: 1px solid #dee2e6; z-index: 10; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        .chat-footer { background-color: #fff; padding: 20px 25px; border-top: 1px solid #dee2e6; z-index: 10; box-shadow: 0 -2px 10px rgba(0,0,0,0.02); }
        
        /* Khung hiển thị nội dung chat */
        .chat-container { flex-grow: 1; overflow-y: auto; padding: 25px 15%; background-color: #f0f2f5; scroll-behavior: smooth; }
        @media (max-width: 992px) { .chat-container { padding: 20px 5%; } }
        
        /* Bong bóng chat */
        .chat-bubble { max-width: 80%; padding: 15px 20px; line-height: 1.6; font-size: 0.95rem; box-shadow: 0 2px 5px rgba(0,0,0,0.04); }
        .chat-bubble ul { margin-bottom: 0; padding-left: 20px; }
        
        .bubble-ai { background-color: #fff; border-bottom-left-radius: 0 !important; border: 1px solid #e9ecef; }
        .bubble-user { background-color: #0d6efd; color: #fff; border-bottom-right-radius: 0 !important; margin-left: auto; }

        /* Vạch ngăn cách ngày */
        .date-divider { text-align: center; position: relative; margin: 30px 0; }
        .date-divider::before { content: ""; position: absolute; top: 50%; left: 0; right: 0; border-top: 2px dashed #dee2e6; z-index: 1; }
        .date-divider span { background-color: #f0f2f5; padding: 0 15px; color: #adb5bd; font-size: 0.85rem; font-weight: bold; position: relative; z-index: 2; border-radius: 20px;}

        /* Nút chức năng */
        .btn-chat-cmd { transition: all 0.2s; border-width: 2px; }
        .btn-chat-cmd:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .btn-chat-cmd:active { transform: scale(0.95); }
        
        .navbar { z-index: 20; }
    </style>
</head>
<body>

    <?php require_once __DIR__ . '/../../components/header.php'; ?>

    <main class="main-chat-wrapper">
        
        <div class="chat-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="position-relative">
                    <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center shadow-sm" style="width: 55px; height: 55px;">
                        <i class="bi bi-robot fs-3"></i>
                    </div>
                    <span class="position-absolute bottom-0 end-0 p-1 border border-2 border-white rounded-circle <?= $canChat ? 'bg-success' : 'bg-warning' ?>" title="<?= $canChat ? 'Trực tuyến' : 'Nghỉ ngơi' ?>"></span>
                </div>
                <div class="ms-3">
                    <h5 class="fw-bold text-dark mb-1">Cố vấn CashFlow AI</h5>
                    <div class="text-muted small fw-semibold">
                        <i class="bi bi-info-circle-fill me-1 text-primary"></i>
                        <?= $canChat ? 'Sẵn sàng tư vấn (1 lần/ngày)' : "Đã dùng lượt hôm nay. Mở khóa sau <span class='text-danger'>{$hoursLeft} giờ</span>." ?>
                    </div>
                </div>
            </div>
            <div class="d-none d-md-block text-muted opacity-25">
                <i class="bi bi-shield-lock-fill fs-3"></i>
            </div>
        </div>

        <div class="chat-container" id="chatBox">
            
            <div class="d-flex mb-4">
                <div class="chat-bubble bubble-ai rounded-4">
                    <span class="fw-bold text-primary d-block mb-1 small"><i class="bi bi-robot me-1"></i>CashFlow AI</span>
                    Xin chào! Trí tuệ nhân tạo đã kết nối thành công với sổ giao dịch của bạn.<br>
                    Hãy chọn một lệnh bên dưới để tôi bắt đầu phân tích nhé!
                </div>
            </div>

            <?php foreach ($groupedHistory as $date => $messages): ?>
                
                <div class="date-divider">
                    <span>
                        <i class="bi bi-calendar-event me-1"></i>
                        <?= $date === date('Y-m-d') ? 'Hôm nay' : date('d/m/Y', strtotime($date)) ?>
                    </span>
                </div>

                <?php foreach ($messages as $msg): ?>
                    <div class="d-flex mb-4 justify-content-end">
                        <div class="chat-bubble bubble-user rounded-4">
                            <i class="bi bi-person-fill me-1"></i> <?= $typeToUserMsg[$msg['type']] ?? 'Lệnh phân tích' ?>
                        </div>
                    </div>

                    <div class="d-flex mb-4">
                        <div class="chat-bubble bubble-ai rounded-4 w-100 border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                <span class="fw-bold text-primary small"><i class="bi bi-robot me-1"></i>Trợ lý Tài chính</span>
                                <span class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i><?= date('H:i', strtotime($msg['created_at'])) ?></span>
                            </div>
                            <div><?= $msg['content'] ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endforeach; ?>

        </div>
        
        <div class="chat-footer">
            <?php if ($canChat): ?>
                <p class="text-center text-muted small fw-bold mb-3"><i class="bi bi-terminal-fill me-2"></i>CHỌN MỘT LỆNH ĐỂ BẮT ĐẦU PHÂN TÍCH</p>
                <div class="row g-2 justify-content-center" id="commandButtons">
                    <div class="col-12 col-md-auto">
                        <button class="btn btn-outline-danger px-md-4 py-2 w-100 rounded-pill fw-bold btn-chat-cmd" onclick="sendChatCommand('warning', 'Tìm điểm lãng phí & Cảnh báo')">
                            <i class="bi bi-exclamation-triangle me-1"></i>Cảnh báo
                        </button>
                    </div>
                    <div class="col-12 col-md-auto">
                        <button class="btn btn-outline-success px-md-4 py-2 w-100 rounded-pill fw-bold btn-chat-cmd" onclick="sendChatCommand('advice', 'Cho tôi lời khuyên tiết kiệm')">
                            <i class="bi bi-lightbulb me-1"></i>Lời khuyên
                        </button>
                    </div>
                    <div class="col-12 col-md-auto">
                        <button class="btn btn-outline-info px-md-4 py-2 w-100 rounded-pill fw-bold btn-chat-cmd" onclick="sendChatCommand('forecast', 'Dự báo tài chính cuối tháng')">
                            <i class="bi bi-graph-up-arrow me-1"></i>Dự báo
                        </button>
                    </div>
                    <div class="col-12 col-md-auto">
                        <button class="btn btn-outline-primary px-md-4 py-2 w-100 rounded-pill fw-bold btn-chat-cmd" onclick="sendChatCommand('summary', 'Tóm tắt tình hình tháng này')">
                            <i class="bi bi-list-check me-1"></i>Tóm tắt
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center text-muted py-2">
                    <h6 class="fw-bold mb-1"><i class="bi bi-hourglass-split text-warning me-2"></i>Hệ thống đang nạp năng lượng!</h6>
                    <p class="small mb-0">Bạn đã sử dụng hết lượt phân tích sâu hôm nay. Vui lòng quay lại sau <strong class="text-danger"><?= $hoursLeft ?> giờ</strong> nữa.</p>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/app.js?v=<?= time() ?>"></script>
    
    <script>
        const chatBox = document.getElementById('chatBox');
        
        // Tự động cuộn xuống cuối khi mới vào trang
        chatBox.scrollTop = chatBox.scrollHeight;

        // Hàm nối tin nhắn của User
        function appendUserMsg(text) {
            const html = `
            <div class="d-flex mb-4 justify-content-end">
                <div class="chat-bubble bubble-user rounded-4 shadow-sm">
                    <i class="bi bi-person-fill me-1"></i> ${text}
                </div>
            </div>`;
            chatBox.insertAdjacentHTML('beforeend', html);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        // Hàm nối hiệu ứng Loading của AI
        function appendLoadingMsg() {
            const id = 'loading_' + Date.now();
            const html = `
            <div class="d-flex mb-4" id="${id}">
                <div class="chat-bubble bubble-ai rounded-4 shadow-sm d-flex align-items-center text-muted border-start border-4 border-primary">
                    <div class="spinner-grow spinner-grow-sm text-primary me-3" role="status"></div>
                    <span class="fst-italic fw-semibold">AI đang đọc sổ giao dịch và tính toán...</span>
                </div>
            </div>`;
            chatBox.insertAdjacentHTML('beforeend', html);
            chatBox.scrollTop = chatBox.scrollHeight;
            return id;
        }

        // Hàm thay thế Loading bằng tin nhắn kết quả
        function replaceLoadingWithMsg(id, text) {
            const loader = document.getElementById(id);
            if (loader) {
                const now = new Date();
                const timeStr = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
                
                loader.innerHTML = `
                <div class="chat-bubble bubble-ai rounded-4 shadow-sm w-100 border-start border-4 border-primary">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <span class="fw-bold text-primary small"><i class="bi bi-robot me-1"></i>Trợ lý Tài chính</span>
                        <span class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i>${timeStr} (Vừa xong)</span>
                    </div>
                    <div>${text}</div>
                </div>`;
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        }

        // Hàm kích hoạt gửi lệnh
        async function sendChatCommand(type, userText) {
            // Ẩn bảng lệnh ngay lập tức để tránh bấm đúp
            document.getElementById('commandButtons').style.display = 'none';
            
            appendUserMsg(userText);
            const loadId = appendLoadingMsg();

            const fd = new FormData();
            fd.append('action', 'chat_consult');
            fd.append('type', type);

            try {
                const res = await fetch('../../controllers/AiController.php', { method: 'POST', body: fd });
                const result = await res.json();

                if (result.status) {
                    replaceLoadingWithMsg(loadId, result.answer);
                    // Tự động reload trang sau 4 giây để chuyển sang màn hình Khóa 24h
                    setTimeout(() => location.reload(), 4000);
                } else {
                    replaceLoadingWithMsg(loadId, `<span class="text-danger fw-bold"><i class="bi bi-x-circle-fill me-1"></i>Lỗi: ${result.message}</span>`);
                    document.getElementById('commandButtons').style.display = 'flex'; // Khôi phục nút nếu lỗi
                }
            } catch (error) {
                replaceLoadingWithMsg(loadId, `<span class="text-danger fw-bold"><i class="bi bi-wifi-off me-1"></i>Mất kết nối máy chủ AI!</span>`);
                document.getElementById('commandButtons').style.display = 'flex';
            }
        }
    </script>
</body>
</html>