<?php
// Tệp: DAL/DailyNoteDAL.php

class DailyNoteDAL {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function saveNote($userId, $date, $content) {
        $sql = "INSERT INTO daily_notes (user_id, note_date, content) 
                VALUES (:uid, :date, :content_insert) 
                ON DUPLICATE KEY UPDATE content = :content_update";
                
        $stmt = $this->db->prepare($sql);
        
        // Truyền vào 4 tham số rõ ràng cho 4 vị trí trong SQL
        return $stmt->execute([
            ':uid'            => $userId, 
            ':date'           => $date, 
            ':content_insert' => $content,
            ':content_update' => $content
        ]);
    }

    // Xóa ghi chú
    public function deleteNote($userId, $date) {
        $sql = "DELETE FROM daily_notes WHERE user_id = :uid AND note_date = :date";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':uid' => $userId, 
            ':date' => $date
        ]);
    }

    // Lấy tất cả ghi chú trong 1 tháng
    public function getNotesByMonth($userId, $month) {
        $sql = "SELECT note_date, content FROM daily_notes WHERE user_id = :uid AND note_date LIKE :month";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':uid' => $userId, 
            ':month' => $month . '-%'
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>