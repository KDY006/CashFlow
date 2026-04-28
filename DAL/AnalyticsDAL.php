<?php

/**
 * AnalyticsDAL.php — Tầng Data Access Layer cho Module Phân tích & Thống kê
 *
 * Nhiệm vụ: Thực thi tất cả câu truy vấn SQL liên quan đến analytics.
 * KHÔNG chứa logic nghiệp vụ — chỉ lấy dữ liệu thô từ DB và trả về.
 *
 * Kiến trúc: Sử dụng PDO Prepared Statements (chống SQL Injection - NFR)
 * Kết nối DB: Lấy từ Singleton trong config/database.php
 */

require_once __DIR__ . '/../config/database.php';

class AnalyticsDAL
{
    private PDO $db;

    public function __construct()
    {
        // Lấy kết nối Singleton từ database.php
        // Giả sử hàm getConnection() hoặc Database::getInstance()->getConnection()
        $this->db = Database::getInstance();
    }

    // =========================================================================
    // FR-401: BÁO CÁO DÒNG TIỀN (Cash Flow Summary)
    // =========================================================================

    /**
     * Lấy tổng thu và tổng chi trong một khoảng thời gian.
     *
     * Dùng cho: Thẻ tóm tắt trên Dashboard (Tổng thu / Tổng chi / Số dư).
     *
     * @param int    $user_id   ID người dùng hiện tại
     * @param string $from_date Ngày bắt đầu (YYYY-MM-DD)
     * @param string $to_date   Ngày kết thúc (YYYY-MM-DD)
     * @return array [['type' => 'income'|'expense', 'total' => float], ...]
     */
    public function getCashFlowSummary(int $user_id, string $from_date, string $to_date): array
    {
        $sql = "
            SELECT
                c.type,
                COALESCE(SUM(amount), 0) AS total
            FROM transactions
            WHERE user_id = :user_id
              AND transaction_date BETWEEN :from_date AND :to_date
            GROUP BY type
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id'   => $user_id,
            ':from_date' => $from_date,
            ':to_date'   => $to_date,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tổng thu và tổng chi theo từng tháng trong một năm.
     *
     * Dùng cho: Bar chart "Thu/Chi 12 tháng" trên trang Reports.
     *
     * @param int $user_id ID người dùng
     * @param int $year    Năm cần lấy (VD: 2026)
     * @return array [['month' => int, 'type' => string, 'total' => float], ...]
     */
    public function getMonthlyCashFlow(int $user_id, int $year): array
    {
        $sql = "
            SELECT
                MONTH(transaction_date) AS month,
                c.type,
                COALESCE(SUM(amount), 0) AS total
            FROM transactions
            WHERE user_id = :user_id
              AND YEAR(transaction_date) = :year
            GROUP BY MONTH(transaction_date), c.type
            ORDER BY month ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':year'    => $year,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tổng thu và tổng chi theo từng tuần trong một tháng.
     *
     * Dùng cho: Line chart "Xu hướng theo tuần" (SRS yêu cầu hỗ trợ cả tuần lẫn tháng).
     *
     * @param int $user_id ID người dùng
     * @param int $year    Năm (VD: 2026)
     * @param int $month   Tháng 1-12
     * @return array [['week' => int, 'type' => string, 'total' => float], ...]
     */
    public function getWeeklyCashFlow(int $user_id, int $year, int $month): array
    {
        $sql = "
            SELECT
                WEEK(transaction_date, 1) AS week,
                c.type,
                COALESCE(SUM(amount), 0) AS total
            FROM transactions
            WHERE user_id = :user_id
              AND YEAR(transaction_date)  = :year
              AND MONTH(transaction_date) = :month
            GROUP BY WEEK(transaction_date, 1), c.type
            ORDER BY week ASC
        ";
        // WEEK(..., 1): tuần bắt đầu từ Thứ Hai (chuẩn ISO 8601)

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':year'    => $year,
            ':month'   => $month,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================================
    // FR-402: PHÂN BỔ CHI TIÊU THEO DANH MỤC (Category Breakdown — Pie Chart)
    // =========================================================================

    /**
     * Lấy tổng chi tiêu theo từng danh mục trong một tháng.
     *
     * Dùng cho: Pie chart "Phân bổ chi tiêu" trên Dashboard.
     *
     * @param int    $user_id ID người dùng
     * @param string $month   Tháng theo định dạng YYYY-MM (VD: '2026-04')
     * @return array [['category_name' => string, 'total' => float, 'category_id' => int], ...]
     */
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
              AND t.type    = 'expense'
              AND DATE_FORMAT(t.transaction_date, '%Y-%m') = :month
            GROUP BY c.id, c.name
            ORDER BY total DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':month'   => $month,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tổng thu nhập theo từng danh mục trong một tháng.
     *
     * Dùng cho: Biểu đồ nguồn thu nhập (tùy chọn mở rộng).
     *
     * @param int    $user_id
     * @param string $month   YYYY-MM
     * @return array
     */
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
              AND t.type    = 'income'
              AND DATE_FORMAT(t.transaction_date, '%Y-%m') = :month
            GROUP BY c.id, c.name
            ORDER BY total DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':month'   => $month,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================================
    // FR-403: XU HƯỚNG THỜI GIAN (Trend — Line/Bar Chart)
    // =========================================================================

    /**
     * Lấy số dư ròng (thu - chi) theo từng tháng trong một năm.
     *
     * Dùng cho: Line chart "Xu hướng số dư" — thể hiện sức khỏe tài chính.
     *
     * @param int $user_id
     * @param int $year
     * @return array [['month' => int, 'net_balance' => float], ...]
     */
    public function getMonthlyNetBalance(int $user_id, int $year): array
    {
        $sql = "
            SELECT
                MONTH(transaction_date) AS month,
                SUM(CASE WHEN c.type = 'income'  THEN amount ELSE 0 END)
              - SUM(CASE WHEN c.type = 'expense' THEN amount ELSE 0 END) AS net_balance
            FROM transactions
            WHERE user_id = :user_id
              AND YEAR(transaction_date) = :year
            GROUP BY MONTH(transaction_date)
            ORDER BY month ASC
        ";
        // CASE WHEN trong SUM: tính income và expense trong 1 lần quét bảng (hiệu quả hơn 2 query)

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':year'    => $year,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy chi tiêu theo từng ngày trong một tháng.
     *
     * Dùng cho: Line chart chi tiết trong tháng (xem ngày nào chi nhiều).
     *
     * @param int $user_id
     * @param int $year
     * @param int $month
     * @return array [['day' => int, 'type' => string, 'total' => float], ...]
     */
    public function getDailyCashFlow(int $user_id, int $year, int $month): array
    {
        $sql = "
            SELECT
                DAY(transaction_date) AS day,
                type,
                COALESCE(SUM(amount), 0) AS total
            FROM transactions
            WHERE user_id = :user_id
              AND YEAR(transaction_date)  = :year
              AND MONTH(transaction_date) = :month
            GROUP BY DAY(transaction_date), c.type
            ORDER BY day ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':year'    => $year,
            ':month'   => $month,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================================
    // DASHBOARD — THỐNG KÊ NHANH (Quick Stats)
    // =========================================================================

    /**
     * Lấy thống kê tổng quan cho tháng hiện tại (dùng ngay khi vào Dashboard).
     *
     * Trả về 1 row duy nhất: tổng thu, tổng chi, số dư, số giao dịch.
     *
     * @param int    $user_id
     * @param string $month   YYYY-MM
     * @return array|false
     */
    public function getCurrentMonthStats(int $user_id, string $month): array|false
    {
        $sql = "
            SELECT
                SUM(CASE WHEN c.type = 'income'  THEN amount ELSE 0 END) AS total_income,
                SUM(CASE WHEN c.type = 'expense' THEN amount ELSE 0 END) AS total_expense,
                SUM(CASE WHEN c.type = 'income'  THEN amount ELSE 0 END)
              - SUM(CASE WHEN c.type = 'expense' THEN amount ELSE 0 END) AS net_balance,
                COUNT(*) AS transaction_count
            FROM transactions
            WHERE user_id = :user_id
              AND DATE_FORMAT(transaction_date, '%Y-%m') = :month
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':month'   => $month,
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy Top N danh mục chi tiêu nhiều nhất trong tháng.
     *
     * Dùng cho: Widget "Top chi tiêu" trên Dashboard.
     *
     * @param int    $user_id
     * @param string $month  YYYY-MM
     * @param int    $limit  Số lượng danh mục muốn lấy (mặc định 5)
     * @return array
     */
    public function getTopExpenseCategories(int $user_id, string $month, int $limit = 5): array
    {
        $sql = "
            SELECT
                c.name AS category_name,
                COALESCE(SUM(t.amount), 0) AS total
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = :user_id
              AND t.type    = 'expense'
              AND DATE_FORMAT(t.transaction_date, '%Y-%m') = :month
            GROUP BY c.id, c.name
            ORDER BY total DESC
            LIMIT :limit_count
        ";
        // LIMIT không thể dùng named placeholder trực tiếp với PDO — phải bindValue
        // Lý do: PDO mặc định bind tham số dạng string, LIMIT cần integer

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id',     $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':month',       $month,   PDO::PARAM_STR);
        $stmt->bindValue(':limit_count', $limit,   PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * So sánh chi tiêu tháng này vs tháng trước (MoM comparison).
     *
     * Dùng cho: Widget "So với tháng trước ▲▼" trên Dashboard.
     *
     * @param int    $user_id
     * @param string $current_month  YYYY-MM
     * @param string $previous_month YYYY-MM
     * @return array [['period' => string, 'type' => string, 'total' => float], ...]
     */
    public function getMonthOverMonthComparison(
        int $user_id,
        string $current_month,
        string $previous_month
    ): array {
        $sql = "
            SELECT
                DATE_FORMAT(transaction_date, '%Y-%m') AS period,
                c.type,
                COALESCE(SUM(amount), 0) AS total
            FROM transactions
            WHERE user_id = :user_id
              AND DATE_FORMAT(transaction_date, '%Y-%m') IN (:current_month, :previous_month)
            GROUP BY period, c.type
            ORDER BY period ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id'        => $user_id,
            ':current_month'  => $current_month,
            ':previous_month' => $previous_month,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================================
    // BÁO CÁO CHI TIẾT (Reports Page)
    // =========================================================================

    /**
     * Lấy danh sách giao dịch có filter + phân trang cho trang Reports.
     *
     * @param int    $user_id
     * @param array  $filters  ['month' => 'YYYY-MM', 'type' => 'income'|'expense'|null, 'category_id' => int|null]
     * @param int    $page     Trang hiện tại (bắt đầu từ 1)
     * @param int    $per_page Số bản ghi mỗi trang
     * @return array ['data' => [...], 'total' => int]
     */
    public function getTransactionsForReport(
        int $user_id,
        array $filters = [],
        int $page = 1,
        int $per_page = 20
    ): array {
        // Xây dựng WHERE động dựa theo filter
        $where  = ['t.user_id = :user_id'];
        $params = [':user_id' => $user_id];

        if (!empty($filters['month'])) {
            $where[]              = "DATE_FORMAT(t.transaction_date, '%Y-%m') = :month";
            $params[':month']     = $filters['month'];
        }

        if (!empty($filters['type'])) {
            $where[]          = 't.type = :type';
            $params[':type']  = $filters['type'];
        }

        if (!empty($filters['category_id'])) {
            $where[]                 = 't.category_id = :category_id';
            $params[':category_id']  = $filters['category_id'];
        }

        $whereClause = implode(' AND ', $where);

        // Đếm tổng bản ghi (dùng cho pagination)
        $countSql = "SELECT COUNT(*) FROM transactions t WHERE $whereClause";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Lấy dữ liệu trang hiện tại
        $offset = ($page - 1) * $per_page;

        $dataSql = "
            SELECT
                t.id,
                t.amount,
                t.type,
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
        // Bind params thường trước
        foreach ($params as $key => $val) {
            $dataStmt->bindValue($key, $val);
        }
        // Bind LIMIT và OFFSET cần PDO::PARAM_INT
        $dataStmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset',   $offset,   PDO::PARAM_INT);
        $dataStmt->execute();

        return [
            'data'  => $dataStmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
        ];
    }
}