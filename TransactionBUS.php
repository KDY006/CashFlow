<?php

require_once __DIR__ . '/../DAL/TransactionDAL.php';
require_once __DIR__ . '/../DAL/CategoryDAL.php';
require_once __DIR__ . '/../DTO/TransactionDTO.php';

class TransactionBUS
{
    private TransactionDAL $transactionDAL;
    private CategoryDAL $categoryDAL;

    public function __construct()
    {
        $this->transactionDAL = new TransactionDAL();
        $this->categoryDAL = new CategoryDAL();
    }

    public function validateTransactionData(int $userId, array $data): array
    {
        $errors = [];

        $amount = isset($data['amount']) ? (float)$data['amount'] : 0;
        $transactionDate = trim($data['transaction_date'] ?? '');
        $categoryId = isset($data['category_id']) ? (int)$data['category_id'] : 0;
        $note = trim($data['note'] ?? '');

        if ($amount <= 0) {
            $errors['amount'] = 'Số tiền phải lớn hơn 0.';
        } elseif ($amount > 999999999999) {
            $errors['amount'] = 'Số tiền quá lớn.';
        }

        if ($transactionDate === '') {
            $errors['transaction_date'] = 'Ngày giao dịch không được để trống.';
        } elseif (!$this->isValidDate($transactionDate)) {
            $errors['transaction_date'] = 'Ngày giao dịch không hợp lệ.';
        } elseif (!$this->isLogicalDate($transactionDate)) {
            $errors['transaction_date'] = 'Ngày giao dịch không hợp lý.';
        }

        if ($categoryId <= 0) {
            $errors['category_id'] = 'Vui lòng chọn danh mục.';
        } else {
            $category = $this->categoryDAL->findByIdAndUser($categoryId, $userId);
            if (!$category) {
                $errors['category_id'] = 'Danh mục không tồn tại hoặc không thuộc về bạn.';
            }
        }

        if (mb_strlen($note) > 500) {
            $errors['note'] = 'Ghi chú không được vượt quá 500 ký tự.';
        }

        return $errors;
    }

    public function createTransaction(int $userId, array $data): array
    {
        $errors = $this->validateTransactionData($userId, $data);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        $transactionDTO = new TransactionDTO(
            null,
            $userId,
            (int)$data['category_id'],
            (float)$data['amount'],
            trim($data['transaction_date']),
            trim($data['note'] ?? ''),
            null,
            null
        );

        $id = $this->transactionDAL->create($transactionDTO);

        return [
            'success' => true,
            'message' => 'Thêm giao dịch thành công.',
            'transaction_id' => $id
        ];
    }

    public function updateTransaction(int $userId, int $transactionId, array $data): array
    {
        $errors = $this->validateTransactionData($userId, $data);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        $existing = $this->transactionDAL->findByIdAndUser($transactionId, $userId);

        if (!$existing) {
            return [
                'success' => false,
                'message' => 'Giao dịch không tồn tại hoặc không thuộc về bạn.'
            ];
        }

        $transactionDTO = new TransactionDTO(
            $transactionId,
            $userId,
            (int)$data['category_id'],
            (float)$data['amount'],
            trim($data['transaction_date']),
            trim($data['note'] ?? ''),
            null,
            null
        );

        $updated = $this->transactionDAL->update($transactionDTO);

        return [
            'success' => $updated,
            'message' => $updated ? 'Cập nhật giao dịch thành công.' : 'Không có thay đổi nào.'
        ];
    }

    public function deleteTransaction(int $userId, int $transactionId): array
    {
        $existing = $this->transactionDAL->findByIdAndUser($transactionId, $userId);

        if (!$existing) {
            return [
                'success' => false,
                'message' => 'Giao dịch không tồn tại hoặc không thuộc về bạn.'
            ];
        }

        $deleted = $this->transactionDAL->delete($transactionId, $userId);

        return [
            'success' => $deleted,
            'message' => $deleted ? 'Xóa giao dịch thành công.' : 'Xóa giao dịch thất bại.'
        ];
    }

    public function getTransactions(
        int $userId,
        ?string $month = null,
        ?int $year = null,
        ?string $type = null,
        int $page = 1,
        int $limit = 20
    ): array {
        $page = max(1, $page);
        $limit = max(1, min($limit, 100));

        if ($month !== null && $month !== '' && !preg_match('/^(0?[1-9]|1[0-2])$/', (string)$month)) {
            return [
                'data' => [],
                'message' => 'Tháng lọc không hợp lệ.'
            ];
        }

        if ($year !== null && ($year < 2000 || $year > 2100)) {
            return [
                'data' => [],
                'message' => 'Năm lọc không hợp lệ.'
            ];
        }

        if ($type !== null && $type !== '' && !in_array($type, ['income', 'expense'], true)) {
            return [
                'data' => [],
                'message' => 'Loại giao dịch không hợp lệ.'
            ];
        }

        return $this->transactionDAL->getAllByUser($userId, $month, $year, $type, $page, $limit);
    }

    public function getTransactionDetail(int $userId, int $transactionId): ?array
    {
        return $this->transactionDAL->findByIdAndUser($transactionId, $userId);
    }

    public function getMonthlyBalance(int $userId, int $month, int $year): array
    {
        if ($month < 1 || $month > 12 || $year < 2000 || $year > 2100) {
            return [
                'total_income' => 0,
                'total_expense' => 0,
                'balance' => 0,
                'message' => 'Tháng hoặc năm không hợp lệ.'
            ];
        }

        $summary = $this->transactionDAL->getMonthlySummary($userId, $month, $year);

        $income = (float)($summary['total_income'] ?? 0);
        $expense = (float)($summary['total_expense'] ?? 0);

        return [
            'total_income' => $income,
            'total_expense' => $expense,
            'balance' => $income - $expense
        ];
    }

    public function getCurrentBalance(int $userId): float
    {
        $summary = $this->transactionDAL->getOverallSummary($userId);

        $income = (float)($summary['total_income'] ?? 0);
        $expense = (float)($summary['total_expense'] ?? 0);

        return $income - $expense;
    }

    public function getQuickSummary(int $userId, int $month, int $year): array
    {
        $monthly = $this->getMonthlyBalance($userId, $month, $year);
        $currentBalance = $this->getCurrentBalance($userId);

        return [
            'monthly_income' => $monthly['total_income'] ?? 0,
            'monthly_expense' => $monthly['total_expense'] ?? 0,
            'monthly_balance' => $monthly['balance'] ?? 0,
            'current_balance' => $currentBalance
        ];
    }

    private function isValidDate(string $date): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    private function isLogicalDate(string $date): bool
    {
        $inputDate = new DateTime($date);
        $today = new DateTime('today');
        $minDate = new DateTime('2000-01-01');

        return $inputDate >= $minDate && $inputDate <= $today;
    }
}