<?php
// Tệp: GUI/controllers/DailyNoteController.php

session_start();
require_once __DIR__ . '/../../autoload.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => false, 'message' => 'Lỗi xác thực.']);
    exit();
}

$userId = $_SESSION['user_id'];
$dailyNoteBUS = new DailyNoteBUS();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    // API: LƯU / XÓA GHI CHÚ
    if ($action === 'save') {
        $date = $_POST['date'] ?? '';
        $content = $_POST['content'] ?? '';
        
        if (empty($date)) {
            echo json_encode(['status' => false, 'message' => 'Không xác định được ngày ghi chú.']);
            exit();
        }
        
        $result = $dailyNoteBUS->saveOrDeleteNote($userId, $date, $content);
        echo json_encode($result);
        exit();
    }

    // API: LẤY GHI CHÚ CHO LỊCH
    if ($action === 'get_all_month') {
        $month = $_GET['month'] ?? date('Y-m');
        $data = $dailyNoteBUS->getNotesByMonth($userId, $month);
        echo json_encode(['status' => true, 'data' => $data]);
        exit();
    }

    echo json_encode(['status' => false, 'message' => 'Hành động không hợp lệ.']);

} catch (PDOException $e) {
    // Bắt lỗi Cơ sở dữ liệu (VD: Quên tạo bảng, sai tên cột...)
    echo json_encode(['status' => false, 'message' => 'Lỗi Database: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Bắt các lỗi hệ thống khác
    echo json_encode(['status' => false, 'message' => 'Lỗi Hệ thống: ' . $e->getMessage()]);
}
?>