<?php
// Tệp: DAL/CategoryDAL.php

class CategoryDAL {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllCategories(int $userId): array {
        $sql = "SELECT * FROM categories WHERE user_id = :user_id OR user_id IS NULL ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoriesByType(int $userId, string $type): array {
        $sql = "SELECT * FROM categories WHERE (user_id = :user_id OR user_id IS NULL) AND type = :type ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId, ':type' => $type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // LẤY DANH MỤC THEO CẤU TRÚC CÂY (CHA - CON)
    public function getCategoryTree($userId) {
        // Ưu tiên hiển thị: Thu nhập trước, Chi tiêu sau. Sau đó sắp xếp theo cha con.
        $sql = "SELECT * FROM categories WHERE user_id = :user_id OR user_id IS NULL ORDER BY type DESC, parent_id ASC, name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tree = [];
        $lookup = [];

        // Khởi tạo mảng con trống cho tất cả các danh mục
        foreach ($rows as $row) {
            $row['children'] = [];
            $lookup[$row['id']] = $row;
        }

        // Bắt đầu nhúng các danh mục con vào danh mục cha tương ứng
        foreach ($lookup as $id => &$item) {
            if ($item['parent_id'] === null) {
                // Nếu không có cha => Là danh mục gốc (Level 1)
                $tree[] = &$item;
            } else {
                // Nếu có cha => Nhét nó vào mảng children của thằng cha
                if (isset($lookup[$item['parent_id']])) {
                    $lookup[$item['parent_id']]['children'][] = &$item;
                }
            }
        }
        return $tree;
    }

    // THÊM DANH MỤC (Hỗ trợ parent_id)
    public function addCategory($userId, $name, $type, $parentId = null) {
        $sql = "INSERT INTO categories (user_id, name, type, parent_id) VALUES (:user_id, :name, :type, :parent_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':name' => $name,
            ':type' => $type,
            ':parent_id' => $parentId
        ]);
    }

    public function getCategoryById($id) {
        $sql = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteCategory($id, $userId) {
        $sql = "DELETE FROM categories WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function isCategoryUsed($id, $userId) {
        $sql = "SELECT COUNT(*) FROM transactions WHERE category_id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
?>