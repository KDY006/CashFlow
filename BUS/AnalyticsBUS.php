<?php

/**
 * AnalyticsBUS.php — Tầng Business Logic cho Module Phân tích & Thống kê
 *
 * Nhiệm vụ:
 *  - Gọi AnalyticsDAL để lấy dữ liệu thô
 *  - Xử lý logic nghiệp vụ: tính %, số dư, so sánh, cảnh báo
 *  - Format dữ liệu thành cấu trúc sẵn sàng cho Chart.js và View
 *
 * KHÔNG chứa câu SQL, KHÔNG echo HTML — chỉ trả về mảng PHP thuần.
 */

require_once __DIR__ . '/../DAL/AnalyticsDAL.php';
require_once __DIR__ . '/../helpers/FormatHelper.php';

class AnalyticsBUS
{
    private AnalyticsDAL $dal;

    public function __construct()
    {
        $this->dal = new AnalyticsDAL();
    }

    // =========================================================================
    // DASHBOARD — THỐNG KÊ THÁNG HIỆN TẠI
    // =========================================================================

    /**
     * Lấy toàn bộ dữ liệu cần thiết cho Dashboard trong 1 lần gọi.
     *
     * Controller chỉ cần gọi hàm này rồi truyền thẳng vào View.
     *
     * @param int    $user_id
     * @param string $month   YYYY-MM (mặc định tháng hiện tại)
     * @return array {
     *   stats: array,          // Thẻ tóm tắt (thu/chi/số dư/số giao dịch)
     *   pie_chart: array,      // Data cho Pie chart danh mục
     *   bar_chart: array,      // Data cho Bar chart 6 tháng
     *   top_categories: array, // Top 5 danh mục chi nhiều
     *   mom_comparison: array, // So sánh tháng này vs tháng trước
     *   alerts: array          // Cảnh báo tài chính
     * }
     */
    public function getDashboardData(int $user_id, string $month = ''): array
    {
        if (empty($month)) {
            $month = date('Y-m'); // Mặc định tháng hiện tại
        }

        // Tháng trước để so sánh MoM
        $prev_month = date('Y-m', strtotime($month . '-01 -1 month'));

        return [
            'stats'          => $this->getCurrentMonthStats($user_id, $month),
            'pie_chart'      => $this->getPieChartData($user_id, $month),
            'bar_chart'      => $this->getBarChartData($user_id, (int) substr($month, 0, 4)),
            'top_categories' => $this->getTopCategories($user_id, $month),
            'mom_comparison' => $this->getMoMComparison($user_id, $month, $prev_month),
            'alerts'         => $this->generateAlerts($user_id, $month),
        ];
    }

    /**
     * Thống kê tháng hiện tại — dùng cho 4 thẻ trên đầu Dashboard.
     *
     * @return array {
     *   total_income:       float,
     *   total_expense:      float,
     *   net_balance:        float,
     *   transaction_count:  int,
     *   formatted: {        // Chuỗi đã format sẵn để hiển thị
     *     total_income:  string,
     *     total_expense: string,
     *     net_balance:   string,
     *   },
     *   balance_status: 'positive'|'negative'|'zero'
     * }
     */
    public function getCurrentMonthStats(int $user_id, string $month): array
    {
        $raw = $this->dal->getCurrentMonthStats($user_id, $month);

        // Đề phòng tháng chưa có giao dịch nào → DAL trả null
        $income  = (float) ($raw['total_income']      ?? 0);
        $expense = (float) ($raw['total_expense']     ?? 0);
        $balance = (float) ($raw['net_balance']       ?? 0);
        $count   = (int)   ($raw['transaction_count'] ?? 0);

        return [
            'total_income'      => $income,
            'total_expense'     => $expense,
            'net_balance'       => $balance,
            'transaction_count' => $count,
            'formatted'         => [
                'total_income'  => FormatHelper::currency($income),
                'total_expense' => FormatHelper::currency($expense),
                'net_balance'   => FormatHelper::currency($balance),
            ],
            // Dùng để tô màu thẻ số dư (xanh / đỏ / xám)
            'balance_status' => $balance > 0 ? 'positive' : ($balance < 0 ? 'negative' : 'zero'),
        ];
    }

    // =========================================================================
    // FR-402: PIE CHART — PHÂN BỔ CHI TIÊU THEO DANH MỤC
    // =========================================================================

    /**
     * Format dữ liệu chi tiêu theo danh mục cho Chart.js Pie/Doughnut chart.
     *
     * Chart.js cần:
     *   labels: ['Ăn uống', 'Giải trí', ...]
     *   datasets[0].data: [500000, 300000, ...]
     *
     * @param int    $user_id
     * @param string $month   YYYY-MM
     * @return array {
     *   labels:      string[],
     *   data:        float[],
     *   percentages: float[],   // % từng danh mục (làm tròn 1 chữ số thập phân)
     *   formatted:   string[],  // Số tiền đã format (dùng cho tooltip)
     *   colors:      string[],  // Màu tự động generate theo index
     *   total:       float,
     *   total_formatted: string
     * }
     */
    public function getPieChartData(int $user_id, string $month): array
    {
        $raw   = $this->dal->getExpenseByCategory($user_id, $month);
        $total = array_sum(array_column($raw, 'total'));

        // Bảng màu cố định cho Pie chart (đủ cho 10 danh mục)
        $palette = [
            '#5DCAA5', '#378ADD', '#EF9F27', '#D4537E',
            '#7F77DD', '#D85A30', '#639922', '#E24B4A',
            '#888780', '#3B8BD4',
        ];

        $labels      = [];
        $data        = [];
        $percentages = [];
        $formatted   = [];
        $colors      = [];

        foreach ($raw as $i => $row) {
            $amount  = (float) $row['total'];
            $percent = $total > 0 ? round(($amount / $total) * 100, 1) : 0;

            $labels[]      = $row['category_name'];
            $data[]        = $amount;
            $percentages[] = $percent;
            $formatted[]   = FormatHelper::currency($amount);
            $colors[]      = $palette[$i % count($palette)];
        }

        return [
            'labels'          => $labels,
            'data'            => $data,
            'percentages'     => $percentages,
            'formatted'       => $formatted,
            'colors'          => $colors,
            'total'           => $total,
            'total_formatted' => FormatHelper::currency($total),
        ];
    }

    // =========================================================================
    // FR-401 + FR-403: BAR CHART — THU/CHI THEO THÁNG
    // =========================================================================

    /**
     * Format dữ liệu thu/chi 12 tháng trong năm cho Chart.js Bar chart.
     *
     * Xử lý trường hợp tháng không có giao dịch → điền 0 thay vì bỏ trống.
     * (Nếu không làm bước này, chart sẽ lệch trục X)
     *
     * @param int $user_id
     * @param int $year    Năm cần lấy (mặc định năm hiện tại)
     * @return array {
     *   labels:   string[],   // ['T1', 'T2', ..., 'T12']
     *   income:   float[],    // Mảng 12 phần tử
     *   expense:  float[],
     *   balance:  float[],    // income - expense từng tháng
     * }
     */
    public function getBarChartData(int $user_id, int $year = 0): array
    {
        if ($year === 0) {
            $year = (int) date('Y');
        }

        $raw = $this->dal->getMonthlyCashFlow($user_id, $year);

        // Index dữ liệu thô theo [tháng][loại] để tra nhanh
        $indexed = [];
        foreach ($raw as $row) {
            $indexed[(int)$row['month']][$row['type']] = (float) $row['total'];
        }

        $labels  = [];
        $income  = [];
        $expense = [];
        $balance = [];

        // Đảm bảo đủ 12 tháng, tháng nào không có dữ liệu thì = 0
        for ($m = 1; $m <= 12; $m++) {
            $labels[]  = 'T' . $m;
            $inc = $indexed[$m]['income']  ?? 0;
            $exp = $indexed[$m]['expense'] ?? 0;
            $income[]  = $inc;
            $expense[] = $exp;
            $balance[] = $inc - $exp;
        }

        return [
            'labels'  => $labels,
            'income'  => $income,
            'expense' => $expense,
            'balance' => $balance,
            'year'    => $year,
        ];
    }

    /**
     * Format dữ liệu thu/chi theo tuần trong tháng cho Line chart.
     *
     * @param int $user_id
     * @param int $year
     * @param int $month
     * @return array { labels, income, expense, balance }
     */
    public function getWeeklyChartData(int $user_id, int $year, int $month): array
    {
        $raw     = $this->dal->getWeeklyCashFlow($user_id, $year, $month);
        $indexed = [];

        foreach ($raw as $row) {
            $indexed[(int)$row['week']][$row['type']] = (float) $row['total'];
        }

        $weeks   = array_keys($indexed);
        $labels  = [];
        $income  = [];
        $expense = [];
        $balance = [];

        // Đổi số tuần tuyệt đối → "Tuần 1", "Tuần 2"...
        $weekNum = 1;
        foreach ($weeks as $w) {
            $labels[]  = 'Tuần ' . $weekNum++;
            $inc = $indexed[$w]['income']  ?? 0;
            $exp = $indexed[$w]['expense'] ?? 0;
            $income[]  = $inc;
            $expense[] = $exp;
            $balance[] = $inc - $exp;
        }

        return compact('labels', 'income', 'expense', 'balance');
    }

    // =========================================================================
    // TOP CATEGORIES & SO SÁNH THÁNG
    // =========================================================================

    /**
     * Lấy top 5 danh mục chi tiêu nhiều nhất, kèm % so với tổng chi.
     *
     * @param int    $user_id
     * @param string $month
     * @param int    $limit
     * @return array [{ category_name, total, formatted, percent }, ...]
     */
    public function getTopCategories(int $user_id, string $month, int $limit = 5): array
    {
        $raw   = $this->dal->getTopExpenseCategories($user_id, $month, $limit);
        $stats = $this->dal->getCurrentMonthStats($user_id, $month);
        $total_expense = (float) ($stats['total_expense'] ?? 0);

        $result = [];
        foreach ($raw as $row) {
            $amount  = (float) $row['total'];
            $percent = $total_expense > 0 ? round(($amount / $total_expense) * 100, 1) : 0;

            $result[] = [
                'category_name' => $row['category_name'],
                'total'         => $amount,
                'formatted'     => FormatHelper::currency($amount),
                'percent'       => $percent,
            ];
        }

        return $result;
    }

    /**
     * So sánh thu/chi tháng này vs tháng trước.
     * Tính % thay đổi và chiều hướng (tăng/giảm).
     *
     * @param int    $user_id
     * @param string $current_month   YYYY-MM
     * @param string $previous_month  YYYY-MM
     * @return array {
     *   income:  { current, previous, change_percent, trend: 'up'|'down'|'same' },
     *   expense: { current, previous, change_percent, trend: 'up'|'down'|'same' },
     * }
     */
    public function getMoMComparison(
        int $user_id,
        string $current_month,
        string $previous_month
    ): array {
        $raw     = $this->dal->getMonthOverMonthComparison($user_id, $current_month, $previous_month);
        $indexed = [];

        foreach ($raw as $row) {
            $indexed[$row['period']][$row['type']] = (float) $row['total'];
        }

        $result = [];
        foreach (['income', 'expense'] as $type) {
            $cur  = $indexed[$current_month][$type]  ?? 0;
            $prev = $indexed[$previous_month][$type] ?? 0;

            // Tính % thay đổi, tránh chia cho 0
            if ($prev > 0) {
                $change = round((($cur - $prev) / $prev) * 100, 1);
            } elseif ($cur > 0) {
                $change = 100; // Tháng trước không có, tháng này có = tăng 100%
            } else {
                $change = 0;
            }

            $result[$type] = [
                'current'        => $cur,
                'previous'       => $prev,
                'current_fmt'    => FormatHelper::currency($cur),
                'previous_fmt'   => FormatHelper::currency($prev),
                'change_percent' => $change,
                // 'up' = chi/thu tăng, 'down' = giảm
                // Với expense: 'up' là xấu; với income: 'up' là tốt — View tự xử lý màu
                'trend'          => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'same'),
            ];
        }

        return $result;
    }

    // =========================================================================
    // CẢNH BÁO TÀI CHÍNH (Financial Alerts)
    // =========================================================================

    /**
     * Sinh danh sách cảnh báo dựa trên dữ liệu tháng hiện tại.
     *
     * Các quy tắc cảnh báo:
     *  1. Chi > Thu → Âm quỹ
     *  2. Chi > 80% Thu → Sắp âm quỹ
     *  3. Không có thu nhập nào trong tháng
     *
     * @param int    $user_id
     * @param string $month
     * @return array [{ type: 'danger'|'warning'|'info', message: string }, ...]
     */
    public function generateAlerts(int $user_id, string $month): array
    {
        $stats   = $this->getCurrentMonthStats($user_id, $month);
        $income  = $stats['total_income'];
        $expense = $stats['total_expense'];
        $alerts  = [];

        if ($income === 0.0 && $expense === 0.0) {
            // Chưa có dữ liệu — không cảnh báo gì
            return [];
        }

        if ($expense > $income) {
            $alerts[] = [
                'type'    => 'danger',
                'message' => 'Chi tiêu đang vượt thu nhập '
                    . FormatHelper::currency($expense - $income) . ' trong tháng này.',
            ];
        } elseif ($income > 0 && ($expense / $income) >= 0.8) {
            $alerts[] = [
                'type'    => 'warning',
                'message' => 'Đã chi ' . round(($expense / $income) * 100) . '% thu nhập — sắp vượt ngân sách.',
            ];
        }

        if ($income === 0.0 && $expense > 0) {
            $alerts[] = [
                'type'    => 'warning',
                'message' => 'Chưa ghi nhận khoản thu nào trong tháng này.',
            ];
        }

        return $alerts;
    }

    // =========================================================================
    // TRANG REPORTS — BÁO CÁO CHI TIẾT
    // =========================================================================

    /**
     * Lấy dữ liệu cho trang Reports: danh sách giao dịch + tóm tắt + phân trang.
     *
     * @param int   $user_id
     * @param array $filters  ['month', 'type', 'category_id']
     * @param int   $page
     * @param int   $per_page
     * @return array {
     *   transactions: array,   // Danh sách giao dịch (đã format số tiền)
     *   summary: array,        // Tổng thu/chi trong filter hiện tại
     *   pagination: array      // { current_page, total_pages, total, per_page }
     * }
     */
    public function getReportData(
        int $user_id,
        array $filters = [],
        int $page = 1,
        int $per_page = 20
    ): array {
        $result = $this->dal->getTransactionsForReport($user_id, $filters, $page, $per_page);

        // Format số tiền và ngày tháng cho từng giao dịch
        $transactions = array_map(function ($row) {
            $row['amount_formatted'] = FormatHelper::currency((float) $row['amount']);
            $row['date_formatted']   = FormatHelper::date($row['transaction_date']);
            return $row;
        }, $result['data']);

        // Tính tổng thu/chi trong kết quả filter (dùng CashFlow summary)
        $from = isset($filters['month'])
            ? $filters['month'] . '-01'
            : date('Y-m-01');
        $to   = isset($filters['month'])
            ? date('Y-m-t', strtotime($filters['month'] . '-01'))
            : date('Y-m-t');

        $cash_flow_raw = $this->dal->getCashFlowSummary($user_id, $from, $to);
        $summary = ['income' => 0, 'expense' => 0, 'net' => 0];
        foreach ($cash_flow_raw as $row) {
            $summary[$row['type']] = (float) $row['total'];
        }
        $summary['net']              = $summary['income'] - $summary['expense'];
        $summary['income_fmt']       = FormatHelper::currency($summary['income']);
        $summary['expense_fmt']      = FormatHelper::currency($summary['expense']);
        $summary['net_fmt']          = FormatHelper::currency($summary['net']);

        $total_pages = (int) ceil($result['total'] / $per_page);

        return [
            'transactions' => $transactions,
            'summary'      => $summary,
            'pagination'   => [
                'current_page' => $page,
                'total_pages'  => max(1, $total_pages),
                'total'        => $result['total'],
                'per_page'     => $per_page,
            ],
        ];
    }

    // =========================================================================
    // HELPER NỘI BỘ
    // =========================================================================

    /**
     * Lấy danh sách năm có dữ liệu giao dịch (dùng cho dropdown filter năm).
     * Mặc định trả về năm hiện tại nếu chưa có dữ liệu.
     *
     * @param int $user_id
     * @return int[]
     */
    public function getAvailableYears(int $user_id): array
    {
        // Đơn giản: trả về 3 năm gần nhất (có thể mở rộng sau)
        $current = (int) date('Y');
        return [$current, $current - 1, $current - 2];
    }
}