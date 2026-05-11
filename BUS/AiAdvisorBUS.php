<?php
require_once __DIR__ . '/../DAL/AiInsightDAL.php';
require_once __DIR__ . '/../DAL/AnalyticsDAL.php';
require_once __DIR__ . '/../DAL/UserDAL.php';

class AiAdvisorBUS
{
    private AiInsightDal $aiDal;
    private AnalyticsDAL $analyticsDal;

    public function __construct() {
        $this->aiDal = new AiInsightDAL();
        $this->analyticsDal = new AnalyticsDAL();
    }

    public function checkCooldown(int $userId): array {
        $insights = $this->aiDal->getInsightsByUser($userId, 10);
        $activeRequests = [];
        $now = time();
        
        foreach ($insights as $msg) {
            $msgTime = strtotime($msg['created_at']);
            $diff = abs($now - $msgTime);
            if ($diff <= 86400) {
                $activeRequests[] = $msgTime;
            }
        }
        
        $used = count($activeRequests);
        $max = 3; 
        
        if ($used >= $max) {
            rsort($activeRequests); 
            $oldestActive = min($activeRequests); 
            
            $diff = abs($now - $oldestActive);
            $hoursLeft = ceil((86400 - $diff) / 3600);
            
            if ($hoursLeft < 1) $hoursLeft = 1;
            if ($hoursLeft > 24) $hoursLeft = 24;
            
            return ['can_consult' => false, 'hours_left' => $hoursLeft, 'used' => $used, 'max' => $max];
        }
        
        return ['can_consult' => true, 'used' => $used, 'max' => $max, 'hours_left' => 0];
    }

    public function chatConsult(int $userId, string $type): array {
        $check = $this->checkCooldown($userId);
        if (!$check['can_consult']) {
            return ["status" => false, "message" => "Vui lòng chờ {$check['hours_left']} giờ nữa để nhận thêm lượt phân tích mới."];
        }

        $transactions = $this->analyticsDal->getTransactionsForReport($userId, ['month' => date('Y-m')], 1, 500);
        $res = $this->callPythonService(['type' => $type, 'transactions' => $transactions['data'] ?? []], '/api/chat');

        if ($res['status']) {
            $dto = new AiInsightDTO($userId, $type, $res['data']['answer']);
            $this->aiDal->insertInsight($dto);
            (new UserDAL())->updateLastAiConsult($userId);
            return ["status" => true, "answer" => $res['data']['answer']];
        }
        return ["status" => false, "message" => $res['error'] ?? 'Lỗi kết nối AI.'];
    }

    public function parseTextToTransaction(int $userId, string $text): array {
        $stmt = Database::getInstance()->prepare("SELECT id, name, type FROM categories WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        $res = $this->callPythonService(['text' => $text, 'categories' => $stmt->fetchAll(PDO::FETCH_ASSOC)], '/api/parse');
        if ($res['status']) return ["status" => true, "data" => $res['data']['data']];
        return ["status" => false, "message" => "AI không hiểu được nội dung này."];
    }

    public function getUserInsights(int $userId): array {
        return $this->aiDal->getInsightsByUser($userId, 100);
    }

    private function callPythonService(array $data, string $endpoint): array {
        $ch = curl_init('http://localhost:5000' . $endpoint);
        $jsonData = json_encode($data);
        
        // TĂNG TIMEOUT LÊN 60 GIÂY ĐỂ TRÁNH LỖI HTTP 0
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, 
            CURLOPT_POST => true, 
            CURLOPT_POSTFIELDS => $jsonData, 
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'], 
            CURLOPT_TIMEOUT => 60 
        ]);
        
        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($code !== 200) return ["status" => false, "error" => "HTTP $code"];
        return ["status" => true, "data" => json_decode($response, true)];
    }
}
?>