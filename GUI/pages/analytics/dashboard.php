<?php
// Tệp mẫu: GUI/pages/analytics/dashboard.php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../autoload.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tổng quan - CashFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <?php require_once __DIR__ . '/../../components/header.php'; ?>

    <main class="container py-4">
        
        <?php require_once __DIR__ . '/../../components/alert.php'; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark mb-0">Bảng điều khiển</h3>
            <button class="btn btn-success rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Thêm giao dịch
            </button>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <p class="text-muted">Nội dung các biểu đồ, thống kê sẽ nằm ở đây...</p>
                        </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../../components/footer.php'; ?>

    <?php require_once __DIR__ . '/../../components/bottom-nav.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>