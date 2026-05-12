<?php
// 1. Nhúng bộ nạp tự động của Composer để load các thư viện bên ngoài (PHPMailer)
require_once __DIR__ . '/vendor/autoload.php';

// 2. Bộ nạp tự động nội bộ của dự án CashFlow (cho DAL, BUS, DTO...)
spl_autoload_register(function ($class_name) {
    $directories = [
        'config/',
        'DTO/',
        'DAL/',
        'BUS/',
        'GUI/controllers/',
        'helpers/',
        'middleware/'
    ];

    foreach ($directories as $directory) {
        $file = __DIR__ . '/' . $directory . $class_name . '.php';
        
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
?>