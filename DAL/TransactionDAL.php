<?php
// Tệp: DAL/TransactionDAL.php

class TransactionDAL {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ==========================================
    // 1. THÊM GIAO DỊCH (Sử dụng SQL Transaction)
    // ==========================================
    public function addTransaction(TransactionDTO $transaction) {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO transactions (user_id, category_id, amount, transaction_date, note) 
                    VALUES (:user_id, :category_id, :amount, :transaction_date, :note)";
            $stmt = $this->db->prepare($sql);

            $userId = $transaction->getUserId();
            $categoryId = $transaction->getCategoryId();
            $amount = $transaction->getAmount();
            $transactionDate = $transaction->getTransactionDate();
            $note = $transaction->getNote();

            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            $stmt->bindParam(':amount', $amount, PDO::PARAM_STR); // DECIMAL truyền dưới dạng STR để giữ độ chính xác
            $stmt->bindParam(':transaction_date', $transactionDate, PDO::PARAM_STR);
            $stmt->bindParam(':note', $note, PDO::PARAM_STR);

            $stmt->execute();
            
            // Lấy ID vừa tạo (nếu cần thiết sau này)
            $lastInsertId = $this->db->lastInsertId();

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            // Ghi log lỗi vào file nếu cần thiết trên môi trường thực tế
            return false;
        }
    }

    // ==========================================
    // 2. LẤY DANH SÁCH GIAO DỊCH (Có phân trang & Lọc)
    // ==========================================
    public function getTransactions($userId, $limit = 20, $offset = 0, $month = null, $year = null, $type = null) {
        // Query cơ bản có JOIN bảng categories
        $sql = "SELECT t.*, c.name as category_name, c.type as category_type 
                FROM transactions t 
                JOIN categories c ON t.category_id = c.id 
                WHERE t.user_id = :user_id";
        
        $params = [':user_id' => $userId];

        // Xây dựng câu lệnh động dựa trên bộ lọc
        if ($month && $year) {
            $sql .= " AND MONTH(t.transaction_date) = :month AND YEAR(t.transaction_date) = :year";
            $params[':month'] = $month;
            $params[':year'] = $year;
        }
        if ($type) {
            $sql .= " AND c.type = :type";
            $params[':type'] = $type;
        }

        // Sắp xếp ngày mới nhất lên đầu, giới hạn phân trang
        $sql .= " ORDER BY t.transaction_date DESC, t.id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        // Bind parameters linh hoạt
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        
        // Đóng gói thành mảng DTO thay vì mảng Array thông thường
        $transactions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dto = new TransactionDTO(
                $row['user_id'], $row['category_id'], $row['amount'], 
                $row['transaction_date'], $row['note'], $row['id']
            );
            $dto->setCategoryName($row['category_name']);
            $dto->setCategoryType($row['category_type']);
            $transactions[] = $dto;
        }
        return $transactions;
    }

    // ==========================================
    // 3. LẤY CHI TIẾT 1 GIAO DỊCH (Để chỉnh sửa)
    // ==========================================
    public function getTransactionById($id, $userId) {
        $sql = "SELECT t.*, c.name as category_name, c.type as category_type 
                FROM transactions t 
                JOIN categories c ON t.category_id = c.id 
                WHERE t.id = :id AND t.user_id = :user_id LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ==========================================
    // 4. CẬP NHẬT GIAO DỊCH
    // ==========================================
    public function updateTransaction(TransactionDTO $transaction) {
        try {
            $this->db->beginTransaction();

            $sql = "UPDATE transactions 
                    SET category_id = :category_id, amount = :amount, 
                        transaction_date = :transaction_date, note = :note 
                    WHERE id = :id AND user_id = :user_id";
            
            $stmt = $this->db->prepare($sql);

            $id = $transaction->getId();
            $userId = $transaction->getUserId();
            $categoryId = $transaction->getCategoryId();
            $amount = $transaction->getAmount();
            $transactionDate = $transaction->getTransactionDate();
            $note = $transaction->getNote();

            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
            $stmt->bindParam(':transaction_date', $transactionDate, PDO::PARAM_STR);
            $stmt->bindParam(':note', $note, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

            $result = $stmt->execute();
            $this->db->commit();
            return $result;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // ==========================================
    // 5. XÓA GIAO DỊCH
    // ==========================================
    public function deleteTransaction($id, $userId) {
        try {
            $this->db->beginTransaction();
            
            $sql = "DELETE FROM transactions WHERE id = :id AND user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            
            $result = $stmt->execute();
            $this->db->commit();
            return $result;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
?>