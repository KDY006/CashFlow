<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Kiểm tra trạng thái đăng nhập chung
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Vui lòng đăng nhập để truy cập hệ thống.";
    header("Location: ../auth/login.php");
    exit();
}

// 2. Chặn và nhốt người dùng chưa thiết lập mật khẩu vào trang setup
if (isset($_SESSION['is_first_login']) && $_SESSION['is_first_login'] == 1) {
    if (basename($_SERVER['PHP_SELF']) !== 'setup-password.php') {
        header("Location: ../auth/setup-password.php");
        exit();
    }
}
?>