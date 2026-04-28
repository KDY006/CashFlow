<?php
session_start();
require_once __DIR__ . '/../../autoload.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/auth/login.php");
    exit();
}

$aiBus = new AiAdvisorBUS();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Hành động 1: Yêu cầu AI phân tích dữ liệu mới
    if ($action === 'analyze') {
        $result = $aiBus->analyzeUserFinances($_SESSION['user_id']);
        
        if ($result['status']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        header("Location: ../pages/ai/advisor.php");
        exit();
    }

    // Hành động 2: Đánh dấu một lời khuyên là đã đọc
    if ($action === 'mark_read') {
        $insight_id = $_POST['insight_id'] ?? 0;
        $aiBus->markInsightAsRead((int)$insight_id, $_SESSION['user_id']);
        
        header("Location: ../pages/ai/advisor.php");
        exit();
    }
}
?>