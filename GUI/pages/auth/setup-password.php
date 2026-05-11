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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .auth-card { border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: none; border-top: 5px solid #ffc107; }
        .input-group-custom { border: 1px solid #dee2e6; border-radius: 12px; overflow: hidden; background-color: #f8f9fa; transition: all 0.2s; }
        .input-group-custom:focus-within { border-color: #ffc107; background-color: #fff; box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.2); }
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
                            <h4 class="fw-bold text-dark">Xin chào, <?= htmlspecialchars($_SESSION['user_name']); ?>!</h4>
                            <p class="text-muted small fw-semibold">Vui lòng thiết lập mật khẩu cá nhân mới của bạn để đảm bảo an toàn tuyệt đối.</p>
                        </div>
                        
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger py-2 px-3 small rounded-3 fw-semibold border-0 bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-x-octagon-fill me-1"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>

                        <form action="../../controllers/AuthController.php" method="POST">
                            <input type="hidden" name="action" value="setup_password">
                            
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-danger ms-1">MẬT KHẨU TẠM THỜI (TRONG EMAIL)</label>
                                <div class="input-group input-group-lg input-group-custom">
                                    <span class="input-group-text text-danger"><i class="bi bi-envelope-open-fill"></i></span>
                                    <input type="password" name="old_password" class="form-control fw-bold text-danger" placeholder="Nhập mã 6 ký tự" required>
                                </div>
                            </div>

                            <hr class="my-4 border-secondary opacity-25">
                            
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted ms-1">MẬT KHẨU CÁ NHÂN MỚI</label>
                                <div class="input-group input-group-lg input-group-custom">
                                    <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                                    <input type="password" name="new_password" class="form-control fw-semibold" placeholder="Tối thiểu 6 ký tự" required minlength="6">
                                </div>
                            </div>
                            
                            <div class="mb-5">
                                <label class="form-label small fw-bold text-muted ms-1">XÁC NHẬN MẬT KHẨU</label>
                                <div class="input-group input-group-lg input-group-custom">
                                    <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                                    <input type="password" name="confirm_password" class="form-control fw-semibold" placeholder="Nhập lại mật khẩu mới" required minlength="6">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-warning text-dark w-100 fw-bold py-3 rounded-pill shadow-sm">
                                <i class="bi bi-key-fill me-2"></i> Xác Nhận & Truy Cập
                            </button>
                        </form>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>