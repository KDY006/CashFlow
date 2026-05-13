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
        $sql = "SELECT DISTINCT YEAR(transaction_date) as year FROM transactions WHERE user_id = :user_id ORDER BY year DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        $years = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($years)) {
            return [(int) date('Y')];
        }
        return array_map('intval', $years);
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

    // ==========================================
    // CÁC HÀM CHO DASHBOARD V2 (GIAO DIỆN MOMO)
    // ==========================================
    private function buildTimeCondition(string $filterType, string $filterVal, array &$params): string
    {
        if ($filterType === 'year') {
            $params[':year'] = $filterVal;
            return "YEAR(t.transaction_date) = :year";
        } elseif ($filterType === 'week') {
            // Tách Dữ liệu: Ví dụ '2026-05-2' thành Năm 2026, Tháng 5, Tuần 2
            $parts = explode('-', $filterVal);
            $params[':year'] = $parts[0];
            $params[':month'] = $parts[1];
            $params[':week'] = $parts[2];
            $firstDayStr = $parts[0] . '-' . $parts[1] . '-01';
            
            // Công thức SQL bóc tách chính xác Tuần của Tháng
            return "YEAR(t.transaction_date) = :year AND MONTH(t.transaction_date) = :month AND CEIL((DAY(t.transaction_date) + WEEKDAY('$firstDayStr')) / 7) = :week";
        } else { // default is month
            $params[':month'] = $filterVal;
            return "DATE_FORMAT(t.transaction_date, '%Y-%m') = :month";
        }
    }

    public function getDashboardV2Stats(int $userId, string $filterType, string $filterVal): array
    {
        $params = [':uid' => $userId];
        $where = $this->buildTimeCondition($filterType, $filterVal, $params);

        $sql = "SELECT
                    SUM(CASE WHEN c.type = 'income' THEN t.amount ELSE 0 END) AS total_income,
                    SUM(CASE WHEN c.type = 'expense' THEN t.amount ELSE 0 END) AS total_expense
                FROM transactions t
                JOIN categories c ON t.category_id = c.id
                WHERE t.user_id = :uid AND $where";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total_income' => 0, 'total_expense' => 0];
    }

    public function getExpenseByParentV2(int $userId, string $filterType, string $filterVal): array
    {
        $params = [':uid' => $userId];
        $where = $this->buildTimeCondition($filterType, $filterVal, $params);

        $sql = "SELECT
                    COALESCE(p.name, c.name) AS category_name,
                    SUM(t.amount) AS total
                FROM transactions t
                JOIN categories c ON t.category_id = c.id
                LEFT JOIN categories p ON c.parent_id = p.id
                WHERE t.user_id = :uid AND c.type = 'expense' AND $where
                GROUP BY COALESCE(p.id, c.id), COALESCE(p.name, c.name)
                ORDER BY total DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getExpenseByChildV2(int $userId, string $filterType, string $filterVal): array
    {
        $params = [':uid' => $userId];
        $where = $this->buildTimeCondition($filterType, $filterVal, $params);

        $sql = "SELECT c.name AS category_name, SUM(t.amount) AS total
                FROM transactions t
                JOIN categories c ON t.category_id = c.id
                WHERE t.user_id = :uid AND c.type = 'expense' AND $where
                GROUP BY c.id, c.name
                ORDER BY total DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIncomeV2(int $userId, string $filterType, string $filterVal): array
    {
        $params = [':uid' => $userId];
        $where = $this->buildTimeCondition($filterType, $filterVal, $params);

        $sql = "SELECT c.name AS category_name, SUM(t.amount) AS total
                FROM transactions t
                JOIN categories c ON t.category_id = c.id
                WHERE t.user_id = :uid AND c.type = 'income' AND $where
                GROUP BY c.id, c.name
                ORDER BY total DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy dữ liệu Thu/Chi cho biểu đồ lịch sử 12 kỳ
    public function getHistoricalData(int $userId, array $periods): array
    {
        $results = [];
        foreach ($periods as $p) {
            $params = [':uid' => $userId];
            $sqlCondition = "";
            
            if ($p['type'] === 'year') {
                $sqlCondition = "YEAR(t.transaction_date) = :year";
                $params[':year'] = $p['year'];
            } elseif ($p['type'] === 'month') {
                $sqlCondition = "YEAR(t.transaction_date) = :year AND MONTH(t.transaction_date) = :month";
                $params[':year'] = $p['year'];
                $params[':month'] = $p['month'];
            } elseif ($p['type'] === 'week') {
                $sqlCondition = "YEAR(t.transaction_date) = :year AND MONTH(t.transaction_date) = :month AND CEIL((DAY(t.transaction_date) + WEEKDAY(:firstDayStr)) / 7) = :week";
                $params[':year'] = $p['year'];
                $params[':month'] = $p['month'];
                $params[':week'] = $p['week'];
                $params[':firstDayStr'] = $p['firstDayStr'];
            }

            $sql = "SELECT 
                        SUM(CASE WHEN c.type = 'income' THEN t.amount ELSE 0 END) AS income,
                        SUM(CASE WHEN c.type = 'expense' THEN t.amount ELSE 0 END) AS expense
                    FROM transactions t
                    JOIN categories c ON t.category_id = c.id
                    WHERE t.user_id = :uid AND " . $sqlCondition;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $results[] = [
                'label'   => $p['label'],
                'income'  => (float)($row['income'] ?? 0),
                'expense' => (float)($row['expense'] ?? 0)
            ];
        }
        return $results;
    }
}
?>