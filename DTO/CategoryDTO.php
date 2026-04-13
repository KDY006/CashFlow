<?php
// Tệp: DTO/CategoryDTO.php

class CategoryDTO {
    private $id;
    private $userId;
    private $name;
    private $type; // 'income' hoặc 'expense'

    // Constructor để khởi tạo nhanh đối tượng
    public function __construct($userId, $name, $type, $id = null) {
        $this->userId = $userId;
        $this->name = $name;
        $this->type = $type;
        $this->id = $id;
    }

    // Các hàm Getters để lấy dữ liệu
    public function getId() { return $this->id; }
    public function getUserId() { return $this->userId; }
    public function getName() { return $this->name; }
    public function getType() { return $this->type; }

    // Các hàm Setters để cập nhật dữ liệu (nếu cần)
    public function setId($id) { $this->id = $id; }
    public function setUserId($userId) { $this->userId = $userId; }
    public function setName($name) { $this->name = $name; }
    public function setType($type) { $this->type = $type; }
}
?>