<?php
/**
 * Tệp: BUS/AnalyticsBUS.php
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

    public function getDashboardData(int $user_id, string $month = ''): array
    {
        if (empty($month)) $month = date('Y-m'); 
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

    public function getCurrentMonthStats(int $user_id, string $month): array
    {
        $raw = $this->dal->getCurrentMonthStats($user_id, $month);

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
                // ĐÃ SỬA LỖI TÊN HÀM Ở ĐÂY
                'total_income'  => FormatHelper::formatCurrency($income),
                'total_expense' => FormatHelper::formatCurrency($expense),
                'net_balance'   => FormatHelper::formatCurrency($balance),
            ],
            'balance_status' => $balance > 0 ? 'positive' : ($balance < 0 ? 'negative' : 'zero'),
        ];
    }

    public function getPieChartData(int $user_id, string $month): array
    {
        $raw   = $this->dal->getExpenseByCategory($user_id, $month);
        $total = array_sum(array_column($raw, 'total'));

        $palette = ['#5DCAA5', '#378ADD', '#EF9F27', '#D4537E', '#7F77DD', '#D85A30', '#639922', '#E24B4A', '#888780', '#3B8BD4'];

        $labels = []; $data = []; $percentages = []; $formatted = []; $colors = [];

        foreach ($raw as $i => $row) {
            $amount  = (float) $row['total'];
            $percent = $total > 0 ? round(($amount / $total) * 100, 1) : 0;

            $labels[]      = $row['category_name'];
            $data[]        = $amount;
            $percentages[] = $percent;
            $formatted[]   = FormatHelper::formatCurrency($amount); // ĐÃ SỬA TÊN HÀM
            $colors[]      = $palette[$i % count($palette)];
        }

        return [
            'labels' => $labels, 'data' => $data, 'percentages' => $percentages,
            'formatted' => $formatted, 'colors' => $colors, 'total' => $total,
            'total_formatted' => FormatHelper::formatCurrency($total), // ĐÃ SỬA TÊN HÀM
        ];
    }

    public function getBarChartData(int $user_id, int $year = 0): array
    {
        if ($year === 0) $year = (int) date('Y');
        $raw = $this->dal->getMonthlyCashFlow($user_id, $year);

        $indexed = [];
        foreach ($raw as $row) {
            $indexed[(int)$row['month']][$row['type']] = (float) $row['total'];
        }

        $labels = []; $income = []; $expense = []; $balance = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[]  = 'T' . $m;
            $inc = $indexed[$m]['income']  ?? 0;
            $exp = $indexed[$m]['expense'] ?? 0;
            $income[]  = $inc; $expense[] = $exp; $balance[] = $inc - $exp;
        }

        return ['labels' => $labels, 'income' => $income, 'expense' => $expense, 'balance' => $balance, 'year' => $year];
    }

    public function getWeeklyChartData(int $user_id, int $year, int $month): array
    {
        $raw = $this->dal->getWeeklyCashFlow($user_id, $year, $month);
        $indexed = [];
        foreach ($raw as $row) { $indexed[(int)$row['week']][$row['type']] = (float) $row['total']; }

        $weeks = array_keys($indexed); $labels = []; $income = []; $expense = []; $balance = [];

        $weekNum = 1;
        foreach ($weeks as $w) {
            $labels[]  = 'Tuần ' . $weekNum++;
            $inc = $indexed[$w]['income']  ?? 0;
            $exp = $indexed[$w]['expense'] ?? 0;
            $income[]  = $inc; $expense[] = $exp; $balance[] = $inc - $exp;
        }
        return compact('labels', 'income', 'expense', 'balance');
    }

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
                'category_name' => $row['category_name'], 'total' => $amount,
                'formatted'     => FormatHelper::formatCurrency($amount), // ĐÃ SỬA TÊN HÀM
                'percent'       => $percent,
            ];
        }
        return $result;
    }

    public function getMoMComparison(int $user_id, string $current_month, string $previous_month): array 
    {
        $raw = $this->dal->getMonthOverMonthComparison($user_id, $current_month, $previous_month);
        $indexed = [];
        foreach ($raw as $row) { $indexed[$row['period']][$row['type']] = (float) $row['total']; }

        $result = [];
        foreach (['income', 'expense'] as $type) {
            $cur  = $indexed[$current_month][$type]  ?? 0;
            $prev = $indexed[$previous_month][$type] ?? 0;

            if ($prev > 0) { $change = round((($cur - $prev) / $prev) * 100, 1); } 
            elseif ($cur > 0) { $change = 100; } else { $change = 0; }

            $result[$type] = [
                'current'        => $cur, 'previous' => $prev,
                'current_fmt'    => FormatHelper::formatCurrency($cur), // ĐÃ SỬA TÊN HÀM
                'previous_fmt'   => FormatHelper::formatCurrency($prev), // ĐÃ SỬA TÊN HÀM
                'change_percent' => $change,
                'trend'          => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'same'),
            ];
        }
        return $result;
    }

    public function generateAlerts(int $user_id, string $month): array
    {
        $stats   = $this->getCurrentMonthStats($user_id, $month);
        $income  = $stats['total_income']; $expense = $stats['total_expense']; $alerts  = [];

        if ($income === 0.0 && $expense === 0.0) return [];

        if ($expense > $income) {
            $alerts[] = ['type' => 'danger', 'message' => 'Chi tiêu đang vượt thu nhập ' . FormatHelper::formatCurrency($expense - $income) . ' trong tháng này.'];
        } elseif ($income > 0 && ($expense / $income) >= 0.8) {
            $alerts[] = ['type' => 'warning', 'message' => 'Đã chi ' . round(($expense / $income) * 100) . '% thu nhập — sắp vượt ngân sách.'];
        }

        if ($income === 0.0 && $expense > 0) {
            $alerts[] = ['type' => 'warning', 'message' => 'Chưa ghi nhận khoản thu nào trong tháng này.'];
        }
        return $alerts;
    }

    public function getReportData(int $user_id, array $filters = [], int $page = 1, int $per_page = 20): array 
    {
        $result = $this->dal->getTransactionsForReport($user_id, $filters, $page, $per_page);

        $transactions = array_map(function ($row) {
            $row['amount_formatted'] = FormatHelper::formatCurrency((float) $row['amount']);
            $row['date_formatted']   = date('d/m/Y', strtotime($row['transaction_date'])); // ĐÃ SỬA THÀNH DATE CHUẨN
            return $row;
        }, $result['data']);

        $from = isset($filters['month']) ? $filters['month'] . '-01' : date('Y-m-01');
        $to   = isset($filters['month']) ? date('Y-m-t', strtotime($filters['month'] . '-01')) : date('Y-m-t');

        $cash_flow_raw = $this->dal->getCashFlowSummary($user_id, $from, $to);
        $summary = ['income' => 0, 'expense' => 0, 'net' => 0];
        foreach ($cash_flow_raw as $row) { $summary[$row['type']] = (float) $row['total']; }
        
        $summary['net']         = $summary['income'] - $summary['expense'];
        $summary['income_fmt']  = FormatHelper::formatCurrency($summary['income']);
        $summary['expense_fmt'] = FormatHelper::formatCurrency($summary['expense']);
        $summary['net_fmt']     = FormatHelper::formatCurrency($summary['net']);

        $total_pages = (int) ceil($result['total'] / $per_page);

        return [
            'transactions' => $transactions, 'summary' => $summary,
            'pagination'   => ['current_page' => $page, 'total_pages' => max(1, $total_pages), 'total' => $result['total'], 'per_page' => $per_page]
        ];
    }
}
?>