<?php
// Tệp: GUI/pages/profile/index.php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../autoload.php';

// Lấy thông tin user mới nhất từ DB
$userId = $_SESSION['user_id'];
$userDAL = new UserDAL();
$currentUser = $userDAL->getUserById($userId);

if (!$currentUser) {
    header("Location: ../../controllers/AuthController.php?action=logout");
    exit();
}

// Logic hiển thị ảnh đại diện
$avatarUrl = !empty($currentUser['avatar_url']) 
    ? '../../' . ltrim($currentUser['avatar_url'], '/') 
    : 'https://ui-avatars.com/api/?name=' . urlencode($currentUser['full_name']) . '&background=0d6efd&color=fff&size=256';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ cá nhân - CashFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* CSS GIAO DIỆN ĐỒNG BỘ */
        .nav-pills-custom .nav-link { border-radius: 25px; color: #6c757d; font-weight: 600; background: #fff; border: 1px solid #dee2e6; margin: 0 5px; padding: 10px 20px; transition: all 0.2s;}
        .nav-pills-custom .nav-link:hover { background-color: #f8f9fa; }
        .nav-pills-custom .nav-link.active { background-color: #e7f1ff; color: #0d6efd; border-color: #0d6efd; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.15);}
        
        .profile-avatar-container { position: relative; display: inline-block; cursor: pointer; }
        .profile-avatar { width: 140px; height: 140px; border-radius: 50%; object-fit: cover; border: 5px solid #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.08); transition: 0.3s; }
        
        .avatar-edit-overlay { position: absolute; bottom: 5px; right: 5px; background: #0d6efd; color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid #fff; transition: 0.2s; box-shadow: 0 3px 8px rgba(13,110,253,0.3); }
        .profile-avatar-container:hover .avatar-edit-overlay { transform: scale(1.1); }
        .profile-avatar-container:hover .profile-avatar { filter: brightness(0.9); }

        .input-group-custom { border: 1px solid #dee2e6; border-radius: 12px; overflow: hidden; transition: 0.2s; }
        .input-group-custom:focus-within { border-color: #0d6efd; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1); }
        .input-group-custom .input-group-text { background-color: #fff; border: none; padding-left: 1.25rem; }
        .input-group-custom .form-control { border: none; box-shadow: none; padding-left: 0.5rem; background-color: #fff; }
        .input-group-custom.disabled-group { background-color: #f8f9fa; }
        .input-group-custom.disabled-group .input-group-text, .input-group-custom.disabled-group .form-control { background-color: #f8f9fa; }
    </style>
</head>
<body class="bg-light">
    <?php require_once __DIR__ . '/../../components/header.php'; ?>

    <main class="container py-4 mb-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>Hồ sơ cá nhân</h3>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm border-0 rounded-4 bg-white text-center py-5 h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <div class="position-relative mb-4">
                            <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="profile-avatar" id="avatarDisplayLeft" style="cursor: default;">
                            <div class="position-absolute" style="bottom: 5px; right: 5px; background: #198754; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid #fff;" title="Đã xác thực">
                                <i class="bi bi-check-lg"></i>
                            </div>
                        </div>

                        <h4 class="fw-bold text-dark mb-1" id="displayFullNameLeft"><?= htmlspecialchars($currentUser['full_name']) ?></h4>
                        <p class="text-muted mb-4"><?= htmlspecialchars($currentUser['email']) ?></p>
                        
                        <div class="w-100 px-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 bg-light p-3 rounded-4">
                                <span class="text-muted small fw-bold">VAI TRÒ</span>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">Hội viên</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-4">
                                <span class="text-muted small fw-bold">NGÀY THAM GIA</span>
                                <span class="text-dark fw-bold"><?= date('d/m/Y', strtotime($currentUser['created_at'] ?? 'now')) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 bg-white h-100 p-2 p-md-4">
                    
                    <ul class="nav nav-pills nav-pills-custom justify-content-center mb-5" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-pane" type="button" role="tab">
                                <i class="bi bi-card-text me-1"></i> Thông tin cá nhân
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security-pane" type="button" role="tab">
                                <i class="bi bi-shield-lock-fill me-1"></i> Đổi mật khẩu
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="profileTabsContent">
                        
                        <div class="tab-pane fade show active px-md-3" id="info-pane" role="tabpanel">
                            <form id="updateProfileForm">
                                <input type="hidden" name="action" value="update_profile">
                                <input type="hidden" name="current_avatar_url" value="<?= htmlspecialchars($currentUser['avatar_url'] ?? '') ?>">
                                
                                <div class="mb-5 text-center">
                                    <label class="profile-avatar-container" for="avatarInput">
                                        <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="profile-avatar" id="avatarDisplayForm">
                                        <div class="avatar-edit-overlay" title="Tải ảnh mới lên"><i class="bi bi-camera-fill"></i></div>
                                    </label>
                                    <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/jpeg,image/png,image/gif" onchange="previewAvatar(this)">
                                    <div class="small text-muted mt-2">Bấm vào ảnh để thay đổi (Tối đa 2MB)</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted small">ĐỊA CHỈ EMAIL</label>
                                    <div class="input-group input-group-lg input-group-custom disabled-group">
                                        <span class="input-group-text"><i class="bi bi-envelope-fill text-muted opacity-50"></i></span>
                                        <input type="email" class="form-control fw-semibold text-muted" value="<?= htmlspecialchars($currentUser['email']) ?>" readonly disabled>
                                    </div>
                                    <div class="form-text mt-1 ms-2"><i class="bi bi-info-circle me-1"></i>Email dùng để đăng nhập, không thể thay đổi.</div>
                                </div>

                                <div class="mb-5">
                                    <label class="form-label fw-bold text-muted small">HỌ VÀ TÊN</label>
                                    <div class="input-group input-group-lg input-group-custom shadow-sm">
                                        <span class="input-group-text"><i class="bi bi-person-vcard-fill text-primary"></i></span>
                                        <input type="text" name="full_name" class="form-control fw-bold text-dark" value="<?= htmlspecialchars($currentUser['full_name']) ?>" placeholder="Nhập họ và tên..." required>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary rounded-pill fw-bold shadow-sm px-5 py-3">
                                        <i class="bi bi-floppy-fill me-2"></i>Lưu thông tin
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade px-md-3" id="security-pane" role="tabpanel">
                            <form id="changePasswordForm">
                                <input type="hidden" name="action" value="change_password">
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted small">MẬT KHẨU HIỆN TẠI</label>
                                    <div class="input-group input-group-lg input-group-custom shadow-sm">
                                        <span class="input-group-text"><i class="bi bi-key text-muted"></i></span>
                                        <input type="password" name="old_password" class="form-control fw-semibold text-dark" placeholder="••••••••" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted small">MẬT KHẨU MỚI</label>
                                    <div class="input-group input-group-lg input-group-custom shadow-sm">
                                        <span class="input-group-text"><i class="bi bi-shield-lock-fill text-warning"></i></span>
                                        <input type="password" name="new_password" class="form-control fw-semibold text-dark" placeholder="Ít nhất 6 ký tự" minlength="6" required>
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <label class="form-label fw-bold text-muted small">XÁC NHẬN MẬT KHẨU MỚI</label>
                                    <div class="input-group input-group-lg input-group-custom shadow-sm">
                                        <span class="input-group-text"><i class="bi bi-check-circle-fill text-success"></i></span>
                                        <input type="password" name="confirm_password" class="form-control fw-semibold text-dark" placeholder="Nhập lại mật khẩu mới" minlength="6" required>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-dark rounded-pill fw-bold shadow-sm px-5 py-3">
                                        <i class="bi bi-arrow-repeat me-2"></i>Cập nhật mật khẩu
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
        <div id="liveToast" class="toast align-items-center border-0 rounded-3 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-semibold px-3 py-3" id="toastMessage">Thông báo!</div>
                <button type="button" class="btn-close btn-close-white me-3 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <script src="../../assets/js/app.js?v=<?= time() ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const toastEl = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastEl);

        function showToast(message, isSuccess = true) {
            document.getElementById('toastMessage').innerText = message;
            toastEl.className = isSuccess 
                ? 'toast align-items-center text-bg-success border-0 rounded-3 shadow-lg' 
                : 'toast align-items-center text-bg-danger border-0 rounded-3 shadow-lg';
            toast.show();
        }

        // Preview ảnh đại diện khi chọn file
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarDisplayLeft').src = e.target.result;
                    document.getElementById('avatarDisplayForm').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Xử lý Form Sửa Thông Tin
        document.getElementById('updateProfileForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = e.target.querySelector('button[type="submit"]');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
            btn.disabled = true;

            const fd = new FormData(e.target);
            try {
                const res = await fetch('../../controllers/UserController.php', { method: 'POST', body: fd });
                const result = await res.json();
                
                showToast(result.message, result.status);
                
                if (result.status) {
                    // Cập nhật tên hiển thị ở cột trái
                    document.getElementById('displayFullNameLeft').innerText = fd.get('full_name');
                    // Lưu ý: Avatar bên góc phải trên cùng sẽ đổi khi người dùng ấn F5 hoặc chuyển trang.
                }
            } catch (error) {
                showToast('Lỗi kết nối máy chủ!', false);
            } finally {
                btn.innerHTML = '<i class="bi bi-floppy-fill me-2"></i>Lưu thông tin';
                btn.disabled = false;
            }
        });

        // Xử lý Form Đổi Mật Khẩu
        document.getElementById('changePasswordForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = e.target.querySelector('button[type="submit"]');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
            btn.disabled = true;

            const fd = new FormData(e.target);
            try {
                const res = await fetch('../../controllers/UserController.php', { method: 'POST', body: fd });
                const result = await res.json();
                
                showToast(result.message, result.status);
                
                if (result.status) {
                    e.target.reset(); // Xóa trắng các ô nhập mật khẩu nếu thành công
                }
            } catch (error) {
                showToast('Lỗi kết nối máy chủ!', false);
            } finally {
                btn.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i>Cập nhật mật khẩu';
                btn.disabled = false;
            }
        });
    </script>
</body>
</html>