<?php
// index.php
session_start();

// Nhúng cơ chế tự động nạp class cho toàn hệ thống
require_once __DIR__ . '/autoload.php';

// Kiểm tra trạng thái đăng nhập
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    // Nếu đã đăng nhập, đẩy thẳng vào "trái tim" của hệ thống là Dashboard
    header("Location: GUI/pages/analytics/dashboard.php");
    exit();
} else {
    // Nếu chưa có Session, đưa về cổng kiểm duyệt
    header("Location: GUI/pages/auth/login.php");
    exit();
}
?>