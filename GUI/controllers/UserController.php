<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../autoload.php';

header('Content-Type: application/json; charset=utf-8');

// Xác minh CSRF cho tất cả POST request
CsrfHelper::verify(true);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => false, 'message' => 'Phiên đăng nhập đã hết hạn.']);
    exit();
}

$userId  = (int) $_SESSION['user_id'];
$userBUS = new UserBUS();
$action  = $_POST['action'] ?? '';

try {
    // 1. CẬP NHẬT THÔNG TIN CÁ NHÂN
    if ($action === 'update_profile') {
        $fullName  = trim($_POST['full_name'] ?? '');
        $avatarUrl = $_POST['current_avatar_url'] ?? null;

        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar'];

            // Giới hạn kích thước file: tối đa 2MB
            if ($file['size'] > 2 * 1024 * 1024) {
                echo json_encode(['status' => false, 'message' => 'Ảnh tải lên không được vượt quá 2MB.']);
                exit();
            }

            // Kiểm tra MIME thực sự bằng finfo (không tin trình duyệt gửi lên)
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $realMime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($realMime, $allowedMimes)) {
                echo json_encode(['status' => false, 'message' => 'Chỉ chấp nhận ảnh JPG, PNG, GIF, WebP.']);
                exit();
            }

            $uploadDir = __DIR__ . '/../assets/images/avatars/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = 'avatar_' . uniqid() . '_' . time() . '.' . $ext;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                if ($avatarUrl && file_exists($uploadDir . $avatarUrl)) {
                    @unlink($uploadDir . $avatarUrl);
                }
                $avatarUrl = $newFileName;
            } else {
                echo json_encode(['status' => false, 'message' => 'Lưu file ảnh thất bại.']);
                exit();
            }
        }

        $result = $userBUS->updateProfile($userId, $fullName, $avatarUrl);
        
        if ($result['status']) {
            $_SESSION['user_name'] = $fullName;
        }
        
        echo json_encode($result);
        exit();
    }
    
    // 2. ĐỔI MẬT KHẨU
    elseif ($action === 'change_password') {
        $oldPassword     = $_POST['old_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($newPassword !== $confirmPassword) {
            echo json_encode(['status' => false, 'message' => 'Mật khẩu xác nhận không khớp!']);
            exit();
        }

        if (strlen($newPassword) < 6) {
            echo json_encode(['status' => false, 'message' => 'Mật khẩu mới phải có ít nhất 6 ký tự.']);
            exit();
        }

        $result = $userBUS->changePassword($userId, $oldPassword, $newPassword);
        echo json_encode($result);
        exit();
    }
    
    // 3. XÓA TÀI KHOẢN
    elseif ($action === 'delete_account') {
        $password = $_POST['password'] ?? '';
        if (empty($password)) {
            echo json_encode(['status' => false, 'message' => 'Vui lòng nhập mật khẩu xác nhận.']);
            exit();
        }

        $result = $userBUS->deleteAccount($userId, $password);
        if ($result['status']) {
            session_destroy();
        }
        echo json_encode($result);
        exit();
    }

    echo json_encode(['status' => false, 'message' => 'Hành động không hợp lệ.']);

} catch (Exception $e) {
    echo json_encode(['status' => false, 'message' => 'Đã xảy ra lỗi hệ thống: ' . $e->getMessage()]);
}
exit();
?>