<?php
/**
 * AnalyticsDAL.php — Tầng Data Access Layer cho Module Phân tích & Thống kê
 */

require_once __DIR__ . '/../config/database.php';

class AnalyticsDAL
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getCashFlowSummary(int $user_id, string $from_date, string $to_date): array
    {
        $sql = "
            SELECT
                c.type,
                COALESCE(SUM(t.amount), 0) AS total
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = :user_id
              AND t.transaction_date BETWEEN :from_date AND :to_date
            GROUP BY c.type
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id, ':from_date' => $from_date, ':to_date' => $to_date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMonthlyCashFlow(int $user_id, int $year): array
    {
        $sql = "
            SELECT
                MONTH(t.transaction_date) AS month,
                c.type,
                COALESCE(SUM(t.amount), 0) AS total
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = :user_id
              AND YEAR(t.transaction_date) = :year
            GROUP BY MONTH(t.transaction_date), c.type
            ORDER BY month ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id, ':year' => $year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWeeklyCashFlow(int $user_id, int $year, int $month): array
    {
        $sql = "
            SELECT
                WEEK(t.transaction_date, 1) AS week,
                c.type,
                COALESCE(SUM(t.amount), 0) AS total
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = :user_id
              AND YEAR(t.transaction_date)  = :year
              AND MONTH(t.transaction_date) = :month
            GROUP BY WEEK(t.transaction_date, 1), c.type
            ORDER BY week ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id, ':year' => $year, ':month' => $month]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getExpenseByCategory(int $user_id, string $month): array
    {
        $sql = "
            SELECT
                c.id   AS category_id,
                c.name AS category_name,
                COALESCE(SUM(t.amount), 0) AS total
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = :user_id
              AND c.type    = 'expense'
              AND DATE_FORMAT(t.transaction_date, '%Y-%m') = :month
            GROUP BY c.id, c.name
            ORDER BY total DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id, ':month' => $month]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIncomeByCategory(int $user_id, string $month): array
    {
        $sql = "
            SELECT
                c.id   AS category_id,
                c.name AS category_name,
                COALESCE(SUM(t.amount), 0) AS total
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = :user_id
              AND c.type    = 'income'
              AND DATE_FORMAT(t.transaction_date, '%Y-%m') = :month
            GROUP BY c.id, c.name
            ORDER BY total DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id, ':month' => $month]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMonthlyNetBalance(int $user_id, int $year): array
    {
        $sql = "
            SELECT
                MONTH(t.transaction_date) AS month,
                SUM(CASE WHEN c.type = 'income'  THEN t.amount ELSE 0 END)
              - SUM(CASE WHEN c.type = 'expense' THEN t.amount ELSE 0 END) AS net_balance
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = :user_id
              AND YEAR(t.transaction_date) = :year
            GROUP BY MONTH(t.transaction_date)
            ORDER BY month ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id, ':year' => $year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDailyCashFlow(int $user_id, int $year, int $month): array
    {
        $sql = "
            SELECT
                DAY(t.transaction_date) AS day,
                c.type,
                COALESCE(SUM(t.amount), 0) AS total
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = :user_id
              AND YEAR(t.transaction_date)  = :year
              AND MONTH(t.transaction_date) = :month
            GROUP BY DAY(t.transaction_date), c.type
            ORDER BY day ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id, ':year' => $year, ':month' => $month]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCurrentMonthStats(int $user_id, string $month): array|false
    {
        $sql = "
            SELECT
                SUM(CASE WHEN c.type = 'income'  THEN t.amount ELSE 0 END) AS total_income,
                SUM(CASE WHEN c.type = 'expense' THEN t.amount ELSE 0 END) AS total_expense,
                SUM(CASE WHEN c.type = 'income'  THEN t.amount ELSE 0 END)
              - SUM(CASE WHEN c.type = 'expense' THEN t.amount ELSE 0 END) AS net_balance,
                COUNT(*) AS transaction_count
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = :user_id
              AND DATE_FORMAT(t.transaction_date, '%Y-%m') = :month
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id, ':month' => $month]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTopExpenseCategories(int $user_id, string $month, int $limit = 5): array
    {
        $sql = "
            SELECT
                c.name AS category_name,
                COALESCE(SUM(t.amount), 0) AS total
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = :user_id
              AND c.type    = 'expense'
              AND DATE_FORMAT(t.transaction_date, '%Y-%m') = :month
            GROUP BY c.id, c.name
            ORDER BY total DESC
            LIMIT :limit_count
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id',     $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':month',       $month,   PDO::PARAM_STR);
        $stmt->bindValue(':limit_count', $limit,   PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMonthOverMonthComparison(int $user_id, string $current_month, string $previous_month): array 
    {
        $sql = "
            SELECT
                DATE_FORMAT(t.transaction_date, '%Y-%m') AS period,
                c.type,
                COALESCE(SUM(t.amount), 0) AS total
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = :user_id
              AND DATE_FORMAT(t.transaction_date, '%Y-%m') IN (:current_month, :previous_month)
            GROUP BY period, c.type
            ORDER BY period ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id, ':current_month' => $current_month, ':previous_month' => $previous_month]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTransactionsForReport(int $user_id, array $filters = [], int $page = 1, int $per_page = 20): array 
    {
        $where  = ['t.user_id = :user_id'];
        $params = [':user_id' => $user_id];

        if (!empty($filters['month'])) {
            $where[]              = "DATE_FORMAT(t.transaction_date, '%Y-%m') = :month";
            $params[':month']     = $filters['month'];
        }

        if (!empty($filters['type'])) {
            $where[]          = 'c.type = :type';
            $params[':type']  = $filters['type'];
        }

        if (!empty($filters['category_id'])) {
            $where[]                 = 't.category_id = :category_id';
            $params[':category_id']  = $filters['category_id'];
        }

        $whereClause = implode(' AND ', $where);

        $countSql = "SELECT COUNT(*) FROM transactions t INNER JOIN categories c ON t.category_id = c.id WHERE $whereClause";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $per_page;

        $dataSql = "
            SELECT
                t.id,
                t.amount,
                c.type,
                t.transaction_date,
                t.note,
                c.name AS category_name
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE $whereClause
            ORDER BY t.transaction_date DESC, t.id DESC
            LIMIT :per_page OFFSET :offset
        ";

        $dataStmt = $this->db->prepare($dataSql);
        foreach ($params as $key => $val) {
            $dataStmt->bindValue($key, $val);
        }
        $dataStmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset',   $offset,   PDO::PARAM_INT);
        $dataStmt->execute();

        return [
            'data'  => $dataStmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
        ];
    }

    public function getAvailableYears(int $user_id): array
    {
        $current = (int) date('Y');
        return [$current, $current - 1, $current - 2];
    }

    // ==========================================
    // DỮ LIỆU LỊCH GIAO DỊCH TRONG THÁNG
    // ==========================================
    public function getCalendarData(int $user_id, string $month): array
    {
        $sql = "
            SELECT
                DATE(t.transaction_date) AS date_val,
                SUM(CASE WHEN c.type = 'income' THEN t.amount ELSE 0 END) AS income,
                SUM(CASE WHEN c.type = 'expense' THEN t.amount ELSE 0 END) AS expense,
                IF(SUM(CASE WHEN c.type = 'expense' THEN t.amount ELSE 0 END) > 1000000, 1, 0) AS is_anomaly
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = :user_id
              AND DATE_FORMAT(t.transaction_date, '%Y-%m') = :month
            GROUP BY DATE(t.transaction_date)
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id, ':month' => $month]);
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $calendarData = [];
        
        // Format lại mảng với Key là Ngày (YYYY-MM-DD) để Frontend vẽ lịch dễ hơn
        foreach ($results as $row) {
            $calendarData[$row['date_val']] = [
                'income'     => (float)$row['income'],
                'expense'    => (float)$row['expense'],
                'is_anomaly' => (bool)$row['is_anomaly']
            ];
        }
        
        return $calendarData;
    }

    // Lấy danh sách giao dịch chi tiết của một ngày
    public function getDailyTransactions(int $user_id, string $date): array
    {
        $sql = "
            SELECT
                t.id,
                t.amount,
                t.note,
                DATE_FORMAT(t.created_at, '%d/%m/%Y %H:%i') AS time_val,
                c.name AS category_name,
                c.type AS category_type
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = :user_id
              AND DATE(t.transaction_date) = :date_val
            ORDER BY t.transaction_date DESC, t.id DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id, ':date_val' => $date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>