<?php 
session_start();

// Nếu người dùng đã đăng nhập thì không cho vào trang này nữa
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
        body {
            background-color: #f0f2f5; /* Nền xám nhạt phong cách hiện đại */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: none;
        }
        .brand-text {
            color: #198754;
            font-weight: 800;
            font-size: 2rem;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                
                <div class="text-center mb-4">
                    <div class="brand-text">
                        <i class="bi bi-wallet2"></i> CashFlow
                    </div>
                    <p class="text-muted mt-1">Quản lý tài chính thông minh</p>
                </div>

                <div class="card login-card">
                    <div class="card-body p-4 p-md-5">
                        <h4 class="fw-bold mb-4 text-center text-dark">Đăng nhập</h4>
                        
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger py-2 px-3 fs-6">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success py-2 px-3 fs-6">
                                <i class="bi bi-check-circle-fill me-1"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>

                        <form action="../../controllers/AuthController.php" method="POST">
                            <input type="hidden" name="action" value="login">
                            
                            <div class="mb-3">
                                <input type="email" name="email" class="form-control form-control-lg fs-6" placeholder="Địa chỉ Email" required autofocus>
                            </div>
                            
                            <div class="mb-3">
                                <input type="password" name="password" class="form-control form-control-lg fs-6" placeholder="Mật khẩu" required>
                            </div>
                            
                            <button type="submit" class="btn btn-success w-100 fw-bold fs-5 py-2 mb-3">Đăng Nhập</button>
                            
                            <div class="text-center mb-3">
                                <a href="forgot-password.php" class="text-success text-decoration-none fw-semibold">Quên mật khẩu?</a>
                            </div>

                            <hr class="text-muted mb-4">
                            
                            <div class="text-center">
                                <a href="register.php" class="btn btn-outline-success fw-bold px-4">Tạo tài khoản mới</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>