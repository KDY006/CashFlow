<?php
// Tệp: GUI/controllers/AnalyticsController.php
session_start();

// Nhúng autoload để nạp các class BUS, DAL
require_once __DIR__ . '/../../autoload.php';

// Ép kiểu dữ liệu trả về của file này là JSON
header('Content-Type: application/json; charset=utf-8');

// 1. Kiểm tra xác thực (Bắt buộc phải đăng nhập mới được xem)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => false, "message" => "Vui lòng đăng nhập để xem dữ liệu."]);
    exit();
}

$userId = $_SESSION['user_id'];
$analyticsBUS = new AnalyticsBUS();

// 2. Xử lý request dạng GET (Lấy dữ liệu API)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? 'get_dashboard';

    // Luồng: Lấy dữ liệu tổng hợp cho Dashboard
    if ($action === 'get_dashboard') {
        // Nhận bộ lọc từ URL (VD: ?filter=last_month), mặc định là this_month
        $filter = $_GET['filter'] ?? 'this_month';
        
        // Gọi BUS xử lý
        $result = $analyticsBUS->getDashboardData($userId, $filter);
        
        // Trả về JSON cho Client (Trình duyệt/Javascript)
        echo json_encode($result);
        exit();
    }
}

// Bắt lỗi nếu gọi sai action
echo json_encode(["status" => false, "message" => "Yêu cầu không hợp lệ."]);
?>