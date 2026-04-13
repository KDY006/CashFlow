<?php
// Tệp: DAL/CategoryDAL.php

class CategoryDAL {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllCategories($userId) {
        $sql = "SELECT * FROM categories WHERE user_id = :user_id ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoriesByType($userId, $type) {
        $sql = "SELECT * FROM categories WHERE user_id = :user_id AND type = :type ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById($id, $userId) {
        $sql = "SELECT * FROM categories WHERE id = :id AND user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =====================================
    // CÁC HÀM MỚI BỔ SUNG CHO CHỨC NĂNG QUẢN LÝ
    // =====================================

    public function addCategory($userId, $name, $type) {
        $sql = "INSERT INTO categories (user_id, name, type) VALUES (:user_id, :name, :type)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Kiểm tra xem danh mục đang được sử dụng trong giao dịch nào không
    public function isCategoryUsed($id, $userId) {
        $sql = "SELECT COUNT(*) FROM transactions WHERE category_id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function deleteCategory($id, $userId) {
        $sql = "DELETE FROM categories WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>