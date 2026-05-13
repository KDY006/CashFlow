<?php

class BudgetDTO {
    private $id;
    private $userId;
    private $categoryId;
    private $amountLimit;
    private $month;
    private $year;

    // ==========================================
    // CÁC THUỘC TÍNH BỔ SUNG (TÍNH TOÁN ĐỘNG)
    // ==========================================
    private $categoryName;
    private $categoryType;
    private $totalSpent = 0;
    private $remainAmount = 0;
    private $progressPercentage = 0;

    public function __construct($userId, $categoryId, $amountLimit, $month, $year, $id = null) {
        $this->userId = $userId;
        $this->categoryId = $categoryId;
        $this->amountLimit = $amountLimit;
        $this->month = $month;
        $this->year = $year;
        $this->id = $id;
    }

    public function getId() { return $this->id; }
    public function getUserId() { return $this->userId; }
    public function getCategoryId() { return $this->categoryId; }
    public function getAmountLimit() { return $this->amountLimit; }
    public function getMonth() { return $this->month; }
    public function getYear() { return $this->year; }

    public function getCategoryName() { return $this->categoryName; }
    public function getCategoryType() { return $this->categoryType; }
    public function getTotalSpent() { return $this->totalSpent; }
    public function getRemainAmount() { return $this->remainAmount; }
    public function getProgressPercentage() { return $this->progressPercentage; }

    public function setCategoryName($categoryName) { $this->categoryName = $categoryName; }
    public function setCategoryType($categoryType) { $this->categoryType = $categoryType; }
    
    public function setTotalSpent($totalSpent) { 
        $this->totalSpent = $totalSpent; 
        $this->remainAmount = $this->amountLimit - $this->totalSpent;
        if ($this->amountLimit > 0) {
            $this->progressPercentage = round(($this->totalSpent / $this->amountLimit) * 100, 1);
        }
    }
}
?>