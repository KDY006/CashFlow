<?php 
session_start();

if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['is_first_login']) && $_SESSION['is_first_login'] == 1) {
        header("Location: setup-password.php");
    } else {
        header("Location: ../analytics/dashboard.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - CashFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .auth-card { border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: none; }
        .input-group-custom { border: 1px solid #dee2e6; border-radius: 12px; overflow: hidden; background-color: #f8f9fa; transition: all 0.2s; }
        .input-group-custom:focus-within { border-color: #198754; background-color: #fff; box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.1); }
        .input-group-custom .input-group-text { background: transparent; border: none; padding-left: 1.25rem; color: #6c757d; }
        .input-group-custom .form-control { background: transparent; border: none; box-shadow: none; padding-left: 0.5rem; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-5 col-xl-4">
                
                <div class="text-center mb-4">
                    <h1 class="fw-bold text-success mb-0"><i class="bi bi-wallet2 me-2"></i>CashFlow</h1>
                    <p class="text-muted mt-2 fw-semibold">Quản lý tài chính thông minh</p>
                </div>

                <div class="card auth-card bg-white">
                    <div class="card-body p-4 p-md-5">
                        <h4 class="fw-bold mb-4 text-center text-dark">Chào mừng trở lại!</h4>
                        
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger py-2 px-3 small rounded-3 fw-semibold border-0 bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success py-2 px-3 small rounded-3 fw-semibold border-0 bg-success bg-opacity-10 text-success">
                                <i class="bi bi-check-circle-fill me-1"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>

                        <form action="../../controllers/AuthController.php" method="POST">
                            <input type="hidden" name="action" value="login">
                            
                            <div class="mb-4">
                                <div class="input-group input-group-lg input-group-custom">
                                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                    <input type="email" name="email" class="form-control fw-semibold" placeholder="Địa chỉ Email" required autofocus>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="input-group input-group-lg input-group-custom">
                                    <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                                    <input type="password" name="password" class="form-control fw-semibold" placeholder="Mật khẩu" required>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end mb-4">
                                <a href="forgot-password.php" class="text-success text-decoration-none small fw-bold">Quên mật khẩu?</a>
                            </div>

                            <button type="submit" class="btn btn-success w-100 fw-bold py-3 rounded-pill shadow-sm mb-4">
                                Đăng Nhập <i class="bi bi-arrow-right-circle ms-1"></i>
                            </button>
                            
                            <div class="text-center small fw-semibold text-muted">
                                Chưa có tài khoản? <a href="register.php" class="text-success text-decoration-none fw-bold ms-1">Đăng ký ngay</a>
                            </div>
                        </form>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>