<?php
// Tệp: DTO/AiInsightDTO.php

class AiInsightDTO {
    private $id;
    private $userId;
    private $type;
    private $content;
    private $isRead;
    private $createdAt;

    /**
     * Constructor khởi tạo đối tượng AiInsightDTO.
     * * Các trường $userId, $type, $content là bắt buộc.
     * Các trường $isRead, $createdAt, $id có giá trị mặc định, thường được sinh ra tự động từ CSDL.
     */
    public function __construct($userId, $type, $content, $isRead = 0, $createdAt = null, $id = null) {
        $this->userId = $userId;
        $this->type = $type;
        $this->content = $content;
        $this->isRead = $isRead;
        $this->createdAt = $createdAt;
        $this->id = $id;
    }

    // =====================================
    // GETTERS (Lấy giá trị)
    // =====================================

    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getType() {
        return $this->type;
    }

    public function getContent() {
        return $this->content;
    }

    public function getIsRead() {
        return $this->isRead;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    // =====================================
    // SETTERS (Gán giá trị mới)
    // =====================================

    public function setId($id) {
        $this->id = $id;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function setType($type) {
        // Chỉ chấp nhận 3 giá trị ENUM đã định nghĩa trong DB
        $validTypes = ['anomaly', 'forecast', 'advice'];
        if (in_array($type, $validTypes)) {
            $this->type = $type;
        }
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function setIsRead($isRead) {
        $this->isRead = $isRead;
    }

    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }
}
?>