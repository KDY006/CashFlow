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
    $month = $_GET['month'] ?? date('n');
    $year = $_GET['year'] ?? date('Y');
    
    $budgets = $budgetBUS->getBudgetsByMonth($userId, $month, $year);
    
    // LOGIC NÂNG CẤP: Tự động duy trì hũ ngân sách sang tháng mới
    if (empty($budgets)) {
        $budgetBUS->clonePreviousMonthBudgets($userId, $month, $year);
        // Tải lại dữ liệu sau khi đã tự động sao chép
        $budgets = $budgetBUS->getBudgetsByMonth($userId, $month, $year);
    }
    
    $data = [];
    foreach ($budgets as $b) {
        $data[] = [
            'id' => $b->getId(),
            'category_id' => $b->getCategoryId(),
            'category_name' => $b->getCategoryName(),
            'category_type' => $b->getCategoryType(), 
            
            'amount_limit' => $b->getAmountLimit(),
            'total_spent' => $b->getTotalSpent(),
            'remain_amount' => $b->getRemainAmount(),
            'progress_percentage' => $b->getProgressPercentage(),
            
            'formatted_limit' => FormatHelper::formatCurrency($b->getAmountLimit()),
            'formatted_spent' => FormatHelper::formatCurrency($b->getTotalSpent()),
            'formatted_remain' => FormatHelper::formatCurrency(abs($b->getRemainAmount())), 
            
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

if ($action === 'clone_previous') {
    $month = $_POST['month'] ?? date('n');
    $year = $_POST['year'] ?? date('Y');
    
    $result = $budgetBUS->clonePreviousMonthBudgets($userId, $month, $year);
    echo json_encode($result);
    exit();
}
?>