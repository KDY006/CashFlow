<?php
class AiInsightDTO {
    private $id;
    private $userId;
    private $type;
    private $content;
    private $isRead;
    private $createdAt;

    public function __construct($userId, $type, $content, $isRead = 0, $createdAt = null, $id = null) {
        $this->userId = $userId;
        $this->setType($type); // Gọi trực tiếp hàm setType để kiểm duyệt ngay từ lúc tạo
        $this->content = $content;
        $this->isRead = $isRead;
        $this->createdAt = $createdAt;
        $this->id = $id;
    }

    public function getId() { return $this->id; }
    public function getUserId() { return $this->userId; }
    public function getType() { return $this->type; }
    public function getContent() { return $this->content; }
    public function getIsRead() { return $this->isRead; }
    public function getCreatedAt() { return $this->createdAt; }

    public function setId($id) { $this->id = $id; }
    public function setUserId($userId) { $this->userId = $userId; }

    public function setType($type) {
        // Chỉ chấp nhận đúng 4 lệnh này
        $validTypes = ['warning', 'advice', 'forecast', 'summary'];
        if (in_array($type, $validTypes)) {
            $this->type = $type;
        } else {
            $this->type = 'summary'; // Mặc định nếu bị lỗi
        }
    }

    public function setContent($content) { $this->content = $content; }
    public function setIsRead($isRead) { $this->isRead = $isRead; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
}
?>