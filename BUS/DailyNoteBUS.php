<?php
// Tệp: BUS/DailyNoteBUS.php

class DailyNoteBUS {
    private $dal;

    public function __construct() {
        $this->dal = new DailyNoteDAL();
    }

    public function saveOrDeleteNote($userId, $date, $content, $pinType) {
        $content = trim($content);
        if (empty($content)) {
            $result = $this->dal->deleteNote($userId, $date);
            return ['status' => $result, 'message' => $result ? 'Đã xóa ghi chú thành công.' : 'Lỗi khi xóa ghi chú.'];
        } else {
            $result = $this->dal->saveNote($userId, $date, $content, $pinType);
            return ['status' => $result, 'message' => $result ? 'Lưu ghi chú thành công!' : 'Lỗi khi lưu ghi chú.'];
        }
    }

    public function getNotesByMonth($userId, $month) {
        $data = $this->dal->getNotesByMonth($userId, $month);
        $finalNotes = [];

        $y = (int)substr($month, 0, 4);
        $m = (int)substr($month, 5, 2);
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $m, $y);

        // 1. Rải các ghi chú ĐƯỢC KẾ THỪA từ tháng trước lên lịch
        foreach ($data['pinned'] as $note) {
            $noteDate = strtotime($note['note_date']);
            
            if ($note['pin_type'] === 'monthly') {
                $day = date('d', $noteDate);
                if ($day <= $daysInMonth) {
                    $targetDate = sprintf('%04d-%02d-%02d', $y, $m, $day);
                    $finalNotes[$targetDate] = ['content' => $note['content'], 'pin_type' => 'monthly', 'is_inherited' => true];
                }
            } elseif ($note['pin_type'] === 'weekly') {
                $dayOfWeek = date('N', $noteDate); // 1 = Thứ 2, 7 = CN
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $targetDate = sprintf('%04d-%02d-%02d', $y, $m, $d);
                    if (date('N', strtotime($targetDate)) == $dayOfWeek) {
                        $finalNotes[$targetDate] = ['content' => $note['content'], 'pin_type' => 'weekly', 'is_inherited' => true];
                    }
                }
            }
        }

        // 2. Rải các ghi chú CỦA THÁNG HIỆN TẠI (Sẽ ghi đè lên kế thừa nếu bị trùng ngày)
        foreach ($data['current'] as $note) {
            $finalNotes[$note['note_date']] = [
                'content' => $note['content'], 
                'pin_type' => $note['pin_type'], 
                'is_inherited' => false
            ];
        }

        // Đóng gói lại thành mảng array
        $resultList = [];
        foreach ($finalNotes as $date => $info) {
            $resultList[] = [
                'note_date' => $date,
                'content' => $info['content'],
                'pin_type' => $info['pin_type'],
                'is_inherited' => $info['is_inherited']
            ];
        }

        return $resultList;
    }
}
?>