<?php
// Tệp: BUS/DailyNoteBUS.php

class DailyNoteBUS {
    private $dal;

    public function __construct() {
        $this->dal = new DailyNoteDAL();
    }

    public function saveOrDeleteNote($userId, $date, $content) {
        $content = trim($content);
        
        // Nếu người dùng xóa sạch chữ và bấm lưu -> Xóa luôn ghi chú trong DB
        if (empty($content)) {
            $result = $this->dal->deleteNote($userId, $date);
            return [
                'status' => $result, 
                'message' => $result ? 'Đã xóa ghi chú thành công.' : 'Lỗi khi xóa ghi chú.'
            ];
        } else {
            // Nếu có chữ -> Cập nhật hoặc Thêm mới
            $result = $this->dal->saveNote($userId, $date, $content);
            return [
                'status' => $result, 
                'message' => $result ? 'Lưu ghi chú thành công!' : 'Lỗi khi lưu ghi chú.'
            ];
        }
    }

    public function getNotesByMonth($userId, $month) {
        return $this->dal->getNotesByMonth($userId, $month);
    }
}
?>