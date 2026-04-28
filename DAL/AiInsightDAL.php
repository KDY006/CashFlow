<?php
/**
 * AiInsightDAL.php — Tầng thao tác cơ sở dữ liệu cho Module AI
 */

require_once __DIR__ . '/../config/database.php';

class AiInsightDAL
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Thêm mới một Insight (lời khuyên/cảnh báo) từ AI vào CSDL.
     */
    public function insertInsight(AiInsightDTO $insight): bool
    {
        $sql = "INSERT INTO ai_insights (user_id, type, content, is_read) 
                VALUES (:user_id, :type, :content, 0)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $insight->getUserId(), PDO::PARAM_INT);
        $stmt->bindValue(':type',    $insight->getType(),   PDO::PARAM_STR);
        $stmt->bindValue(':content', $insight->getContent(),PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    /**
     * Lấy danh sách các Insight gần đây của người dùng.
     */
    public function getInsightsByUser(int $user_id, int $limit = 10): array
    {
        $sql = "SELECT id, type, content, is_read, created_at 
                FROM ai_insights 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT :limit_count";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id',     $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit_count', $limit,   PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đánh dấu một Insight là đã đọc.
     */
    public function markAsRead(int $insight_id, int $user_id): bool
    {
        $sql = "UPDATE ai_insights 
                SET is_read = 1 
                WHERE id = :id AND user_id = :user_id";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id',      $insight_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id,    PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}