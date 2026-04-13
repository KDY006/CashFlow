<?php
// Tệp: GUI/controllers/BudgetController.php

session_start();
require_once __DIR__ . '/../../autoload.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => false, 'message' => 'Phiên đăng nhập hết hạn.']);
    exit();
}

$budgetBUS = new BudgetBUS();
$userId = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Lấy danh sách hũ ngân sách của một tháng cụ thể
if ($action === 'get_by_month') {
    // Nếu Client không truyền tháng/năm, mặc định lấy tháng/năm hiện tại
    $month = $_GET['month'] ?? date('n');
    $year = $_GET['year'] ?? date('Y');
    
    $budgets = $budgetBUS->getBudgetsByMonth($userId, $month, $year);
    
    $data = [];
    foreach ($budgets as $b) {
        $data[] = [
            'id' => $b->getId(),
            'category_id' => $b->getCategoryId(),
            'category_name' => $b->getCategoryName(),
            'category_type' => $b->getCategoryType(), // Sẽ dùng để tránh lập ngân sách cho khoản Thu
            
            // Số liệu gốc để tính toán nếu cần
            'amount_limit' => $b->getAmountLimit(),
            'total_spent' => $b->getTotalSpent(),
            'remain_amount' => $b->getRemainAmount(),
            'progress_percentage' => $b->getProgressPercentage(),
            
            // Chuỗi đã định dạng sẵn (VD: 3.000.000 đ) để hiển thị ngay
            'formatted_limit' => FormatHelper::formatCurrency($b->getAmountLimit()),
            'formatted_spent' => FormatHelper::formatCurrency($b->getTotalSpent()),
            'formatted_remain' => FormatHelper::formatCurrency(abs($b->getRemainAmount())), // Lấy trị tuyệt đối để hiện chữ "Âm" hoặc "Vượt mức" trên UI
            
            // Sinh màu sắc đồng bộ với Module Giao dịch
            'color_class' => FormatHelper::getCategoryBadgeColor($b->getCategoryId())
        ];
    }
    echo json_encode(['status' => true, 'data' => $data]);
    exit();
}

if ($action === 'add') {
    $result = $budgetBUS->addBudget($userId, $_POST['category_id'], $_POST['amount_limit'], $_POST['month'], $_POST['year']);
    echo json_encode($result);
    exit();
}

if ($action === 'update') {
    $result = $budgetBUS->updateBudget($_POST['id'], $userId, $_POST['amount_limit']);
    echo json_encode($result);
    exit();
}

if ($action === 'delete') {
    $result = $budgetBUS->deleteBudget($_POST['id'], $userId);
    echo json_encode($result);
    exit();
}
?>