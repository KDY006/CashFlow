<?php
session_start();
require_once __DIR__ . '/../../autoload.php';

header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user_id'])) exit(json_encode(['status' => false, 'message' => 'Lỗi xác thực.']));

$aiBus = new AiAdvisorBUS();
$action = $_POST['action'] ?? '';

if ($action === 'parse_text') {
    echo json_encode($aiBus->parseTextToTransaction($_SESSION['user_id'], $_POST['text'] ?? ''));
    exit();
}

if ($action === 'chat_consult') {
    echo json_encode($aiBus->chatConsult($_SESSION['user_id'], $_POST['type'] ?? 'summary'));
    exit();
}
echo json_encode(['status' => false, 'message' => 'Hành động không hợp lệ.']);
?>