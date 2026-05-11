<?php
// Tệp: GUI/controllers/CategoryController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../autoload.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => false, 'message' => 'Lỗi xác thực.']);
    exit();
}

$categoryBUS = new CategoryBUS();
$userId = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Xác minh CSRF cho các action thay đổi dữ liệu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CsrfHelper::verify(true);
}

// TRẢ VỀ CẤU TRÚC CÂY DANH MỤC
if ($action === 'get_tree') {
    $tree = $categoryBUS->getCategoryTree($userId);
    echo json_encode(['status' => true, 'data' => $tree]);
    exit();
}

// THÊM DANH MỤC (Có parent_id)
if ($action === 'add') {
    $name     = trim($_POST['name'] ?? '');
    $type     = trim($_POST['type'] ?? 'expense');
    $parentId = !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null;

    $result = $categoryBUS->addCategory($userId, $name, $type, $parentId);
    echo json_encode($result);
    exit();
}

// XÓA DANH MỤC
if ($action === 'delete') {
    $result = $categoryBUS->deleteCategory((int) ($_POST['id'] ?? 0), $userId);
    echo json_encode($result);
    exit();
}
?>