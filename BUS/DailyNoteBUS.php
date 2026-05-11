<?php
class DailyNoteBUS 
{
    private DailyNoteDAL $dal;

    public function __construct() 
    {
        $this->dal = new DailyNoteDAL();
    }

    public function saveOrDeleteNote(int $userId, string $date, string $content, string $pinType): array 
    {
        $content = trim($content);
        
        if (empty($content)) {
            $isSuccess = $this->dal->deleteNote($userId, $date);
            return [
                'status'  => $isSuccess, 
                'message' => $isSuccess ? 'Đã xóa ghi chú thành công.' : 'Lỗi khi xóa ghi chú.'
            ];
        } 
        
        $isSuccess = $this->dal->saveNote($userId, $date, $content, $pinType);
        return [
            'status'  => $isSuccess, 
            'message' => $isSuccess ? 'Lưu ghi chú thành công!' : 'Lỗi khi lưu ghi chú.'
        ];
    }

    public function getNotesByMonth(int $userId, string $month): array 
    {
        $data = $this->dal->getNotesByMonth($userId, $month);
        $finalNotes = [];

        $year        = (int) substr($month, 0, 4);
        $monthNum    = (int) substr($month, 5, 2);
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $monthNum, $year);

        // 1. Kế thừa ghi chú từ các tháng trước
        foreach ($data['pinned'] as $note) {
            $noteDate = strtotime($note['note_date']);
            
            if ($note['pin_type'] === 'monthly') {
                $day = (int) date('d', $noteDate);
                if ($day <= $daysInMonth) {
                    $targetDate = sprintf('%04d-%02d-%02d', $year, $monthNum, $day);
                    $finalNotes[$targetDate] = [
                        'content'      => $note['content'], 
                        'pin_type'     => 'monthly', 
                        'is_inherited' => true
                    ];
                }
            } elseif ($note['pin_type'] === 'weekly') {
                $dayOfWeek = (int) date('N', $noteDate); 
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $targetDate = sprintf('%04d-%02d-%02d', $year, $monthNum, $d);
                    if ((int) date('N', strtotime($targetDate)) === $dayOfWeek) {
                        $finalNotes[$targetDate] = [
                            'content'      => $note['content'], 
                            'pin_type'     => 'weekly', 
                            'is_inherited' => true
                        ];
                    }
                }
            }
        }

        // 2. Ghi đè bằng ghi chú của tháng hiện tại
        foreach ($data['current'] as $note) {
            $finalNotes[$note['note_date']] = [
                'content'      => $note['content'], 
                'pin_type'     => $note['pin_type'], 
                'is_inherited' => false
            ];
        }

        // 3. Đóng gói kết quả
        $resultList = [];
        foreach ($finalNotes as $date => $info) {
            $resultList[] = [
                'note_date'    => $date,
                'content'      => $info['content'],
                'pin_type'     => $info['pin_type'],
                'is_inherited' => $info['is_inherited']
            ];
        }

        return $resultList;
    }
}
?>