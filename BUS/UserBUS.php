<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class UserBUS {
    private $userDAL;

    public function __construct() {
        $this->userDAL = new UserDAL();
    }

    private function generateRandomPassword($length = 6) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $max)];
        }
        return $password;
    }

    public function register($fullName, $email) {
        if ($this->userDAL->getUserByEmail($email)) {
            return ["status" => false, "message" => "Email này đã được đăng ký!"];
        }

        $randomPassword = $this->generateRandomPassword();
        $passwordHash = password_hash($randomPassword, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(32));

        $newUser = new UserDTO($fullName, $email, $passwordHash);
        $result = $this->userDAL->createUserWithToken($newUser, $token);

        if ($result) {
            return $this->sendActivationEmail($email, $token, $randomPassword, "Kích hoạt tài khoản CashFlow");
        }
        return ["status" => false, "message" => "Có lỗi xảy ra khi tạo tài khoản."];
    }

    public function processForgotPassword($email) {
        $user = $this->userDAL->getUserByEmail($email);
        if ($user == null) {
            return ["status" => false, "message" => "Email không tồn tại trong hệ thống!"];
        }

        $randomPassword = $this->generateRandomPassword();
        $passwordHash = password_hash($randomPassword, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(32));

        $this->userDAL->resetAccountForActivation($email, $passwordHash, $token);

        return $this->sendActivationEmail($email, $token, $randomPassword, "Yêu cầu phục hồi mật khẩu - CashFlow");
    }

    public function verifyLoginToken($token) {
        $user = $this->userDAL->getUserByLoginToken($token);
        if ($user) {
            $this->userDAL->clearLoginToken($user['id']);
            return ["status" => true, "message" => "Xác thực thành công.", "data" => $user];
        }
        return ["status" => false, "message" => "Link đăng nhập không hợp lệ hoặc đã được sử dụng."];
    }

    public function login($email, $password) {
        $userDTO = $this->userDAL->getUserByEmail($email);
        if ($userDTO == null) return ["status" => false, "message" => "Email không tồn tại!"];

        $fullUserData = $this->userDAL->getUserById($userDTO->getId());
        
        if ($fullUserData['is_first_login'] == 1) {
            return ["status" => false, "message" => "Tài khoản đang chờ thiết lập mật khẩu. Vui lòng truy cập bằng đường link hệ thống đã gửi trong email!"];
        }

        if (password_verify($password, $userDTO->getPasswordHash())) {
            return ["status" => true, "message" => "Đăng nhập thành công!", "data" => $fullUserData];
        }
        return ["status" => false, "message" => "Mật khẩu không chính xác!"];
    }

    public function changePasswordFirstTime($id, $oldPassword, $newPassword) {
        // Lấy thông tin user hiện tại để đối chiếu mật khẩu
        $user = $this->userDAL->getUserById($id);
        if ($user == null) {
            return ["status" => false, "message" => "Tài khoản không tồn tại!"];
        }

        // Kiểm tra xem mật khẩu tạm thời người dùng nhập có khớp với CSDL không
        if (!password_verify($oldPassword, $user['password_hash'])) {
            return ["status" => false, "message" => "Mật khẩu tạm thời không chính xác! Vui lòng kiểm tra lại email."];
        }

        // Nếu hợp lệ, tiến hành băm mật khẩu mới và cập nhật
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $result = $this->userDAL->updatePasswordAndClearFirstLogin($id, $newPasswordHash);
        
        if ($result) {
            return ["status" => true, "message" => "Đổi mật khẩu thành công!"];
        }
        return ["status" => false, "message" => "Không thể cập nhật mật khẩu lúc này."];
    }

    public function updateProfile($id, $fullName) {
        if (empty(trim($fullName))) return ["status" => false, "message" => "Họ tên không được để trống!"];
        $result = $this->userDAL->updateProfile($id, $fullName);
        if ($result) return ["status" => true, "message" => "Cập nhật thông tin thành công!"];
        return ["status" => false, "message" => "Không thể cập nhật lúc này."];
    }

    public function changePassword($id, $oldPassword, $newPassword) {
        $user = $this->userDAL->getUserById($id);
        if ($user == null) return ["status" => false, "message" => "Người dùng không tồn tại!"];
        if (!password_verify($oldPassword, $user['password_hash'])) return ["status" => false, "message" => "Mật khẩu cũ không chính xác!"];

        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $result = $this->userDAL->updatePassword($id, $newPasswordHash);
        
        if ($result) return ["status" => true, "message" => "Đổi mật khẩu thành công!"];
        return ["status" => false, "message" => "Đã xảy ra lỗi khi đổi mật khẩu."];
    }

    private function sendActivationEmail($email, $token, $randomPassword, $subject) {
        $loginLink = "http://localhost/CashFlow/GUI/controllers/AuthController.php?action=verify_login&token=" . $token;
        
        $body = "
            <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <h2>Xác thực tài khoản CashFlow</h2>
                <p>Hệ thống đã tạo mật khẩu truy cập tạm thời cho bạn là: 
                   <strong style='color: #e74c3c; font-size: 1.2em;'>{$randomPassword}</strong>
                </p>
                <p>Vui lòng click vào link bên dưới để đăng nhập và thiết lập mật khẩu cá nhân của bạn ngay lập tức:</p>
                <p style='margin: 20px 0;'>
                    <a href='{$loginLink}' style='background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>
                       ĐĂNG NHẬP & THIẾT LẬP MẬT KHẨU
                    </a>
                </p>
                <p style='color: #7f8c8d; font-size: 0.9em;'>Lưu ý: Bạn KHÔNG THỂ đăng nhập bằng form cho đến khi hoàn tất bước này.</p>
            </div>";
        
        return $this->sendMail($email, $subject, $body);
    }

    private function sendMail($toEmail, $subject, $body) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            // ==========================================
            // THAY ĐỔI THÔNG TIN TÀI KHOẢN GMAIL TẠI ĐÂY
            // ==========================================
            $mail->Username   = 'bongrong009kk@gmail.com';
            $mail->Password   = 'cgsz sxjk lphy naov';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom($mail->Username, 'CashFlow');
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return ["status" => true, "message" => "Email đã được gửi! Vui lòng kiểm tra hòm thư của bạn."];
        } catch (Exception $e) {
            return ["status" => false, "message" => "Lỗi cấu hình gửi mail."];
        }
    }
}
?>