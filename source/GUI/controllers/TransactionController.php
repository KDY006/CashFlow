<?php
// Tệp: GUI/controllers/TransactionController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../autoload.php';

// Trả về JSON header để trình duyệt hiểu đây là API
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => false, 'message' => 'Phiên đăng nhập hết hạn. Vui lòng tải lại trang.']);
    exit();
}

$transactionBUS = new TransactionBUS();
$userId = $_SESSION['user_id'];

// AJAX thường gửi dữ liệu qua POST, nhưng đôi khi dùng GET để lấy dữ liệu
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Xác minh CSRF cho các action thay đổi dữ liệu (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CsrfHelper::verify(true);
}

// Luồng THÊM GIAO DỊCH
if ($action === 'add') {
    $result = $transactionBUS->addTransaction($userId, $_POST['category_id'], $_POST['amount'], $_POST['transaction_date'], $_POST['note'] ?? '');
    echo json_encode($result);
    exit();
}

// Luồng SỬA GIAO DỊCH
if ($action === 'edit') {
    $result = $transactionBUS->updateTransaction($_POST['id'], $userId, $_POST['category_id'], $_POST['amount'], $_POST['transaction_date'], $_POST['note'] ?? '');
    echo json_encode($result);
    exit();
}

// Luồng XÓA GIAO DỊCH
if ($action === 'delete') {
    $result = $transactionBUS->deleteTransaction($_POST['id'], $userId);
    echo json_encode($result);
    exit();
}

// LẤY CHI TIẾT 1 GIAO DỊCH (Đổ dữ liệu vào form Edit)
if ($action === 'get') {
    $data = $transactionBUS->getTransactionById($_GET['id'], $userId);
    if ($data) {
        echo json_encode(['status' => true, 'data' => $data]);
    } else {
        echo json_encode(['status' => false, 'message' => 'Không tìm thấy giao dịch.']);
    }
    exit();
}

// LẤY DANH SÁCH GIAO DỊCH (Bản V2 hỗ trợ lọc Cha/Con)
if ($action === 'get_all') {
    $result = $transactionBUS->getAllTransactionsFormatted($userId);
    echo json_encode(['status' => true, 'data' => $result]);
    exit();
}
?>