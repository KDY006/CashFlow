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
    <title>Đăng ký tài khoản - CashFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; background: #0f1117; color: #e2e8f0; }
        .auth-left {
            flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center;
            padding: 60px 40px;
            background: linear-gradient(135deg, #0d1829 0%, #1a1f2e 50%, #0f1117 100%);
            position: relative; overflow: hidden;
        }
        .auth-left::before { content: ''; position: absolute; width: 600px; height: 600px; background: radial-gradient(circle, rgba(59,130,246,0.12) 0%, transparent 70%); top: -150px; left: -100px; pointer-events: none; }
        .auth-left::after  { content: ''; position: absolute; width: 400px; height: 400px; background: radial-gradient(circle, rgba(16,185,129,0.08) 0%, transparent 70%); bottom: -100px; right: -50px; pointer-events: none; }
        .brand-logo { display: flex; align-items: center; gap: 14px; margin-bottom: 60px; align-self: flex-start; }
        .brand-icon { width: 48px; height: 48px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; color: white; box-shadow: 0 8px 24px rgba(16,185,129,0.35); }
        .brand-name { font-size: 22px; font-weight: 800; color: #f1f5f9; letter-spacing: -0.5px; }
        .left-hero { position: relative; z-index: 1; max-width: 420px; }
        .left-hero h1 { font-size: 40px; font-weight: 800; line-height: 1.2; color: #f1f5f9; letter-spacing: -1px; margin-bottom: 20px; }
        .left-hero h1 span { color: #60a5fa; }
        .left-hero p { font-size: 16px; color: #94a3b8; line-height: 1.7; margin-bottom: 40px; }
        .step-list { display: flex; flex-direction: column; gap: 16px; }
        .step-item { display: flex; align-items: flex-start; gap: 14px; }
        .step-num { width: 28px; height: 28px; border-radius: 50%; background: rgba(96,165,250,0.15); color: #60a5fa; font-size: 13px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .step-text { font-size: 14px; color: #cbd5e1; font-weight: 500; padding-top: 4px; line-height: 1.5; }
        .auth-right { width: 480px; flex-shrink: 0; background: #ffffff; display: flex; align-items: center; justify-content: center; padding: 60px 50px; }
        .auth-form-wrap { width: 100%; max-width: 360px; }
        .form-heading { margin-bottom: 32px; }
        .form-heading h2 { font-size: 26px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; margin-bottom: 6px; }
        .form-heading p  { font-size: 14px; color: #64748b; font-weight: 500; }
        .form-field { margin-bottom: 18px; }
        .field-label { display: block; font-size: 12px; font-weight: 700; color: #374151; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 8px; }
        .field-input { width: 100%; padding: 13px 16px 13px 46px; border: 1.5px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-size: 15px; font-weight: 500; color: #0f172a; font-family: 'Inter', sans-serif; transition: all 0.2s; outline: none; }
        .field-input:focus { border-color: #3b82f6; background: #fff; box-shadow: 0 0 0 4px rgba(59,130,246,0.1); }
        .field-input::placeholder { color: #94a3b8; font-weight: 400; }
        .field-wrap { position: relative; }
        .field-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 16px; pointer-events: none; transition: color 0.2s; }
        .field-wrap:focus-within .field-icon { color: #3b82f6; }
        .info-note { display: flex; align-items: flex-start; gap: 8px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 12px 14px; font-size: 13px; color: #1d4ed8; font-weight: 500; margin-top: 10px; }
        .btn-primary-auth { width: 100%; padding: 14px; background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border: none; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; transition: all 0.2s; letter-spacing: 0.3px; box-shadow: 0 4px 15px rgba(59,130,246,0.35); margin-top: 8px; }
        .btn-primary-auth:hover { transform: translateY(-1px); box-shadow: 0 8px 25px rgba(59,130,246,0.45); }
        .divider { display: flex; align-items: center; gap: 12px; margin: 24px 0; }
        .divider-line { flex: 1; height: 1px; background: #e2e8f0; }
        .divider-text { font-size: 12px; color: #94a3b8; font-weight: 500; white-space: nowrap; }
        .auth-footer { text-align: center; font-size: 14px; color: #64748b; font-weight: 500; }
        .auth-footer a { color: #2563eb; font-weight: 700; text-decoration: none; }
        .alert-auth { padding: 12px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
        .alert-error { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        @media (max-width: 900px) { .auth-left { display: none; } .auth-right { width: 100%; padding: 40px 24px; } }
    </style>
</head>
<body>
    <div class="auth-left">
        <div class="brand-logo">
            <div class="brand-icon"><i class="bi bi-wallet2"></i></div>
            <span class="brand-name">CashFlow</span>
        </div>
        <div class="left-hero">
            <h1>Bắt đầu hành trình<br>tài chính <span>thông minh</span></h1>
            <p>Chỉ cần 3 bước đơn giản để có ngay công cụ quản lý tài chính cá nhân mạnh mẽ nhất.</p>
            <div class="step-list">
                <div class="step-item">
                    <div class="step-num">1</div>
                    <div class="step-text">Đăng ký với Họ tên và Email của bạn</div>
                </div>
                <div class="step-item">
                    <div class="step-num">2</div>
                    <div class="step-text">Nhận mật khẩu tạm thời qua hòm thư email</div>
                </div>
                <div class="step-item">
                    <div class="step-num">3</div>
                    <div class="step-text">Đăng nhập và bắt đầu ghi chép tài chính ngay!</div>
                </div>
            </div>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-form-wrap">
            <div class="form-heading">
                <h2>Tạo tài khoản mới ✨</h2>
                <p>Miễn phí hoàn toàn, không cần thẻ ngân hàng</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert-auth alert-error">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <?= htmlspecialchars($_SESSION['error']); ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="../../controllers/AuthController.php" method="POST">
                <input type="hidden" name="action" value="register">
                <?= CsrfHelper::inputField() ?>

                <div class="form-field">
                    <label class="field-label">Họ và Tên</label>
                    <div class="field-wrap">
                        <i class="bi bi-person-fill field-icon"></i>
                        <input type="text" name="full_name" class="field-input" placeholder="Nguyễn Văn A" required autofocus>
                    </div>
                </div>

                <div class="form-field">
                    <label class="field-label">Địa chỉ Email</label>
                    <div class="field-wrap">
                        <i class="bi bi-envelope-fill field-icon"></i>
                        <input type="email" name="email" class="field-input" placeholder="your@email.com" required>
                    </div>
                    <div class="info-note">
                        <i class="bi bi-info-circle-fill" style="margin-top:1px;flex-shrink:0"></i>
                        Mật khẩu tạm thời sẽ được gửi đến Email này. Hãy kiểm tra cả hòm thư Spam.
                    </div>
                </div>

                <button type="submit" class="btn-primary-auth">
                    Tạo tài khoản &nbsp;<i class="bi bi-stars"></i>
                </button>
            </form>

            <div class="divider">
                <div class="divider-line"></div>
                <span class="divider-text">ĐÃ CÓ TÀI KHOẢN?</span>
                <div class="divider-line"></div>
            </div>
            <div class="auth-footer">
                <a href="login.php">Đăng nhập ngay →</a>
            </div>
        </div>
    </div>
</body>
</html>