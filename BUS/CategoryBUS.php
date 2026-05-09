<?php
// Tệp: BUS/CategoryBUS.php

class CategoryBUS {
    private $categoryDAL;

    public function __construct() {
        $this->categoryDAL = new CategoryDAL();
    }

    public function getCategoryTree($userId) {
        return $this->categoryDAL->getCategoryTree($userId);
    }

    public function addCategory($userId, $name, $type, $parentId = null) {
        $name = trim($name);
        if (empty($name)) {
            return ['status' => false, 'message' => 'Tên danh mục không được để trống.'];
        }

        // Quy tắc: Nếu là Chi tiêu (expense) thì bắt buộc phải chọn nhóm cha
        if ($type === 'expense' && empty($parentId)) {
            return ['status' => false, 'message' => 'Danh mục chi tiêu bắt buộc phải thuộc một nhóm cha.'];
        }

        // Nếu là Thu nhập (income) thì ép parentId về null (không có cha)
        if ($type === 'income') {
            $parentId = null;
        }

        $result = $this->categoryDAL->addCategory($userId, $name, $type, $parentId);
        if ($result) {
            return ['status' => true, 'message' => 'Thêm danh mục thành công!'];
        }
        return ['status' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại.'];
    }

    public function deleteCategory($id, $userId) {
        if ($this->categoryDAL->isCategoryUsed($id, $userId)) {
            return ['status' => false, 'message' => 'Danh mục này đã có giao dịch, không thể xóa!'];
        }
        $result = $this->categoryDAL->deleteCategory($id, $userId);
        return ['status' => $result, 'message' => $result ? 'Đã xóa danh mục.' : 'Lỗi xóa danh mục.'];
    }
}
?>