<?php
// Tệp: GUI/controllers/UserController.php
session_start();
require_once __DIR__ . '/../../autoload.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => false, 'message' => 'Phiên đăng nhập đã hết hạn.']);
    exit();
}

$userId = $_SESSION['user_id'];
$userBUS = new UserBUS();
$action = $_POST['action'] ?? '';

try {
    // 1. CẬP NHẬT THÔNG TIN CÁ NHÂN
    if ($action === 'update_profile') {
        $fullName = trim($_POST['full_name'] ?? '');
        $avatarUrl = $_POST['current_avatar_url'] ?? null;

        // Xử lý upload ảnh
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            
            if (!in_array($file['type'], $allowedTypes)) {
                echo json_encode(['status' => false, 'message' => 'Chỉ chấp nhận ảnh JPG, PNG, GIF.']);
                exit();
            }

            $uploadDir = __DIR__ . '/../assets/images/avatars/';
            
            // Dùng @ để bỏ qua Warning nếu thư mục đã tồn tại
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }

            $filename = uniqid('avatar_') . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
            $targetFile = $uploadDir . $filename;

            // Dùng @ để bỏ qua Warning nếu lỗi phân quyền ghi file
            if (@move_uploaded_file($file['tmp_name'], $targetFile)) {
                // Xóa file cũ
                if ($avatarUrl && file_exists(__DIR__ . '/..' . $avatarUrl)) {
                    @unlink(__DIR__ . '/..' . $avatarUrl);
                }
                $avatarUrl = '/assets/images/avatars/' . $filename;
            } else {
                echo json_encode(['status' => false, 'message' => 'Lưu file ảnh thất bại. Kiểm tra quyền thư mục assets/images.']);
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
    if ($action === 'change_password') {
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
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

    echo json_encode(['status' => false, 'message' => 'Hành động không hợp lệ.']);

} catch (Exception $e) {
    echo json_encode(['status' => false, 'message' => 'Lỗi ngoại lệ: ' . $e->getMessage()]);
} catch (Error $e) {
    // Bắt các lỗi Fatal Error (Như sai số lượng tham số hàm)
    echo json_encode(['status' => false, 'message' => 'Lỗi cấu trúc: ' . $e->getMessage()]);
}
?>