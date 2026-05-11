<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../autoload.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => false, 'message' => 'Lỗi xác thực.']);
    exit();
}

// Kiểm tra CSRF cho các yêu cầu POST (lưu ghi chú)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CsrfHelper::verify(true);
}

$userId = (int) $_SESSION['user_id'];
$dailyNoteBUS = new DailyNoteBUS(); 
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'save') {
    $date    = $_POST['date'] ?? '';
    $content = $_POST['content'] ?? '';
    $pinType = $_POST['pin_type'] ?? 'none'; 
    
    if (empty($date)) {
        echo json_encode(['status' => false, 'message' => 'Không xác định được ngày.']);
        exit();
    }
    
    $result = $dailyNoteBUS->saveOrDeleteNote($userId, $date, $content, $pinType);
    echo json_encode($result);
    exit();
} 
elseif ($action === 'get_all_month') {
    $month = $_GET['month'] ?? date('Y-m');
    $data  = $dailyNoteBUS->getNotesByMonth($userId, $month);
    
    echo json_encode(['status' => true, 'data' => $data]);
    exit();
}

echo json_encode(['status' => false, 'message' => 'Hành động không hợp lệ.']);
exit();
?>