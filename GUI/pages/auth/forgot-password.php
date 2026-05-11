<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - CashFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .auth-card { border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: none; }
        .input-group-custom { border: 1px solid #dee2e6; border-radius: 12px; overflow: hidden; background-color: #f8f9fa; transition: all 0.2s; }
        .input-group-custom:focus-within { border-color: #0d6efd; background-color: #fff; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); }
        .input-group-custom .input-group-text { background: transparent; border: none; padding-left: 1.25rem; color: #6c757d; }
        .input-group-custom .form-control { background: transparent; border: none; box-shadow: none; padding-left: 0.5rem; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-5 col-xl-4">
                
                <div class="card auth-card bg-white">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="bg-primary bg-opacity-10 text-primary d-inline-flex p-3 rounded-circle mb-3">
                                <i class="bi bi-key-fill fs-2"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">Phục hồi tài khoản</h4>
                            <p class="text-muted small fw-semibold">Nhập email để nhận mật khẩu tạm thời</p>
                        </div>
                        
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger py-2 px-3 small rounded-3 fw-semibold border-0 bg-danger bg-opacity-10 text-danger">
                                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success py-2 px-3 small rounded-3 fw-semibold border-0 bg-success bg-opacity-10 text-success">
                                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>

                        <form action="../../controllers/AuthController.php" method="POST">
                            <input type="hidden" name="action" value="forgot_password">
                            
                            <div class="mb-4">
                                <div class="input-group input-group-lg input-group-custom">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control fw-semibold" placeholder="Nhập địa chỉ Email..." required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 fw-bold py-3 rounded-pill shadow-sm mb-4">
                                Gửi Link Phục Hồi <i class="bi bi-send-fill ms-1"></i>
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <a href="login.php" class="text-muted text-decoration-none small fw-bold"><i class="bi bi-arrow-left me-1"></i> Quay lại đăng nhập</a>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>