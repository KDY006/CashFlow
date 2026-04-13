<?php
// Tệp: BUS/TransactionBUS.php

class TransactionBUS {
    private $transactionDAL;
    private $categoryDAL;

    public function __construct() {
        $this->transactionDAL = new TransactionDAL();
        $this->categoryDAL = new CategoryDAL();
    }

    // Lấy danh mục để nạp vào form Thêm/Sửa
    public function getCategories($userId, $type = null) {
        if ($type) {
            return $this->categoryDAL->getCategoriesByType($userId, $type);
        }
        return $this->categoryDAL->getAllCategories($userId);
    }

    // Xử lý logic Thêm giao dịch
    public function addTransaction($userId, $categoryId, $amount, $transactionDate, $note) {
        // 1. Validation cơ bản
        if (empty($categoryId)) return ["status" => false, "message" => "Vui lòng chọn danh mục giao dịch."];
        if (!is_numeric($amount) || $amount <= 0) return ["status" => false, "message" => "Số tiền phải là số hợp lệ và lớn hơn 0."];
        if (empty($transactionDate)) return ["status" => false, "message" => "Vui lòng chọn ngày giao dịch."];

        // 2. Chống nhập ngày tương lai phi logic (cho phép lùi 1 ngày để bù trừ múi giờ nếu có)
        if (strtotime($transactionDate) > strtotime('+1 day')) {
            return ["status" => false, "message" => "Ngày giao dịch không thể là một ngày trong tương lai chưa xảy ra."];
        }

        // 3. Đóng gói DTO và gọi DAL
        $dto = new TransactionDTO($userId, $categoryId, $amount, $transactionDate, trim($note));
        $result = $this->transactionDAL->addTransaction($dto);

        if ($result) {
            return ["status" => true, "message" => "Ghi nhận giao dịch thành công!"];
        }
        return ["status" => false, "message" => "Hệ thống đang bận, không thể lưu giao dịch lúc này."];
    }

    // Xử lý logic Lấy danh sách giao dịch
    public function getTransactions($userId, $limit = 20, $offset = 0, $month = null, $year = null, $type = null) {
        return $this->transactionDAL->getTransactions($userId, $limit, $offset, $month, $year, $type);
    }

    // Xử lý logic Lấy chi tiết một giao dịch
    public function getTransactionById($id, $userId) {
        return $this->transactionDAL->getTransactionById($id, $userId);
    }

    // Xử lý logic Cập nhật giao dịch
    public function updateTransaction($id, $userId, $categoryId, $amount, $transactionDate, $note) {
        if (empty($categoryId)) return ["status" => false, "message" => "Vui lòng chọn danh mục giao dịch."];
        if (!is_numeric($amount) || $amount <= 0) return ["status" => false, "message" => "Số tiền phải là số hợp lệ và lớn hơn 0."];
        if (empty($transactionDate)) return ["status" => false, "message" => "Vui lòng chọn ngày giao dịch."];

        $dto = new TransactionDTO($userId, $categoryId, $amount, $transactionDate, trim($note), $id);
        $result = $this->transactionDAL->updateTransaction($dto);

        if ($result) {
            return ["status" => true, "message" => "Cập nhật thông tin giao dịch thành công!"];
        }
        return ["status" => false, "message" => "Lưu thay đổi thất bại."];
    }

    // Xử lý logic Xóa giao dịch
    public function deleteTransaction($id, $userId) {
        $result = $this->transactionDAL->deleteTransaction($id, $userId);
        if ($result) return ["status" => true, "message" => "Đã xóa giao dịch khỏi hệ thống."];
        return ["status" => false, "message" => "Không thể xóa giao dịch này."];
    }
}
?>