<?php
// Tệp: DAL/DailyNoteDAL.php

class DailyNoteDAL {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function saveNote($userId, $date, $content, $pinType = 'none') {
        $sql = "INSERT INTO daily_notes (user_id, note_date, content, pin_type) 
                VALUES (:uid, :date, :content, :pin_type) 
                ON DUPLICATE KEY UPDATE content = :content_update, pin_type = :pin_type_update";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':uid' => $userId, 
            ':date' => $date, 
            ':content' => $content,
            ':pin_type' => $pinType,
            ':content_update' => $content,
            ':pin_type_update' => $pinType
        ]);
    }

    public function deleteNote($userId, $date) {
        $sql = "DELETE FROM daily_notes WHERE user_id = :uid AND note_date = :date";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':uid' => $userId, ':date' => $date]);
    }

    public function getNotesByMonth($userId, $month) {
        // Lấy ghi chú của riêng tháng hiện tại
        $sql1 = "SELECT note_date, content, pin_type FROM daily_notes WHERE user_id = :uid AND note_date LIKE :month_like";
        $stmt1 = $this->db->prepare($sql1);
        $stmt1->execute([':uid' => $userId, ':month_like' => $month . '-%']);
        $currentNotes = $stmt1->fetchAll(PDO::FETCH_ASSOC);

        // Lấy các ghi chú ĐÃ GHIM từ các tháng trước đó để kế thừa
        $sql2 = "SELECT note_date, content, pin_type FROM daily_notes WHERE user_id = :uid AND pin_type != 'none' AND note_date < :month_start";
        $stmt2 = $this->db->prepare($sql2);
        $stmt2->execute([':uid' => $userId, ':month_start' => $month . '-01']);
        $pinnedNotes = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        return ['current' => $currentNotes, 'pinned' => $pinnedNotes];
    }
}
?>