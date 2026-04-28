<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

try {
    $conn = Database::getInstance();
    echo "Kết nối database thành công!";
} catch (Exception $e) {
    echo "Lỗi kết nối: " . $e->getMessage();
}

require_once 'config/database.php';

try {
    $conn = Database::getInstance();
    echo "Kết nối database thành công!";
} catch (Exception $e) {
    echo "Lỗi kết nối: " . $e->getMessage();
}
?>