<?php
session_start();
require_once __DIR__ . '/../../autoload.php';

$userBUS = new UserBUS();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Luồng Đăng ký
    if ($action === 'register') {
        $result = $userBUS->register(trim($_POST['full_name']), trim($_POST['email']));
        if ($result['status']) {
            $_SESSION['success'] = $result['message'];
            header("Location: ../pages/auth/login.php");
        } else {
            $_SESSION['error'] = $result['message'];
            header("Location: ../pages/auth/register.php");
        }
        exit();
    }

    // Luồng Đăng nhập
    if ($action === 'login') {
        $result = $userBUS->login(trim($_POST['email']), $_POST['password']);
        if ($result['status']) {
            $_SESSION['user_id'] = $result['data']['id'];
            $_SESSION['user_name'] = $result['data']['full_name'];
            $_SESSION['is_first_login'] = $result['data']['is_first_login'];
            header("Location: ../pages/analytics/dashboard.php");
        } else {
            $_SESSION['error'] = $result['message'];
            header("Location: ../pages/auth/login.php");
        }
        exit();
    }

    // Luồng Thiết lập mật khẩu (khi click link từ email)
    if ($action === 'setup_password') {
        if (!isset($_SESSION['user_id'])) { header("Location: ../pages/auth/login.php"); exit(); }

        $oldPassword = $_POST['old_password']; 
        $newPassword = $_POST['new_password'];
        
        if ($newPassword !== $_POST['confirm_password']) {
            $_SESSION['error'] = "Mật khẩu xác nhận không khớp!";
            header("Location: ../pages/auth/setup-password.php");
            exit();
        }

        $result = $userBUS->changePasswordFirstTime($_SESSION['user_id'], $oldPassword, $newPassword);
        
        if ($result['status']) {
            // Hủy phiên làm việc cũ (Đăng xuất)
            session_unset();
            session_destroy();
            
            // Khởi tạo phiên làm việc mới chỉ để lưu thông báo Flash Message
            session_start();
            $_SESSION['success'] = "Tuyệt vời! Bạn đã thiết lập mật khẩu thành công. Vui lòng đăng nhập lại để truy cập hệ thống.";
            header("Location: ../pages/auth/login.php");
        } else {
            $_SESSION['error'] = $result['message'];
            header("Location: ../pages/auth/setup-password.php");
        }
        exit();
    }

    // Luồng Xin cấp lại mật khẩu
    if ($action === 'forgot_password') {
        $result = $userBUS->processForgotPassword(trim($_POST['email']));
        if ($result['status']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        header("Location: ../pages/auth/forgot-password.php");
        exit();
    }

    // Luồng Cập nhật Profile
    if ($action === 'update_profile') {
        if (!isset($_SESSION['user_id'])) { header("Location: ../pages/auth/login.php"); exit(); }
        $result = $userBUS->updateProfile($_SESSION['user_id'], trim($_POST['full_name']));
        if ($result['status']) {
            $_SESSION['user_name'] = trim($_POST['full_name']);
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        header("Location: ../pages/analytics/dashboard.php"); 
        exit();
    }

    // Luồng Đổi mật khẩu chủ động
    if ($action === 'change_password') {
        if (!isset($_SESSION['user_id'])) { header("Location: ../pages/auth/login.php"); exit(); }
        if ($_POST['new_password'] !== $_POST['confirm_password']) {
            $_SESSION['error'] = "Mật khẩu xác nhận không khớp!";
            header("Location: ../pages/analytics/dashboard.php");
            exit();
        }
        
        $result = $userBUS->changePassword($_SESSION['user_id'], $_POST['old_password'], $_POST['new_password']);
        
        if ($result['status']) { 
            // Hủy phiên làm việc cũ (Đăng xuất)
            session_unset();
            session_destroy();
            
            // Khởi tạo phiên làm việc mới
            session_start();
            $_SESSION['success'] = "Đổi mật khẩu thành công! Vui lòng đăng nhập lại bằng mật khẩu mới của bạn.";
            header("Location: ../pages/auth/login.php");
        } else { 
            $_SESSION['error'] = $result['message']; 
            header("Location: ../pages/analytics/dashboard.php");
        }
        exit();
    }
}

// Xử lý GET request
if (isset($_GET['action'])) {
    // Luồng Xác thực từ Email
    if ($_GET['action'] === 'verify_login') {
        $result = $userBUS->verifyLoginToken($_GET['token'] ?? '');
        if ($result['status']) {
            $_SESSION['user_id'] = $result['data']['id'];
            $_SESSION['user_name'] = $result['data']['full_name'];
            $_SESSION['is_first_login'] = $result['data']['is_first_login'];

            if ($_SESSION['is_first_login'] == 1) {
                header("Location: ../pages/auth/setup-password.php");
            } else {
                header("Location: ../pages/analytics/dashboard.php");
            }
        } else {
            $_SESSION['error'] = $result['message'];
            header("Location: ../pages/auth/login.php");
        }
        exit();
    }

    // Luồng Đăng xuất
    if ($_GET['action'] === 'logout') {
        session_unset();
        session_destroy();
        header("Location: ../pages/auth/login.php");
        exit();
    }
}
?>