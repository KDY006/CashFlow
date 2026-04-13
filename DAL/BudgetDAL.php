<?php

class BudgetDAL {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ==========================================
    // LẤY NGÂN SÁCH THEO THÁNG (KÈM TÍNH TOÁN)
    // ==========================================
    public function getBudgetsByMonth($userId, $month, $year) {
        $sql = "SELECT 
                    b.*, 
                    c.name as category_name, 
                    c.type as category_type,
                    COALESCE(SUM(t.amount), 0) as total_spent
                FROM budgets b
                JOIN categories c ON b.category_id = c.id
                LEFT JOIN transactions t ON t.category_id = b.category_id 
                                        AND t.user_id = b.user_id 
                                        AND MONTH(t.transaction_date) = b.month 
                                        AND YEAR(t.transaction_date) = b.year
                WHERE b.user_id = :user_id AND b.month = :month AND b.year = :year
                GROUP BY b.id
                ORDER BY b.amount_limit DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->execute();

        $budgets = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dto = new BudgetDTO(
                $row['user_id'], $row['category_id'], $row['amount_limit'], 
                $row['month'], $row['year'], $row['id']
            );
            $dto->setCategoryName($row['category_name']);
            $dto->setCategoryType($row['category_type']);
            $dto->setTotalSpent($row['total_spent']); 
            
            $budgets[] = $dto;
        }
        return $budgets;
    }

    // ==========================================
    // THÊM NGÂN SÁCH MỚI
    // ==========================================
    public function addBudget(BudgetDTO $budget) {
        try {
            $sql = "INSERT INTO budgets (user_id, category_id, amount_limit, month, year) 
                    VALUES (:user_id, :category_id, :amount_limit, :month, :year)";
            
            $stmt = $this->db->prepare($sql);
            
            $userId = $budget->getUserId();
            $categoryId = $budget->getCategoryId();
            $amountLimit = $budget->getAmountLimit();
            $month = $budget->getMonth();
            $year = $budget->getYear();

            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            $stmt->bindParam(':amount_limit', $amountLimit, PDO::PARAM_STR);
            $stmt->bindParam(':month', $month, PDO::PARAM_INT);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // ==========================================
    // CẬP NHẬT NGÂN SÁCH
    // ==========================================
    public function updateBudget(BudgetDTO $budget) {
        try {
            $sql = "UPDATE budgets 
                    SET amount_limit = :amount_limit 
                    WHERE id = :id AND user_id = :user_id";
            
            $stmt = $this->db->prepare($sql);
            
            $id = $budget->getId();
            $userId = $budget->getUserId();
            $amountLimit = $budget->getAmountLimit();

            $stmt->bindParam(':amount_limit', $amountLimit, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // ==========================================
    // XÓA NGÂN SÁCH
    // ==========================================
    public function deleteBudget($id, $userId) {
        $sql = "DELETE FROM budgets WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>