<?php 
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php'; 
require_once __DIR__ . '/../../../helpers/CsrfHelper.php';

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
    <link rel="icon" href="../../assets/images/logo/logo.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; background: #0f1117; }
        .auth-left { flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 60px 40px; background: linear-gradient(135deg, #1c1500 0%, #1e1a08 50%, #0f1117 100%); position: relative; overflow: hidden; }
        .auth-left::before { content: ''; position: absolute; width: 600px; height: 600px; background: radial-gradient(circle, rgba(245,158,11,0.1) 0%, transparent 70%); top: -150px; left: -100px; }
        .brand-logo { display: flex; align-items: center; gap: 14px; margin-bottom: 60px; align-self: flex-start; }
        .brand-icon { width: 48px; height: 48px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; color: white; }
        .brand-name { font-size: 22px; font-weight: 800; color: #f1f5f9; }
        .left-hero { position: relative; z-index: 1; max-width: 400px; }
        .key-visual { width: 110px; height: 110px; background: rgba(245,158,11,0.1); border-radius: 28px; display: flex; align-items: center; justify-content: center; font-size: 48px; color: #fbbf24; margin-bottom: 28px; border: 1px solid rgba(245,158,11,0.2); }
        .left-hero h1 { font-size: 36px; font-weight: 800; line-height: 1.25; color: #f1f5f9; letter-spacing: -1px; margin-bottom: 16px; }
        .left-hero h1 span { color: #fbbf24; }
        .left-hero p { font-size: 15px; color: #94a3b8; line-height: 1.7; margin-bottom: 32px; }
        .warning-box { background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.25); border-radius: 12px; padding: 16px; }
        .warning-box p { font-size: 13px; color: #fcd34d; font-weight: 500; line-height: 1.6; }
        .warning-box strong { color: #fbbf24; }
        .auth-right { width: 520px; flex-shrink: 0; background: #ffffff; display: flex; align-items: center; justify-content: center; padding: 60px 50px; overflow-y: auto; }
        .auth-form-wrap { width: 100%; max-width: 380px; }
        .form-heading { margin-bottom: 28px; }
        .form-heading h2 { font-size: 24px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; margin-bottom: 6px; }
        .form-heading p  { font-size: 14px; color: #64748b; }
        .form-field { margin-bottom: 18px; }
        .field-label { display: block; font-size: 12px; font-weight: 700; color: #374151; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 8px; }
        .field-input { width: 100%; padding: 13px 16px 13px 46px; border: 1.5px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-size: 15px; font-weight: 500; color: #0f172a; font-family: 'Inter', sans-serif; transition: all 0.2s; outline: none; }
        .field-input:focus { border-color: #f59e0b; background: #fff; box-shadow: 0 0 0 4px rgba(245,158,11,0.1); }
        .field-input::placeholder { color: #94a3b8; font-weight: 400; }
        .field-input.field-danger { border-color: #fca5a5; background: #fff5f5; }
        .field-input.field-danger:focus { border-color: #ef4444; box-shadow: 0 0 0 4px rgba(239,68,68,0.1); }
        .field-wrap { position: relative; }
        .field-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 16px; }
        .field-wrap:focus-within .field-icon { color: #f59e0b; }
        .field-hint { font-size: 12px; color: #94a3b8; margin-top: 6px; font-weight: 500; }
        .divider-section { border-top: 1px solid #f1f5f9; margin: 22px 0; padding-top: 22px; }
        .divider-section span { display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px; }
        .btn-auth { width: 100%; padding: 14px; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border: none; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 15px rgba(245,158,11,0.35); }
        .btn-auth:hover { transform: translateY(-1px); box-shadow: 0 8px 25px rgba(245,158,11,0.45); }
        .alert-auth { padding: 12px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
        .alert-error { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
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
            <div class="key-visual"><i class="bi bi-key-fill"></i></div>
            <h1>Chào mừng,<br><span><?= htmlspecialchars($_SESSION['user_name'] ?? 'bạn'); ?>!</span></h1>
            <p>Bạn cần thiết lập mật khẩu cá nhân trước khi sử dụng hệ thống. Đây là bước bảo mật quan trọng nhất.</p>
            <div class="warning-box">
                <p><strong>⚠️ Lưu ý bảo mật:</strong> Mật khẩu tạm thời chỉ có giá trị <strong>một lần duy nhất</strong>. Sau khi thiết lập thành công, bạn sẽ được yêu cầu đăng nhập lại.</p>
            </div>
        </div>
    </div>
    <div class="auth-right">
        <div class="auth-form-wrap">
            <div class="form-heading">
                <h2>Thiết lập mật khẩu 🔐</h2>
                <p>Kiểm tra email và nhập mật khẩu tạm thời bên dưới</p>
            </div>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert-auth alert-error"><i class="bi bi-x-octagon-fill"></i><?= htmlspecialchars($_SESSION['error']); ?><?php unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <form action="../../controllers/AuthController.php" method="POST">
                <input type="hidden" name="action" value="setup_password">
                <?= CsrfHelper::inputField() ?>
                <div class="form-field">
                    <label class="field-label" style="color:#dc2626;">Mật khẩu tạm thời (trong Email)</label>
                    <div class="field-wrap">
                        <i class="bi bi-envelope-open-fill field-icon" style="color:#fca5a5;"></i>
                        <input type="password" name="old_password" class="field-input field-danger" placeholder="Nhập mã từ email..." required autocomplete="off">
                    </div>
                    <p class="field-hint">Kiểm tra cả thư mục Spam nếu không thấy.</p>
                </div>
                <div class="divider-section">
                    <span>Mật khẩu mới của bạn</span>
                    <div class="form-field">
                        <label class="field-label">Mật khẩu cá nhân mới</label>
                        <div class="field-wrap">
                            <i class="bi bi-shield-lock field-icon"></i>
                            <input type="password" name="new_password" class="field-input" placeholder="Tối thiểu 6 ký tự..." required minlength="6">
                        </div>
                    </div>
                    <div class="form-field">
                        <label class="field-label">Xác nhận mật khẩu</label>
                        <div class="field-wrap">
                            <i class="bi bi-check-circle field-icon"></i>
                            <input type="password" name="confirm_password" class="field-input" placeholder="Nhập lại mật khẩu mới..." required minlength="6">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-auth"><i class="bi bi-key-fill me-2"></i>Xác nhận &amp; Kích hoạt tài khoản</button>
            </form>
        </div>
    </div>
</body>
</html>