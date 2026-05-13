<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../../helpers/CsrfHelper.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - CashFlow</title>
    <link rel="icon" href="../../assets/images/logo/logo.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; background: #0f1117; }
        .auth-left { flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 60px 40px; background: linear-gradient(135deg, #1a0a2e 0%, #1e1530 50%, #0f1117 100%); position: relative; overflow: hidden; }
        .auth-left::before { content: ''; position: absolute; width: 600px; height: 600px; background: radial-gradient(circle, rgba(139,92,246,0.12) 0%, transparent 70%); top: -150px; left: -100px; }
        .brand-logo { display: flex; align-items: center; gap: 14px; margin-bottom: 60px; align-self: flex-start; }
        .brand-icon { width: 48px; height: 48px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; color: white; }
        .brand-name { font-size: 22px; font-weight: 800; color: #f1f5f9; }
        .left-hero { position: relative; z-index: 1; max-width: 400px; }
        .lock-visual { width: 110px; height: 110px; background: rgba(139,92,246,0.12); border-radius: 28px; display: flex; align-items: center; justify-content: center; font-size: 48px; color: #a78bfa; margin-bottom: 28px; border: 1px solid rgba(139,92,246,0.2); }
        .left-hero h1 { font-size: 36px; font-weight: 800; line-height: 1.25; color: #f1f5f9; letter-spacing: -1px; margin-bottom: 16px; }
        .left-hero h1 span { color: #a78bfa; }
        .left-hero p { font-size: 15px; color: #94a3b8; line-height: 1.7; }
        .auth-right { width: 480px; flex-shrink: 0; background: #ffffff; display: flex; align-items: center; justify-content: center; padding: 60px 50px; }
        .auth-form-wrap { width: 100%; max-width: 360px; }
        .form-heading { margin-bottom: 32px; }
        .form-heading h2 { font-size: 26px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; margin-bottom: 6px; }
        .form-heading p  { font-size: 14px; color: #64748b; }
        .form-field { margin-bottom: 20px; }
        .field-label { display: block; font-size: 12px; font-weight: 700; color: #374151; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 8px; }
        .field-input { width: 100%; padding: 13px 16px 13px 46px; border: 1.5px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-size: 15px; font-weight: 500; color: #0f172a; font-family: 'Inter', sans-serif; transition: all 0.2s; outline: none; }
        .field-input:focus { border-color: #8b5cf6; background: #fff; box-shadow: 0 0 0 4px rgba(139,92,246,0.1); }
        .field-input::placeholder { color: #94a3b8; font-weight: 400; }
        .field-wrap { position: relative; }
        .field-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 16px; }
        .field-wrap:focus-within .field-icon { color: #8b5cf6; }
        .btn-auth { width: 100%; padding: 14px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; border: none; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 15px rgba(139,92,246,0.35); margin-top: 8px; }
        .btn-auth:hover { transform: translateY(-1px); box-shadow: 0 8px 25px rgba(139,92,246,0.45); }
        .back-link { display: flex; align-items: center; gap: 8px; justify-content: center; margin-top: 24px; font-size: 14px; font-weight: 600; color: #64748b; text-decoration: none; }
        .back-link:hover { color: #7c3aed; }
        .alert-auth { padding: 12px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
        .alert-error   { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .alert-success { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        @media (max-width: 900px) { .auth-left { display: none; } .auth-right { width: 100%; padding: 40px 24px; } }
    </style>
</head>
<body>
    <div class="auth-left">
        <div class="brand-logo">
            <img src="../../assets/images/logo/logo.png" alt="CashFlow Logo" style="width: 52px; height: 52px; object-fit: contain;">
            <span class="brand-name">CashFlow</span>
        </div>
        <div class="left-hero">
            <div class="lock-visual"><i class="bi bi-shield-lock-fill"></i></div>
            <h1>Khôi phục<br>quyền truy cập <span>của bạn</span></h1>
            <p>Chúng tôi sẽ gửi mật khẩu tạm thời đến email đã đăng ký để bạn có thể truy cập lại tài khoản an toàn.</p>
        </div>
    </div>
    <div class="auth-right">
        <div class="auth-form-wrap">
            <div class="form-heading">
                <h2>Quên mật khẩu? 🔑</h2>
                <p>Nhập email đã đăng ký để nhận mật khẩu tạm thời</p>
            </div>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert-auth alert-error"><i class="bi bi-exclamation-triangle-fill"></i><?= htmlspecialchars($_SESSION['error']); ?><?php unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert-auth alert-success"><i class="bi bi-check-circle-fill"></i><?= htmlspecialchars($_SESSION['success']); ?><?php unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <form action="../../controllers/AuthController.php" method="POST">
                <input type="hidden" name="action" value="forgot_password">
                <?= CsrfHelper::inputField() ?>
                <div class="form-field">
                    <label class="field-label">Địa chỉ Email đã đăng ký</label>
                    <div class="field-wrap">
                        <i class="bi bi-envelope-fill field-icon"></i>
                        <input type="email" name="email" class="field-input" placeholder="your@email.com" required autofocus>
                    </div>
                </div>
                <button type="submit" class="btn-auth">Gửi mật khẩu phục hồi &nbsp;<i class="bi bi-send-fill"></i></button>
            </form>
            <a href="login.php" class="back-link"><i class="bi bi-arrow-left"></i> Quay lại đăng nhập</a>
        </div>
    </div>
</body>
</html>