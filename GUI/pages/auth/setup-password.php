<?php 
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php'; 

if (!isset($_SESSION['is_first_login']) || $_SESSION['is_first_login'] != 1) {
    header("Location: ../analytics/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thiết lập mật khẩu - CashFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow border-0 rounded-3 border-top border-4 border-warning">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h4 class="fw-bold">Xin chào, <?= htmlspecialchars($_SESSION['user_name']); ?>!</h4>
                            <p class="text-muted">Để đảm bảo an toàn, vui lòng nhập mật khẩu tạm thời từ email và thiết lập mật khẩu cá nhân mới của bạn.</p>
                        </div>
                        
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger py-2"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>

                        <form action="../../controllers/AuthController.php" method="POST">
                            <input type="hidden" name="action" value="setup_password">
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-danger">Mật khẩu tạm thời (Trong email)</label>
                                <input type="password" name="old_password" class="form-control" placeholder="Nhập mã 6 ký tự" required>
                            </div>

                            <hr class="my-4">
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mật khẩu cá nhân mới</label>
                                <input type="password" name="new_password" class="form-control" placeholder="Tối thiểu 6 ký tự" required minlength="6">
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Xác nhận mật khẩu</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu mới" required minlength="6">
                            </div>
                            
                            <button type="submit" class="btn btn-warning w-100 fw-bold py-2">Xác Nhận & Truy Cập</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>