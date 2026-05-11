<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - CashFlow</title>
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
            <div class="col-12 col-md-7 col-lg-6 col-xl-5">
                
                <div class="card auth-card bg-white mt-4 mt-md-0">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-primary mb-2"><i class="bi bi-person-plus-fill me-2"></i>Tạo tài khoản</h2>
                            <p class="text-muted small fw-semibold">Bắt đầu hành trình quản lý tài chính của bạn</p>
                        </div>
                        
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger py-2 px-3 small rounded-3 fw-semibold border-0 bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>

                        <form action="../../controllers/AuthController.php" method="POST">
                            <input type="hidden" name="action" value="register">
                            
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted ms-1">HỌ VÀ TÊN</label>
                                <div class="input-group input-group-lg input-group-custom">
                                    <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                                    <input type="text" name="full_name" class="form-control fw-semibold" placeholder="Nguyễn Văn A" required>
                                </div>
                            </div>
                            
                            <div class="mb-5">
                                <label class="form-label small fw-bold text-muted ms-1">ĐỊA CHỈ EMAIL</label>
                                <div class="input-group input-group-lg input-group-custom">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control fw-semibold" placeholder="name@example.com" required>
                                </div>
                                <div class="form-text small mt-2 ms-1 text-primary"><i class="bi bi-info-circle me-1"></i>Mật khẩu mặc định sẽ được gửi tới Email này.</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 fw-bold py-3 rounded-pill shadow-sm mb-4">
                                Đăng Ký Trải Nghiệm <i class="bi bi-stars ms-1"></i>
                            </button>
                        </form>
                        
                        <div class="text-center small fw-semibold text-muted">
                            Đã có tài khoản? <a href="login.php" class="text-primary text-decoration-none fw-bold ms-1">Đăng nhập</a>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>