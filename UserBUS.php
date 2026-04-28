<?php

require_once __DIR__ . '/../DAL/UserDAL.php';
require_once __DIR__ . '/../DTO/UserDTO.php';

class UserBUS
{
    private UserDAL $userDAL;

    public function __construct()
    {
        $this->userDAL = new UserDAL();
    }

    public function validateRegisterData(array $data): array
    {
        $errors = [];

        $fullName = trim($data['full_name'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = (string)($data['password'] ?? '');
        $confirmPassword = (string)($data['confirm_password'] ?? '');

        if ($fullName === '') {
            $errors['full_name'] = 'Họ tên không được để trống.';
        } elseif (mb_strlen($fullName) < 2) {
            $errors['full_name'] = 'Họ tên phải có ít nhất 2 ký tự.';
        } elseif (mb_strlen($fullName) > 100) {
            $errors['full_name'] = 'Họ tên không được vượt quá 100 ký tự.';
        }

        if ($email === '') {
            $errors['email'] = 'Email không được để trống.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không đúng định dạng.';
        } elseif ($this->userDAL->existsByEmail($email)) {
            $errors['email'] = 'Email đã tồn tại trong hệ thống.';
        }

        if ($password === '') {
            $errors['password'] = 'Mật khẩu không được để trống.';
        } elseif (!$this->isStrongPassword($password)) {
            $errors['password'] = 'Mật khẩu phải từ 8 ký tự, có chữ hoa, chữ thường và số.';
        }

        if ($confirmPassword === '') {
            $errors['confirm_password'] = 'Vui lòng xác nhận mật khẩu.';
        } elseif ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Mật khẩu xác nhận không khớp.';
        }

        return $errors;
    }

    public function register(array $data): array
    {
        $errors = $this->validateRegisterData($data);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        $userDTO = new UserDTO(
            null,
            trim($data['full_name']),
            trim($data['email']),
            password_hash((string)$data['password'], PASSWORD_BCRYPT),
            null,
            null
        );

        $userId = $this->userDAL->create($userDTO);

        return [
            'success' => true,
            'message' => 'Đăng ký thành công.',
            'user_id' => $userId
        ];
    }

    public function validateLoginData(string $email, string $password): array
    {
        $errors = [];

        if (trim($email) === '') {
            $errors['email'] = 'Email không được để trống.';
        }

        if (trim($password) === '') {
            $errors['password'] = 'Mật khẩu không được để trống.';
        }

        return $errors;
    }

    public function login(string $email, string $password): array
    {
        $errors = $this->validateLoginData($email, $password);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        $email = trim($email);
        $password = trim($password);

        $user = $this->userDAL->findByEmail($email);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Tài khoản không tồn tại.'
            ];
        }

        if (!password_verify($password, $user['password_hash'])) {
            return [
                'success' => false,
                'message' => 'Mật khẩu không chính xác.'
            ];
        }

        $this->startSessionIfNeeded();

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];

        return [
            'success' => true,
            'message' => 'Đăng nhập thành công.',
            'user' => $user
        ];
    }

    public function logout(): array
    {
        $this->startSessionIfNeeded();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        return [
            'success' => true,
            'message' => 'Đăng xuất thành công.'
        ];
    }

    public function checkSession(): bool
    {
        $this->startSessionIfNeeded();
        return isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] > 0;
    }

    public function requireLogin(): void
    {
        if (!$this->checkSession()) {
            header('Location: index.php?page=login');
            exit;
        }
    }

    public function getCurrentUserId(): ?int
    {
        $this->startSessionIfNeeded();
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    public function validateUpdateProfileData(int $userId, array $data): array
    {
        $errors = [];

        $fullName = trim($data['full_name'] ?? '');
        $email = trim($data['email'] ?? '');

        if ($fullName === '') {
            $errors['full_name'] = 'Họ tên không được để trống.';
        } elseif (mb_strlen($fullName) > 100) {
            $errors['full_name'] = 'Họ tên không được vượt quá 100 ký tự.';
        }

        if ($email === '') {
            $errors['email'] = 'Email không được để trống.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ.';
        } elseif ($this->userDAL->findAnotherUserByEmail($userId, $email)) {
            $errors['email'] = 'Email này đã được sử dụng.';
        }

        return $errors;
    }

    public function updateProfile(int $userId, array $data): array
    {
        $errors = $this->validateUpdateProfileData($userId, $data);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        $updated = $this->userDAL->updateProfile(
            $userId,
            trim($data['full_name']),
            trim($data['email'])
        );

        if ($updated) {
            $this->startSessionIfNeeded();
            $_SESSION['user_name'] = trim($data['full_name']);
            $_SESSION['user_email'] = trim($data['email']);
        }

        return [
            'success' => $updated,
            'message' => $updated ? 'Cập nhật thông tin thành công.' : 'Không có thay đổi nào.'
        ];
    }

    public function validateChangePasswordData(array $data): array
    {
        $errors = [];

        $currentPassword = (string)($data['current_password'] ?? '');
        $newPassword = (string)($data['new_password'] ?? '');
        $confirmNewPassword = (string)($data['confirm_new_password'] ?? '');

        if ($currentPassword === '') {
            $errors['current_password'] = 'Vui lòng nhập mật khẩu hiện tại.';
        }

        if ($newPassword === '') {
            $errors['new_password'] = 'Vui lòng nhập mật khẩu mới.';
        } elseif (!$this->isStrongPassword($newPassword)) {
            $errors['new_password'] = 'Mật khẩu mới phải từ 8 ký tự, có chữ hoa, chữ thường và số.';
        }

        if ($confirmNewPassword === '') {
            $errors['confirm_new_password'] = 'Vui lòng xác nhận mật khẩu mới.';
        } elseif ($newPassword !== $confirmNewPassword) {
            $errors['confirm_new_password'] = 'Xác nhận mật khẩu mới không khớp.';
        }

        if ($currentPassword !== '' && $newPassword !== '' && $currentPassword === $newPassword) {
            $errors['new_password'] = 'Mật khẩu mới phải khác mật khẩu hiện tại.';
        }

        return $errors;
    }

    public function changePassword(int $userId, array $data): array
    {
        $errors = $this->validateChangePasswordData($data);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        $user = $this->userDAL->findById($userId);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy người dùng.'
            ];
        }

        if (!password_verify((string)$data['current_password'], $user['password_hash'])) {
            return [
                'success' => false,
                'errors' => [
                    'current_password' => 'Mật khẩu hiện tại không đúng.'
                ]
            ];
        }

        $newHash = password_hash((string)$data['new_password'], PASSWORD_BCRYPT);
        $updated = $this->userDAL->updatePassword($userId, $newHash);

        return [
            'success' => $updated,
            'message' => $updated ? 'Đổi mật khẩu thành công.' : 'Đổi mật khẩu thất bại.'
        ];
    }

    private function isStrongPassword(string $password): bool
    {
        return strlen($password) >= 8
            && preg_match('/[A-Z]/', $password)
            && preg_match('/[a-z]/', $password)
            && preg_match('/\d/', $password);
    }

    private function startSessionIfNeeded(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}