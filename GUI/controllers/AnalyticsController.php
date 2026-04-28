<?php
// Tệp: GUI/controllers/AnalyticsController.php
session_start();

require_once __DIR__ . '/../../autoload.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => false, "message" => "Vui lòng đăng nhập để xem dữ liệu."]);
    exit();
}

$userId = $_SESSION['user_id'];
$analyticsBUS = new AnalyticsBUS();

$action = $_GET['action'] ?? 'get_dashboard';

if ($action === 'get_dashboard') {
    // Nhận biến month định dạng YYYY-MM từ Input Month của Frontend
    $month = $_GET['month'] ?? date('Y-m');
    
    // Gọi BUS xử lý (BUS của bạn đã lo toàn bộ logic bên trong rất mượt)
    $result = $analyticsBUS->getDashboardData($userId, $month);
    
    // Trả về JSON cho Client, bọc trong object data để frontend dễ xử lý
    echo json_encode(['status' => true, 'data' => $result]);
    exit();
}

echo json_encode(["status" => false, "message" => "Yêu cầu không hợp lệ."]);
?>