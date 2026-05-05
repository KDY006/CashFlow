<?php
// Tệp: BUS/BudgetBUS.php

class BudgetBUS {
    private $budgetDAL;

    public function __construct() {
        $this->budgetDAL = new BudgetDAL();
    }

    public function getBudgetsByMonth($userId, $month, $year) {
        return $this->budgetDAL->getBudgetsByMonth($userId, $month, $year);
    }

    public function addBudget($userId, $categoryId, $amountLimit, $month, $year) {
        if (empty($categoryId)) return ["status" => false, "message" => "Vui lòng chọn danh mục."];
        if (!is_numeric($amountLimit) || $amountLimit <= 0) return ["status" => false, "message" => "Số tiền ngân sách phải lớn hơn 0."];

        $dto = new BudgetDTO($userId, $categoryId, $amountLimit, $month, $year);
        $result = $this->budgetDAL->addBudget($dto);

        if ($result) return ["status" => true, "message" => "Đã thiết lập hũ ngân sách mới!"];
        
        // Bắt lỗi Unique Key từ Database (Đã có hũ cho danh mục này trong tháng)
        return ["status" => false, "message" => "Danh mục này đã được lập ngân sách trong tháng. Vui lòng chọn 'Sửa' thay vì thêm mới."];
    }

    public function updateBudget($id, $userId, $amountLimit) {
        if (!is_numeric($amountLimit) || $amountLimit <= 0) return ["status" => false, "message" => "Số tiền phải lớn hơn 0."];

        // Tạo DTO giả để truyền dữ liệu update (không cần tháng, năm, category_id vì không cho phép sửa các thông tin đó)
        $dto = new BudgetDTO($userId, 0, $amountLimit, 0, 0, $id);
        $result = $this->budgetDAL->updateBudget($dto);

        if ($result) return ["status" => true, "message" => "Cập nhật giới hạn hũ thành công!"];
        return ["status" => false, "message" => "Lỗi khi cập nhật ngân sách."];
    }

    public function deleteBudget($id, $userId) {
        $result = $this->budgetDAL->deleteBudget($id, $userId);
        if ($result) return ["status" => true, "message" => "Đã xóa hũ ngân sách."];
        return ["status" => false, "message" => "Không thể xóa ngân sách này."];
    }

    public function clonePreviousMonthBudgets($userId, $currentMonth, $currentYear) {
        // Tính toán tháng và năm liền trước
        $prevMonth = $currentMonth - 1;
        $prevYear = $currentYear;
        
        if ($prevMonth == 0) {
            $prevMonth = 12;
            $prevYear -= 1;
        }

        // Gọi DAL để thực hiện
        $clonedCount = $this->budgetDAL->clonePreviousMonthBudgets($userId, $prevMonth, $prevYear, $currentMonth, $currentYear);

        if ($clonedCount === false) {
            return ["status" => false, "message" => "Đã xảy ra lỗi hệ thống khi sao chép."];
        }
        if ($clonedCount === 0) {
            return ["status" => false, "message" => "Tháng trước không có chiếc hũ nào để thừa kế!"];
        }

        return ["status" => true, "message" => "Đã thừa kế thành công {$clonedCount} hũ ngân sách!"];
    }
}
?>