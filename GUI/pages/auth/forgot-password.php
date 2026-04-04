<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - CashFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow border-0 rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-primary">Phục hồi tài khoản</h3>
                            <p class="text-muted">Nhập email để nhận link thiết lập lại mật khẩu</p>
                        </div>
                        
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger py-2"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success py-2"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                        <?php endif; ?>

                        <form action="../../controllers/AuthController.php" method="POST">
                            <input type="hidden" name="action" value="forgot_password">
                            
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Địa chỉ Email</label>
                                <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Gửi Link Phục Hồi</button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <a href="login.php" class="text-decoration-none fw-semibold">Quay lại đăng nhập</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>