<?php
class AiAdvisorBUS
{
    private const MAX_DAILY_CONSULTS = 3;
    private const COOLDOWN_SECONDS = 86400;
    private const AI_TIMEOUT = 60;

    private AiInsightDal $aiDal;
    private AnalyticsBUS $analyticsBus;
    private CategoryBUS $categoryBus;
    private UserBUS $userBus;

    public function __construct() {
        $this->aiDal       = new AiInsightDAL();
        $this->analyticsBus = new AnalyticsBUS();
        $this->categoryBus  = new CategoryBUS();
        $this->userBus      = new UserBUS();
    }

    public function checkCooldown(int $userId): array {
        $insights = $this->aiDal->getInsightsByUser($userId, 10);
        $activeRequests = [];
        $now = time();
        
        foreach ($insights as $msg) {
            $msgTime = strtotime($msg['created_at']);
            $diff = abs($now - $msgTime);
            if ($diff <= self::COOLDOWN_SECONDS) {
                $activeRequests[] = $msgTime;
            }
        }
        
        $used = count($activeRequests);
        $max = self::MAX_DAILY_CONSULTS; 
        
        if ($used >= $max) {
            $oldestActive = min($activeRequests); 
            
            $diff = abs($now - $oldestActive);
            $hoursLeft = ceil((self::COOLDOWN_SECONDS - $diff) / 3600);
            
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

        $transactions = $this->analyticsBus->getReportData($userId, ['month' => date('Y-m')], 1, 500);
        $res = $this->callPythonService(['type' => $type, 'transactions' => $transactions['transactions'] ?? []], '/api/chat');

        if ($res['status']) {
            $dto = new AiInsightDTO($userId, $type, $res['data']['answer']);
            $this->aiDal->insertInsight($dto);
            $this->userBus->updateLastAiConsult($userId);
            return ["status" => true, "answer" => $res['data']['answer']];
        }
        return ["status" => false, "message" => $res['error'] ?? 'Lỗi kết nối AI.'];
    }

    public function parseTextToTransaction(int $userId, string $text): array {
        $categories = $this->categoryBus->getCategoryTree($userId);
        $res = $this->callPythonService(['text' => $text, 'categories' => $categories], '/api/parse');
        if ($res['status']) return ["status" => true, "data" => $res['data']['data']];
        return ["status" => false, "message" => "AI không hiểu được nội dung này."];
    }

    public function getUserInsights(int $userId): array {
        return $this->aiDal->getInsightsByUser($userId, 100);
    }

    private function callPythonService(array $data, string $endpoint): array {
        $baseUrl = rtrim($_ENV['AI_SERVICE_URL'] ?? 'http://localhost:5000', '/');
        $ch = curl_init($baseUrl . $endpoint);
        $jsonData = json_encode($data);
        
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, 
            CURLOPT_POST => true, 
            CURLOPT_POSTFIELDS => $jsonData, 
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'], 
            CURLOPT_TIMEOUT => self::AI_TIMEOUT 
        ]);
        
        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($code !== 200) return ["status" => false, "error" => "HTTP $code"];
        return ["status" => true, "data" => json_decode($response, true)];
    }
}
?>