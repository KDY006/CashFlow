<?php

require_once __DIR__ . '/../DAL/BudgetDAL.php';
require_once __DIR__ . '/../DAL/CategoryDAL.php';
require_once __DIR__ . '/../DTO/BudgetDTO.php';

class BudgetBUS
{
    private BudgetDAL $budgetDAL;
    private CategoryDAL $categoryDAL;

    public function __construct()
    {
        $this->budgetDAL = new BudgetDAL();
        $this->categoryDAL = new CategoryDAL();
    }

    public function validateBudgetData(int $userId, array $data, ?int $excludeBudgetId = null): array
    {
        $errors = [];

        $categoryId = isset($data['category_id']) ? (int)$data['category_id'] : 0;
        $budgetMonth = trim($data['budget_month'] ?? '');
        $amountLimit = isset($data['amount_limit']) ? (float)$data['amount_limit'] : 0;
        $alertThreshold = isset($data['alert_threshold']) ? (int)$data['alert_threshold'] : 80;

        if ($budgetMonth === '') {
            $errors['budget_month'] = 'Vui lòng chọn tháng ngân sách.';
        } elseif (!preg_match('/^\d{4}\-(0[1-9]|1[0-2])$/', $budgetMonth)) {
            $errors['budget_month'] = 'Tháng ngân sách phải theo định dạng YYYY-MM.';
        }

        if ($amountLimit <= 0) {
            $errors['amount_limit'] = 'Hạn mức ngân sách phải lớn hơn 0.';
        } elseif ($amountLimit > 999999999999) {
            $errors['amount_limit'] = 'Hạn mức ngân sách quá lớn.';
        }

        if ($alertThreshold < 1 || $alertThreshold > 100) {
            $errors['alert_threshold'] = 'Ngưỡng cảnh báo phải từ 1 đến 100.';
        }

        if ($categoryId < 0) {
            $errors['category_id'] = 'Danh mục không hợp lệ.';
        }

        if ($categoryId > 0) {
            $category = $this->categoryDAL->findByIdAndUser($categoryId, $userId);

            if (!$category) {
                $errors['category_id'] = 'Danh mục không tồn tại hoặc không thuộc về bạn.';
            } elseif (isset($category['type']) && $category['type'] !== 'expense') {
                $errors['category_id'] = 'Ngân sách chỉ được áp dụng cho danh mục chi tiêu.';
            }
        }

        if (empty($errors)) {
            if ($excludeBudgetId === null) {
                if ($this->budgetDAL->existsByUserMonthCategory($userId, $budgetMonth, $categoryId)) {
                    $errors['duplicate'] = 'Ngân sách cho tháng và danh mục này đã tồn tại.';
                }
            } else {
                if ($this->budgetDAL->findDuplicateBudget($excludeBudgetId, $userId, $budgetMonth, $categoryId)) {
                    $errors['duplicate'] = 'Đã tồn tại ngân sách khác trùng tháng và danh mục.';
                }
            }
        }

        return $errors;
    }

    public function createBudget(int $userId, array $data): array
    {
        $errors = $this->validateBudgetData($userId, $data);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        $budgetDTO = new BudgetDTO(
            null,
            $userId,
            (int)($data['category_id'] ?? 0),
            trim($data['budget_month']),
            (float)$data['amount_limit'],
            (int)($data['alert_threshold'] ?? 80)
        );

        $budgetId = $this->budgetDAL->create($budgetDTO);

        return [
            'success' => true,
            'message' => 'Tạo ngân sách thành công.',
            'budget_id' => $budgetId
        ];
    }

    public function updateBudget(int $userId, int $budgetId, array $data): array
    {
        $existing = $this->budgetDAL->findByIdAndUser($budgetId, $userId);

        if (!$existing) {
            return [
                'success' => false,
                'errors' => [
                    'not_found' => 'Không tìm thấy ngân sách.'
                ]
            ];
        }

        $errors = $this->validateBudgetData($userId, $data, $budgetId);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        $budgetDTO = new BudgetDTO(
            $budgetId,
            $userId,
            (int)($data['category_id'] ?? 0),
            trim($data['budget_month']),
            (float)$data['amount_limit'],
            (int)($data['alert_threshold'] ?? 80)
        );

        $updated = $this->budgetDAL->update($budgetDTO);

        return [
            'success' => $updated,
            'message' => $updated ? 'Cập nhật ngân sách thành công.' : 'Không có thay đổi nào.'
        ];
    }

    public function deleteBudget(int $userId, int $budgetId): array
    {
        $existing = $this->budgetDAL->findByIdAndUser($budgetId, $userId);

        if (!$existing) {
            return [
                'success' => false,
                'message' => 'Ngân sách không tồn tại hoặc không thuộc về bạn.'
            ];
        }

        $deleted = $this->budgetDAL->delete($budgetId, $userId);

        return [
            'success' => $deleted,
            'message' => $deleted ? 'Xóa ngân sách thành công.' : 'Xóa ngân sách thất bại.'
        ];
    }

    public function getBudgetsWithUsage(int $userId, ?string $budgetMonth = null): array
    {
        $budgets = $this->budgetDAL->getAllWithUsageByUser($userId, $budgetMonth);

        foreach ($budgets as &$budget) {
            $budget = $this->enrichBudgetData($budget);
        }

        return $budgets;
    }

    public function getBudgetDetail(int $userId, int $budgetId): ?array
    {
        $budget = $this->budgetDAL->findByIdAndUser($budgetId, $userId);

        if (!$budget) {
            return null;
        }

        return $this->enrichBudgetData($budget);
    }

    public function calculateUsedPercent(float $spentAmount, float $amountLimit): float
    {
        if ($amountLimit <= 0) {
            return 0;
        }

        return round(($spentAmount / $amountLimit) * 100, 2);
    }

    public function calculateRemainingAmount(float $spentAmount, float $amountLimit): float
    {
        return round($amountLimit - $spentAmount, 2);
    }

    public function calculateExceededAmount(float $spentAmount, float $amountLimit): float
    {
        if ($spentAmount <= $amountLimit) {
            return 0;
        }

        return round($spentAmount - $amountLimit, 2);
    }

    public function determineBudgetStatus(float $spentAmount, float $amountLimit, int $alertThreshold = 80): string
    {
        if ($amountLimit <= 0) {
            return 'invalid';
        }

        if ($spentAmount > $amountLimit) {
            return 'over';
        }

        $usedPercent = $this->calculateUsedPercent($spentAmount, $amountLimit);

        if ($usedPercent >= $alertThreshold) {
            return 'warning';
        }

        return 'safe';
    }

    public function getStatusLabel(string $status): string
    {
        return match ($status) {
            'over' => 'Vượt ngân sách',
            'warning' => 'Sắp chạm ngưỡng',
            'safe' => 'An toàn',
            default => 'Không hợp lệ',
        };
    }

    public function getStatusClass(string $status): string
    {
        return match ($status) {
            'over' => 'danger',
            'warning' => 'warning',
            'safe' => 'success',
            default => 'secondary',
        };
    }

    public function getProgressWidth(float $usedPercent): float
    {
        if ($usedPercent < 0) {
            return 0;
        }

        return min($usedPercent, 100);
    }

    public function getAlertBudgets(int $userId, ?string $budgetMonth = null): array
    {
        $budgets = $this->getBudgetsWithUsage($userId, $budgetMonth);

        return array_values(array_filter($budgets, function ($budget) {
            return in_array($budget['status'], ['warning', 'over'], true);
        }));
    }

    public function getOverBudgetItems(int $userId, ?string $budgetMonth = null): array
    {
        $budgets = $this->getBudgetsWithUsage($userId, $budgetMonth);

        return array_values(array_filter($budgets, function ($budget) {
            return $budget['status'] === 'over';
        }));
    }

    public function getBudgetSummary(int $userId, ?string $budgetMonth = null): array
    {
        $budgets = $this->getBudgetsWithUsage($userId, $budgetMonth);

        $totalBudget = 0;
        $totalSpent = 0;
        $warningCount = 0;
        $overCount = 0;
        $safeCount = 0;

        foreach ($budgets as $budget) {
            $totalBudget += (float)$budget['amount_limit'];
            $totalSpent += (float)$budget['spent_amount'];

            switch ($budget['status']) {
                case 'warning':
                    $warningCount++;
                    break;
                case 'over':
                    $overCount++;
                    break;
                case 'safe':
                    $safeCount++;
                    break;
            }
        }

        return [
            'total_budget' => round($totalBudget, 2),
            'total_spent' => round($totalSpent, 2),
            'total_remaining' => round($totalBudget - $totalSpent, 2),
            'used_percent' => $this->calculateUsedPercent($totalSpent, $totalBudget),
            'warning_count' => $warningCount,
            'over_count' => $overCount,
            'safe_count' => $safeCount,
            'total_items' => count($budgets)
        ];
    }

    public function getBudgetAlertsForDashboard(int $userId, ?string $budgetMonth = null, int $limit = 5): array
    {
        $alerts = $this->getAlertBudgets($userId, $budgetMonth);

        usort($alerts, function ($a, $b) {
            return (float)$b['used_percent'] <=> (float)$a['used_percent'];
        });

        return array_slice($alerts, 0, $limit);
    }

    public function canCreateOverallBudget(int $userId, string $budgetMonth): bool
    {
        return !$this->budgetDAL->existsByUserMonthCategory($userId, $budgetMonth, 0);
    }

    private function enrichBudgetData(array $budget): array
    {
        $amountLimit = (float)($budget['amount_limit'] ?? 0);
        $spentAmount = (float)($budget['spent_amount'] ?? 0);
        $alertThreshold = (int)($budget['alert_threshold'] ?? 80);

        $remainingAmount = $this->calculateRemainingAmount($spentAmount, $amountLimit);
        $exceededAmount = $this->calculateExceededAmount($spentAmount, $amountLimit);
        $usedPercent = $this->calculateUsedPercent($spentAmount, $amountLimit);
        $status = $this->determineBudgetStatus($spentAmount, $amountLimit, $alertThreshold);

        $budget['remaining_amount'] = $remainingAmount;
        $budget['exceeded_amount'] = $exceededAmount;
        $budget['used_percent'] = $usedPercent;
        $budget['status'] = $status;
        $budget['status_label'] = $this->getStatusLabel($status);
        $budget['status_class'] = $this->getStatusClass($status);
        $budget['progress_width'] = $this->getProgressWidth($usedPercent);
        $budget['is_over_budget'] = $status === 'over';
        $budget['is_warning'] = $status === 'warning';
        $budget['category_display'] = ((int)($budget['category_id'] ?? 0) === 0)
            ? 'Ngân sách tổng'
            : ($budget['category_name'] ?? 'Không xác định');

        return $budget;
    }
}