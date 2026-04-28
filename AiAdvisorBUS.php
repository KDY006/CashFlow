<?php

class AiAdvisorBUS
{
    private string $pythonApiUrl;
    private int $timeout;

    public function __construct()
    {
        $this->pythonApiUrl = defined('AI_API_URL') ? AI_API_URL : 'http://127.0.0.1:5000';
        $this->timeout = 15;
    }

    public function validatePayload(array $payload): array
    {
        $errors = [];

        if (empty($payload)) {
            $errors['payload'] = 'Dữ liệu gửi sang AI không được rỗng.';
        }

        return $errors;
    }

    public function analyzeSpending(array $payload): array
    {
        $errors = $this->validatePayload($payload);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        return $this->sendPostRequest('/analyze-spending', $payload);
    }

    public function detectAnomalies(array $payload): array
    {
        $errors = $this->validatePayload($payload);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        return $this->sendPostRequest('/detect-anomalies', $payload);
    }

    public function forecastBudgetRisk(array $payload): array
    {
        $errors = $this->validatePayload($payload);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        return $this->sendPostRequest('/forecast-budget-risk', $payload);
    }

    public function generateAdvice(array $payload): array
    {
        $errors = $this->validatePayload($payload);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        return $this->sendPostRequest('/generate-advice', $payload);
    }

    public function getFullAdvisorReport(array $payload): array
    {
        $errors = $this->validatePayload($payload);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $spending = $this->analyzeSpending($payload);
        $anomalies = $this->detectAnomalies($payload);
        $forecast = $this->forecastBudgetRisk($payload);
        $advice = $this->generateAdvice($payload);

        return [
            'success' => true,
            'spending_analysis' => $spending,
            'anomalies' => $anomalies,
            'forecast' => $forecast,
            'advice' => $advice
        ];
    }

    private function sendPostRequest(string $endpoint, array $payload): array
    {
        $url = rtrim($this->pythonApiUrl, '/') . $endpoint;
        $ch = curl_init($url);

        if ($ch === false) {
            return [
                'success' => false,
                'message' => 'Không khởi tạo được cURL.'
            ];
        }

        $jsonData = json_encode($payload, JSON_UNESCAPED_UNICODE);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_TIMEOUT => $this->timeout
        ]);

        $response = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($response === false || $curlError) {
            return [
                'success' => false,
                'message' => 'Không thể kết nối đến AI service.',
                'error' => $curlError
            ];
        }

        $decoded = json_decode($response, true);

        if (!is_array($decoded)) {
            return [
                'success' => false,
                'message' => 'AI service trả về dữ liệu không hợp lệ.',
                'raw_response' => $response,
                'http_code' => $httpCode
            ];
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            return [
                'success' => false,
                'message' => $decoded['message'] ?? 'AI service xử lý thất bại.',
                'http_code' => $httpCode,
                'data' => $decoded
            ];
        }

        return [
            'success' => true,
            'message' => $decoded['message'] ?? 'Gọi AI service thành công.',
            'data' => $decoded
        ];
    }
}