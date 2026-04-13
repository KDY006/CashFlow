<?php
// Tệp: BUS/CategoryBUS.php

class CategoryBUS {
    private $categoryDAL;

    public function __construct() {
        $this->categoryDAL = new CategoryDAL();
    }

    public function getAllCategories($userId) {
        return $this->categoryDAL->getAllCategories($userId);
    }

    public function addCategory($userId, $name, $type) {
        if (empty(trim($name))) return ["status" => false, "message" => "Tên danh mục không được để trống."];
        if (!in_array($type, ['income', 'expense'])) return ["status" => false, "message" => "Loại danh mục không hợp lệ."];

        $result = $this->categoryDAL->addCategory($userId, trim($name), $type);
        if ($result) return ["status" => true, "message" => "Đã thêm danh mục mới!"];
        return ["status" => false, "message" => "Lỗi khi thêm danh mục."];
    }

    public function deleteCategory($id, $userId) {
        // Kiểm tra xem danh mục có đang chứa dòng tiền nào không
        if ($this->categoryDAL->isCategoryUsed($id, $userId)) {
            return ["status" => false, "message" => "Không thể xóa! Danh mục này đang chứa các giao dịch lịch sử."];
        }

        $result = $this->categoryDAL->deleteCategory($id, $userId);
        if ($result) return ["status" => true, "message" => "Đã xóa danh mục."];
        return ["status" => false, "message" => "Lỗi khi xóa danh mục."];
    }
}
?>