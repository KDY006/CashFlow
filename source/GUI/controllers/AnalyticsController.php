<?php
// Tệp: GUI/controllers/AnalyticsController.php
session_start();

require_once __DIR__ . '/../../autoload.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => false, 'message' => 'Vui lòng đăng nhập để xem dữ liệu.']);
    exit();
}

$userId = $_SESSION['user_id'];
$analyticsBUS = new AnalyticsBUS();
$action = $_GET['action'] ?? 'get_dashboard';

// 1. DASHBOARD V2 (Giao diện tổng quan)
if ($action === 'get_dashboard') {
    $filterType = $_GET['filter_type'] ?? 'month';
    $filterVal  = $_GET['filter_val'] ?? date('Y-m');
    
    $result = $analyticsBUS->getDashboardV2Data($userId, $filterType, $filterVal);
    echo json_encode(['status' => true, 'data' => $result]);
    exit();
} 
// 2. LẤY DỮ LIỆU LỊCH
elseif ($action === 'get_calendar') {
    $month = $_GET['month'] ?? date('Y-m');
    
    $result = $analyticsBUS->getCalendarOnly($userId, $month);
    echo json_encode(['status' => true, 'data' => $result]);
    exit();
}
// 3. LẤY CHI TIẾT GIAO DỊCH TRONG NGÀY
elseif ($action === 'get_daily_transactions') {
    $date = $_GET['date'] ?? date('Y-m-d');
    
    $result = $analyticsBUS->getDailyTransactions($userId, $date);
    echo json_encode(['status' => true, 'data' => $result]);
    exit();
}

// 4. LỖI KHÔNG TÌM THẤY ACTION
echo json_encode(['status' => false, 'message' => 'Yêu cầu không hợp lệ.']);
exit();
?>