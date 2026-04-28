<?php
/**
 * AiAdvisorBUS.php — Cầu nối giữa PHP Backend và Python AI Service
 */

require_once __DIR__ . '/../DAL/AiInsightDAL.php';
require_once __DIR__ . '/../DAL/AnalyticsDAL.php';

class AiAdvisorBUS
{
    private AiInsightDal $aiDal;
    private AnalyticsDAL $analyticsDal;
    
    // URL của Python API (Giả sử bạn chạy Flask ở cổng 5000)
    private string $aiServiceUrl = 'http://localhost:5000/api/analyze';

    public function __construct()
    {
        $this->aiDal = new AiInsightDAL();
        $this->analyticsDal = new AnalyticsDAL();
    }

    /**
     * Hàm chính: Gửi dữ liệu cho Python phân tích và lưu kết quả.
     */
    public function analyzeUserFinances(int $user_id): array
    {
        // 1. Chuẩn bị dữ liệu (Lấy tối đa 500 giao dịch gần nhất của tháng này để AI học)
        $currentMonth = date('Y-m');
        $transactionsData = $this->analyticsDal->getTransactionsForReport($user_id, ['month' => $currentMonth], 1, 500);
        
        if (empty($transactionsData['data'])) {
            return ["status" => false, "message" => "Không đủ dữ liệu giao dịch để AI phân tích."];
        }

        $payload = [
            'user_id'      => $user_id,
            'month'        => $currentMonth,
            'transactions' => $transactionsData['data']
        ];

        // 2. Gọi sang API của Python bằng cURL
        $aiResponse = $this->callPythonService($payload);

        if (!$aiResponse['status']) {
            return ["status" => false, "message" => "Lỗi kết nối đến AI Service: " . $aiResponse['error']];
        }

        // 3. Xử lý dữ liệu Python trả về (Giả định Python trả về mảng các insights)
        $insights = $aiResponse['data']['insights'] ?? [];
        $savedCount = 0;

        foreach ($insights as $item) {
            // Đóng gói vào DTO
            $dto = new AiInsightDTO(
                $user_id, 
                $item['type'],    // 'anomaly', 'forecast', hoặc 'advice'
                $item['content']
            );
            
            // Lưu xuống cơ sở dữ liệu
            if ($this->aiDal->insertInsight($dto)) {
                $savedCount++;
            }
        }

        return [
            "status"  => true, 
            "message" => "AI đã phân tích xong và tạo ra {$savedCount} lời khuyên mới."
        ];
    }

    /**
     * Hàm lấy lịch sử cảnh báo để hiển thị ra View.
     */
    public function getUserInsights(int $user_id): array
    {
        return $this->aiDal->getInsightsByUser($user_id);
    }

    /**
     * Đánh dấu đã đọc.
     */
    public function markInsightAsRead(int $insight_id, int $user_id): bool
    {
        return $this->aiDal->markAsRead($insight_id, $user_id);
    }

    // =========================================================================
    // HELPER: GIAO TIẾP VỚI MICROSERVICE PYTHON (cURL)
    // =========================================================================

    private function callPythonService(array $data): array
    {
        $ch = curl_init($this->aiServiceUrl);
        $jsonData = json_encode($data);

        // Cấu hình cURL POST request
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);
        
        // Cài đặt timeout (chờ AI xử lý tối đa 10 giây)
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        
        curl_close($ch);

        if ($curlError || $httpCode !== 200) {
            return ["status" => false, "error" => $curlError ?: "HTTP Code: {$httpCode}"];
        }

        $decoded = json_decode($response, true);
        return ["status" => true, "data" => $decoded];
    }
}