<?php
// Tệp: DTO/TransactionDTO.php

class TransactionDTO {
    private $id;
    private $userId;
    private $categoryId;
    private $amount;
    private $transactionDate;
    private $note;
    
    // Thuộc tính bổ sung phục vụ cho tầng GUI (sinh ra từ lệnh JOIN)
    private $categoryName;
    private $categoryType;

    public function __construct($userId, $categoryId, $amount, $transactionDate, $note = "", $id = null) {
        $this->userId = $userId;
        $this->categoryId = $categoryId;
        $this->amount = $amount;
        $this->transactionDate = $transactionDate;
        $this->note = $note;
        $this->id = $id;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->userId; }
    public function getCategoryId() { return $this->categoryId; }
    public function getAmount() { return $this->amount; }
    public function getTransactionDate() { return $this->transactionDate; }
    public function getNote() { return $this->note; }
    
    // Getters cho thuộc tính bổ sung
    public function getCategoryName() { return $this->categoryName; }
    public function getCategoryType() { return $this->categoryType; }

    // Setters bổ sung (Dùng khi DAL fetch dữ liệu từ DB lên)
    public function setCategoryName($categoryName) { $this->categoryName = $categoryName; }
    public function setCategoryType($categoryType) { $this->categoryType = $categoryType; }
}
?>