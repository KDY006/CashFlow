<?php

require_once __DIR__ . '/../DAL/AnalyticsDAL.php';

class AnalyticsBUS
{
    private AnalyticsDAL $analyticsDAL;

    public function __construct()
    {
        $this->analyticsDAL = new AnalyticsDAL();
    }

    public function validateDateRange(?string $fromDate, ?string $toDate): array
    {
        $errors = [];

        if ($fromDate !== null && $fromDate !== '' && !$this->isValidDate($fromDate)) {
            $errors['from_date'] = 'Từ ngày không hợp lệ.';
        }

        if ($toDate !== null && $toDate !== '' && !$this->isValidDate($toDate)) {
            $errors['to_date'] = 'Đến ngày không hợp lệ.';
        }

        if (empty($errors) && $fromDate && $toDate && $fromDate > $toDate) {
            $errors['date_range'] = 'Khoảng thời gian không hợp lệ.';
        }

        return $errors;
    }

    public function getDashboardSummary(int $userId, ?string $fromDate = null, ?string $toDate = null): array
    {
        $errors = $this->validateDateRange($fromDate, $toDate);

        if (!empty($errors)) {
            return [
                'total_income' => 0,
                'total_expense' => 0,
                'net_cash_flow' => 0,
                'errors' => $errors
            ];
        }

        $summary = $this->analyticsDAL->getIncomeExpenseSummary($userId, $fromDate, $toDate);

        $income = (float)($summary['total_income'] ?? 0);
        $expense = (float)($summary['total_expense'] ?? 0);

        return [
            'total_income' => $income,
            'total_expense' => $expense,
            'net_cash_flow' => round($income - $expense, 2)
        ];
    }

    public function getExpenseByCategory(int $userId, ?string $fromDate = null, ?string $toDate = null): array
    {
        $errors = $this->validateDateRange($fromDate, $toDate);

        if (!empty($errors)) {
            return [];
        }

        $rows = $this->analyticsDAL->getExpenseByCategory($userId, $fromDate, $toDate);

        $totalExpense = 0;
        foreach ($rows as $row) {
            $totalExpense += (float)($row['total_amount'] ?? 0);
        }

        foreach ($rows as &$row) {
            $amount = (float)($row['total_amount'] ?? 0);
            $row['percentage'] = $totalExpense > 0
                ? round(($amount / $totalExpense) * 100, 2)
                : 0;
        }

        usort($rows, function ($a, $b) {
            return (float)$b['total_amount'] <=> (float)$a['total_amount'];
        });

        return $rows;
    }

    public function getCashFlowTrendByMonth(int $userId, int $year): array
    {
        if ($year < 2000 || $year > 2100) {
            return [];
        }

        $rows = $this->analyticsDAL->getCashFlowTrendByMonth($userId, $year);

        $result = [];
        for ($month = 1; $month <= 12; $month++) {
            $result[$month] = [
                'month' => $month,
                'total_income' => 0,
                'total_expense' => 0,
                'balance' => 0
            ];
        }

        foreach ($rows as $row) {
            $month = (int)$row['month'];
            $income = (float)($row['total_income'] ?? 0);
            $expense = (float)($row['total_expense'] ?? 0);

            $result[$month] = [
                'month' => $month,
                'total_income' => $income,
                'total_expense' => $expense,
                'balance' => round($income - $expense, 2)
            ];
        }

        return array_values($result);
    }

    public function getCashFlowTrendByWeek(int $userId, string $month): array
    {
        if (!preg_match('/^\d{4}\-(0[1-9]|1[0-2])$/', $month)) {
            return [];
        }

        return $this->analyticsDAL->getCashFlowTrendByWeek($userId, $month);
    }

    public function getTopExpenseCategory(int $userId, ?string $fromDate = null, ?string $toDate = null): ?array
    {
        $categories = $this->getExpenseByCategory($userId, $fromDate, $toDate);

        return !empty($categories) ? $categories[0] : null;
    }

    public function compareMonthlyCashFlow(int $userId, int $month, int $year): array
    {
        $currentFrom = sprintf('%04d-%02d-01', $year, $month);
        $currentTo = date('Y-m-t', strtotime($currentFrom));

        $previousDate = date('Y-m-01', strtotime('-1 month', strtotime($currentFrom)));
        $previousFrom = $previousDate;
        $previousTo = date('Y-m-t', strtotime($previousDate));

        $current = $this->getDashboardSummary($userId, $currentFrom, $currentTo);
        $previous = $this->getDashboardSummary($userId, $previousFrom, $previousTo);

        return [
            'current' => $current,
            'previous' => $previous,
            'income_change' => round(($current['total_income'] ?? 0) - ($previous['total_income'] ?? 0), 2),
            'expense_change' => round(($current['total_expense'] ?? 0) - ($previous['total_expense'] ?? 0), 2),
            'cash_flow_change' => round(($current['net_cash_flow'] ?? 0) - ($previous['net_cash_flow'] ?? 0), 2)
        ];
    }

    public function getRecentAnalyticsInsights(int $userId): array
    {
        $currentMonth = date('Y-m');
        $fromDate = $currentMonth . '-01';
        $toDate = date('Y-m-t', strtotime($fromDate));
        $currentYear = (int)date('Y');
        $currentMonthNumber = (int)date('m');

        $summary = $this->getDashboardSummary($userId, $fromDate, $toDate);
        $topCategory = $this->getTopExpenseCategory($userId, $fromDate, $toDate);
        $comparison = $this->compareMonthlyCashFlow($userId, $currentMonthNumber, $currentYear);
        $monthlyTrend = $this->getCashFlowTrendByMonth($userId, $currentYear);

        return [
            'summary' => $summary,
            'top_expense_category' => $topCategory,
            'comparison' => $comparison,
            'monthly_trend' => $monthlyTrend
        ];
    }

    private function isValidDate(string $date): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}