<?php
class UserDAL {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        $row = $stmt->fetch();
        if ($row) return new UserDTO($row['full_name'], $row['email'], $row['password_hash'], $row['id']);
        return null;
    }

    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function createUserWithToken(UserDTO $user, $loginToken) {
        $sql = "INSERT INTO users (full_name, email, password_hash, login_token, is_first_login) 
                VALUES (:full_name, :email, :password_hash, :login_token, 1)";
        $stmt = $this->db->prepare($sql);
        
        $fullName = $user->getFullName();
        $email = $user->getEmail();
        $passwordHash = $user->getPasswordHash();

        $stmt->bindParam(':full_name', $fullName, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password_hash', $passwordHash, PDO::PARAM_STR);
        $stmt->bindParam(':login_token', $loginToken, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Đưa tài khoản về trạng thái đăng nhập lần đầu (Dùng cho Quên mật khẩu)
    public function resetAccountForActivation($email, $passwordHash, $loginToken) {
        $sql = "UPDATE users SET password_hash = :password_hash, login_token = :login_token, is_first_login = 1 WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':password_hash', $passwordHash, PDO::PARAM_STR);
        $stmt->bindParam(':login_token', $loginToken, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function getUserByLoginToken($token) {
        $sql = "SELECT * FROM users WHERE login_token = :token LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function clearLoginToken($id) {
        $sql = "UPDATE users SET login_token = NULL WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateProfile($id, $fullName) {
        $sql = "UPDATE users SET full_name = :full_name WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':full_name', $fullName, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updatePasswordAndClearFirstLogin($id, $passwordHash) {
        $sql = "UPDATE users SET password_hash = :password_hash, is_first_login = 0 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':password_hash', $passwordHash, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updatePassword($id, $passwordHash) {
        $sql = "UPDATE users SET password_hash = :password_hash WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':password_hash', $passwordHash, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>