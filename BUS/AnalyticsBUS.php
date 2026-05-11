<?php
/**
 * Tệp: BUS/AnalyticsBUS.php
 */

require_once __DIR__ . '/../DAL/AnalyticsDAL.php';
require_once __DIR__ . '/../DAL/BudgetDAL.php';
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

        // Sử dụng $this->dal thay vì khởi tạo mới
        $calendar_data = $this->dal->getCalendarData($user_id, $month);
        $budget_health = $this->getBudgetHealth($user_id, $month);

        return [
            'stats'          => $this->getCurrentMonthStats($user_id, $month),
            'pie_chart'      => $this->getPieChartData($user_id, $month),
            'bar_chart'      => $this->getBarChartData($user_id, (int) substr($month, 0, 4)),
            'top_categories' => $this->getTopCategories($user_id, $month),
            'mom_comparison' => $this->getMoMComparison($user_id, $month, $prev_month),
            'alerts'         => $this->generateAlerts($user_id, $month),
            'calendar_data'  => $calendar_data,
            'budget_health'  => $budget_health
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
            $formatted[]   = FormatHelper::formatCurrency($amount);
            $colors[]      = $palette[$i % count($palette)];
        }

        return [
            'labels' => $labels, 'data' => $data, 'percentages' => $percentages,
            'formatted' => $formatted, 'colors' => $colors, 'total' => $total,
            'total_formatted' => FormatHelper::formatCurrency($total),
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
        foreach ($raw as $row) { 
            $indexed[(int)$row['week']][$row['type']] = (float) $row['total']; 
        }

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
                'category_name' => $row['category_name'], 
                'total'         => $amount,
                'formatted'     => FormatHelper::formatCurrency($amount),
                'percent'       => $percent,
            ];
        }
        return $result;
    }

    public function getMoMComparison(int $user_id, string $current_month, string $previous_month): array 
    {
        $raw = $this->dal->getMonthOverMonthComparison($user_id, $current_month, $previous_month);
        $indexed = [];
        foreach ($raw as $row) { 
            $indexed[$row['period']][$row['type']] = (float) $row['total']; 
        }

        $result = [];
        foreach (['income', 'expense'] as $type) {
            $cur  = $indexed[$current_month][$type]  ?? 0;
            $prev = $indexed[$previous_month][$type] ?? 0;

            if ($prev > 0) { 
                $change = round((($cur - $prev) / $prev) * 100, 1); 
            } elseif ($cur > 0) { 
                $change = 100; 
            } else { 
                $change = 0; 
            }

            $result[$type] = [
                'current'        => $cur, 
                'previous'       => $prev,
                'current_fmt'    => FormatHelper::formatCurrency($cur),
                'previous_fmt'   => FormatHelper::formatCurrency($prev),
                'change_percent' => $change,
                'trend'          => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'same'),
            ];
        }
        return $result;
    }

    public function generateAlerts(int $user_id, string $month): array
    {
        $stats   = $this->getCurrentMonthStats($user_id, $month);
        $income  = $stats['total_income']; 
        $expense = $stats['total_expense']; 
        $alerts  = [];

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
            $row['date_formatted']   = date('d/m/Y', strtotime($row['transaction_date']));
            return $row;
        }, $result['data']);

        $from = isset($filters['month']) ? $filters['month'] . '-01' : date('Y-m-01');
        $to   = isset($filters['month']) ? date('Y-m-t', strtotime($filters['month'] . '-01')) : date('Y-m-t');

        $cash_flow_raw = $this->dal->getCashFlowSummary($user_id, $from, $to);
        $summary = ['income' => 0, 'expense' => 0, 'net' => 0];
        
        foreach ($cash_flow_raw as $row) { 
            $summary[$row['type']] = (float) $row['total']; 
        }
        
        $summary['net']         = $summary['income'] - $summary['expense'];
        $summary['income_fmt']  = FormatHelper::formatCurrency($summary['income']);
        $summary['expense_fmt'] = FormatHelper::formatCurrency($summary['expense']);
        $summary['net_fmt']     = FormatHelper::formatCurrency($summary['net']);

        $total_pages = (int) ceil($result['total'] / $per_page);

        return [
            'transactions' => $transactions, 
            'summary'      => $summary,
            'pagination'   => ['current_page' => $page, 'total_pages' => max(1, $total_pages), 'total' => $result['total'], 'per_page' => $per_page]
        ];
    }

    private function getBudgetHealth(int $userId, string $monthStr): array
    {
        $budgetDal = new BudgetDAL();
        $parts = explode('-', $monthStr);
        $year = (int)$parts[0];
        $month = (int)$parts[1];
        
        $budgets = $budgetDal->getBudgetsByMonth($userId, $month, $year);
        $healthData = [];
        
        foreach ($budgets as $b) {
            $healthData[] = [
                'category_name'       => $b->getCategoryName(),
                'amount_limit'        => $b->getAmountLimit(),
                'total_spent'         => $b->getTotalSpent(),
                'progress_percentage' => $b->getProgressPercentage()
            ];
        }
        
        return $healthData;
    }

    public function getCalendarOnly(int $user_id, string $month): array
    {
        // Tối ưu: Dùng biến lớp
        return $this->dal->getCalendarData($user_id, $month);
    }

    public function getDailyTransactions(int $user_id, string $date): array
    {
        // Tối ưu: Dùng biến lớp
        return $this->dal->getDailyTransactions($user_id, $date);
    }

    // ==========================================
    // HÀM XỬ LÝ CHO DASHBOARD V2
    // ==========================================
    public function getDashboardV2Data(int $userId, string $filterType, string $filterVal): array
    {
        // Tối ưu: Sử dụng $this->dal thay vì new AnalyticsDAL()
        $stats = $this->dal->getDashboardV2Stats($userId, $filterType, $filterVal);
        
        $total_income  = (float)($stats['total_income'] ?? 0);
        $total_expense = (float)($stats['total_expense'] ?? 0);

        $formatList = function($dataList, $totalAmount) {
            $result = [];
            foreach ($dataList as $row) {
                $amt = (float)$row['total'];
                $pct = $totalAmount > 0 ? round(($amt / $totalAmount) * 100, 1) : 0;
                $result[] = [
                    'name'       => $row['category_name'],
                    'amount'     => $amt,
                    'amount_fmt' => FormatHelper::formatCurrency($amt),
                    'percent'    => $pct
                ];
            }
            return $result;
        };

        $expense_parent = $formatList($this->dal->getExpenseByParentV2($userId, $filterType, $filterVal), $total_expense);
        $expense_child  = $formatList($this->dal->getExpenseByChildV2($userId, $filterType, $filterVal), $total_expense);
        $income_list    = $formatList($this->dal->getIncomeV2($userId, $filterType, $filterVal), $total_income);

        $b_year = (int)date('Y'); 
        $b_month = (int)date('m');
        
        if ($filterType === 'month') {
            $parts = explode('-', $filterVal);
            $b_year = (int)$parts[0]; 
            $b_month = (int)$parts[1];
        }
        
        $budgetDal = new BudgetDAL();
        $budgetsRaw = $budgetDal->getBudgetsByMonth($userId, $b_month, $b_year);
        $budgets = [];
        
        foreach ($budgetsRaw as $b) {
            $budgets[] = [
                'name'       => $b->getCategoryName(),
                'limit'      => $b->getAmountLimit(),
                'spent'      => $b->getTotalSpent(),
                'percent'    => $b->getProgressPercentage(),
                'remain_fmt' => FormatHelper::formatCurrency(abs($b->getAmountLimit() - $b->getTotalSpent()))
            ];
        }
        usort($budgets, fn($a, $b) => $b['percent'] <=> $a['percent']);

        $historicalPeriods = [];
        
        if ($filterType === 'year') {
            $currentYear = (int)$filterVal;
            for ($i = -5; $i <= 6; $i++) {
                $y = $currentYear + $i;
                $historicalPeriods[] = ['label' => "Năm $y", 'sql' => "YEAR(t.transaction_date) = $y"];
            }
        } elseif ($filterType === 'week') {
            $parts = explode('-', $filterVal);
            $currY = (int)$parts[0];
            $currM = (int)$parts[1];
            $currW = (int)$parts[2];
            
            $getMaxWeeks = function($y, $m) {
                $firstDayStr = sprintf('%04d-%02d-01', $y, $m);
                $lastDay = (int)date('t', strtotime($firstDayStr));
                $startOffset = (int)date('N', strtotime($firstDayStr)) - 1; 
                return ceil(($lastDay + $startOffset) / 7);
            };

            for ($i = -5; $i <= 6; $i++) {
                $y = $currY; $m = $currM; $w = $currW;
                
                if ($i < 0) {
                    $steps = abs($i);
                    while ($steps > 0) {
                        $w--;
                        if ($w < 1) { 
                            $m--; 
                            if ($m < 1) { $m = 12; $y--; }
                            $w = $getMaxWeeks($y, $m);
                        }
                        $steps--;
                    }
                } elseif ($i > 0) {
                    $steps = $i;
                    while ($steps > 0) {
                        $w++;
                        $maxW = $getMaxWeeks($y, $m);
                        if ($w > $maxW) { 
                            $w = 1; $m++; 
                            if ($m > 12) { $m = 1; $y++; }
                        }
                        $steps--;
                    }
                }
                
                $padM = str_pad($m, 2, '0', STR_PAD_LEFT);
                $firstDayStr = sprintf('%04d-%02d-01', $y, $m);
                $historicalPeriods[] = [
                    'label' => "T$w/Th$padM",
                    'sql'   => "YEAR(t.transaction_date) = $y AND MONTH(t.transaction_date) = $m AND CEIL((DAY(t.transaction_date) + WEEKDAY('$firstDayStr')) / 7) = $w"
                ];
            }
        } else {
            $parts = explode('-', $filterVal);
            $currY = (int)$parts[0];
            $currM = (int)$parts[1];
            
            for ($i = -5; $i <= 6; $i++) {
                $time = strtotime(sprintf('%04d-%02d-01', $currY, $currM) . " $i months");
                $y = date('Y', $time); 
                $m = date('n', $time);
                $padM = str_pad($m, 2, '0', STR_PAD_LEFT);
                $historicalPeriods[] = [
                    'label' => "Th$padM/$y", 
                    'sql'   => "YEAR(t.transaction_date) = $y AND MONTH(t.transaction_date) = $m"
                ];
            }
        }

        $history = $this->dal->getHistoricalData($userId, $historicalPeriods);
        $net_cashflow = $total_income - $total_expense;

        return [
            'overview' => [
                'total_expense'     => $total_expense,
                'total_expense_fmt' => FormatHelper::formatCurrency($total_expense),
                'total_income'      => $total_income,
                'total_income_fmt'  => FormatHelper::formatCurrency($total_income),
                'net_cashflow'      => $net_cashflow,
                'net_cashflow_fmt'  => FormatHelper::formatCurrency(abs($net_cashflow)),
            ],
            'history_chart' => $history,
            'expense'       => ['parent' => $expense_parent, 'child' => $expense_child],
            'income'        => $income_list,
            'budgets'       => $budgets
        ];
    }
}
?>