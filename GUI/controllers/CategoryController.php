<?php
// Tệp: GUI/controllers/CategoryController.php

session_start();
require_once __DIR__ . '/../../autoload.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => false, 'message' => 'Lỗi xác thực.']);
    exit();
}

$categoryBUS = new CategoryBUS();
$userId = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'add') {
    $result = $categoryBUS->addCategory($userId, $_POST['name'], $_POST['type']);
    echo json_encode($result);
    exit();
}

if ($action === 'delete') {
    $result = $categoryBUS->deleteCategory($_POST['id'], $userId);
    echo json_encode($result);
    exit();
}

if ($action === 'get_all') {
    $categories = $categoryBUS->getAllCategories($userId);
    // Bổ sung thêm mã màu CSS vào dữ liệu JSON trả về cho JS
    foreach ($categories as &$cat) {
        $cat['color_class'] = FormatHelper::getCategoryBadgeColor($cat['id']);
    }
    echo json_encode(['status' => true, 'data' => $categories]);
    exit();
}
?>