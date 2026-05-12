<?php
/**
 * Tệp: helpers/CsrfHelper.php
 * Cung cấp các hàm tạo và xác minh CSRF Token để bảo vệ form POST.
 */
class CsrfHelper {

    /**
     * Tạo và lưu CSRF token vào session nếu chưa có.
     * @return string Token CSRF hiện tại.
     */
    public static function generateToken(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Trả về HTML input hidden chứa CSRF token.
     * Dùng trực tiếp trong form: <?= CsrfHelper::inputField() ?>
     * @return string HTML input hidden.
     */
    public static function inputField(): string {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Xác minh CSRF token từ POST request.
     * Nếu không hợp lệ, trả về response lỗi và dừng script.
     * @param bool $isJson Nếu true, trả về JSON error thay vì redirect.
     */
    public static function verify(bool $isJson = false): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $submitted = $_POST['csrf_token'] ?? '';
        $stored    = $_SESSION['csrf_token'] ?? '';

        // hash_equals chống timing attack
        if (empty($submitted) || empty($stored) || !hash_equals($stored, $submitted)) {
            if ($isJson) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(403);
                echo json_encode(['status' => false, 'message' => 'Yêu cầu không hợp lệ (CSRF). Vui lòng tải lại trang.']);
            } else {
                http_response_code(403);
                $_SESSION['error'] = 'Yêu cầu không hợp lệ. Vui lòng thử lại.';
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../pages/auth/login.php'));
            }
            exit();
        }
        // Không xoay token ở đây để tránh lỗi cho các request AJAX đồng thời
    }
}
?>
